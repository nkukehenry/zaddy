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
        $this->load->model('payment/payment_mdl','pay_mdl');
    }

    public function validateCustomer($request)
  {

      $paymentCode = $request->getPaymentCode();
      $route=$this->val_mdl->getRoute($paymentCode);

      $requestReference = $this->generateRef();
      $request->setRequestRef($requestReference);

      if(strrpos($route,'ezee')>-1){
         $requestReference = $this->generateEzeeRef();
         $request->setRequestRef($requestReference);
      }

       $this->val_mdl->save($request->expose()); //log to db & file

      return Modules::run("validation/".$route,$request);
  }

  public function ezeeMoney($request){
      $valresp=Modules::run("ezee/validate",$request);
      $requestReference = $this->generateRef();
      $valresponse=$this->validationResponse->setFromJson($valresp);
      $this->val_mdl->update($requestReference,$valresponse); //update to db & file
      return $valresponse;
  }

 public function ellypay($request){
      $valresp     = Modules::run("ellypay/validate",$request);
      $requestReference = $this->generateRef();
      $valresponse = $this->validationResponse->setFromJson($valresp);
      $this->val_mdl->update($requestReference,$valresponse); //update to db & file
      return $valresponse;
  }

 function iswOff($request){
 
  $valresponse= array();
  $valresponse['customerName'] = "54566666";
  $valresponse['shortTransactionRef'] = $request->getRequestRef();
  $valresponse['responseCode'] = "70013";
  $valresponse['responseMessage'] = "We are sorry,system Updates in progress";
  $valresponse['amount'] = 1000;
  $valresponse['paymentItem']="Bill Payment";
  $valresponse['isAmountFixed']=false;
  $valresponse['surcharge']=100;
  $valresponse['excise']=0;
  $valresponse['customerId']="0221223332";
  $valresponse['biller']= "54545454545";
  $valresponse['balance'] = 0;
  $valresponse['balanceType'] = "display";
  $valresponse['surchargeType']=true;
  $valresponse['balanceNarration'] = "<b>OUTSTANDING BALANCE: </b><br> UGX ";
  return $valresponse;
 
 }

    public function interswitch($request)
  {

      $paymentCode = $request->getPaymentCode();
      $customerId = $request->getCustomerId();
      $customerMobile = $request->getPhoneNumber();
      $amount = $request->getAmount();
      $requestReference=$request->getRequestRef();

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
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, PROCESS_TIMEOUT);

        curl_setopt($ch,CURLOPT_POST,1);
        //set curl time..processing time out
        curl_setopt($ch, CURLOPT_TIMEOUT, PROCESS_TIMEOUT);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Perform the request, and save content to $result
        ini_set("max_execution_time",EXEC_TIMEOUT);
        $result = curl_exec($ch);
          //curl error handling
          $curl_errno = curl_errno($ch);
                  $curl_error = curl_error($ch);
                  if ($curl_errno > 0) {
                  		 curl_close($ch);
                       return "CURL Error ($curl_errno): $curl_error\n";
                      }
           curl_close($ch);

         $decoded=json_decode($result);

         $charges = $this->pay_mdl->getCharges($paymentCode,$request->getAmount());
         $ourCharge = $charges->ourCharge;
         
         $valresponse=$this->validationResponse->setFromJson($decoded);
 
         if($ourCharge > 0) {
            $this->validationResponse->setSurchargeType(false);
         }

         $valresponse->setSurcharge($ourCharge*100 + $valresponse->getSurcharge() );

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
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, PROCESS_TIMEOUT);

      curl_setopt($ch,CURLOPT_POST,0);
      curl_setopt($ch, CURLOPT_TIMEOUT, PROCESS_TIMEOUT);
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      ini_set("max_execution_time",EXEC_TIMEOUT);
      $result = curl_exec($ch);
        //curl error handling
        $curl_errno = curl_errno($ch);
                $curl_error = curl_error($ch);
                if ($curl_errno > 0) {
                    curl_close($ch);
                     return "CURL Error ($curl_errno): $curl_error\n";
                    }
         curl_close($ch);

         $decoded=json_decode($result);

        echo number_format($decoded->balance);
    
    
  }

  public function generateRef(){
    //"0Z".strtoupper(uniqid(rand(1000,999))).mt_rand(200,1000);
  		$nums = '0123456789';
       $length=13;

   // First number shouldn't be zero
    $out = $nums[mt_rand( 1, strlen($nums)-1 )];  

   // Add random numbers to your string
    for ($p = 0; $p < $length-1; $p++)
        $out .= $nums[mt_rand( 0, strlen($nums)-1 )];

      $out="09".$out;
      $ref = REF_PREFIX.substr(time(),0,5).substr($out,0,11)."0";
  
     if(!$this->checkRef($ref))
     {
        $this->generateRef();
     }
      return $ref;
  }

  public function generateEzeeRef(){
    $ref="ZAD09".mt_rand(10242456,99999999).mt_rand(20000,20100);
    //"0Z".strtoupper(uniqid(rand(1000,999))).mt_rand(200,1000);
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
