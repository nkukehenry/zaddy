<?php

class Ellypay extends MX_Controller{

function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('payment/payment_mdl','pay_mdl');
        $this->categories = ["MMONEY","AIRTIME","WATER","INTERNET","TV","TAXES","ELECTRICITY"];
		$this->success = ["200","SUCCESSFUL"];
        $this->test = ["ZAD00001"];
    }

   function generateSignature() {
     $apiKey    = ELLYPAY_USERNAME;
     $secretKey = ELLYPAY_SECRET;
     $requestIp = "206.189.122.185"; /*add your server IP here*/
     $stringToSign = $apiKey.":".$requestIp;
     return hash_hmac('sha256', $stringToSign, $secretKey, false);
    }

	public function getId(){
    	$this->db->select('max(id) as itemId');
        return ($this->db->get("billeritems")->row()->itemId)+1;
    }
	public function getProducts($category){
    
      $res = $this->productList($category);
      $billerId = 3000;
    	//saveBillerItem
    foreach($res as $item):
        //print_r(json_encode($res))."\n";
    $itemId = $this->getId();
    if(!$item->has_price_list  && !$item->has_choice_list):
        
        $data = array('itemAmount'=>0,
                  'itemName'=>  str_replace("\\u00a0"," ",str_replace("\u00a0"," ",str_replace("-&#41;","#",str_replace("&#47;",")",str_replace("&#40;","(",$item->name))))),
                  'itemCode'=>  $item->code,
                  'billerId' => $billerId,
                  'itemId'  => $itemId,
                  'paymentCode'=>"ELLY".$billerId.$itemId,
                  'providerObject' => json_encode($item)
                   );
       $this->db->insert('billeritems',$data);
     else:
        //if($item->has_choice_list)
        //  continue;
        $this->getItems($item->code,$billerId);
     endif;
   endforeach;
    
    }

   function getItems($code,$billerId){
    $res = $this->productItems($code);
   	foreach($res as $item):
        $itemId = $this->getId();
        $data = array('itemAmount'=>$item->price,
                  'itemName'=>  str_replace("\\u00a0"," ",str_replace("\u00a0"," ",str_replace("-&#41;","#",str_replace("&#47;",")",str_replace("&#40;","(",$item->name))))),
                  'itemCode'=>  $item->code,
                  'billerId' => $billerId,
                  'itemId'  => $itemId,
                  'status'=>1,
                  'paymentCode'=>"ELLY".$billerId.$itemId,
                  'providerObject' => json_encode($item)
                   );
      $this->db->insert('billeritems',$data);
     endforeach;
		
   }

	// Get Product List -GET
	public function productList($category){
  	  $endPoint = "product-list?category={$category}";
      $response = $this->sendRequest($endPoint);
      return $response->data->products;
    }

	//items under a product -GET
   public function productItems($productCode){
  	  $endPoint = "/products/price-list?product_code={$productCode}";
      $response = $this->sendRequest($endPoint);
      return $response->data->price_list;
    }

   //product choices without price like nwsc 
   public function productChoices($listId=""){
  	  $endPoint = "/products/choice-list?list_id=={$listId}";
      $response = $this->sendRequest($endPoint);
      print_r(json_encode($response));
    }

public function validate($request){
  $paymentCode = $request->getPaymentCode();
  $ourCharge   = 0;
  $isInclusive = true;

  $charges   = $this->pay_mdl->getCharges($paymentCode,$request->getAmount());
  $totalCharge = 0;
  if(!empty($charges)):
    $ourCharge = ($charges->ourCharge)?$charges->ourCharge:0;
    $billerCharge = ($charges->billerCharge)?$charges->billerCharge:0;
    $totalCharge = $ourCharge + $billerCharge;
    $isInclusive = $charges->isInclusive;
  endif;

  $response = $this->validateAcc($request);

  $responseCode = "90020";

  $isSurchargeInAmount = false;

 if(($isInclusive ==1)){
    		$isSurchargeInAmount = true;
   }

 $success = ["200","201"];
 $name = "";
 $message = $response->message;
 $biller  = "";
 $balance = 0;

   if(in_array($response->code,$success)){

     Modules::run("cache/setStr",$request->getRequestRef(),$response->data->validation_reference);
   	 $name = ($response->data->customer_name)?$response->data->customer_name:$response->data->account_name;
     $responseCode = "90000";
     $biller =   $response->data->product_name;
     $balance =  $response->data->balance_due;

   }

  $valresponse= array();
  $valresponse['customerName']        = $name;
  $valresponse['shortTransactionRef'] = $request->getRequestRef();
  $valresponse['responseCode']     = $responseCode;
  $valresponse['responseMessage']  = $message;
  $valresponse['amount']           = $request->getAmount()*100; //api module compliance
  $valresponse['paymentItem']      = "Bill Payment";
  $valresponse['isAmountFixed']    = false;
  $valresponse['surcharge']        = $totalCharge*100; // *100 api module compliance
  $valresponse['excise']           = 0;
  $valresponse['customerId']       = $request->getCustomerId();
  $valresponse['biller']           = $biller;
  $valresponse['balance']          = $balance;
  $valresponse['balanceType']      = ($balance>0)?"display":"any";
  $valresponse['surchargeType']    = $isSurchargeInAmount;
  $valresponse['balanceNarration'] = "<b>OUTSTANDING BALANCE: </b><br> UGX ";
  return $valresponse;
}

