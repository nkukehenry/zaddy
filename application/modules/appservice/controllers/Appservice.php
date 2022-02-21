<?php

class Appservice extends MX_Controller{

function __construct()
    {
        // Construct the parent class
        parent::__construct();
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        header('Access-Control-Max-Age: 86400');    // cache for 1 day

        include('dto/ValidationRequest.php');
        include('dto/PaymentRequest.php');
        include('dto/ErrorResponse.php');
        $this->validationRequest= new ValidationRequest();
        $this->paymentRequest= new PaymentRequest();
        $this->errorResponse= new ErrorResponse();
        $this->session->set_userdata(array('role'=>'api'));
    }

    public function validateCustomer(){

        //$headers =  $_SERVER;
        $headers = apache_request_headers(); 
        $request=json_decode(file_get_contents('php://input')); //$this->input->post();

        $this->logToFile($headers ,AGENT_IN_VAL." HEADERS");
        $this->logToFile($request,AGENT_IN_VAL);

        if(!$this->isAuthorized($headers)){ //chec APP_KEY_HEADER

            $this->logToFile($this->getAuthError(),AGENT_OUT_VAL);
            echo $this->getAuthError();
            return;
        }


        //VERIFY AGENT/WALLET STATUS
        $verificationResult=$this->isAgentActive($headers);

        if(!is_bool($verificationResult)){
            $this->logToFile($verificationResult,AGENT_OUT_VAL);
            echo $verificationResult;
            return;
        }

    
	    $valRequest=$this->validationRequest->setFromJson($request);
    
        if($request->paymentCode=="LOAD" || $request->paymentCode=="SHARE"){
			$agentNo=$headers[AGENT_ID_HEADER];
            echo $this->validateAgent($agentNo);
        }
    	else{
        $validationResponse=Modules::run('validation/validateCustomer',$valRequest);
        $apiResponse=$validationResponse->expose();

        if($this->isSuccess($apiResponse['responseCode'])){
            $apiResponse['amount']=$apiResponse['amount']/100;
            $apiResponse['surcharge']=$apiResponse['surcharge']/100;
            $apiResponse['customerName']=strtoUpper($apiResponse['customerName']);
        }

        $this->logToFile($apiResponse,AGENT_OUT_VAL); //log response to agent

        header("Access-Control-Allow-Origin: *");
        echo json_encode($apiResponse);
        }
    }

    public function validateAgent($agentNo){

        $apiResponse=Modules::run('agents/getAgent',$agentNo);
        $apiResponse->shortTransactionRef="LD".time();
    if(!empty($apiResponse->names)){
        $apiResponse->customerName=$apiResponse->names;
        $apiResponse->responseCode='9000';
        $apiResponse->surcharge='0';
    }else{
      $apiResponse->responseCode='90020';
      $apiResponse->responseMesssage='Wrong receiver agent number';
    }
        return json_encode($apiResponse);
    }

     public function payment(){

        //$headers = $_SERVER;
     	$headers = apache_request_headers(); 
        $request=json_decode(file_get_contents('php://input')); //$this->input->post();

        $this->logToFile($headers ,AGENT_IN_PAY." HEADERS");
        $this->logToFile($request,AGENT_IN_PAY);


        if(!$this->isAuthorized($headers)){ //chec APP_KEY_HEADER

            $this->logToFile($this->getAuthError(),AGENT_OUT_PAY);
            echo $this->getAuthError();
            return;
        }

        //VERIFY AGENT/WALLET STATUS
        $verificationResult=$this->isAgentActive($headers);

        if(!is_bool($verificationResult)){
            $this->logToFile($verificationResult,AGENT_OUT_PAY);
            echo $verificationResult;
            return;
        }

        $payRequest=$this->paymentRequest->setFromJson($request);

            //check balance vs amount
         if(!$this->hasBalance($headers,$payRequest)){
            echo $this->getFundsError();
             return;
         }

        $noDups=Modules::run("payment/noDuplicate",$payRequest->getRequestRef());

        if($noDups){ // checking for duplicates

        //no dups continue

        if($payRequest->getPaymentCode()=="LOAD" || $payRequest->getPaymentCode()=="SHARE"){

           $paymentResponse=Modules::run('payment/shareFloat',$payRequest);
        }
        else{

            $paymentResponse=Modules::run('payment/postPayment',$payRequest);
        }

        if($paymentResponse->getResponseCode()==SUCCESS_RESPONSE_CODE || $paymentResponse->getResponseCode()==SUCCESS_RESPONSE_CODE2){
            $paymentResponse->setResponseMessage(SUCCESS_RESPONSE_MSG);
          }
          else if($paymentResponse->getResponseCode()==FUNDS_RESPONSE_CODE){
                $paymentResponse->setResponseMessage(FUNDS_RESPONSE_MSG);
          }

        $apiResponse=$paymentResponse->expose();

        }
        else{
            //duplicate
            $apiResponse=array("responseCode"=>"90096","responseMessage"=>"Duplicate Posting is not allowed");
        }

        $this->logToFile($apiResponse,AGENT_OUT_PAY); //log response to agent

        echo json_encode($apiResponse);
    }


