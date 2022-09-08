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
	  
	   public function setItemCode($n){
	  	  $this->itemCode=$n;
	  }

	  public function getPaymentCode(){
	  	 return $this->paymentCode;
	  }

    public function setPaymentCode($n){
	  	  $this->paymentCode=$n;
	  }
	public function getCustomerId(){
	  	 return $this->customerId;
	  }
	  
     public function setCustomerId($n){
	  	  $this->customerId=$n;
	  }
	  
	public function getAmount(){
	  	 return $this->amount;
	  }

     public function setAmount($n){
	  	  $this->amount=$n;
	  }

	public function getPhoneNumber(){
	  	 return $this->phoneNumber;
	  }
	  
	   public function setPhoneNumber($n){
	  	  $this->phoneNumber=$n;
	  }


	  public function getNarration(){
	  	 return $this->narration;
	  }
	  
	  public function setNarration($n){
	  	  $this->narration=$n;
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