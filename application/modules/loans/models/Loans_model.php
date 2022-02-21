<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loans_model extends CI_Model {

	public function __construct()
        {
                parent::__construct();
                date_default_timezone_set("Africa/Kampala");

        }

	
	public function getAll($limit=null,$start=null,$search=null){

        if(!empty($search['names']))
            $this->db->where("customerName like '%".$search['names']."%'");
    
		$qry=$this->db->get('loan_customers');
		return $qry->result();

	}
	
	public function getAllLoans($limit=null,$start=null,$search=null){

        $this->db->join("loan_customers","loan_customers.cid=loans.customerid");
		$qry=$this->db->get('loans');
		return $qry->result();

	}
	
	public function getCustomerBalance($id=null){
	    
	    $this->db->select('amount,id');
	    if(!empty($id))
	    $this->db->where('customerid',$id);
	    $qry=$this->db->get('loans');
	    $loans=$qry->result();
	    
	    $paid=0;
	    $loanAmt=0;
	    
	    foreach($loans as $loan):
	        
	       $loanAmt += $loan->amount;
	        
	    $this->db->select('amount');
	    $this->db->where('loanId',$loan->id);
	    $qry2=$this->db->get('loan_payments');
	    $payments=$qry2->result();
	    
	    foreach($payments as $pay){
	      $paid += $pay->amount;
	    }
	    
	    endforeach;
	    
	    return $loanAmt-$paid;
	    
	}
	
	public function count($search=null){

		$qry=$this->db->get('loan_customers');
		return count($qry->result_array());

	}
	
	
	public function getCustomer($id){
	    
	    $this->db->where('cid',$id);
		$qry=$this->db->get('loan_customers');
		
		return $qry->row();
	}
	
	public function saveLoan($postdata)
	{
		$table="loans";
		
		$saved=$query=$this->db->insert($table,$postdata);

		if($saved){

			return "ok";
		}
		else{

			return "failed";
		}
	}
	
	public function repayLoan($id,$postdata)
	{
		$table="loan_payments";
		
		$data=array(
		    "loanId"=>$id,
		    "amount"=>$postdata['amount'],
		    "paymentDate"=>date("Y-m-d"),
		    "details"=>$postdata['details']
		    );
		    
		$saved=$query=$this->db->insert($table,$data);

		if($saved){

			return "ok";
		}
		else{
			return "failed";
		}
	}
	
	public function getPaidAmount($loanId){
	    $this->db->select("sum(amount) as paid");
	    $this->db->where("loanId",$loanId);
	    $qry=$this->db->get("loan_payments");
	    $res=$qry->row();
	    $paid=$res->paid;
	    
	    if(empty($paid))
	     $paid="0";
	     
	    return  $paid;
	}
	
	public function saveCustomer($postdata)
	{
		$table="loan_customers";
		
		$saved=$query=$this->db->insert($table,$postdata);

		if($saved){

			return "ok";
		}
		else{

			return "failed";
		}
	}
	public function updateCustomer($id,$postdata)
	{
		$table="loan_customers";
		$this->db->where('cid',$id);
		$saved=$query=$this->db->update($table,$postdata);

		if($saved){

			return "ok";
		}
		else{

			return "failed";
		}
	}



}
