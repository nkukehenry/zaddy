<?php

class Payment_mdl extends CI_Model{

function __construct()
    {
    	parent::__construct();
    	$this->table="transactions";
    	
    	$this->positiveImpactItems=array('28310716','LOAD','COMMS');
    	
    	date_default_timezone_set("Africa/Kampala");
    }

   
    public function countSuccess(){
        
        $this->db->where('finalStatus',"SUCCESSFUL");
       $qry= $this->db->get($this->table);
       
       return count($qry->result());
    }
    
    public function markAsSuccess($ref,$reason){
        
    $this->db->where('requestRef',$ref);
    $qry = $this->db->get($this->table);
    $res = $qry->row();
    
        $status = array(
        'tranStatus'=>'SUCCESSFUL',
        'responseCode'=>'90009A',
        'finalStatus'=>'SUCCESSFUL',
        'narration'=>$reason,
        'paymentDate' => $res->paymentDate
        );
        $this->db->where('requestRef',$ref);
        $this->db->update($this->table,$status);
        return true;
    }
    
    public function markAsFailed($ref,$reason){
    
        $this->db->where('requestRef',$ref);
        $qry = $this->db->get($this->table);
        $res = $qry->row();
        
        $status = array('tranStatus'=>'FAILED',
                      'responseCode'=>'90009F',
                      'finalStatus'=>'FAILED',
                      'narration'=>$reason,
                      'paymentDate' => $res->paymentDate
                     );
        $this->db->where('requestRef',$ref);
        $this->db->update($this->table,$status);
        return true;
    }
    
     public function countPending(){
        
        $this->db->where('finalStatus',"PENDING");
       $qry= $this->db->get($this->table);
       
       return count($qry->result());
    }
    
     public function countFailed(){
        
        $this->db->where('finalStatus',"FAILED");
       $qry= $this->db->get($this->table);
       
       return count($qry->result());
    }

    public function save($payment){

    	$this->logToFile($payment,SYSTEM_OUT_PAY);
    	
    	
    	$impact=$payment->getAmount()*-1;
    	
    	//PAYMENTCODES THAT HAVE POSITIVE IMPACT
    	if(in_array($payment->getPaymentCode(),$this->positiveImpactItems))
    	{
    	   $impact= $payment->getAmount();
    	}
    
    	$ourfee=number_format((float) $this->getFees($payment->getAmount(),$payment->getPaymentCode()), 1, '.', '');
    	$agentfee=number_format((float)$ourfee*COMMISSION, 1, '.', ''); //in config
        
        $tranData=array(
            "requestRef"=>$payment->getRequestRef(),
            "amount"=>$payment->getAmount(),
            "agentNo"=>$payment->getAgentId(),
            "charges"=>$payment->getSurcharge(),
            "customerNo"=>$payment->getCustomerId(),
            "paymentCode"=>$payment->getPaymentCode(),
            "customerName"=>$payment->getCustomerName(),
            "customerPhone"=>$payment->getPhoneNumber(),
            "impact"=>$impact,
            "tranStage"=>'POST_PAYMENT',
            "tranStatus"=>'PENDING',
            "requestObject"=>json_encode($payment->expose()),
            "retryCount"=>0,
            "paymentDate"=>date("Y-m-d h:i:s"),
            'our_fee'=>$ourfee,
            'agent_fee'=>$agentfee,
            'narration'=>(!empty($payment->getNarration()))?$payment->getNarration():'',
            "noDup"=>$payment->getRequestRef().$payment->getCustomerId()
        );
        
        $done=false;
        
        if($this->noDup($payment->getRequestRef())){
            $this->db->limit(1);
    	    $done=$this->db->insert($this->table,$tranData);
        }
    	  
    	return $done;
    }
    
    public function noDup($ref){
        
        $this->db->where("requestRef",$ref);
        $qry=$this->db->get($this->table);
        
        if($qry->num_rows>0)
          return false;
        
        return true;
    }
    
    public function saveDebit($payment){

    	$this->logToFile($payment,SYSTEM_OUT_PAY);
    	
    	$impact=$payment->getAmount()*-1;
    	
        $tranData=array(
            "requestRef"=>$payment->getRequestRef(),
            "amount"=>$payment->getAmount(),
            "agentNo"=>$payment->getAgentId(),
            "charges"=>$payment->getSurcharge(),
            "customerNo"=>$payment->getCustomerId(),
            "paymentCode"=>($payment->getPaymentCode()=="LOAD")? "SHARE":$payment->getPaymentCode(),
            "customerName"=>$payment->getCustomerName(),
            "customerPhone"=>$payment->getPhoneNumber(),
            "impact"=>$impact,
            "tranStage"=>'POST_PAYMENT',
            "tranStatus"=>'SUCCESSFUL',
            "requestObject"=>json_encode($payment->expose()),
            "paymentDate"=>date("Y-m-d h:i:s")
        );
        
        $done=false;
        
        if($this->noDup($payment->getRequestRef()))
    	  $done=$this->db->insert($this->table,$tranData);
    	  
    	return $done;
    }