     public function categoryBillers($categoryId){

        //$headers = $_SERVER;
		$headers = apache_request_headers(); 
     
        if(!$this->isAuthorized($headers)){ //chec APP_KEY_HEADER

            $this->logToFile($headers,"HEADERS IN");
            echo $this->getAuthError();
           return;
        }

        //VERIFY AGENT/WALLET STATUS
        $verificationResult=$this->isAgentActive($headers);

        if(!is_bool($verificationResult)){
            $this->logToFile($verificationResult,AGENT_OUT_VAL);
            echo $verificationResult;
            return;
        }

        $billersResponse=Modules::run('billers/getCategoryBillers',$categoryId);
        $apiResponse=json_encode($billersResponse);

        echo $apiResponse;
        
    }

    public function sortPending(){

        print_r(Modules::run('payment/sortPending'));

    }


    public function transactionCheck($requestRef){

        //$headers = $_SERVER;
		$headers = apache_request_headers(); 
        if(!$this->isAuthorized($headers)){ //chec APP_KEY_HEADER

            $this->logToFile($headers,"HEADERS IN");
            echo $this->getAuthError();
           return;
        }

        //VERIFY AGENT/WALLET STATUS
        $verificationResult=$this->isAgentActive($headers);

        if(!is_bool($verificationResult)){
            $this->logToFile($verificationResult,AGENT_OUT_VAL);
            echo $verificationResult;
            return;
        }


        $statusResponse=Modules::run('payment/getTranStatus',$requestRef);
        $apiResponse=json_encode($statusResponse);

        echo $apiResponse;
    }



    public function billerItems($billerId){

        //$headers =  $_SERVER;
		$headers = apache_request_headers(); 
        if(!$this->isAuthorized($headers)){ //chec APP_KEY_HEADER
            echo $this->getAuthError();
            return;
        }

        //VERIFY AGENT/WALLET STATUS
        $verificationResult=$this->isAgentActive($headers);

        if(!is_bool($verificationResult)){
            $this->logToFile($verificationResult,AGENT_OUT_VAL);
            echo $verificationResult;
            return;
        }


        $itemsResponse=Modules::run('billers/getBillItems',$billerId);
        $apiResponse=json_encode($itemsResponse);

        echo $apiResponse;
    }


    public function agentLogin(){
        
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        header('Access-Control-Max-Age: 86400'); 
   		file_put_contents('logs.txt','Login');
       $headers =  apache_request_headers();
       $request=json_decode(file_get_contents('php://input')); 
    
       file_put_contents('sample.txt',$request);

       $this->logToFile($headers ,"LOGIN HEADERS");
       $this->logToFile($request,"LOGIN REQUEST");

        if(!$this->isAuthorized($headers)){ //chec APP_KEY_HEADER
            $this->logToFile($this->getAuthError(),"AUTH ON LOGIN");
            echo $this->getAuthError();
            return;
        }
        $username=$request->username;
        $password=$request->password;
        //VERIFY AGENT/WALLET STATUS
        $apiResponse=Modules::run('agents/agentLogin',$username, $password);
        $apiResponse->password="";
        $this->logToFile($apiResponse,"LOGIN RESPONSE"); //log response to agent

        echo json_encode($apiResponse);
    }

