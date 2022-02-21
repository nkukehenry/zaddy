<?php

class Billpay extends MX_Controller{

function __construct()
    {
        // Construct the parent class
        parent::__construct();
        include('scripts/InterswitchAuth.php');
        //include('scripts/ValidationReuest.php');
 
    }


     public function billPaymentAdvice($phone_number,$amount,$customerInfo){
	    
	    $paymentCode = "151155";
		$customerId = $customerInfo->CustomerId;
		$customerMobile = $phone_number;

		$requestReference = $customerInfo->ShortTransactionRef;
		$amount = $amount;
		$customerEmail = "";
		$httpMethod = "POST";
		$resourceUrl =SVA_BASE_URL."sendAdviceRequest";
		$clientId =CLIENT_ID;
		$clientSecretKey = CLIENT_SECRET;
		$signatureMethod = SIGNATURE_REQ_METHOD;

		//collected transaction data into array, then into json obj

		$transaction_data= array(
			"paymentCode"=> $paymentCode,
			"customerId"=>$customerId,
			"customerMobile"=>$customerMobile,
			"requestReference"=>$requestReference,
			"deviceTerminalId"=>DEVICE_TERMINAL,
			"amount"=>$amount,
			"terminalId"=>MY_TERMINAL,
			"bankCbnCode"=>CBN_CODE,
			"surcharge"=>$customerInfo->Surcharge,
			"transactionRef"=>$customerInfo->TransactionRef,
			"narration"=>$customerInfo->Narration,
			"depositorName"=>$customerInfo->CustomerName,
			"customerEmail"=>$customerEmail,
			"location"=>"Wandegeya" //to be changed with db
	   );

        $final_trans_data=json_encode($transaction_data);
		$interswitchAuth=new InterswitchAuth();
		$AuthData=$interswitchAuth->generateInterswitchAuth($httpMethod, $resourceUrl, CLIENT_ID,
						CLIENT_SECRET, "", SIGNATURE_REQ_METHOD);
            $request_headers = array();
			$request_headers[] = "Authorization:".$AuthData['AUTHORIZATION'];
			$request_headers[] = "Timestamp:".$AuthData['TIMESTAMP'];
			$request_headers[] = "Nonce:".$AuthData['NONCE'];
			$request_headers[] = "Signature:".$AuthData['SIGNATURE'];
			$request_headers[] = "SignatureMethod:".$AuthData['SIGNATURE_METHOD'];
			$request_headers[] = "TerminalId:".$deviceTerminalId;
			$request_headers[] = 'Content-Type: application/json';

			// Initialize cURL session
			$ch = curl_init($resourceUrl);
			// Disable SSL verification
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			// Option to Return the Result, rather than just true/false
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// Set Request Headers 
			curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
			//time to while waiting for connection...indefinite
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
			//allow post
			curl_setopt($ch,CURLOPT_POST,1);
			 //post values
            curl_setopt($ch,CURLOPT_POSTFIELDS,$final_trans_data);
			//set curl time..processing time out
			curl_setopt($ch, CURLOPT_TIMEOUT, PROCESS_TIMEOUT);
			
			ini_set("max_execution_time",EXEC_TIMEOUT);
			// Perform the request, and save content to $result
			$result = curl_exec($ch);
				
				//curl error handling
				$curl_errno = curl_errno($ch);
                $curl_error = curl_error($ch);

                if ($curl_errno > 0) {

                echo "CURL Error ($curl_errno): $curl_error\n";

                    }
               //print_r($result);
			// Close the cURL resource, and free up system resources!
			curl_close($ch);
			$response=json_decode($result);

				return $response;
	}

	

}


?>