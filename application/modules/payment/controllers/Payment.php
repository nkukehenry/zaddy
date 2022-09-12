<?php

class Payment extends MX_Controller{

function __construct()
    {
        // Construct the parent class
        parent::__construct();
        include('scripts/InterswitchAuth.php');
        include('dto/PaymentResponse.php');
        $this->paymentResponse=new PaymentResponse();
        $this->load->model('payment_mdl','pay_mdl');
        $this->withdraws =array('28310716','4432361');
    }

public function countSuccess(){

        return $this->pay_mdl->countSuccess();
    }

public function countPending(){

        return $this->pay_mdl->countPending();
    }

public function countFailed(){

        return $this->pay_mdl->countFailed();
    }

public function noDuplicate($ref){
    return $this->pay_mdl->noDup($ref);
}

public function postPayment($request)
  {

    $paymentCode = $request->getPaymentCode();
	  $agentNo = $request->getAgentId();
	
    //clear cached history for agent
    Modules::run('cache/trashdata','HISTORY_'.$agentNo);

    //get route from redis
    $route = Modules::run('cache/getStr','ROUTE_'.$paymentCode);

    if(empty($route)){ 
      //route not in redis, pick from db
        $route = $this->pay_mdl->getRoute($paymentCode);
        Modules::run('cache/setStr','ROUTE_'.$paymentCode,$route);
    }

    logToFile($route,"ROUTE PICKED");

	if($this->pay_mdl->checkDoublePost($request)==true){
       return $this->duplicateDetected($request);
    }

    return Modules::run("payment/".$route,$request);
  }

public function settleFloatLoan($ref){

    $this->db->where('requestRef',$ref);
	$this->db->update('transactions',array('loan_settled'=>1));

    echo "Loan settled successfully";
}

public function ellypay($request){

	  $paymentCode = $request->getPaymentCode();
      $charges     = $this->pay_mdl->getCharges($paymentCode,$request->getAmount());
      $ourCharge   = $charges->ourCharge;
      $isInclusive = $charges->isInclusive;
      $ourShare  = $charges->us;//amout of charge left for us to share with agent
      $amt = $request->getAmount();


      if($ourCharge > 0) {
        //take off Top up

              if($request->getPaymentCode()=="503509"){ //yaka
                 $amt = $amt - (round($ourCharge-($amt*0.025)));
               }
             else{
                 $amt = ($inclusive==1)?$amt - $ourShare: $amt - $ourShare;//inclusive send diff, else send amount as is
               }
        }

    try{
     
      $save=$this->pay_mdl->save($request); //log to db & file

      if(!$save)
        return;
    }
    catch(mysqli_sql_exception $ex){

      file_put_contents(LOG_FILE, "\n DB ERROR ".$ex,FILE_APPEND);

    }
   $request->setAmount($amt);
   $requestReference = $request->getRequestRef();

   file_put_contents(LOG_FILE, "\n ELLYPAY OUT -CHARGES".$request,FILE_APPEND);
   $payresponse= Modules::run("ellypay/payment",$request);
   file_put_contents(LOG_FILE, "\n ELLYPAY RESPONSE FINAL".$payresponse,FILE_APPEND);

   $this->pay_mdl->update($requestReference,$payresponse);

   return $payresponse;
}

public function ezeeMoney($request){

	    $paymentCode = $request->getPaymentCode();
      $charges     = $this->pay_mdl->getCharges($paymentCode,$request->getAmount());
      $ourCharge   = $charges->ourCharge;
      $isInclusive = $charges->isInclusive;
      $ourShare  = $charges->us;//amout of charge left for us to share with agent
      $amt = $request->getAmount();


      if($ourCharge > 0) {
        //take off Top up

              if($request->getPaymentCode()=="503509"){ //yaka
                 $amt = $amt - (round($ourCharge-($amt*0.025)));
               }
             else{
                 $amt = ($inclusive==1)?$amt - $ourShare: $amt - $ourShare;//inclusive send diff, else send amount as is
               }
        }

    try{
     
      $save=$this->pay_mdl->save($request); //log to db & file

      if(!$save)
        return;
    }
    catch(mysqli_sql_exception $ex){

      file_put_contents(LOG_FILE, "\n DB ERROR ".$ex,FILE_APPEND);

    }

     


   $request->setAmount($amt);
   $requestReference = $request->getRequestRef();

   file_put_contents(LOG_FILE, "\n EZEE OUT -CHARGES".$request,FILE_APPEND);
   $payresponse= Modules::run("ezee/ezeePay",$request);
   file_put_contents(LOG_FILE, "\n EZEE RESPONSE FINAL".$payresponse,FILE_APPEND);

   $this->pay_mdl->update($requestReference,$payresponse);

   return $payresponse;
}

public function interswitchPayment($request)
  {

    $paymentCode = $request->getPaymentCode();
    $customerId = $request->getCustomerId();
    $customerMobile = $request->getPhoneNumber();
    $amt = $request->getAmount();
    $charge1 = $request->getSurcharge();
    $transactionRef = $request->getPaymentRef();
    $customerName = $request->getCustomerName();
    $narration = $request->getNarration();
    $requestReference = $request->getRequestRef();
    $otp = $request->getOtp();
	
//charges
    
    $charges = $this->pay_mdl->getCharges($paymentCode,$amt);
    $ourCharge = $charges->ourCharge;

	//amount on top
    if($ourCharge > 0) {
    	$request->setAmount($amt+$ourCharge);
        $charge1 = $charge1-$ourCharge;
      }
   try{
      $save=$this->pay_mdl->save($request); //log to db & file
      if(!$save)
        return;
    }
    catch(mysqli_sql_exception $ex){
      file_put_contents(LOG_FILE, "\n DB ERROR ".$ex,FILE_APPEND);
    }
	
     $amount = $amt *100;
     $charges = $charge1*100;

    $customerEmail = "bills@bill.com";
    $httpMethod = "POST";

    $resourceUrl = SVA_BASE_URL."sendAdviceRequest";

     if(in_array($paymentCode,WITHDRAWS)) //withdraws
       $resourceUrl = SVA_BASE_URL."cashwithdrawal";

    //collected transaction data into array, then into json obj
    $transaction_data= array(
      "paymentCode"=> $paymentCode,
      "customerId"=>$customerId,
      "customerMobile"=>$customerMobile,
      "requestReference"=>$requestReference,
      "terminalId"=>MY_TERMINAL,
      "amount"=>$amount,
      "surcharge"=>0,
      "bankCbnCode"=>CBN_CODE,
      "customerEmail"=>$customerEmail,
      "cardPan"=>"",
      "deviceTerminalId"=>DEVICE_TERMINAL,
      "transactionRef"=>$transactionRef,
      "depositorName"=>$customerName,
      "narration"=>$narration,
      "location"=>"Wandegeya",
      "pin"=>$otp,
      "otp"=>$otp
     );

    $additionalParams=$amount.DEVICE_TERMINAL.$requestReference.$customerId.$paymentCode;

    if($otp=='')
       $otp='null';

    if(in_array($paymentCode ,$this->withdraws)){
       $additionalParams.=$otp;
    }

    file_put_contents(LOG_FILE, "\n PARAMS OUT ".$additionalParams,FILE_APPEND);

    $final_trans_data=json_encode($transaction_data);

   file_put_contents(LOG_FILE, "\n REQ OUT OUT ".$final_trans_data,FILE_APPEND);

    $interswitchAuth=new InterswitchAuth();

    $AuthData=$interswitchAuth->generateInterswitchAuth($httpMethod, $resourceUrl, CLIENT_ID,
            CLIENT_SECRET, $additionalParams, SIGNATURE_REQ_METHOD);
            //request headers
            $request_headers = array();
      $request_headers[] = "Authorization:".$AuthData['AUTHORIZATION'];
      $request_headers[] = "Timestamp:".$AuthData['TIMESTAMP'];
      $request_headers[] = "Nonce:".$AuthData['NONCE'];
      $request_headers[] = "Signature:".$AuthData['SIGNATURE'];
      $request_headers[] = "SignatureMethod:".$AuthData['SIGNATURE_METHOD'];
      $request_headers[] = "TerminalId:".DEVICE_TERMINAL;
      $request_headers[] = 'Content-Type: application/json';

      $ch = curl_init($resourceUrl);

      file_put_contents(LOG_FILE, "\n HEADERS OUT ".json_encode($request_headers),FILE_APPEND);

       //post values
      curl_setopt($ch,CURLOPT_POSTFIELDS,$final_trans_data);
      // Option to Return the Result, rather than just true/false
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      // Set Request Headers
      curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
      //time to wait while waiting for connection...indefinite
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, PROCESS_TIMEOUT);

      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

