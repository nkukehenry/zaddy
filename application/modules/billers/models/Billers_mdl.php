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
        $this->db->where('visible',1);
		$rows=$this->db->get($table)->result_array();
		return count($rows);
	}

	public function getAll($limit,$start)
	{
		$table=$this->getTable();
        $this->db->where('visible',1);
        if($limit)
         $this->db->limit($limit, $start);
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

public function getCategoryBillers($catId){

	$this->db->where("categoryId",$catId);
    $this->db->where('status',1);
	$qry=$this->db->get("billers");

	return $qry->result();

}

public function getBillerItems($billerId){
    
    $this->db->where("status",1);
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

public function countBillers(){
		$table="billers";
		$this->db->select('id');
		$rows=$this->db->get($table)->result_array();
		return count($rows);
	}

public function getAllBillers($limit=false,$start=false){

	$qry=$this->db->get("billers");
	return $qry->result();
}

public function getBillerById($billerId){
    $this->db->where("billerId",$billerId);
	$qry=$this->db->get("billers");
	return $qry->row();
}

public function getItem($paymentcode){

    $this->db->where('paymentCode',$paymentcode);
    $qry=$this->db->get('billeritems');

    return $qry->row();
}


public function saveBillerUpdate($billerid,$data){

    $this->db->where('billerId',$billerid);
    $save=$this->db->update('billers',$data);

    return $save;
}

public function saveItems($data){

	$this->db->select('max(itemId) as itemId');
	$rows=$this->db->get('billeritems')->row();
	$itemId=$rows->itemId;

	$data['itemId']=$itemId+1;
	$data['paymentCode']=$data['billerId'].$itemId;

	return $this->db->insert('billeritems',$data);

}

public function getItemById($id){
    $this->db->where('id',$id);
    $qry=$this->db->get('billeritems');
    return $qry->row();
}

public function saveItemEdit($id,$item){

if(empty($item['usesPhone']))
 $item['usesPhone'] =0;
if(empty($item['requiresAmount']))
 $item['requiresAmount'] =0;
if(empty($item['requiresPin']))
 $item['requiresPin'] =0;
if(empty($item['requiresNarration']))
 $item['requiresNarration'] =0;
if(empty($item['status']))
 $item['status'] =0;

$this->db->where('id',$id);
return  $this->db->update('billeritems',$item);
}




}
