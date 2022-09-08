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
    }
    
    
    public function countSuccess(){
        
        return $this->pay_mdl->countSuccess();
    }
    
    public function countPending(){
        
        return $this->pay_mdl->countPending();
    }
    
     public function markSuccess($requestRef,$reason=""){
        
          $this->pay_mdl->markAsSuccess($requestRef,$reason);
          
          Modules::run("templates/setFlash","Operation Successful");
         redirect('reports/transactions');
    }
    
    public function markFailed($requestRef,$reason=""){
        
          $this->pay_mdl->markAsFailed($requestRef,$reason);
          
          Modules::run("templates/setFlash","Operation Successful");
         redirect('reports/transactions');
    }
    
     public function countFailed(){
        
        return $this->pay_mdl->countFailed();
    }

public function noDuplicate($ref){
    
    return $this->pay_mdl->noDup($ref);
}


public function standardPhone($originalNumber){
 
   $countryCode = '256'; // Replace with known country code of user.
   $stdNumber = preg_replace('/^0/', $countryCode, $originalNumber);
   
   return $stdNumber;
}

public function nonZeroPhone($originalNumber){
 
   $stdNumber = preg_replace('/^0/', '', $originalNumber);
   
   return $stdNumber;
}

public function mtnData($request)
  {
       try{
      $save=$this->pay_mdl->save($request); //log to db & file
      
      if(!$save)
        return;
    }
    catch(mysqli_sql_exception $ex){

      file_put_contents(LOG_FILE, "\n DB ERROR ".$ex,FILE_APPEND);

    }

    $paymentCode = $request->getPaymentCode();
    $customerId = $this->standardPhone( $request->getCustomerId() );
    $customerMobile = $request->getPhoneNumber();
    $amount = $request->getAmount()*100;
    $charges = $request->getSurcharge()*100;
    $transactionRef=$request->getPaymentRef();
    $customerName=$request->getCustomerName();
    $narration=$request->getNarration();
    $requestReference=$request->getRequestRef();
    
    $itemCode=$request->getItemCode();
    $item="15MB Daily 500UGX";

    $transaction_data= array(
      "subscriptionId"=> $itemCode,
      "beneficiaryId"=>$customerId,
      "sendSMSNotification"=>1,
      "subscriptionPaymentSource"=>"EVDS",
      "registrationChannel"=>"ELLY_PAY_APP",
      "subscriptionName"=>$item,
      "subscriptionProviderId"=>"CIS"
     );
     
     $resourceUrl="https://uganda.api.mtn.com/v2/customers/".MTN_DATA_NO."/subscriptions";
     //https://uganda.api.mtn.com/v2/customers/".$mtn_no."/subscriptions";
     $trans_data=json_encode($transaction_data);

      $request_headers = array();
      $request_headers[] = "Content-Type: application/json;charset=UTF-8";
      $request_headers[] = "transactionID:".$requestReference;
      $request_headers[] = "x-api-key:".MTN_KEY;
      //$request_headers[] = "Host:uganda.api.mtn.com";
     // $request_headers[] = "Authorization: Bearer ".$accessToken;

      $ch = curl_init($resourceUrl);

      curl_setopt($ch,CURLOPT_POSTFIELDS,$trans_data);
      // Option to Return the Result, rather than just true/false
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      // Set Request Headers 
      curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
      //time to wait while waiting for connection...indefinite
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);

      curl_setopt($ch, CURLOPT_HTTP_VERSION, '1.1');

      curl_setopt($ch,CURLOPT_POST,1);
      //set curl time..processing time out
      curl_setopt($ch, CURLOPT_TIMEOUT, PROCESS_TIMEOUT);
      curl_setopt($ch, CURLINFO_HEADER_OUT, true);
      // Perform the request, and save content to $result
      ini_set("max_execution_time",EXEC_TIMEOUT);
      
      $result = curl_exec($ch);
        //curl error handling
        $curl_errno = curl_errno($ch);
                $curl_error = curl_error($ch);
                if ($curl_errno > 0) {
                    echo "CURL Error ($curl_errno): $curl_error\n";
                    }
          $info = curl_getinfo($ch);
          $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

          curl_close($ch);
         //var_dump($info);

          //echo $httpcode."<br>";
          
          $responseCode="90020";
          $responseMsg="Transaction Failed at Mtn";
          
          if($httpcode=="201"){
              
             $responseCode="9000";
             $responseMsg="Transaction Successful";
          }
          
    $responseObj=array(
         "transferCode"=>$requestReference,
	     "requestReference"=>$requestReference,
	     "transactionRef"=>$requestReference,
	     "rechargePIN"=>($responseCode=="9000")? "SUCCESSFUL":"FAILED",
	     "responseCode"=>$responseCode,
	     "responseMessage"=>$responseMsg);

    $payresponse=$this->paymentResponse->setFromJson($responseObj);

   //$this->pay_mdl->update($requestReference,$payresponse); //update to db & file
    
    $this->updateStatus($requestReference,$payresponse);

         return $payresponse;
  }
  
  public function updateStatus($requestReference,$payresponse){
    $this->pay_mdl->update($requestReference,$payresponse); 
  }
  
 public function postPayment($request)
  {
    $paymentCode = $request->getPaymentCode();
    
    $route=$this->pay_mdl->getRoute($paymentCode);
    
    return Modules::run("payment/".$route,$request);
    
    //return $this->interswitchPayment($request);
  }
  
 public function testRoute(){
     
      $route=$this->pay_mdl->getRoute("246617");
      print_r($route);
      
  }
  
  public function interswitchPayment($request){
      
       try{
      $save=$this->pay_mdl->save($request); //log to db & file
      
      if(!$save)
        return;
    }
    catch(mysqli_sql_exception $ex){

      file_put_contents(LOG_FILE, "\n DB ERROR ".$ex,FILE_APPEND);

    }

      
    $paymentCode = $request->getPaymentCode();
    $customerId = $request->getCustomerId();
    $customerMobile = $request->getPhoneNumber();
    $amount = $request->getAmount()*100;
    $charges = $request->getSurcharge()*100;
    $transactionRef=$request->getPaymentRef();
    $customerName=$request->getCustomerName();
    $narration=$request->getNarration();
    $requestReference=$request->getRequestRef();
    
    $customerEmail = "bills@bill.com";
    $httpMethod = "POST";
    
    $resourceUrl = SVA_BASE_URL."sendAdviceRequest";
    
     if($paymentCode=='28310716') //withdraws
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
      "location"=>"Wandegeya"
     );


    $additionalParams=$amount.DEVICE_TERMINAL.$requestReference.$customerId.$paymentCode;
    
    if($paymentCode=='28310716') 
       $additionalParams.='null';

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
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

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
                    echo "CURL Error ($curl_errno): $curl_error\n";
                    }
         curl_close($ch);

         $decoded=json_decode($result);

         $payresponse=$this->paymentResponse->setFromJson($decoded);

         //$this->pay_mdl->update($requestReference,$payresponse); //update to db & file
         $this->updateStatus($requestReference,$payresponse);
            
         return $payresponse;
  }
  
  
