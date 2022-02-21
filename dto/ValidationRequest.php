<?php

class ValidationRequest {


	private $itemCode;
	private $paymentCode;
	private $customerId;
	private $amount;
	private $phoneNumber;
	private $narration="";
	private $agentId;
	private $requestRef;

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

	  public function expose() {
      return get_object_vars($this);
    }
	  

 
}

?>