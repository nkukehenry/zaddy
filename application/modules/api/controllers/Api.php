<?php

class Api extends MX_Controller{

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

       // $headers =  $_SERVER;
        $headers = get_headers(); 
        $request=json_decode(file_get_contents('php://input')); //$this->input->post();

        $this->logToFile($headers ,AGENT_IN_VAL." HEADERS");
        $this->logToFile($request,AGENT_IN_VAL);

        if(!$this->isAuthorized($headers)){ //chec app_key supplied

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
        
        $canTransact=$this->canAgentTransact($headers);
    	//CHECK BANNED AGENT--can login but can't transact
    	 if(!is_bool($canTransact)){
            $this->logToFile($canTransact,AGENT_OUT_VAL);
            echo $canTransact;
            return;
        }


	    $valRequest=$this->validationRequest->setFromJson($request);
        $validationResponse = Modules::run('validation/validateCustomer',$valRequest);
    
       
        $apiResponse=$validationResponse->expose();

        if($this->isSuccess($apiResponse['responseCode'])){
            $apiResponse['amount']=$apiResponse['amount']/100;
            $apiResponse['surcharge']=$apiResponse['surcharge']/100;
            $apiResponse['customerName']=strtoUpper($apiResponse['customerName']);
        }

        $this->logToFile($apiResponse,AGENT_OUT_VAL); //log response to agent
        echo json_encode($apiResponse);
    
    }

