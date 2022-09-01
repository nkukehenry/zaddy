<?php

class PaymentResponse {


	private $transferCode;
	private $requestReference;
	private $transactionRef;
	private $rechargePIN;
	private $responseCode;
	private $responseMessage;

    public function setFromJSON($jsonArray){

		   foreach($jsonArray as $key=>$value){
		      $this->$key = $value;
		   }

		   return $this;
     }

	  public function getResponseCode(){
	  	 return $this->responseCode;
	  }

	  public function getResponseMessage(){
	  	 return $this->responseMessage;
	  }

	   public function setResponseMessage($msg){
	  	 $this->responseMessage=$msg;
	  }


	public function getRechargePIN(){
	  	 return $this->rechargePIN;
	  }

	  public function getTransferCode(){
	  	 return $this->transferCode;
	  }

	  public function getRequestRef(){
	  	 return $this->requestReference;
	  }

	  public function expose() {
      return get_object_vars($this);
    }
	  

 
}

?>