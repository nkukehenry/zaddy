<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Billers_mdl extends CI_Model {

	public function __construct()
        {
                parent::__construct();
                $this->table="billers";
                //$this->user=$this->session->userdata['user_id'];
        }

	
	public function getTable()
	{
		
		$table="billers";
		return $table;
	}

	public function count(){

		$table=$this->getTable();
		$this->db->select('id');
		$rows=$this->db->get($table)->result_array();
		return count($rows);
	}

	public function getAll($limit=10,$start=0)
	{
		$table=$this->getTable();
		//$this->db->limit($limit,$start);
		/*if($this->team_id)
			$this->db->where('team_id',$this->team_id);*/
		
		$query=$this->db->get($table);
		
		return $query->result();
	}


	public function getBy_id($biller_id)
	{
		$table=$this->getTable();
		$this->db->where('id',$biller_id);
		$query=$this->db->get($table);
		
		return $query->row_array();
	}

	
		public function saveBiller($postdata)
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

	
	public function saveBillerItem($postdata){

        $this->db->where('paymentCode',$postdata['paymentCode']);
        $q = $this->db->get('billeritems');
    
        $saved=true;
        
        /*if ($q->num_rows() = 0) 
         {*/
		   $saved=$this->db->insert("billeritems",$postdata);
         //}

		if($saved){
			return "ok";
		}
		else{
			return "failed";
		}
	}

	public function trashBillerItems($billerId){

		$this->db->where('billerId',$billerId);
		$this->db->delete('billeritems');
	}
	public function trashBillers($catgoryId){

		$this->db->where('categoryId',$catgoryId);
		$this->db->delete('billers');
	}

	public function deleteBiller($biller_id){

			$this->db->where('id',$biller_id);
			$done=$this->db->delete($this->table);

		if($done){

			return 'ok';
		}

		else{

			return 'failed';
		}



	}


public function updateBiller($postdata){

	$id=$postdata['biller_id'];
	$this->db->where("id",$id);
	$done=$this->db->update($this->table,$data);

	if($done){

		return "ok";
	}
	else{

		return "failed";
	}

}

public function blockBiller($billerId){

	$data=array('status'=>'0');
	$this->db->where("billerId",$billerId);
	$done=$this->db->update($this->table,$data);

	if($done){

		return "ok";
	}
	else{

		return "failed";
	}

}

public function unblockBiller($billerId){

	$data=array('status'=>'1');
	$this->db->where("billerId",$billerId);
	$done=$this->db->update($this->table,$data);

	if($done){

		return "ok";
	}
	else{

		return "failed";
	}

}

public function getCategoryBillers($catId){
    
	$this->db->where("categoryId",$catId);
    $this->db->where('status','1');
	$qry=$this->db->get("billers");

	return $qry->result();

}

public function getBillerItems($billerId){
	$this->db->where("billerId",$billerId);
	$qry=$this->db->get("billeritems");

	return $qry->result();

}

public function getCategories($id=false){
    if($id)
    $this->db->where('providerId',$id);
    
    $qry=$this->db->get('categories');
    
    return $qry->result();
    
}

public function getItem($paymentcode){
    
    $this->db->where('paymentCode',$paymentcode);
    $qry=$this->db->get('billeritems');
    
    return $qry->row();
}





}