    public function validateAgent($agentNo){

        //$headers =  $_SERVER;
		$headers = get_headers(); 
        $this->logToFile($headers ,AGENT_IN_VAL." HEADERS");

        if(!$this->isAuthorized($headers)){ //chec app_key

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

        $apiResponse=Modules::run('agents/getAgent',$agentNo);
        $apiResponse->shortTransactionRef="LD".time();

        $this->logToFile($apiResponse,AGENT_OUT_VAL); //log response to agent
        header("Access-Control-Allow-Origin: *");
        echo json_encode($apiResponse);
    }

     public function payment(){

        
     	$headers = get_headers(); 
        $request=json_decode(file_get_contents('php://input')); //$this->input->post();

        $this->logToFile($headers ,AGENT_IN_PAY." HEADERS");
        $this->logToFile($request,AGENT_IN_PAY);


        if(!$this->isAuthorized($headers)){ //chec app_key

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

        if($payRequest->getPaymentCode()=="LOAD"){

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
          $apiResponse = $paymentResponse->expose();

        }
        else{
            //duplicate
            $apiResponse=array("responseCode"=>"90096","responseMessage"=>"Duplicate Posting is not allowed");
        }
        if(strpos($apiResponse["responseMessage"],"sufficient")>-1){
        
         	$apiResponse=array("responseCode"=>"90096","responseMessage"=>"Provider general failure");
         }
     
        $this->logToFile($apiResponse,AGENT_OUT_PAY); //log response to agent

        echo json_encode($apiResponse);
    }


     public function categoryBillers($categoryId){

        
		$headers = get_headers(); 
        if(!$this->isAuthorized($headers)){ //chec app_key

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

        
		$headers = get_headers(); 
        if(!$this->isAuthorized($headers)){ //chec app_key

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
		$headers = get_headers(); 
        if(!$this->isAuthorized($headers)){ //chec app_key
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
       $headers =  get_headers();
       $request=json_decode(file_get_contents('php://input')); 
    
       //file_put_contents('sample.txt',$request);

       $this->logToFile($headers ,"LOGIN HEADERS");
       $this->logToFile($request,"LOGIN REQUEST");

        /*if(!$this->isAuthorized($headers)){ //chec app_key
            $this->logToFile($this->getAuthError(),"AUTH ON LOGIN");
            echo $this->getAuthError();
            return;
        }*/
        $username=$request->username;
        $password=$request->password;
        //VERIFY AGENT/WALLET STATUS
        $apiResponse=Modules::run('agents/agentLogin',$username, $password);
        unset($apiResponse->password);

        $this->logToFile($apiResponse,"LOGIN RESPONSE"); //log response to agent

        echo json_encode($apiResponse);
    }

     public function agentHistory(){

        
		$headers = get_headers(); 
        if(!$this->isAuthorized($headers)){ //chec app_key

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

        $agentNo=$headers[AGENT_ID];

        $historyResponse=Modules::run('agents/getAgentHistory',$agentNo);
        $apiResponse=json_encode($historyResponse);

        echo $apiResponse;
    }

   public function agentStatement(){

        
		$headers = get_headers(); 
        if(!$this->isAuthorized($headers)){ //chec app_key

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

        $agentNo=$headers[AGENT_ID];

        $agentStatement=Modules::run('agents/getStatement',$agentNo);
        $apiResponse=json_encode($agentStatement);

        echo $apiResponse;
    }


   public function profileUpdate(){

      $headers =  get_headers();
      $request=json_decode(file_get_contents('php://input')); //

       $this->logToFile($headers ,"PROFILE HEADERS");
       $this->logToFile($request,"PROFILE REQUEST");

        $apiResponse=Modules::run('agents/profileUpdate',$request);
        $this->logToFile($apiResponse,"PROFILE RESPONSE"); //log response to agent

        echo json_encode($apiResponse);
    }

    public function setPin(){

      //$headers =  $_SERVER;
      $headers = get_headers(); 
      $request=json_decode(file_get_contents('php://input')); //

       $this->logToFile($headers ,"SET PIN HEADERS");
       $this->logToFile($request,"SET PIN REQUEST");

        if(!$this->isAuthorized($headers)){ //chec app_key
            echo $this->getAuthError();
            return;
        }

        $apiResponse=Modules::run('agents/setTranPin',$request);
        $this->logToFile($apiResponse,"SET PIN RESPONSE"); //log response to agent

        echo json_encode($apiResponse);
    }

     public function setPassword(){

       $headers =  $_SERVER;
      $request=json_decode(file_get_contents('php://input')); //

       $this->logToFile($headers ,"SET PASSWORD HEADERS");
       $this->logToFile($request,"SET PASSWORD REQUEST");

        if(!$this->isAuthorized($headers)){ //chec app_key
            echo $this->getAuthError();
            return;
        }

        $apiResponse=Modules::run('agents/setPassword',$request);
        $this->logToFile($apiResponse,"SET PASSWORD RESPONSE"); //log response to agent

        echo json_encode($apiResponse);
    }


    public function getAgentBalance(){

         
         $headers = get_headers(); 
          $this->logToFile($headers,"HEADERS IN");

        if(!$this->isAuthorized($headers)){ //chec app_key

            echo $this->getAuthError();
            return;
        }

         $agentNo=$headers[AGENT_ID];

         $balance=Modules::run("agents/getAgentBalance",$agentNo);

         if(empty($balance))
           $balance="0.00";

        echo $balance;
    }

      public function getCommission(){

         $headers = get_headers(); 
        $this->logToFile($headers,"HEADERS IN");

        if(!$this->isAuthorized($headers)){ //chec app_key

            echo $this->getAuthError();
            return;
        }

         $agentNo=$headers[AGENT_ID];

         $comm=Modules::run("agents/getAgentCommission",$agentNo);

         if(empty($comm))
           $comm="0.00";

        echo $comm;
    }

   public function getReferralCommission(){

         
   		 $headers = get_headers(); 
          $this->logToFile($headers,"HEADERS IN");

        if(!$this->isAuthorized($headers)){ //chec app_key

            echo $this->getAuthError();
            return;
        }

         $agentNo=$headers[AGENT_ID];
         $comm=Modules::run("agents/getRefferalCommission",$agentNo);
         if(empty($comm))
           $comm="0.00";

        echo $comm;
    }

 public function getReferrals(){
    
    $headers = get_headers(); 
	$agentNo = $headers[AGENT_ID];
	$referrals = Modules::run('agents/getRefferals',$agentNo);
    echo json_encode($referrals);
 }

 public function appInfo(){

	$info = '<div style="padding:2em; padding-top:4em; text-align:center"> <h3> We are preparing an awesome guide, stand by </h3></div>';
	$response = array("data" => $info);
   echo json_encode($response);
 }

  function agentAlerts(){
		$result = [
        	array(
            "title"=>"Thanks for the loyalty!",
            "summary"=>"We thank you for the trust...",
            "message" =>"We thank you for keeping the trust during times we didn't offer the service deserve",
             "date" =>"2020-04-21"
            ),
        array(
            "title"=>"We are back for good",
            "summary"=>"Expect the best ever...",
            "message" =>"Expect the best ever as we have finished migrating all our systems to very 
             liable servers and we have made sure you never have to worry again ",
             "date" =>"2020-04-21"
            )
         ];
		echo json_encode($result);
  }


    function hasBalance($headers,$paymentReq){

         $agentNo=$headers[AGENT_ID];

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


        $usedKey=(!empty($headers[APP_KEY]))? $headers[APP_KEY]: '';
        $agentId=(!empty($headers[AGENT_ID]))? $headers[AGENT_ID]: '';

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

        $agent=Modules::run('agents/getByAgentNo',$headers[AGENT_ID]);

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

   function canAgentTransact($headers){

        $agent=Modules::run('agents/getByAgentNo',$headers[AGENT_ID]);

        if($agent->status==2)
        {
                $this->errorResponse->setResponseCode("90096");
                $this->errorResponse->setResponseMessage("Service temporarily unavailable");
                return json_encode($this->errorResponse->expose());
        }

        return false;
    }

	function cron(){
    
    	$data="CRON IS WORKING";
    	$this->logToFile($data,"CRON TEST");
    	Modules::run('payment/sortPending');
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
