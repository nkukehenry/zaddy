<?php

class PaymentRequest {


	private $itemCode;
	private $paymentCode;
	private $customerId;
	private $amount;
	private $surcharge;
	private $phoneNumber;
	private $narration="";
	private $agentId;
	private $requestRef;
	private $transactionRef;
	private $customerName;
	private $token;
    private $refferalCode;
    private $otp;

    public function setFromJSON($jsonArray){

		   foreach($jsonArray as $key=>$value){
		      $this->$key = $value;
		   }

		   return $this;
     }

	  public function getItemCode(){
	  	 return $this->itemCode;
	  }
      public function getOtp(){
	  	 return $this->otp;
	  }
      public function getReferralCode(){
	  	 return $this->refferalCode;
	  }

	  public function getPaymentCode(){
	  	 return $this->paymentCode;
	  }

	public function getCustomerId(){
	  	 return $this->customerId;
	  }

	public function getAmount(){
	  	 return $this->amount;
	  }

	public function getPhoneNumber(){
	  	 return $this->phoneNumber;
	  }

	  public function getNarration(){
	  	 return $this->narration;
	  }

	  public function getRequestRef(){
	  	 return $this->requestRef;
	  }

	  public function setRequestRef($ref){
	  	  $this->requestRef=$ref;
	  }
	   public function getSurcharge(){
	  	  return $this->surcharge;
	  }

	  public function getAgentId(){
	  	  return $this->agentId;
	  }

	  public function getPaymentRef(){
	  	  return $this->transactionRef;
	  }

	  public function getCustomerName(){
	  	return $this->customerName;
	  }
		public function getToken(){
	  	return $this->token;
	  }

	  public function setAgentId($agentId){
	  	$this->agentId=$agentId;
	  }
		public function setToken($token){
	  	$this->token=$token;
	  }

		public function setAmount($amount){
	  	$this->amount=$amount;
	  }


	  public function expose() {
      return get_object_vars($this);
    }



}

?>