public function mtnAirtime($request)
  {
      
      
      try{
      $save=$this->pay_mdl->save($request); //log to db & file
      
      if(!$save)
        return;
    }
    catch(mysqli_sql_exception $ex){

      file_put_contents(LOG_FILE, "\n DB ERROR ".$ex,FILE_APPEND);

    }

      
    $paymentCode = $request->getPaymentCode();
    $customerId = $this->standardPhone( $request->getCustomerId() );
    $customerMobile = $request->getPhoneNumber();
    $amount = $request->getAmount();
    $charges = $request->getSurcharge();
    $transactionRef=$request->getPaymentRef();
    $customerName=$request->getCustomerName();
    $narration=$request->getNarration();
    $requestReference=$request->getRequestRef();
    

    $transaction_data= array(
      "msisdn"=> $customerId,
      "amount"=>$amount,
      "ref"=>$requestReference,
      "vendorNo"=>MTN_VENDORNO,
      "agentNo"=>"ELLYPAYAPP",
      "authKey"=>base64_encode(MTN_AUTH_KEY),
      "proof"=>str_replace("==","",base64_encode($requestReference.$amount."ELLYPAYAPP"))
     );
     
     $resourceUrl=MTN_AT_URL;
     $trans_data=json_encode($transaction_data);

      $request_headers = array();
      $request_headers[] = "Content-Type: application/json;charset=UTF-8";
      $request_headers[] = "TIMESATMP:ELM".time();
      $request_headers[] = "API_KEY:".MTN_KEY;

      $ch = curl_init($resourceUrl);

      curl_setopt($ch,CURLOPT_POSTFIELDS,$trans_data);
      // Option to Return the Result, rather than just true/false
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      // Set Request Headers 
      curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
      //time to wait while waiting for connection...indefinite
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);

      curl_setopt($ch, CURLOPT_HTTP_VERSION, '1.1');

      curl_setopt($ch,CURLOPT_POST,1);
      //set curl time..processing time out
      curl_setopt($ch, CURLOPT_TIMEOUT, PROCESS_TIMEOUT);
      curl_setopt($ch, CURLINFO_HEADER_OUT, true);
      // Perform the request, and save content to $result
      ini_set("max_execution_time",EXEC_TIMEOUT);
      
      $result = curl_exec($ch);
        //curl error handling
        $curl_errno = curl_errno($ch);
                $curl_error = curl_error($ch);
                if ($curl_errno > 0) {
                    echo "CURL Error ($curl_errno): $curl_error\n";
                    }
          $info = curl_getinfo($ch);
          $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

          curl_close($ch);
          
         $response=json_decode($result);
        
         $responseCode="90020";
         $msg=$response->status;
        
          if($response->statusCode=="0"){
            
            $responseCode="9000";
          }
          
          $responseObj=array(
         "transferCode"=>$response->rechargePin,
	     "requestReference"=>$requestReference,
	     "transactionRef"=>$requestReference,
	     "rechargePIN"=>$response->rechargePin,
	     "responseCode"=>$responseCode,
	     "responseMessage"=>$msg);
          
     $payresponse=$this->paymentResponse->setFromJson($responseObj);

      $this->updateStatus($requestReference,$payresponse);
   
    return $payresponse;
  }
  
  