      curl_setopt($ch,CURLOPT_POST,1);
      //set curl time..processing time out
      curl_setopt($ch, CURLOPT_TIMEOUT, PROCESS_TIMEOUT);
      // Perform the request, and save content to $result
      ini_set("max_execution_time",EXEC_TIMEOUT);
      $result = curl_exec($ch);
        //curl error handling
        $curl_errno = curl_errno($ch);
                $curl_error = curl_error($ch);
                if ($curl_errno > 0) {
                    curl_close($ch);
                    echo "CURL Error ($curl_errno): $curl_error\n";
                    }
         curl_close($ch);

         $decoded=json_decode($result);

         $payresponse=$this->paymentResponse->setFromJson($decoded);

         $this->pay_mdl->update($requestReference,$payresponse,$paymentCode); //update to db & file
		
         return $payresponse;
  }

  public function duplicateDetected($request){
  
   	$response = array(
     "responseCode"=>'90064',
     "responseMessage"=>"Duplicate transaction, be sure it's not a repeat before you try again",
     "requestRef" => $request->getRequestRef()
    );
    //clear cached history for agent
    Modules::run('cache/trashdata','HISTORY_'.$request->getAgentId());
  	return $this->paymentResponse->setFromJson($response);
  }


  //LOADS

  public function loads(){

      $data['module']='payment';
      $data['view']='wallet_load';
      $data['page']="Wallet Load";

      echo Modules::run("templates/admin",$data);
  }

  public function saveAgentLoad(){

      include('dto/PaymentRequest.php');

      $postdata=$this->input->post();

      $payment=new PaymentRequest();

      $agent=Modules::run('agents/getAgent',$postdata['agentNo']);

      $tranData=array(
            "requestRef"=>"LD".time(),
            "amount"=>$postdata['amount'],
            "agentId"=>$postdata['agentNo'],
            "surcharge"=>0,
            "customerId"=>$postdata['agentNo'],
            "paymentCode"=>'LOAD',
            "customerName"=>$agent->names,
            "customerPhone"=>'',
            "narration"=>$postdata['narration'],
            'transactionRef'=>$postdata['agentNo'].'|'.$postdata['amount']."|LOAD",
            "impact"=>$postdata['amount']
        );

        $request=$payment->setFromJSON($tranData);

        $this->pay_mdl->save($request);

        $confirmation= array();

        $confirmation['transferCode']=$tranData['requestRef'];
        $confirmation['requestReference']=$tranData['requestRef'];;
        $confirmation['rechargePIN']='Load successful';
        $confirmation['responseCode']='9000';
        $confirmation['responseMessage']='SUCCESSFUL';

        $payconfirmation=new PaymentResponse();

        $payconfirmation->setFromJSON($confirmation);

    if(!empty($payconfirmation->getResponseCode()))
       $res= $this->pay_mdl->update($tranData['requestRef'],$payconfirmation);

      if($res){

			$msg= "Agent Loaded successfully Added";
            //clear cached history for agent
            Modules::run('cache/trashdata','HISTORY_'.$postdata['agentNo']);
		}

		else{

			$msg= "Operation failed, please try again";

		}

		Modules::run("templates/setFlash",$msg);

		redirect($this->loads());

  }

  public function payCommission($comagents,$searchData){
       
      
      include('dto/PaymentRequest.php');
      
      $postdata=$this->input->post();
      
      $payment=new PaymentRequest();
      
      foreach($comagents as $agent):
     
      $tranData=array(
            "requestRef"=>"LD".$agent['agentNo'].time(),
            "amount"=>$agent['commission'],
            "agentId"=>$agent['agentNo'],
            "surcharge"=>0,
            "customerId"=>$agent['agentNo'],
            "paymentCode"=>'COMMS',
            "customerName"=>$agent['names'],
            "customerPhone"=>'',
            "narration"=>'COMMISSION'.$searchData['narration'],
            'transactionRef'=>$agent['agentNo'].'|'.$agent['commission']."|COMMS",
            "impact"=>$agent['commission']
        );
        
        $request=$payment->setFromJSON($tranData);
        
        $this->pay_mdl->markPaidCommision($agent['agentNo'],$searchData['start'],$searchData['end']); 
        
        $this->pay_mdl->save($request); 
        
        $confirmation= array();
        
        $confirmation['transferCode']=$tranData['requestRef'];
        $confirmation['requestReference']=$tranData['requestRef'];;
        $confirmation['rechargePIN']='Load successful';
        $confirmation['responseCode']='9000';
        $confirmation['responseMessage']='SUCCESSFUL';
        
        $payconfirmation=new PaymentResponse();
        
        $payconfirmation->setFromJSON($confirmation);
        
    if(!empty($payconfirmation->getResponseCode()))
       $res= $this->pay_mdl->update($tranData['requestRef'],$payconfirmation);
  //clear cached history for agent
            Modules::run('cache/trashdata','HISTORY_'.$agent['agentNo']);
        
   endforeach;

    Modules::run("templates/setFlash","Commission Paid successfully");
    
	redirect("agents/list");
	
        
  }

   public function shareFloat($request){


        $this->pay_mdl->saveDebit($request);

        //Proceed with credit
        $request->setAgentId(LOAD_TERMIANL);
        $this->pay_mdl->save($request);

        $confirmation= array();

        $confirmation['transferCode']=$request->getRequestRef();
        $confirmation['requestReference']=$request->getRequestRef();
        $confirmation['rechargePIN']='Float share successful';
        $confirmation['responseCode']='9000';
        $confirmation['responseMessage']='SUCCESSFUL';

       $payconfirmation=new PaymentResponse();
       $payconfirmation->setFromJSON($confirmation);
       $res= $this->pay_mdl->update($request->getRequestRef(),$payconfirmation);
   //clear cached history for agent
       Modules::run('cache/trashdata','HISTORY_'.$request->getAgentId());
       Modules::run('cache/trashdata','HISTORY_'.$request->getCustomerId());

       return $payconfirmation;

  }

  public function sortPending(){

      $transactions=Modules::run("reports/getPending");

      foreach($transactions as $tran){
          $this->getTranStatus($tran->requestRef);
      }
  }

  public function checkStatus($requestRef){

      $status=$this->getTranStatus($requestRef);

      echo json_encode($status);
  }

  public function getTranStatus($requestRef){

      $transaction= $this->pay_mdl->getTransaction($requestRef);

      $originalResponseCode =$transaction->responseCode;
  
   //clear cached history for agent
     Modules::run('cache/trashdata','HISTORY_'.$transaction->agentNo);

      $paymentCode = $transaction->paymentCode;
      $statusRoute = $this->pay_mdl->getStatusMethod($paymentCode); //endpoint for status

    if(($transaction->tranStatus=="SUCCESSFUL" || $transaction->tranStatus=="PENDING" || $transaction->tranStatus=="FAILED")  || empty(trim($transaction->tranStatus)) || strlen($transaction->tranStatus)<4 ){

          $transaction1=Modules::run($statusRoute,$requestRef);
          //increment retry
           $this->pay_mdl->incrementRetry($transaction->requestRef);

          if($transaction1->getResponseCode()==PENDING_CODE){

              //if($transaction->retryCount==3) //retried 2 times
              //  $this->notifyForPending($transaction1);
          }
           $transaction=$transaction1;

           if(in_array($transaction->getResponseCode(),SUCCESS_CODES) || ($transaction->getResponseCode()!=PENDING_CODE && !in_array($originalResponseCode,SUCCESS_CODES) )){

                $this->pay_mdl->updateStatus($requestRef,$transaction);

                $transaction= $this->pay_mdl->getTransaction($requestRef); //fetch updated
            }
    }
  
          

       return $transaction;

  }

  public function fetchRemoteStatus ($requestRef){

       $httpMethod = "GET";

      $resourceUrl = SVA_BASE_URL."transactions/".$requestRef;


    $additionalParams="";

    $interswitchAuth=new InterswitchAuth();

    $AuthData=$interswitchAuth->generateInterswitchAuth($httpMethod, $resourceUrl, CLIENT_ID,
            CLIENT_SECRET, $additionalParams, SIGNATURE_REQ_METHOD);
            //request headers
            $request_headers = array();
      $request_headers[] = "Authorization:".$AuthData['AUTHORIZATION'];
      $request_headers[] = "Timestamp:".$AuthData['TIMESTAMP'];
      $request_headers[] = "Nonce:".$AuthData['NONCE'];
      $request_headers[] = "Signature:".$AuthData['SIGNATURE'];
      $request_headers[] = "SignatureMethod:".$AuthData['SIGNATURE_METHOD'];
      $request_headers[] = "TerminalId:".DEVICE_TERMINAL;
      $request_headers[] = 'Content-Type: application/json';

      $ch = curl_init($resourceUrl);

      file_put_contents(LOG_FILE, "\n HEADERS OUT ".json_encode($request_headers),FILE_APPEND);

       //post values
      //curl_setopt($ch,CURLOPT_POSTFIELDS,$final_trans_data);
      // Option to Return the Result, rather than just true/false
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      // Set Request Headers
      curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
      //time to wait while waiting for connection...indefinite
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, PROCESS_TIMEOUT);
  
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

      curl_setopt($ch,CURLOPT_POST,0);
      //set curl time..processing time out
      curl_setopt($ch, CURLOPT_TIMEOUT, PROCESS_TIMEOUT);
      // Perform the request, and save content to $result
      ini_set("max_execution_time",EXEC_TIMEOUT);
      $result = curl_exec($ch);
        //curl error handling
        $curl_errno = curl_errno($ch);
                $curl_error = curl_error($ch);
                if ($curl_errno > 0) {
                    curl_close($ch);
                    echo "CURL Error ($curl_errno): $curl_error\n";
                    }
         curl_close($ch);

         $decoded=json_decode($result);
         $payconfirmation = new PaymentResponse();

         $payconfirmation->setFromJSON($decoded);
         return $payconfirmation;

         //print_r($result);
  }

  public function notifyForPending($transaction){

      $body="Hello Support,<br>";
      $body.="Please advise about the status of transaction <b>".$transaction->requestReference."</b> and update the status on your side for our system  to be able to fetch and update on our side";

      $body.="<br> <br> Status Inquiry Response: <br> <b>".json_encode($transaction)."</b><br> <br>";

      $body.="<br> We shall appreciate your timely feedback. <br> <br> Regards, <br>Rawtte Group";

        $this->load->library('email');
         $config = array();

        //$config['useragent']           = "CodeIgniter";
        $config['mailpath']            = "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
        $config['protocol']            = "smtp";
        $config['smtp_host']           = "localhost";
        $config['smtp_port']           = "25";
        $config['mailtype'] = 'html';
        $config['charset']  = 'utf-8';
        $config['newline']  = "\r\n";
        $config['wordwrap'] = TRUE;


        $this->email->initialize($config);

        $this->email->to("support.ug@interswitchgroup.com");
        $this->email->from('support@rawttegroup.com');
        $this->email->subject('Transaction status - '.$transaction->requestReference);
        $this->email->message($body);

       // $this->email->send();

         //echo $this->email->print_debugger();

  }

  public function getShare(){
     $share =  $this->pay_mdl->getShare('503509',1000);
     echo $share;
  }

 public function getShares(){
     $share =  $this->pay_mdl->getSharable('5015011',1000);
     print_r($share);
  }

 

  public function testEmail(){

      $body="Hello Support,<br>";
      $body.="Please advise about the status of transaction <b></b> and update the status on your side for our system  to be able to fetch and update on our side";

      $body.="<br> <br> Status Inquiry Response: <br> <b></b><br> <br>";

      $body.="<br> We shall appreciate your timely feedback. <br> <br> Regards, <br>Rawtte Group";

        $this->load->library('email');
         $config = array();

        //$config['useragent']           = "CodeIgniter";
        $config['mailpath']            = "/usr/sbin/sendmail";
        $config['protocol']            = "smtp";
        $config['smtp_host']           = "localhost";
        $config['smtp_port']           = "25";
        $config['mailtype'] = 'html';
        $config['charset']  = 'utf-8';
        $config['newline']  = "\r\n";
        $config['wordwrap'] = TRUE;


        $this->email->initialize($config);

        $this->email->to("henricsanyu@gmail.com");
        $this->email->from('nkukehenric@gmail.com');
        $this->email->subject('Transaction status');
        $this->email->message($body);

       $this->email->send();

        echo $this->email->print_debugger();

  }

public function sortComms(){
	$trans = $this->pay_mdl->sortWrongComms();

		foreach($trans as $tran):
			echo $tran->requestRef."<br>";
        endforeach;
}


}

?>
