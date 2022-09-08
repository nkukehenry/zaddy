<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agents_mdl extends CI_Model {

	public function __construct()
        {
                parent::__construct();

                $this->table="agents";
               // $this->user=$this->session->userdata['user_id'];
        }

	
	public function getAgentLogin($username,$password){

		$this->db->where('username',$username);
		$this->db->where('password',md5($password));
		$this->db->where('agents.status',1);
		$this->db->join('agents','agents.userId=users.user_id');
		$qry=$this->db->get('users');
		return $qry->row();

	}

	public function getAgentUser($userId){
		$this->db->where('user_id',$userId);
		$qry=$this->db->get('users');
		return $qry->row();
	}

	public function getTable()
	{
		
		$table="agents";
		return $table;
	}

    public function getByUserId($uid){
        
         $this->db->where('userId',$uid);
         $row=$this->db->get('agents')->row();
         
         return $row;
    }
	public function count($searchData=null){

		$table="agents";

        if(!empty($searchData['agentNo']))
		  $this->db->where('agentNo',$searchData['agentNo']);
		if(!empty($searchData['names']))
		  $this->db->where("names like '%".$searchData['names']."%'");

		$this->db->limit($limit,$start);
		$this->db->order_by("id","desc");
		$rows=$this->db->get($table)->result_array();
		return count($rows);
	}

	public function getAll($limit=10,$start=0,$searchData=null)
	{
		$table="agents";
		
		if(!empty($searchData['agentNo']))
		  $this->db->where('agentNo',$searchData['agentNo']);
		if(!empty($searchData['names']))
		  $this->db->where("names like '%".$searchData['names']."%'");

		$this->db->join('users','users.user_id=agents.userId','left');
		$this->db->limit($limit,$start);
		//$this->db->order_by("id","desc");
		$query=$this->db->get($table);
		
		return $query->result();
	}
	
	
	public function getPaidCommissions()
	{
		$table="paid_commissions";
		$this->db->order_by("id","desc");
		$query=$this->db->get($table);
		
		return $query->result();
	}
	public function saveReceivedCommission($data){
	    $table="paid_commissions";
	    $save=$this->db->insert($table,$data);
	    
	    return $save;
	}
	


	public function getByAgentNo($agentNo)
	{
		$table=$this->table;
		$this->db->where('agentNo',$agentNo);
		$query=$this->db->get($table);
		
		return $query->row();
	}
	
	public function genAgentNo(){
	    
	    $qry=$this->db->query("SELECT max(agentNo) as agentNo FROM `agents` where agentNo REGEXP '^[0-9]+$'");
	    
	    $agentNo=$qry->row()->agentNo;
	    
	    return $agentNo+1;
	    
	}


		public function saveAgent($postdata)
	{
		$table=$this->getTable();
		
		$saved=$query=$this->db->insert($table,$postdata);

		if($saved){

			return "ok";
		}
		else{

			return "failed";
		}
	}
	
  public function updateAgent($agentNo,$postdata)
	{
		$table=$this->getTable();
		
		$this->db->where('agentNo',$agentNo);
		$saved=$this->db->update($table,$postdata);

		if($saved){

			return "ok";
		}
		else{

			return "failed";
		}
	}
	
	
	

	public function deleteAgent($agentNo){

			$this->db->where('agentNo',$agentNo);
			$done=$this->db->delete($this->table);

		if($done){

			return 'ok';
		}

		else{

			return 'failed';
		}



	}




public function getBalance($agentNo){

	$this->db->select("sum(impact) as balance");
	$this->db->where("( 
	( tranStatus in ('SUCCESSFUL','PENDING') AND impact<0) 
	OR ( tranStatus in ('SUCCESSFUL','PENDING') AND impact>0 and paymentDate<'2019-12-30 03:24') 
	OR ( tranStatus in ('SUCCESSFUL') AND impact>0 and paymentDate>='2019-12-30 03:24'))
	");  //date refers to when pending withdraws stopped being credits
	
    $this->db->where(" (agentNo='".$agentNo."' OR (customerNo='".$agentNo."' AND agentNo ='".LOAD_TERMIANL."'))");
    
	$qry=$this->db->get("transactions");
	//return $this->db->last_query();

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



public function getBalance2($agentNo){

	$this->db->select("sum(impact) as balance");
    $this->db->where(" (agentNo='".$agentNo."' OR (customerNo='".$agentNo."' AND agentNo ='".LOAD_TERMIANL."'))");

	$qry=$this->db->get("transactions_view");
	
        return $this->db->last_query();
}

public function getTotalBalance(){
    
    $this->db->select("sum(impact) as balance");
    	$this->db->where("( 
	( tranStatus in ('SUCCESSFUL','PENDING') AND impact<0) 
	OR ( tranStatus in ('SUCCESSFUL','PENDING') AND impact>0 and paymentDate<'2019-12-30 03:24') 
	OR ( tranStatus in ('SUCCESSFUL') AND impact>0 and paymentDate>='2019-12-30 03:24'))
	");  //date refers to when pending withdraws stopped being credits
	
	$this->db->where(" (agentNo in (SELECT agentNo from agents) OR (customerNo in (SELECT agentNo from agents)  AND agentNo ='".LOAD_TERMIANL."'))");
	$qry=$this->db->get("transactions_view");
	echo  number_format($qry->row()->balance);
    
}


public function getAgentHistory($agentNo){
    $this->db->where(" (agentNo='".$agentNo."' or(agentNo='".LOAD_TERMIANL."' AND customerNo='".$agentNo."'))");    
    $this->db->limit(30,0);
    $this->db->order_by("id",'DESC');
    $qry=$this->db->get("transactions_view");
    return $qry->result();
}


public function setAgentTranPin($request){
    
    $agentNo=$request->agentNo;
    $tranPin=$request->oldPin;
    if(!empty($request->newPin))
    $tranPin=$request->newPin;
    
    $agent=$this->getByAgentNo($agentNo);
    
    $data=array('tranPin'=> $tranPin);
    
    $this->db->where('user_id',$agent->userId);
    $update=$this->db->update('users',$data);
    
    return $update;
    
}


public function setAgentPassword($request){
    
    $agentNo=$request->agentNo;
    
    $oldPin=md5($request->oldPin);
    
    $newPin=md5($request->newPin);
    
    $agent=$this->getByAgentNo($agentNo);
    
    $data=array('password'=> $newPin,"pwd_changed"=>1);
    
    $this->db->where('user_id',$agent->userId);
    $qry=$this->db->get("users");
    
    $user=$qry->row();
    
    if($user->password==$oldPin){
        
      $this->db->where('user_id',$agent->userId);
      $update=$this->db->update('users',$data);
      $response="SUCCESS";
    }
    else{
        $response="INCORRECT OLD PASSWORD";
    }
    
    return $response;
    
}





}