public function africellPayment($request)
  {
      
      $request->setRequestRef("09".time().mt_rand(100,999));
      
      try{
      $save=$this->pay_mdl->save($request); //log to db & file
      
      if(!$save)
        return;
    }
    catch(mysqli_sql_exception $ex){

      file_put_contents(LOG_FILE, "\n DB ERROR ".$ex,FILE_APPEND);

    }

    $paymentCode = $request->getPaymentCode();
    $customerId = $this->nonZeroPhone( $request->getCustomerId() );
    $customerMobile = $request->getPhoneNumber();
    $amount = $request->getAmount();
    $charges = $request->getSurcharge();
    $transactionRef=$request->getPaymentRef();
    $customerName=$request->getCustomerName();
    $narration=$request->getNarration();
    $requestReference=$request->getRequestRef();
    
    $itemCode = $request->getItemCode();
    $selector = explode("_",$itemCode);
    
    $transaction_data = array(
      "msisdn" => $customerId,
      "amount" => $amount,
      "ref" => $requestReference,
      "vendorNo" => AFRICELL_VENDORNO,
      "agentNo" => "ELLYPAYAPP",
      "itemCode" => $selector[1],
      "login" => base64_encode(AFRICELL_USER),
      "password" => base64_encode(AFRICELL_PASS),
      "proof" => str_replace("==","",base64_encode($requestReference.$amount."ELLYPAYAPP"))
     );
     
     $resourceUrl=AFRICELL_DATA_URL;
     $trans_data=json_encode($transaction_data);

      $request_headers = array();
      $request_headers[] = "Content-Type: application/json;charset=UTF-8";
 
      $ch = curl_init($resourceUrl);

      curl_setopt($ch,CURLOPT_POSTFIELDS,$trans_data);
      // Option to Return the Result, rather than just true/false
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      // Set Request Headers 
      curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
      //time to wait while waiting for connection...indefinite
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);

      curl_setopt($ch, CURLOPT_HTTP_VERSION, '1.1');

      curl_setopt($ch,CURLOPT_POST,1);
      //set curl time..processing time out
      curl_setopt($ch, CURLOPT_TIMEOUT, PROCESS_TIMEOUT);
      curl_setopt($ch, CURLINFO_HEADER_OUT, true);
      // Perform the request, and save content to $result
      ini_set("max_execution_time",EXEC_TIMEOUT);
      
      $result = curl_exec($ch);
        //curl error handling
        $curl_errno = curl_errno($ch);
                $curl_error = curl_error($ch);
                if ($curl_errno > 0) {
                    echo "CURL Error ($curl_errno): $curl_error\n";
                    }
          $info = curl_getinfo($ch);
          $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

          curl_close($ch);
          
         $response=json_decode($result);
        
         $responseCode="90020";
         
         $msg= $response->status;
        
          if($response->statusCode=="200"){
            
            $responseCode="9000";
            
            $msg="Ellypay Africell payment successful";
          }
          
          
          $responseObj = array(
         "transferCode" => ($responseCode=="9000")?$response->rechargePin:"Failed, ".$msg,
	     "requestReference" => $requestReference,
	     "transactionRef" => $requestReference,
	     "rechargePIN" => ($responseCode=="9000")?$response->rechargePin:$msg,
	     "responseCode" => $responseCode,
	     "responseMessage" => $msg);
          
	     
     $payresponse=$this->paymentResponse->setFromJson($responseObj);
     $this->updateStatus($requestReference,$payresponse);
   
    return $payresponse;
  }