//payment
public function payment($request){
     
     
	 $payresponse  = array(); //will contain the response
   $paymentCode  = $request->getPaymentCode();
	 $requestRef   = $request->getRequestRef();
    
     $pending = ["202","201"];
     $success = ["200"];
	 
     $validationRef = Modules::run("cache/getStr", $requestRef);
     $response = $this->processPayment($validationRef,$request);

     $responseCode = "90009";
     $msg ="";
	
     $transferCode ="";
     $message="Request is being processed, check status to confirm";
   
      $transferCode = $response->message;
      $token = "";
	//Straight Success
    if(!empty($response) && in_array($response->code,$success) ){
        $responseCode="9000";
        $message = $response->message;

     if(in_array($response->code,$success)):
         $message = $response->message;
         $data    = $response->data;
         $transferCode = $data->internal_reference;
         $token = ($data->details->token)?$data->details->token:$data->internal_reference;
     endif;

     }

     //Callback listener
    else if( empty($response) || in_array($response->code,$pending) ){
       
       $responseCode="90009";
       $msg = "Request is being processed, check status to confirm";
       $checkAgain = true;
       $retries = 0;
    
       do{
       
         $callback = Modules::run("cache/getData",$requestRef);
       
         if(!empty($callback->agent_reference)){
            
            $checkAgain = false;
         
            if($callback->status == "SUCCESSFUL"){
            
                if($paymentCode=="50017548")//airtel withdraw
                   $payresponse['amount']  = $callback->amount;
                 
            	$msg = ($callback->description)?$callback->description:$callback->message;
                $transferCode = $callback->internal_reference;
                $token = ($callback->details->token)?$callback->details->token:$callback->internal_reference;
                $responseCode = "9000";
            }else{
                $responseCode = "900096";
            	$msg = ($callback->description)?$callback->description:$callback->message;
            }
         }
       
       	 $retries++;
         
         if($checkAgain)
       	 sleep(3);
       
       }while($checkAgain && $retries<8);
       
     }
     else if( !empty($response) && !in_array($response->code,$success)){
    	   
         $responseCode="90020";
         $message = $response->message;
    }

     
     $payresponse['transferCode']     = $transferCode;
     $payresponse['requestReference'] = $request->getRequestRef();
     $payresponse['rechargePIN']      = $token;
     $payresponse['responseCode']     = $responseCode;
     $payresponse['responseMessage']  = $message;

     $payconfirmation = new PaymentResponse();

     $payconfirmation->setFromJSON($payresponse);

    return $payconfirmation;
  }

  //Account validation
   public function validateAcc($request){
  	  $endPoint = "/validate-account";
	  $extraParams = array("customer_phone"=> $request->getPhoneNumber());
   
      $itemCode = $request->getItemCode();
   
      if(empty($itemCode)):
       $this->db->where('paymentCode', $request->getPaymentCode());
       $itemCode = $this->db->get('billeritems')->row()->itemCode;
      endif;
   
   	  $payLoad = array(
      "agent_reference" => $request->getRequestRef(),
	  "account_number" => $request->getCustomerId(),
	  "product_code" =>  $itemCode,
	  "amount" => $request->getAmount(),
      "extra_params" => $extraParams
      );
   
      $response = $this->sendRequest($endPoint,json_encode($payLoad),true);
       return $response;
    }

	public function  processPayment($validationRef,$request){
    $endPoint = "/process-payment";
    $payload = array(
	  "validation_reference" => $validationRef,
	  "pin" => $this->getPin(),
	  "secret_code" => ($request->getOtp())?base64_encode($request->getOtp()):''
      );
     $response = $this->sendRequest($endPoint,json_encode($payload),true);
     return $response;
    
    }

	public function handleCallback(){
    
     $callback = json_decode(file_get_contents('php://input'));
     $ref = $callback->agent_reference;
      $response = $callback;
    
      Modules::run("cache/setData",$ref,$callback);
    
      $data = array(
        'responseCode'=>(in_array($response->status,$this->success))?"9000":"90020",
        'status'=>(in_array($response->status,$this->success))?"SUCCESSFUL":"FAILED",
        'amount'=>$response->amount,
        'token'=>(!empty($response->details->token))?$response->details->token:$response->internal_reference,
      	'response'=>json_encode($response),
      	'message'=>$response->description
        );
    
      $this->pay_mdl->callbackUpdate($ref,$data);
    
      file_put_contents(CALLBACK_FILE, "\n CALLBACK RECEIVED ".json_encode($callback),FILE_APPEND);
    	/*{
      "id": 24546,
      "amount": 30000,
      "product_code": "YAKA",
      "product_name": "UMEME YAKA",
      "agent_reference": "CSTREFYRWWVRKLG6W1P3",
      "internal_reference": "ELPREFYRWWM8FKMBH1A5A",
      "agent_commission": 700,
      "status": "SUCCESSFUL",
      "description": "Purchase completed successfully for 04250513159",
      "details": {
           "token": "6931 3037 4217 9675 2152",
           "units": 39.4,
           "customer_name": "SSUNA, RICHARD",
           "account_number": "04250513159"
         }
       }*/
      echo  json_encode(array("message"=>"Received Successfully"));
    }

	function getPin() {
     $pin = ELLYPAY_PIN;
     return strtoupper(sha1($pin));
    }
   // provider level requests interceptor/forwarder
   public function sendRequest($endPoint,$body=[],$isPost=false){
     $url = "https://api.ellypayapp.com/merchant/".$endPoint;
   
     $headers[]='Content-Type:application/json';
     $headers[]='ApiKey:'. ELLYPAY_USERNAME;
     $headers[]='SecretKey:'. ELLYPAY_SECRET;
     $headers[]='Signature:'.$this->generateSignature();
    //check if is post
    if($isPost)
     return Modules::run('utils/sendHttpPost',$url,$headers,$body); //for post requests
   	//for get requests
     return Modules::run('utils/sendHttpGet',$url,$headers,$body);

   }

   public function test($code){
      print_r($this->productItems($code));
   }



    }


?>