    public function update($ref,$paymentResponse){

    	$this->logToFile($paymentResponse->expose(),SYSTEM_IN_PAY);

        $code=$paymentResponse->getResponseCode();
        
        if($code=="9000"){
            $status="SUCCESSFUL";
        }
        else if ($code=="90009"){
            $status="PENDING";
        }
        else if(!empty($code) && $code!==null){
            $status="FAILED";
        }
        
        $responceCode=$paymentResponse->getResponseCode();
        
        if(strpos($paymentResponse->getResponseMessage(),'failed') !==false){
            $status="FAILED";
            $responceCode="90009F";
        }

    	$data=array(
    		'responseCode'=>$responceCode,
            'tranStatus'=>$status,
            'tranStage'=>"PROVIDER_NOTIFIED",
            'finalStatus'=>$status,
            "valueCode"=>(!empty($paymentResponse->getRechargePIN()))? $paymentResponse->getRechargePIN() : $paymentResponse->getTransferCode(),
    		'responseMessage'=>$paymentResponse->getResponseMessage().$paymentResponse->getRechargePIN(),
            "responseObject"=>json_encode($paymentResponse->expose())
        );

    	$this->db->where('requestRef',$ref);

    	$done=$this->db->update($this->table,$data);

    	return $done;
    }
    
    
    public function updateStatus($ref,$transaction){

    	file_put_contents(LOG_FILE, $transaction,FILE_APPEND);

        $code=$transaction->responseCode;
        
        if(in_array($code,SUCCESS_CODES)){
            $status="SUCCESSFUL";
        }
        else if ($code===PENDING_CODE){
            $status="PENDING";
        }
        else if(!empty($code) && $code!==null){
            $status="FAILED";
        }

    	$data=array(
    		'responseCode'=>$code,
            'tranStatus'=>$status,
            'finalStatus'=>$status,
            "valueCode"=>(!empty($transaction->transferCode))?$transaction->transferCode:'',
    		'responseMessage'=>$transaction->responseMessage." : ".$transaction->transferCode,
            "statusCheckResponse"=>json_encode($transaction)
        );
        
        $done=false;
        
        if($transaction!==null){
            
    	$this->db->where('requestRef',$ref);
    	$done=$this->db->update($this->table,$data);
    	
        }

    	return $done;
    }
    
    
    public function getTransaction($ref){
        
        $this->db->where('requestRef',$ref);
        $qry=$this->db->get($this->table);
        return $qry->row();
            
    }
    