public function africellAirtime($request)
  {
      
      $request->setRequestRef("09".time().mt_rand(100,999));
      
      try{
      $save=$this->pay_mdl->save($request); //log to db & file
      
      if(!$save)
        return;
    }
    catch(mysqli_sql_exception $ex){

      file_put_contents(LOG_FILE, "\n DB ERROR ".$ex,FILE_APPEND);

    }

    $paymentCode = $request->getPaymentCode();
    $customerId = $this->nonZeroPhone( $request->getCustomerId() );
    $customerMobile = $request->getPhoneNumber();
    $amount = $request->getAmount();
    $charges = $request->getSurcharge();
    $transactionRef=$request->getPaymentRef();
    $customerName=$request->getCustomerName();
    $narration=$request->getNarration();
    $requestReference=$request->getRequestRef();
    
    $itemCode = $request->getItemCode();
    $selector = explode("_",$itemCode);
    
    $transaction_data = array(
      "msisdn" => $customerId,
      "amount" => $amount,
      "ref" => $requestReference,
      "vendorNo" => AFRICELL_VENDORNO,
      "agentNo" => "ELLYPAYAPP",
      "itemCode" => $selector[1],
      "login" => base64_encode(AFRICELL_USER),
      "password" => base64_encode(AFRICELL_PASS),
      "proof" => str_replace("==","",base64_encode($requestReference.$amount."ELLYPAYAPP"))
     );
     
     $resourceUrl=AFRICELL_API_URL;
     $trans_data=json_encode($transaction_data);

      $request_headers = array();
      $request_headers[] = "Content-Type: application/json;charset=UTF-8";
 
      $ch = curl_init($resourceUrl);

      curl_setopt($ch,CURLOPT_POSTFIELDS,$trans_data);
      // Option to Return the Result, rather than just true/false
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      // Set Request Headers 
      curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
      //time to wait while waiting for connection...indefinite
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);

      curl_setopt($ch, CURLOPT_HTTP_VERSION, '1.1');

      curl_setopt($ch,CURLOPT_POST,1);
      //set curl time..processing time out
      curl_setopt($ch, CURLOPT_TIMEOUT, PROCESS_TIMEOUT);
      curl_setopt($ch, CURLINFO_HEADER_OUT, true);
      // Perform the request, and save content to $result
      ini_set("max_execution_time",EXEC_TIMEOUT);
      
      $result = curl_exec($ch);
        //curl error handling
        $curl_errno = curl_errno($ch);
                $curl_error = curl_error($ch);
                if ($curl_errno > 0) {
                    echo "CURL Error ($curl_errno): $curl_error\n";
                    }
          $info = curl_getinfo($ch);
          $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

          curl_close($ch);
          
         $response=json_decode($result);
        
         $responseCode="90020";
         
         $msg= $response->status;
        
          if($response->statusCode=="200"){
            
            $responseCode="9000";
            
            $msg="Ellypay Africell payment successful";
          }
          
          
          $responseObj = array(
         "transferCode" => ($responseCode=="9000")?$response->rechargePin:"Failed, ".$msg,
	     "requestReference" => $requestReference,
	     "transactionRef" => $requestReference,
	     "rechargePIN" => ($responseCode=="9000")?$response->rechargePin:$msg,
	     "responseCode" => $responseCode,
	     "responseMessage" => $msg);
          
	     
     $payresponse=$this->paymentResponse->setFromJson($responseObj);
     $this->updateStatus($requestReference,$payresponse);
   
    return $payresponse;
  }

  
    
  
   public function webPayment(){
      
      include('dto/PaymentRequest.php');
      
      $postdata=json_decode($this->input->post("data"));
      
      $agent=$this->session->get_userdata()['agentNo'];
      
      $tranData=array(
            "requestRef"=>$postdata->requestRef,
            "amount"=>$postdata->amount,
            "agentId"=>$agent,
            "surcharge"=>0,
            "customerId"=>$postdata->customerId,
            "paymentCode"=>$postdata->paymentCode,
            "customerName"=>$postdata->customerName,
            "phoneNumber"=>$postdata->customerMobile,
            "narration"=>'',
            'transactionRef'=>$postdata->transactionRef
        );
      
        $payment=new PaymentRequest();
        $request=$payment->setFromJSON($tranData);
        
        if($this->hasBalance($request)){
            $paymentResponse=$this->postPayment($request);
            $response=$paymentResponse->expose();
        
        }else{
            
            $response=array("responseCode"=>"900051","responseMessage"=>"INADEQUATE WALLET BALANCE");
        }
        
        echo json_encode($response);
        
    
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
            "narration"=>urldecode($postdata['narration']),
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

		}

		else{

			$msg= "Operation failed, please try again";

		}
		
	
	        echo $msg;
  }
  
   public function payCommission($comagents,$searchData){
       
      
      include('dto/PaymentRequest.php');
      
      $postdata=$this->input->post();
      
      $payment=new PaymentRequest();
      
      foreach($comagents as $agent):
     
      $tranData=array(
            "requestRef"=>"LD".time(),
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
        
   endforeach;

    Modules::run("templates/setFlash","Commission Paid successfully");
    
	redirect("agents/list");
	
        
  }
  
    public function redeemCommission($agentNo,$amount){
        
        $reddemed=$this->pay_mdl->redeemCommission($agentNo,intval($amount));
        
        if($reddemed)
        return "SUCCESS";
        
        return "FAILED";
        
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
      
      if($transaction->tranStatus=="PENDING"  || empty(trim($transaction->tranStatus)) || strlen($transaction->tranStatus)<4 ){
          
          $transaction1=$this->fetchRemoteStatus($requestRef);
          
          
          //increment retry
           $this->pay_mdl->incrementRetry($transaction->requestRef);
      
          if($transaction1->responseCode==PENDING_CODE){
              
              if($transaction->retryCount==3) //retried 2 times
                $this->notifyForPending($transaction1);
              
              return $transaction;
          }
          
           $transaction=$transaction1;
      }
      
     if(in_array($transaction->responseCode,SUCCESS_CODES) ){
         
         //|| $transaction->responseCode!=PENDING_CODE
          
          $this->pay_mdl->updateStatus($requestRef,$transaction);
          
          $transaction= $this->pay_mdl->getTransaction($requestRef); //fetch updated
          
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
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

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
                    echo "CURL Error ($curl_errno): $curl_error\n";
                    }
         curl_close($ch);

         $decoded=json_decode($result);

         //return $decoded;
         
         print_r($result);
  }
  
  public function notifyForPending($transaction){
      
      $body="Hello Interswitch team,<br>";
      $body.="Please provide the final status of transaction <b>".$transaction->requestReference."</b> and update the status in your system for us to synchronize";
      
      $body.="<br> <br> Status Check Response: <br> <b>".json_encode($transaction)."</b><br> <br>";
      
      $body.="<br> Our customer needs this feedback ASAP. <br> <br> Regards, <br>Elia Investments";
      
        $this->load->library('email');
         $config = array();
        
        $config['useragent']           = "CodeIgniter";
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
        //$this->email->reply_to('support@ellypay.com', 'Elly Pay Support');
        $this->email->from('support@ellypayapp.com');
        $this->email->cc('elia@eliainvestmentsltd.com');
        $this->email->cc('support@ellypayapp.com');
        $this->email->subject('Pending transaction '.$transaction->requestReference);
        $this->email->message($body);
       
        $this->email->send();
        
         //echo $this->email->print_debugger();
        
  }
  
   function hasBalance($paymentReq){

         $agentNo=$this->session->get_userdata()['agentNo'];

         $balance=Modules::run("agents/getAgentBalance",$agentNo);

         if($balance >= $paymentReq->getAmount())
            return true;
            
        if($paymentReq->getPaymentCode()=='28310716')
              return true;

        return false;
    }
    
    
  

}

?>