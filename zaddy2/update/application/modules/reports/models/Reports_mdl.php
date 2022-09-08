<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_mdl extends CI_Model {

	public function __construct()
        {
                parent::__construct();

                $this->table="transactions";

                $this->user=$this->session->userdata['user_id'];
        }

	
	public function countTransactions($searchData=null){
        
        if($searchData){
            
        $startDate=$searchData['startDate'];
		$endDate=$searchData['endDate'];
		
		if(!empty($startDate))
		$this->db->where("paymentDate >='".$startDate."' AND paymentDate < DATE_ADD('".$endDate."', INTERVAL 1 DAY)");
		
		if(!empty($searchData['agentNo']))
		   $this->db->where("agentNo",$searchData['agentNo']);
		   
		if(!empty($searchData['customerNo']))
		   $this->db->where("customerNo",$searchData['customerNo']);
		
		if(!empty($searchData['tranStatus']))
		   $this->db->where("tranStatus",$searchData['tranStatus']); 
        }
        
		$table=$this->table;
		$this->db->select('requestRef');
		$rows=$this->db->get($table)->result_array();
		return count($rows);
	}

	public function getTransactions($limit=0,$start=0,$searchData=null)
	{
	    
		
		$startDate=$searchData['startDate'];
		$endDate=$searchData['endDate'];
		$hint=$searchData['key'];
		
		 if(!empty($hint))
		     $this->db->like('itemName','%'.$hint.'%');
		   
		if(!empty($startDate))
		$this->db->where("paymentDate >='".$startDate."' AND paymentDate < DATE_ADD('".$endDate."', INTERVAL 1 DAY)");
		
		if(!empty($searchData['agentNo']))
		   $this->db->where("agentNo",$searchData['agentNo']);
		
		if(!empty($searchData['tranStatus']))
		   $this->db->where("tranStatus",$searchData['tranStatus']); 
		   
	    if(!empty($searchData['customerNo']))
		   $this->db->where("customerNo",$searchData['customerNo']);
		   
		$table="transactions_view";

		if($limit>0)
		$this->db->limit($limit,$start);
		$this->db->order_by('id','DESC');
		$query=$this->db->get($table);
		
		return $query->result();
	}
	
	public function getCommissionList($searchData){
	    
	    $table=$this->table;
		
		 $startDate=$searchData['start'];
		 $endDate=$searchData['end'];
		 
		$this->db->select('agentNo');
		 if(!empty($searchData['agentNo']))
		$this->db->where('agentNo',$searchData['agentNo']);
		$this->db->where("paymentDate >='".$startDate."' AND paymentDate < DATE_ADD('".$endDate."', INTERVAL 1 DAY)");
		$this->db->where("tranStatus",'SUCCESSFUL'); 
		$this->db->where("commissionState",0); 
		$this->db->order_by("id","desc");
		$this->db->group_by("agentNo");
		$query=$this->db->get($table);
		
		$agents=$query->result();
		
		$commList=array();
		
		foreach( $agents as $agent){
		    
		    
		$this->db->select("sum(agent_fee) as commission,transactions.agentNo,agents.names");
		
		$this->db->where('transactions.agentNo',$agent->agentNo);
		$this->db->where("paymentDate >='".$startDate."' AND paymentDate < DATE_ADD('".$endDate."', INTERVAL 1 DAY)");
		$this->db->where("tranStatus",'SUCCESSFUL'); 
		$this->db->where("commissionState",0); 
		$this->db->join("agents","agents.agentNo=transactions.agentNo");
		$this->db->order_by("transactions.id","desc");
		
		$query2=$this->db->get($table);
		
		$comms=$query2->row();
		
		$row=array(
		    "commission"=>number_format((float)$comms->commission, 2, '.', ''),
		    "agentNo"=>$comms->agentNo,
		    "names"=>$comms->names
		    );
		if(!empty($comms->agentNo))
		 array_push($commList,$row);
		
		}
		
		return $commList;
		
	    
	}
	
	public function getPendingTransactions(){
	    
	    $table=$this->table;
	    $this->db->where("retryCount <=3");
		$this->db->where("tranStatus","PENDING");
		$this->db->order_by('id','DESC');
		$query=$this->db->get($table);
		
		return $query->result();
	}
	
	public function countStatement($agentNo){

		$table=$this->table;
		
		$this->db->select('requestRef');
		$this->db->where_in('responseCode',SUCCESS_CODES);
		$this->db->or_where('responseCode',PENDING_CODE);
		//$this->db->where('agentNo',$agentNo);
		$this->db->where(" (agentNo='".$agentNo."' or(agentNo='".LOAD_TERMIANL."' AND customerNo='".$agentNo."'))");
		$rows=$this->db->get($table)->result_array();
		
		if(empty($agentNo))
		 return 0;
		
		return count($rows);
	}

	public function getStatement($agentNo,$searchData,$limit=0,$start=0)
	{
		$table="transactions";
		
		$startDate=$searchData['startDate'];
		$endDate=$searchData['endDate'];
		
		$startBal =$this->getStartBal($startDate,$agentNo);

    	$this->db->SELECT("transactions.*,
    		(SELECT Max(agents.names) from agents where 
    		(agentNo=transactions.agentNo OR agentNo=transactions.customerNo)) as names ");

		$this->db->where("( 
	( tranStatus in ('SUCCESSFUL','PENDING') AND impact<0) 
	OR ( tranStatus in ('SUCCESSFUL','PENDING') AND impact>0 and paymentDate<'2019-12-30 03:24') 
	OR ( tranStatus in ('SUCCESSFUL') AND impact>0 and paymentDate>='2019-12-30 03:24'))
	");  //date refers to when pending withdraws stopped being credits
	
    $this->db->where(" (transactions.agentNo='".$agentNo."' OR (customerNo='".$agentNo."' AND transactions.agentNo ='".LOAD_TERMIANL."'))");

		$this->db->where("paymentDate >='".$startDate."' AND paymentDate < DATE_ADD('".$endDate."', INTERVAL 1 DAY)");

		$this->db->order_by('transactions.id','ASC');
		$query=$this->db->get($table);
		
		$statement=array("startingBal"=>$startBal,"statement"=>$query->result());
		return $statement;
		
	}



	public function getStartBal($startDate,$agentNo){

		$this->db->select("sum(impact) as starting_balance");
    	$this->db->where("paymentDate <='".$startDate."'");
	
		$this->db->where("( 
	( tranStatus in ('SUCCESSFUL','PENDING') AND impact<0) 
	OR ( tranStatus in ('SUCCESSFUL','PENDING') AND impact>0 and paymentDate<'2019-12-30 03:24') 
	OR ( tranStatus in ('SUCCESSFUL') AND impact>0 and paymentDate>='2019-12-30 03:24'))
	");  //date refers to when pending withdraws stopped being credits
	
    $this->db->where(" (agentNo='".$agentNo."' OR (customerNo='".$agentNo."' AND agentNo ='".LOAD_TERMIANL."'))");
    	
    	$qry=$this->db->get("transactions");
    	$prev=$qry->row();
    	

    	$startBal=$prev->starting_balance;
    	 return $startBal;
	}

	public function getByRef($tranref)
	{
		$table=$this->table;;
		$this->db->where('requestRef',$tranref);
		$query=$this->db->get($table);
		return $query->row_array();
	}
	
	public function getFees($amount,$paymentCode)
	{
	     /*if(strpos($paymentCode,"379")==0){
	        $paymentCode="379";
	    }*/
	    
		$this->db->where(" upperlimit>='".$amount."' AND paymentCode='".$paymentCode."' AND (lowerlimit-$amount+2)<0");
		$query=$this->db->get("fees");
		$row=$query->row();
		
	  if($row){
		  $isPercentage=$row->isPercentage;
		  if($isPercentage)
		    return $amount*($row->us/100);
		    
		    return $row->us;
		}
		
		return 0;
//	return $this->db->last_query();
		
	}
	
public function getAllAgentCommission(){

	$this->db->select("sum(agent_fee) as commission");
	$this->db->where("tranStatus in ('SUCCESSFUL','PENDING')");  
	$this->db->where('commissionState',0);
	//date refers to when pending withdraws stopped being credits
    
	$qry=$this->db->get("transactions");
	return $qry->row()->commission;

}

public function getAllPaidCommission(){

	$this->db->select("sum(agent_fee) as commission");
	$this->db->where("tranStatus in ('SUCCESSFUL','PENDING')");  
	$this->db->where('commissionState',1);
	//date refers to when pending withdraws stopped being credits
    
	$qry=$this->db->get("transactions");
	return $qry->row()->commission;

}

public function getOurCommission(){

	$this->db->select("sum(our_fee) as commission");
	$this->db->where("tranStatus in ('SUCCESSFUL','PENDING')");  //date refers to when pending withdraws stopped being credits
    
	$qry=$this->db->get("transactions");
	return $qry->row()->commission;

}

public function updateTransaction($postdata){

	$ref=$postdata['requestRef'];
	$this->db->where("requestRef",$ref);

	$done=$this->db->update($this->table,$data);

	if($done){

		return "ok";
	}
	else{

		return "failed";
	}
	
	

}

	public function getPaidCommissions()
	{
		$table="paid_commissions";
		$this->db->select("sum(amount) as amount");
		$query=$this->db->get($table);
		$res= $query->row();
		return $res->amount;
	}


    	public function getTransactionsSpec()
	{
		$table=$this->table;
		$this->db->order_by('id','DESC');
		$query=$this->db->get($table);
		
		return $query->result();
	}
	
	public function getTestTransactions($limit=10,$start=0,$searchData=null)
	{
		$startDate=$searchData['startDate'];
		$endDate=$searchData['endDate'];
		if(!empty($startDate))
		$this->db->where("paymentDate >='".$startDate."' AND paymentDate < DATE_ADD('".$endDate."', INTERVAL 1 DAY)");
		
		if(!empty($searchData['agentNo']))
		   $this->db->where("agentNo",$searchData['agentNo']);
		
		if(!empty($searchData['tranStatus']))
		   $this->db->where("tranStatus",$searchData['tranStatus']); 
		   
	    if(!empty($searchData['customerNo']))
		   $this->db->where("customerNo",$searchData['customerNo']);
		   
		$table=$this->table;
		$this->db->limit($limit,$start);
		$this->db->order_by('id','DESC');
		$query=$this->db->get("test_transactions");
		
		return $query->result();
	}


public function getBalance($agentNo){

	$qry = $this->db->query("SELECT
        SUM(internal_impact_sum) as balance
    FROM
        ((SELECT
            SUM(`transactions`.impact) AS internal_impact_sum 
        FROM
            `transactions` 
        WHERE
            (
                (
                    `transactions`.`tranStatus` IN (
                        'SUCCESSFUL'
                    ) 
                    AND `transactions`.`impact` > 0 
                    AND `transactions`.paymentDate >= '2019-12-30 03:24'
                )
            ) 
            AND (
                (
                    `transactions`.`customerNo` = '".$agentNo."' 
                    AND `transactions`.agentNo = '3LD0001'
                )
            )) 
    UNION
    DISTINCT (SELECT
        SUM(`transactions`.impact) AS internal_impact_sum 
    FROM
        `transactions` 
    WHERE
        ((`transactions`.`tranStatus` IN ('SUCCESSFUL', 'PENDING') 
        AND `transactions`.`impact` > 0 
        AND `transactions`.`paymentDate` < '2019-12-30 03:24')) 
        AND ((`transactions`.`customerNo` = '".$agentNo."' 
        AND `transactions`.agentNo = '3LD0001'))) 
UNION
DISTINCT (SELECT
    SUM(`transactions`.impact) AS internal_impact_sum 
FROM
    `transactions` 
WHERE
    ((`transactions`.`tranStatus` IN ('SUCCESSFUL') 
    AND `transactions`.`impact` > 0 
    AND `transactions`.paymentDate >= '2019-12-30 03:24')) 
    AND (`transactions`.`agentNo` = '".$agentNo."')) 
UNION
DISTINCT (SELECT
SUM(`transactions`.impact) AS internal_impact_sum 
FROM
`transactions` 
WHERE
((`transactions`.tranStatus IN ('SUCCESSFUL', 'PENDING') 
AND `transactions`.`impact` < 0)) 
AND ((`transactions`.`customerNo` = 'EL10001' 
AND `transactions`.agentNo = '3LD0001'))) 
UNION
DISTINCT (SELECT
SUM(`transactions`.impact) AS internal_impact_sum 
FROM
`transactions` 
WHERE
((`transactions`.`tranStatus` IN ('SUCCESSFUL', 'PENDING') 
AND `transactions`.`impact` > 0 
AND `transactions`.`paymentDate` < '2019-12-30 03:24')) 
AND (`transactions`.`agentNo` = '".$agentNo."')) 
UNION
DISTINCT (SELECT
SUM(`transactions`.impact) AS internal_impact_sum 
FROM
`transactions` 
WHERE
((`transactions`.tranStatus IN ('SUCCESSFUL', 'PENDING') 
AND `transactions`.`impact` < 0)) 
AND (`transactions`.`agentNo` = '".$agentNo."'))
) AS union1");
	
	return $qry->row()->balance;
}

public function getCommission($agentNo){

	$this->db->select("sum(agent_fee) as commission");
	$this->db->where("tranStatus in ('SUCCESSFUL','PENDING')"); 
    $this->db->where("agentNo",$agentNo);
    $this->db->where('commissionState',0);
    
	$qry=$this->db->get("transactions");

	return $qry->row()->commission;

}


public function balancesReport($agentNo){

	$this->db->select("agentNo,names");
	$this->db->where("agentNo !='3LD0001'");
	
	if(!empty($agentNo))
		$this->db->where("agentNo",$agentNo);

	$qry = $this->db->get("agents");
	$res = $qry->result();
	$i=0;
	foreach ($res as $agent) {
		$res[$i]->balance = number_format((float)$this->getBalance($agent->agentNo), 1, '.', '');
		$res[$i]->commission = number_format((float)$this->getCommission($agent->agentNo), 1, '.', '');
		$i++;
	}
	return $res;

}


	

}
