<?php

class ValidationResponse {

	private $customerName;
	private $responseCode;
  private $responseMessage='';
	private $biller;
	private $excise;
	private $surchargeType;
	private $transactionRef;
	private $isAmountFixed;
	private $paymentItemId;
	private $amount;
  private $surcharge;
	private $shortTransactionRef;
	private $paymentItem;
	private $balance;
	private $collectionsAccountNumber;
	private $customerId;
	private $collectionsAccountType;
	private $balanceType;
	private $displayBalance;

    public function setFromJSON($jsonArray){

	   foreach($jsonArray as $key=>$value){
	      $this->$key = $value;
	   }

	   return $this;
  }

  public function getCustomerName(){
  	return $this->customerName;
  }
  public function getResponseCode(){
  	return $this->responseCode;
  }

  public function getResponseMessage(){
    return $this->responseMessage;
  }
  public function getExcise(){
  	return $this->excise;
  }
  public function getSurcharge(){
    return $this->surcharge;
  }
  public function getAmount(){
    return $this->amount;
  }
  public function getSurchargeType(){
  	return $this->surchargeType;
  }
  public function getTransactionRef(){
  	return $this->transactionRef;
  }
  public function getBalance(){
  	return $this->balance;
  }
  public function getCustomerId(){
  	return $this->customerId;
  }
  public function getPaymentItem(){
  	return $this->paymentItem;
  }
  public function getBalanceType(){
  	return $this->balanceType;
  }
  public function getDisplayBalance(){
  	return $this->displayBalance;
  }

  public function expose() {
    return get_object_vars($this);
 }

 
}

?>