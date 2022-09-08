<?php

class Validation extends MX_Controller{

function __construct()
    {
        // Construct the parent class
        parent::__construct();
        include('scripts/InterswitchAuth.php');
        include('dto/ValidationResponse.php');
        $this->validationResponse=new ValidationResponse();
        $this->load->model('validation_mdl','val_mdl');
    }
    
    public function webValidation(){
        
        include('dto/ValidationRequest.php');
        $decoded=json_decode($this->input->post('data'));
        $decoded->requestRef="";
        
        $val=new ValidationRequest();
        
        $val->setFromJson($decoded);
        
        $validationResponse=$this->validateCustomer($val);
         $apiResponse=$validationResponse->expose();
         
        echo json_encode($apiResponse);
    }
    
    public function validateOption($request){

        $requestReference = $this->generateRef();
    
        $request->setRequestRef($requestReference);
    
        $this->val_mdl->save($request->expose()); //log to db & file
            
        $paymentCode = $request->getPaymentCode();
        
        $route=$this->val_mdl->getRoute($paymentCode);
        
        return Modules::run("validation/".$route,$request);
    }
    
    public function offlineValidate($valRequest){
        
             $apiResponse = array();
        
             $apiResponse['responseCode']='90020';
              
              if($valRequest->getAmount()==0){
                  
                  $apiResponse['responseMessage']='Invalid Amount';
              }
              else if(strlen($valRequest->getCustomerId())<10){
                  $apiResponse['responseMessage']='Invalid Phone Number';
              }
              else{
                  $apiResponse['responseCode']='9000';
              }
              
              $item = $this->val_mdl->getItem($valRequest->getPaymentCode());
              $apiResponse['paymentItem']= $item->itemName;
              
              $amount = $valRequest->getAmount();
              
            
             $apiResponse['amount']= $amount *100;
             $apiResponse['surcharge'] = '';
             $apiResponse['transactionRef'] = $this->generateRef(true);
             $apiResponse['shortTransactionRef'] = $valRequest->getRequestRef();
             $apiResponse['customerName']="No: ".$valRequest->getCustomerId();
             
             $result = json_encode($apiResponse);
             $decoded=json_decode($result);
             $valresponse=$this->validationResponse->setFromJson($decoded);
             
             return $valresponse;
    }

    public function validateCustomer($request)
    {

    $paymentCode = $request->getPaymentCode();
    $customerId = $request->getCustomerId();
    $customerMobile = $request->getPhoneNumber();
    $amount = $request->getAmount();
    $requestReference = $request->getRequestRef();
    
    $customerEmail = "bills@bill.com";
    $httpMethod = "POST";
    $resourceUrl = SVA_BASE_URL."validateCustomer";
    //collected transaction data into array, then into json obj
    $transaction_data= array(
      "paymentCode"=> $paymentCode,
      "customerId"=>$customerId,
      "customerMobile"=>$customerMobile,
      "requestReference"=>$requestReference,
      "terminalId"=>MY_TERMINAL,
      "deviceTerminalId"=>DEVICE_TERMINAL,
      "amount"=>$amount*100,
      "bankCbnCode"=>CBN_CODE,
      "customerEmail"=>$customerEmail,
      "cardPan"=>""
     );

    $final_trans_data=json_encode($transaction_data);

    file_put_contents(LOG_FILE, "\n TO PROVIDER \n".$final_trans_data,FILE_APPEND);

    $interswitchAuth=new InterswitchAuth();

    $AuthData=$interswitchAuth->generateInterswitchAuth($httpMethod, $resourceUrl, CLIENT_ID,
            CLIENT_SECRET, "", SIGNATURE_REQ_METHOD);
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

         $valresponse=$this->validationResponse->setFromJson($decoded);

         $this->val_mdl->update($requestReference,$valresponse); //update to db & file

         return $valresponse;
  }
  
  
    public function checkBalance()
  {

    $httpMethod = "GET";
    $resourceUrl = APPSERVICE_URL."merchants/" .DEVICE_TERMINAL. "/accounts/balance";
    //collected transaction data into array, then into json obj

      $ch = curl_init($resourceUrl);

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);

      curl_setopt($ch,CURLOPT_POST,0);
      curl_setopt($ch, CURLOPT_TIMEOUT, PROCESS_TIMEOUT);
      ini_set("max_execution_time",EXEC_TIMEOUT);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      $result = curl_exec($ch);
        //curl error handling
        $curl_errno = curl_errno($ch);
                $curl_error = curl_error($ch);
                if ($curl_errno > 0) {
                    echo "CURL Error ($curl_errno): $curl_error\n";
                    }
         curl_close($ch);

         $decoded=json_decode($result);

        echo number_format($decoded->balance);
  }
  
  public function generateRef($simple=false){
      
      $prefix=REF_PREFIX;
      
      $ref=$prefix.strtoupper(uniqid(rand(10*45, 100*98)));
      
      if($simple)
      {
          $prefix=mt_rand(22222,99999);
          $ref= date('Ymdhi').$prefix;
          
      }
      
     if(!$this->checkRef($ref))
     {
         $this->generateRef();
     }
      
      return $ref;
      
  }
  
  function checkRef($ref){
      
      $this->db->where("requestRef",$ref);
      $qry=$this->db->get("transactions");
      $result=$qry->num_rows();
      
      if($result>0)
        return false;
       
       return true;
  }

}

?>