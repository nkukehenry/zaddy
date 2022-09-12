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
	private $referralCode;

    public function setFromJSON($jsonArray){

		   foreach($jsonArray as $key=>$value){
		      $this->$key = $value;
		   }

		   return $this;
     }

	  public function getItemCode(){
	  	 return $this->itemCode;
	  }

	  public function getPaymentCode(){
	  	 return $this->paymentCode;
	  }

	  public function getReferralCode(){
	  	return $this->referralCode;
	  }

	public function getCustomerId(){
	  	 return $this->customerId;
	  }

	  public function setAmount($amount){
	  	 return $this->amount = $amount;
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
	  
	  public function setAgentId($agentId){
	  	$this->agentId=$agentId;
	  }


	  public function expose() {
      return get_object_vars($this);
    }
	  

 
}

?>