     public function agentHistory(){

        //$headers = $_SERVER;
		$headers = apache_request_headers(); 
        if(!$this->isAuthorized($headers)){ //chec APP_KEY_HEADER

            $this->logToFile($headers,"HEADERS IN");
            echo $this->getAuthError();
            return;
        }

        //VERIFY AGENT/WALLET STATUS
        $verificationResult=$this->isAgentActive($headers);

        if(!is_bool($verificationResult)){
            $this->logToFile($verificationResult,AGENT_OUT_VAL);
            echo $verificationResult;
            return;
        }

        $agentNo=$headers[AGENT_ID_HEADER];

        $historyResponse=Modules::run('agents/getAgentHistory',$agentNo);
        $apiResponse=json_encode($historyResponse);

        echo $apiResponse;
    }

   public function agentStatement(){

        //$headers = $_SERVER;
		$headers = apache_request_headers(); 
        if(!$this->isAuthorized($headers)){ //chec APP_KEY_HEADER

            $this->logToFile($headers,"HEADERS IN");
            echo $this->getAuthError();
            return;
        }

        //VERIFY AGENT/WALLET STATUS
       
        $agentNo=$headers[AGENT_ID_HEADER];

         $verificationResult=$this->isAgentActive($headers);
        if(!is_bool($verificationResult)){
            $this->logToFile($verificationResult,AGENT_OUT_VAL);
            echo $verificationResult;
            return;
        }
        $agentNo=$headers[AGENT_ID_HEADER];
        $agentStatement=Modules::run('reports/mobileStatement',$agentNo);
    
        $balance = 0;
        $currentBalance =0;

        foreach($agentStatement as $tran):
    	 $currentBalance += $balance+$tran->impact;
         $tran->balance = $currentBalance;
        endforeach;
    
        $response    = array("transactions"=>$agentStatement,"starting_balance"=>$balance,"balance"=>$currentBalance);
        $apiResponse = json_encode($response);
        echo $apiResponse;
    }


   public function profileUpdate(){

      $headers =  apache_request_headers();
      $request=json_decode(file_get_contents('php://input')); //

       $this->logToFile($headers ,"PROFILE HEADERS");
       $this->logToFile($request,"PROFILE REQUEST");

        $apiResponse=Modules::run('agents/profileUpdate',$request);
        $this->logToFile($apiResponse,"PROFILE RESPONSE"); //log response to agent

        echo json_encode($apiResponse);
    }

    public function setPin(){

      //$headers =  $_SERVER;
      $headers = apache_request_headers(); 
      $request=json_decode(file_get_contents('php://input')); //

       $this->logToFile($headers ,"SET PIN HEADERS");
       $this->logToFile($request,"SET PIN REQUEST");

        if(!$this->isAuthorized($headers)){ //chec APP_KEY_HEADER
            echo $this->getAuthError();
            return;
        }

        $apiResponse=Modules::run('agents/setTranPin',$request);
        $this->logToFile($apiResponse,"SET PIN RESPONSE"); //log response to agent

        echo json_encode($apiResponse);
    }

     public function setPassword(){

     // $headers =  $_SERVER;
      $headers = apache_request_headers(); 
      $request=json_decode(file_get_contents('php://input')); //

       $this->logToFile($headers ,"SET PASSWORD HEADERS");
       $this->logToFile($request,"SET PASSWORD REQUEST");

        if(!$this->isAuthorized($headers)){ //chec APP_KEY_HEADER
            echo $this->getAuthError();
            return;
        }

        $apiResponse=Modules::run('agents/setPassword',$request);
        $this->logToFile($apiResponse,"SET PASSWORD RESPONSE"); //log response to agent

        echo json_encode($apiResponse);
    }


    public function getAgentBalance(){

         $headers = apache_request_headers(); 
    
          $this->logToFile($headers,"HEADERS IN");

        if(!$this->isAuthorized($headers)){ //chec APP_KEY_HEADER

            echo $this->getAuthError();
            return;
        }

         $agentNo=$headers[AGENT_ID_HEADER];

         $balance=Modules::run("agents/getAgentBalance",$agentNo);

         if(empty($balance))
           $balance="0.00";

        echo $balance;
    }

