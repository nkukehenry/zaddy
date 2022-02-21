<?php

class PaymentResponse {


	private $transferCode;
	private $requestReference;
	private $transactionRef;
	private $rechargePIN;
	private $responseCode;
	private $responseMessage;
    private $amount;

    public function setFromJSON($jsonArray){

		   foreach($jsonArray as $key=>$value){
		      $this->$key = $value;
           
           if($key=='amount') //returned amount is always *100
              $this->$key == $value/100;
           
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

      public function setResponseCode($code){
	  	 $this->$responseCode=$code;
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

      public function getAmount(){
	  	 return $this->amount;
	  }

	  public function expose() {
      return get_object_vars($this);
    }
	  

 
}

?>