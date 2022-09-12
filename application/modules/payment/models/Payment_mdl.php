<?php

class Payment_mdl extends CI_Model{

function __construct()
    {
    	parent::__construct();
    	$this->table="transactions";

    	$this->positiveImpactItems = array('50017548','28317550','LOAD','COMMS'); // lead to an increase in float
		
        $this->updateAmount = array('50017548');

    	date_default_timezone_set("Africa/Kampala");
    }

    public function getCharges($paymentCode,$amount){

      $charges = array("ourCharge" => 0, "billerCharge" => 0); //default
      $this->db->select("ourCharge,billerCharge");
      $this->db->where('paymentCode',$paymentCode);
      $this->db->where(" upperlimit>='".$amount."' AND paymentCode='".$paymentCode."' AND (lowerlimit-$amount+2)<0");
      $res = $this->db->get('charges')->row();

      if(!empty($res))
         $charges =$res;
       return $charges;
    }

    public function getShare($paymentCode,$amount){

      $this->db->select("toShare");
      $this->db->where('paymentCode',$paymentCode);
      $this->db->where(" upperlimit>='".$amount."' AND paymentCode='".$paymentCode."' AND (lowerlimit-$amount+2)<0");
      $res = $this->db->get('fees')->row();
      
      return $res->toShare;
    }
	//v2 of the upper
   public function getSharable($paymentCode,$amount){

      $this->db->where('paymentCode',$paymentCode);
      $this->db->where(" upperlimit>='".$amount."' AND paymentCode='".$paymentCode."' AND (lowerlimit-$amount+2)<0");
      $res = $this->db->get('fees')->row();
      $agentfee = 0;
      $ourfee = 0;
      $ourfee = $res->toShare;
      $agentfee =  number_format((float) $ourfee * ($res->agent_share/100), 1, '.', '');
   
    if($res->isPercentage == 1)
        $ourfee = ( $amount * ($res->toShare/100));
   
    if($res->isPercentage == 1 && $res->toShare_is_percentage==1): //we & agent takes a percentage of amount 
        $ourfee = ( $amount * ($res->toShare/100));
        $agentfee =  number_format((float) $amount * ($res->agent_share/100), 1, '.', ''); 
      if($res->toShare_is_percentage==1 && $res->toShare_is_percentage==0)// agent takes percentage of our share 
        $agentfee =  number_format((float) $ourfee * ($res->agent_share/100), 1, '.', '');
   	 endif;
 
        return array("agentfee"=>$agentfee,"ourfee"=>$ourfee);
    }

