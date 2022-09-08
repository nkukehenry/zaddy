<?php

class Validation_mdl extends CI_Model{

function __construct()
    {
    	parent::__construct();
    	$this->table="validations";
    }


    public function save($validation){

    	$this->logToFile($validation,SYSTEM_OUT_VAL);
    	$validation['providerAmount']=$validation['amount'];
    	$done=$this->db->insert($this->table,$validation);

    	return $done;
    }

    public function update($ref,$validationResponse){

    	$this->logToFile($validationResponse->expose(),SYSTEM_IN_VAL);

    	$data=array(
    		'status'=>$validationResponse->getResponseCode(),
    		'charges'=>$validationResponse->getSurcharge()/100,
    		'providerAmount'=>$validationResponse->getAmount()/100,
            "transactionRef"=>$validationResponse->getTransactionRef(),
    		'providerMessage'=>$validationResponse->getResponseMessage()
        );

    	$this->db->where('requestRef',$ref);

    	$done=$this->db->update($this->table,$data);

    	return $done;
    }
    
    public function getRoute($paymentCode){
        
        $this->db->where('paymentCode',$paymentCode);
        $item = $this->db->get("billeritems")->row();
        
        $billerId=$item->billerId;
        
        $this->db->where('billerId',$billerId);
        $biller=$this->db->get('routing')->row();
        
        if(empty($biller)){
            
            return "validateCustomer";
        }
        return $biller->validate;
        
    }
    
    public function getItem($code){
        $this->db->where('paymentCode',$code);
        $qry = $this->db->get('billeritems');
        return $qry->row();
    }


     function logToFile($reqdata,$type){

    	$currentdate=date("Y-m-d h:i:s");

    	if(is_array($reqdata))
    		$reqdata="\n".json_encode($reqdata);
 
    	$start="\n\n=========".$type." ".$currentdate." =========\n";

    	$data=$start.$reqdata;
    	file_put_contents(LOG_FILE, $data,FILE_APPEND);

    }



}