
<?php
class ErrorResponse{

	private $responseCode;
	private $responseMessage;

	public function setResponseCode($code){

		$this->responseCode=$code;
	}

	public function setResponseMessage($message){

		$this->responseMessage=$message;
	}

	public function expose() {
      return get_object_vars($this);
    }

}

?>