    public function countSuccess(){

        $this->db->where('finalStatus',"SUCCESSFUL");
       $qry= $this->db->get($this->table);

       return count($qry->result());
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

	//save payment to db, make it pending
    public function save($payment){

    	logToFile($payment,"DB ACTION");


    	$impact=$payment->getAmount()*-1;

    	//PAYMENTCODES THAT HAVE POSITIVE IMPACT
    	if(in_array($payment->getPaymentCode(),$this->positiveImpactItems))
    	{
    	   $impact= $payment->getAmount();
    	}
    
   
      $charges     = $this->getCharges($payment->getPaymentCode(),$payment->getAmount()); //retrieve charge
      $ourCharge   = $charges->ourCharge;
      $billerCharge= $charges->billerCharge;
      $isInclusive = $charges->isInclusive; //whether charge is within amount or separate
    
      if($isInclusive==0 && ($ourCharge>0 || $billerCharge>0) && $payment->getPaymentCode() !== "503509"){
            //get charge from agent
      		$amt = $payment->getAmount()+$billerCharge;
            $amt = $amt+$ourCharge;
            $impact = $amt * -1;
      }
     
	  
      $fees = $this->getSharable($payment->getPaymentCode(),$payment->getAmount());
    
      $ourfee   = $fees['ourfee'];
      $agentfee = $fees['agentfee']; //in config
      $refferralCommission  = 0;
    
    	if(!($payment->getReferralCode()) &&  $payment->getReferralCode()!==null && $payment->getReferralCode()!=='null'){
           
           $refferralCommission = REFERRAL_COMS * $agentfee;
           $agentfee  = $agentfee - $refferralCommission;
        }
     
        $tranData=array(
            "requestRef" => $payment->getRequestRef(),
            "amount" => $payment->getAmount(),
            "agentNo" => $payment->getAgentId(),
            "charges" => $payment->getSurcharge(),
            "customerNo" => $payment->getCustomerId(),
            "paymentCode" => $payment->getPaymentCode(),
            "customerName" => $payment->getCustomerName(),
            "customerPhone" => $payment->getPhoneNumber(),
            "impact" => $impact,
            "tranStage"=>'POST_PAYMENT',
            "tranStatus"=>'PENDING',
            "requestObject"=>json_encode($payment->expose()),
            "retryCount"=>0,
            "paymentDate"=>date("Y-m-d h:i:s"),
            'our_fee'=>$ourfee,
            'agent_fee'=>$agentfee,
            "noDup"=>$payment->getRequestRef().$payment->getCustomerId(),
        	"referralComms"=>$refferralCommission,
        	"referralAgent"=>$payment->getReferralCode()
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

    	logToFile($payment,SYSTEM_OUT_PAY);

    	$impact=$payment->getAmount()*-1;

        $tranData=array(
            "requestRef"=>($payment->getPaymentCode()=="LOAD")?"SH-".$payment->getRequestRef():$payment->getRequestRef(),
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

   function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '0';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
    }

    public function update($ref,$paymentResponse,$paymentCode=0){

      logToFile($paymentResponse->expose(),SYSTEM_IN_PAY);
      $code=$paymentResponse->getResponseCode();

    	 $status="PENDING";
    
        if($code=="9000"){
            $status="SUCCESSFUL";
        	$this->trackTime($ref);
        }
        else if ($code=="90009"){
            $status="PENDING";
        	$this->trackTime($ref);
        }
        else if(!empty($code) && $code!==null){
            $status="FAILED";
        }
    
    	$data=array(
    		'responseCode'=>$paymentResponse->getResponseCode(),
            'tranStatus'=>$status,
            'tranStage'=>"PROVIDER_NOTIFIED",
            'finalStatus'=>$status,
            "valueCode"=>(!empty($paymentResponse->getRechargePIN()))? $paymentResponse->getRechargePIN() : $paymentResponse->getTransferCode(),
    		'responseMessage'=>$paymentResponse->getResponseMessage()." : ".$paymentResponse->getRechargePIN(),
            "responseObject"=>json_encode($paymentResponse->expose())
        );
    	
      $responseMessage =  $paymentResponse->getResponseMessage();
    	
      $this->db->select('paymentCode');
      $this->db->where('requestRef',$ref);
      $row = $this->db->get($this->table)->row();
      $paymentCode = $row->paymentCode;
    
     if(in_array($paymentCode,$this->updateAmount) && in_array($code,SUCCESS_CODES)){
     
       $parsedAmount =  $paymentResponse->getAmount();
       //trim($this->get_string_between($responseMessage, 'UGX', 'was'));

       $data['amount'] = $parsedAmount;
       $data['impact'] = $parsedAmount;
     
       $fees = $this->getSharable($paymentCode,$parsedAmount);
    	
       if(!empty($fees)):
         $ourfee   = $fees['ourfee'];
         $agentfee = $fees['agentfee']; //in config
         $data['our_fee'] = $ourfee;
         $data['agent_fee'] = $agentfee;
       endif;

          }
    
    	$this->db->where_in('requestRef',array($ref,"SH-".$ref));
    	$done=$this->db->update($this->table,$data);

    	return $done;
    }

	public function callbackUpdate($ref,$callback){
    
    	$data = array(
    		'responseCode'=>$callback['responseCode'],
            'tranStatus'=>$callback['status'],
            'tranStage'=>"PROVIDER_NOTIFIED",
            'finalStatus'=>$callback['status'],
            "valueCode"=>$callback['token'],
    		'responseMessage'=>$callback['message'],
            "responseObject"=>$callback['response']
           );
       
        $this->db->select('paymentCode');
        $this->db->where('requestRef',$ref);
        $row = $this->db->get($this->table)->row();
        $paymentCode = $row->paymentCode;
    	//positive impact amount items
        if(in_array($paymentCode,$this->positiveImpactItems) && in_array($callback['responseCode'],SUCCESS_CODES)):
    	   $data['impact']= $callback['amount'];
    	endif;
    	//update amount
        if(in_array($paymentCode,$this->updateAmount) && in_array($code,SUCCESS_CODES)):
    	  $data['amount']= $callback['amount'];
        endif;
    
          $fees = $this->getSharable($paymentCode,$callback['amount']);
    	
       if(!empty($fees)):
         $ourfee   = $fees['ourfee'];
         $agentfee = $fees['agentfee']; //in config
         $data['our_fee'] = $ourfee;
         $data['agent_fee'] = $agentfee;
       endif;
      $done = false;
      if($callback!==null):
              $this->db->where('requestRef',$ref);
      $done = $this->db->update($this->table,$data);
      endif;
     
       
      return array("agentfee"=>$agentfee,"ourfee"=>$ourfee);
    
      return $done;
    }

    public function updateStatus($ref,$transaction){

    	file_put_contents(LOG_FILE, $transaction,FILE_APPEND);

        $code=$transaction->getResponseCode();

        if(in_array($code,SUCCESS_CODES)){
            $status="SUCCESSFUL";
        	$this->trackTime($ref);
        }
        else if ($code===PENDING_CODE){
            $status="PENDING";
            $this->trackTime($ref);
        }
        else if(!empty($code) && $code!==null){
            $status="FAILED";
        }

    	$data=array(
    		'responseCode'=>$code,
            'tranStatus'=>$status,
            'finalStatus'=>$status,
            "valueCode"=>(!empty($transaction->getRechargePIN()))?$transaction->getRechargePIN():'',
    		'responseMessage'=>$transaction->getresponseMessage()." : ".$transaction->gettransferCode(),
            "responseObject"=>json_encode($transaction)
        );
    
      $this->db->select('paymentCode');
      $this->db->where('requestRef',$ref);
      $row = $this->db->get($this->table)->row();
      $paymentCode = $row->paymentCode;
    
      $responseMessage = $transaction->getresponseMessage();
    
      if((in_array($paymentCode,$this->updateAmount) || strpos($responseMessage, 'Airtel Withdraw')>0) && in_array($code,SUCCESS_CODES)){
     
     	$parsedAmount = trim($this->get_string_between($responseMessage, 'UGX', 'was'));

        $data['amount'] = $parsedAmount;
        $data['impact'] = $parsedAmount;

       $ourfee=number_format((float) $this->getFees($parsedAmount ,$paymentCode), 1, '.', '');
       $agentfee=number_format((float)$ourfee*COMMISSION, 1, '.', ''); //in config

       $data['our_fee'] = $ourfee;
       $data['agent_fee'] = $agentfee;

       }
    
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

     public function markPaidCommision($agentNo,$startDate,$endDate){

            $this->db->where("agentNo",$agentNo);
        	$this->db->where("paymentDate >='".$startDate."' AND paymentDate < DATE_ADD('".$endDate."', INTERVAL 1 DAY)");
        	$this->db->update($this->table,array("commissionState"=>1));

        	return true;
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

      if(!isset($biller->route)){
          return "ezeeMoney";
      }

      return $biller->route;

  }


  public function getRouteMethod($paymentCode){

    $this->db->where('paymentCode',$paymentCode);
    $item = $this->db->get("billeritems")->row();
    $billerId=$item->billerId;
    $this->db->where('billerId',$billerId);
    $biller=$this->db->get('routing')->row();
    return $biller->method;

}


public function getValidateMethod($paymentCode){

  $this->db->where('paymentCode',$paymentCode);
  $item = $this->db->get("billeritems")->row();
  $billerId=$item->billerId;
  $this->db->where('billerId',$billerId);

  $biller=$this->db->get('routing')->row();
  return $biller->validate;
}

public function getStatusMethod($paymentCode){

  $this->db->where('paymentCode',$paymentCode);
  $item = $this->db->get("billeritems")->row();
  $billerId=$item->billerId;
  $this->db->where('billerId',$billerId);

  $biller=$this->db->get('routing')->row();

  if(empty($biller))
    return "payment/fetchRemoteStatus";

  return $biller->statusRoute;
}

public function trackTime($ref){
	
	$this->db->where('requestRef',$ref);
	$row = $this->db->get($this->table)->row();
	$sql=array(
    'requestRef'=>$row->requestRef,
    'amount'=>$row->amount,
    'paymentCode'=>$row->paymentCode,
    'agentId'=>$row->agentNo
    );

    $this->db->where('agentId',$row->agentNo);
    $this->db->delete('timetracker');

	$this->db->insert('timetracker',$sql);
}

 public function checkDoublePost($request){
 	
    date_default_timezone_set("Africa/Kampala");
 
 	$agentNo = $request->getAgentId();
    $paycode = $request->getPaymentCode();
    $amount  = $request->getAmount();
 
    $current = date('Y-m-d h:i:s');
 
 	$this->db->select("TIME_FORMAT(TIMEDIFF('".$current."',time),'%i') as difference");
    $this->db->where('agentId',$agentNo);
    $this->db->where('paymentCode',$paycode);
    $this->db->where('amount',$amount);
    $row = $this->db->get('timetracker')->row();
    
   if(($row->difference)>0 && ($row->difference)<2){
   
       if($row->retries>0){
   		 return false; //agent insists
       }else{
         $this->db->where('agentId',$agentNo);
         $this->db->update('timetracker',array('retries'=>1));
         return true; // agent should be warned
       }
   }else{
      return false; //refresh tran
   }
  
 }
 
 public function sortWrongComms(){
 
  $this->db->where('id >=109100 AND id <109600');
  $trans = $this->db->get('transactions')->result();
  
  foreach($trans as $tran):
     $this->db->where('toShare_is_percentage',1);
     $this->db->like('paymentCode',$tran->paymentCode);
     $row = $this->db->get('fees')->row();
     
      if(!empty($row)):
        $ref  = $tran->requestRef;
        $amt  = $tran->amount;
        $code = $tran->paymentCode;
        
        $shares = $this->getSharable($code,$amt);
        
        $data = array(
         'agent_fee' => $shares['agentfee'],
         'our_fee' => $shares['ourfee']
        );
        
        $this->db->where('requestRef',$ref);
        $this->db->update('transactions',$data);
        
      endif;
       
  endforeach;
  
  return $trans;
 
 }



}