    public function getFees($amount,$paymentCode)
	{
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
	
	public function redeemCommission($agentNo,$amount){
	    
	    if(empty($amount) || empty($agentNo))
	      return false;
	    
	    $Date=date("Y-m-d");
	    $validDate=date('Y-m-d', strtotime($Date. ' - '.COMMISSION_DAYS.' days'));
	    
	    $this->db->select("requestRef,paymentDate,agent_fee as commission");
	    $this->db->where("agentNo",$agentNo);
	    //$this->db->where("paymentDate <='".$validDate."'");
	    $this->db->where("tranStatus","SUCCESSFUL");
	    $this->db->where("commissionState",0);
	    $this->db->where("agent_fee>0");
	    $qry=$this->db->get($this->table);
	    $commissions=$qry->result();

	    $payable=0;
	    
	    $paid=array();
	    
	    foreach($commissions as $com):
	        
	            if($payable==$amount)
	             continue;
	             
	             //if($com->commission<=$amount){
	                $payable+=$com->commission;
	               array_push($paid,$com);
	               
	            // }
	                
	        endforeach;
	        
	        $reddemed=false;
	        
	        if($payable>0){
	            
    	        $tranData=array(
                "requestRef"=>"COMS".time(),
                "amount"=>$payable,
                "agentNo"=>LOAD_TERMIANL,
                "charges"=>0,
                "customerNo"=>$agentNo,
                "paymentCode"=>"LOAD",
                "customerName"=>"Commission",
                "customerPhone"=>"",
                "impact"=>$payable,
                "tranStage"=>'POST_PAYMENT',
                "tranStatus"=>'SUCCESSFUL',
                "requestObject"=>"",
                "paymentDate"=>date("Y-m-d h:i:s"),
                "tranStatus"=>"SUCCESSFUL",
                "finalStatus"=>"SUCCESSFUL",
                "responseCode"=>"9000"
               );
        
            $reddemed=$this->db->insert($this->table,$tranData);
            
                    if($reddemed){
                        
                        foreach($paid as $com):
        	               //mark as paid
        	               $this->db->where("requestRef",$com->requestRef);
        	               $this->db->update($this->table,array("commissionState"=>1));
        	            endforeach;
                    }
            
	        }
	    
	    return $reddemed;
	    
	}

     public function markPaidCommision($agentNo,$startDate,$endDate){
        
            $this->db->where("agentNo",$agentNo);
        	$this->db->where("paymentDate >='".$startDate."' AND paymentDate < DATE_ADD('".$endDate."', INTERVAL 1 DAY)");
        	$this->db->update($this->table,array("commissionState"=>1));
        	
        	return true;
    }

     function logToFile($reqdata,$type){

    	$currentdate=date("Y-m-d h:i:s");

    	if(is_array($reqdata))
    		$reqdata="\n".json_encode($reqdata);

        if(is_object($reqdata)){
        
            $reqdata="\n".json_encode($reqdata->expose());
           
        }
 
    	$start="\n\n=========".$type." ".$currentdate." =========\n";

    	$data=$start.$reqdata;
    	file_put_contents(LOG_FILE, $data,FILE_APPEND);

    }
    
    public function incrementRetry($ref){
        
        $this->db->query("UPDATE transactions set retryCount=retryCount+1 where requestRef='".$ref."'");
        
    }
    
    public function getRoute($paymentCode){
        
        $this->db->where('paymentCode',$paymentCode);
        $item = $this->db->get("billeritems")->row();
        
        $billerId=$item->billerId;
        
        $this->db->where('billerId',$billerId);
        $biller=$this->db->get('routing')->row();
        
        if(empty($biller)){
            
            return "interswitchPayment";
        }
        
        return $biller->route;
        
    }
    
    public function saveTest($payment){

    	$this->logToFile($payment,SYSTEM_OUT_PAY);
    	
    	
    	$impact=$payment->getAmount()*-1;
    	
    	//PAYMENTCODES THAT HAVE POSITIVE IMPACT
    	if(in_array($payment->getPaymentCode(),$this->positiveImpactItems))
    	{
    	   $impact= $payment->getAmount();
    	}
    
    	$ourfee=number_format((float) $this->getFees($payment->getAmount(),$payment->getPaymentCode()), 1, '.', '');
    	$agentfee=number_format((float)$ourfee*COMMISSION, 1, '.', ''); //in config
        
        $tranData=array(
            "requestRef"=>$payment->getRequestRef(),
            "amount"=>$payment->getAmount(),
            "agentNo"=>$payment->getAgentId(),
            "charges"=>$payment->getSurcharge(),
            "customerNo"=>$payment->getCustomerId(),
            "paymentCode"=>$payment->getPaymentCode(),
            "customerName"=>$payment->getCustomerName(),
            "customerPhone"=>$payment->getPhoneNumber(),
            "impact"=>$impact,
            "tranStage"=>'POST_PAYMENT',
            "tranStatus"=>'PENDING',
            "requestObject"=>json_encode($payment->expose()),
            "retryCount"=>0,
            "paymentDate"=>date("Y-m-d h:i:s"),
            'our_fee'=>$ourfee,
            'agent_fee'=>$agentfee,
            "noDup"=>$payment->getRequestRef().$payment->getCustomerId()
        );
        
        $done=false;
        
        if($this->noDup($payment->getRequestRef())){
            $this->db->limit(1);
    	    $done=$this->db->insert("test_transactions",$tranData);
        }
    	  
    	return $done;
    }
    
     public function updateTest($ref,$paymentResponse){

    	$this->logToFile($paymentResponse->expose(),SYSTEM_IN_PAY);

        $code=$paymentResponse->getResponseCode();
        
        if($code=="9000"){
            $status="SUCCESSFUL";
        }
        else if ($code=="90009"){
            $status="PENDING";
        }
        else if(!empty($code) && $code!==null){
            $status="FAILED";
        }
        
        $responceCode=$paymentResponse->getResponseCode();
        
        if(strpos($paymentResponse->getResponseMessage(),'failed') !==false){
            $status="FAILED";
            $responceCode="90009F";
        }

    	$data=array(
    		'responseCode'=>$responceCode,
            'tranStatus'=>$status,
            'tranStage'=>"PROVIDER_NOTIFIED",
            'finalStatus'=>$status,
            "valueCode"=>(!empty($paymentResponse->getRechargePIN()))? $paymentResponse->getRechargePIN() : $paymentResponse->getTransferCode(),
    		'responseMessage'=>$paymentResponse->getResponseMessage()." : ".$paymentResponse->getRechargePIN(),
            "responseObject"=>json_encode($paymentResponse->expose())
        );

    	$this->db->where('requestRef',$ref);

    	$done=$this->db->update("test_transactions",$data);

    	return $done;
    }
    



}