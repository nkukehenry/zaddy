<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_mdl extends CI_Model {

	public function __construct()
        {
                parent::__construct();

                $this->table="transactions";

                $this->user=$this->session->userdata['user_id'];
    			date_default_timezone_set("Africa/Kampala");
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

	public function getTransactions($limit=10,$start=0,$searchData=null)
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
		$query=$this->db->get($table);
		
		return $query->result();
	}
   public function getFloatLoans($limit=10,$start=0,$searchData=null)
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
		   
		$table="agent_loans";
		$this->db->limit($limit,$start);
		$this->db->order_by('id','DESC');
		$query=$this->db->get($table);
		
		return $query->result();
   }

    public function countFloatLoans($searchData=null){
        
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
        
		$table="agent_loans";
		$this->db->select('requestRef');
		$rows=$this->db->get($table)->result_array();
		return count($rows);
	}
	

    public function getCommTransactions($limit=10,$start=0,$paymentCode)
	{
		if(!empty($paymentCode))
		$this->db->where("paymentCode",$paymentCode);
    
        $this->db->where("finalStatus","SUCCESSFUL");
		$table=$this->table;
		$this->db->limit($limit,$start);
		$this->db->order_by('id','DESC');
		$query=$this->db->get($table);
		
		return $query->result();
	}
	
	public function getCommissionList($searchData){
	    
	    $table=$this->table;
		
		 $startDate=$searchData['start'];
		 $endDate=$searchData['end'];
    
        $this->db->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
		 
		$this->db->select('agentNo');
		 if(!empty($searchData['agentNo']))
		$this->db->where('agentNo',$searchData['agentNo']);
		$this->db->where("paymentDate >='".$startDate."' AND paymentDate < DATE_ADD('".$endDate."', INTERVAL 1 DAY)");
		$this->db->where("tranStatus",'SUCCESSFUL'); 
		$this->db->where("commissionState",0); 
		//$this->db->order_by("transactions.id","desc");
		$this->db->group_by("agentNo");
		$query=$this->db->get($table);
		
		$agents=$query->result();
		
		$commList=array();
		
		foreach( $agents as $agent){
		    
		    
		$this->db->select("sum(agent_fee) as commission,transactions.agentNo,GROUP_CONCAT(agents.names)as names");
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
		    "names"=>explode(',',$comms->names)[0]
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
        //$this->db->where("paymentDate","'".date('Y-m-d')."'");
        $this->db->limit(5);
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

	public function getStatement($agentNo,$limit=10,$start=0)
	{
		$table=$this->table;;
		//$this->db->limit($limit,$start);
		//$this->db->where_in('responseCode',SUCCESS_CODES);
		//$this->db->or_where('responseCode',PENDING_CODE);
		//$this->db->where_in("tranStatus",array("SUCCESSFUL","PENDING"));
    	$this->db->where("( 
    	( tranStatus in ('SUCCESSFUL','PENDING') AND impact<0) 
    	OR ( tranStatus in ('SUCCESSFUL','PENDING') AND impact>0 and paymentDate<'2019-12-30 03:24') 
    	OR ( tranStatus in ('SUCCESSFUL') AND impact>0 and paymentDate>='2019-12-30 03:24'))
    	"); 
		//$this->db->where('agentNo',$agentNo);
		$this->db->where(" (agentNo='".$agentNo."' OR (customerNo='".$agentNo."' AND agentNo ='".LOAD_TERMIANL."'))");
		$this->db->order_by('id','ASC');
		$query=$this->db->get($table);
		
		return $query->result();
	}

public function getMobileStatement($agentNo,$limit=10,$start=0)
	{
		$table=$this->table;;
		
		$this->db->join('billeritems','transactions.paymentCode = billeritems.paymentCode');
    	$this->db->where("( 
    	( tranStatus in ('SUCCESSFUL','PENDING') AND impact<0) 
    	OR ( tranStatus in ('SUCCESSFUL','PENDING') AND impact>0 and paymentDate<'2019-12-30 03:24') 
    	OR ( tranStatus in ('SUCCESSFUL') AND impact>0 and paymentDate>='2019-12-30 03:24'))
    	"); 
		$this->db->where(" (agentNo='".$agentNo."' OR (customerNo='".$agentNo."' AND agentNo ='".LOAD_TERMIANL."'))");
		$this->db->order_by('transactions.id','ASC');
		$query=$this->db->get($table);
		return $query->result();
	}

   public function searchTran($key)
	{
        $key ='%'.$key.'%';
		$table=$this->table;
		$this->db->where("requestRef like '".$key."' OR customerNo like '".$key."' OR customerName like '".$key."'");
		$query=$this->db->get($table);
		return $query->result();
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




}