      public function getCommission(){

         //$headers = $_SERVER;
         $headers = apache_request_headers(); 
          $this->logToFile($headers,"HEADERS IN");

        if(!$this->isAuthorized($headers)){ //chec APP_KEY_HEADER

            echo $this->getAuthError();
            return;
        }

         $agentNo=$headers[AGENT_ID_HEADER];

         $comm=Modules::run("agents/getAgentCommission",$agentNo);

         if(empty($comm))
           $comm="0.00";

        echo $comm;
    }

   public function getReferralCommission(){

         //$headers = $_SERVER;
   		 $headers = apache_request_headers(); 
          $this->logToFile($headers,"HEADERS IN");

        if(!$this->isAuthorized($headers)){ //chec APP_KEY_HEADER

            echo $this->getAuthError();
            return;
        }

         $agentNo=$headers[AGENT_ID_HEADER];
         $comm=Modules::run("agents/getRefferalCommission",$agentNo);
         if(empty($comm))
           $comm="0.00";

        echo $comm;
    }

 public function getReferrals(){
    //$headers = $_SERVER;
    $headers = apache_request_headers(); 
	$agentNo = $headers[AGENT_ID_HEADER];
 
	$referrals = Modules::run('agents/getRefferals',$agentNo);
    echo json_encode($referrals);
 }

  function agentAlerts(){
		$result = [
        	array(
            "title"=>"Thanks for the loyalty!",
            "summary"=>"We thank you for keeping the trust...",
            "message" = >"We thank you for keeping the trust during times we didn't offer the service deserve"
            )
         ];
		echo json_encode($result);
  }
  function appInfo(){
     $headers = apache_request_headers(); 
	 $agentNo = $headers[AGENT_ID_HEADER];
		$result = array();
		echo json_encode($result);
    }

 function searchTransaction($key){
		$result = Modules::run('reports/searchTran',$key);
		echo json_encode($result);
    }

    //CHECKS

    function hasBalance($headers,$paymentReq){

         $agentNo=$headers[AGENT_ID_HEADER];

         $balance=Modules::run("agents/getAgentBalance",$agentNo);

         if($balance >= $paymentReq->getAmount())
            return true;
        if(in_array($paymentReq->getPaymentCode(),WITHDRAWS))
              return true;

        return false;
    }

    function isSuccess($code){

        if($code=='9000' || $code=='90000')
            return true;
        return false;

    }

    function isAuthorized($headers){


        $usedKey=(!empty($headers[APP_KEY_HEADER]))? $headers[APP_KEY_HEADER]: '';
        $agentId=(!empty($headers[AGENT_ID_HEADER]))? $headers[AGENT_ID_HEADER]: '';

        $STANDARD=WALLET_KEY.$agentId;
        $SENT_IN=$usedKey.$agentId;

        if($SENT_IN==$STANDARD)
          return true;

        return false;
    }

    function getAuthError(){

        $this->errorResponse->setResponseCode(AUTH_ERROR_CODE);
        $this->errorResponse->setResponseMessage(AUTH_ERROR_MESSAGE);

        return json_encode($this->errorResponse->expose());
    }

    function getFundsError(){

        $this->errorResponse->setResponseCode("951");
        $this->errorResponse->setResponseMessage("INADEQUATE WALLET BALANCE");
        return json_encode($this->errorResponse->expose());
    }

    function isAgentActive($headers){

        $agent=Modules::run('agents/getByAgentNo',$headers[AGENT_ID_HEADER]);

       if (!is_object($agent )){
                $this->errorResponse->setResponseCode(INVALID_AGENT_CODE);
                $this->errorResponse->setResponseMessage(INVALID_AGENT_MESSAGE);
                return json_encode($this->errorResponse->expose());

         }
        else if($agent->status==0)
        {
                $this->errorResponse->setResponseCode(RESTRICTED_AGENT_CODE);
                $this->errorResponse->setResponseMessage(RESTRICTED_AGENT_MESSAGE);

                return json_encode($this->errorResponse->expose());
        }

        return true;
    }



    function logToFile($reqdata,$type){

        $currentdate=date("Y-m-d h:i:s");
        if(is_array($reqdata))
            $reqdata="\n".json_encode($reqdata);
        if(is_object($reqdata))
            $reqdata="\n".json_encode($reqdata);

        $start="\n\n=========".$type." ".$currentdate." =========\n";
        $data=$start.$reqdata;
        file_put_contents(LOG_FILE, $data,FILE_APPEND);

    }



}
    ?>
