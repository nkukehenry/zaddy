<?php

class Ezee extends MX_Controller{

function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('payment/payment_mdl','pay_mdl');
    }

public function validate($request){
  $paymentCode = $request->getPaymentCode();
  $method      = $this->pay_mdl->getValidateMethod($paymentCode); //endpoint in this class

  $charges   = $this->pay_mdl->getCharges($paymentCode,$request->getAmount());
  $ourCharge = $charges->ourCharge;
  $isInclusive = $charges->isInclusive;

  if($method == "offlineValidate")
     return $this->offlineValidate($request);

  $response = Modules::run('ezee/'.$method,$request);

  $responseCode = "90020";

  if($response->respcode == "0")
     $responseCode="9000";

  $isSurchargeInAmount = false;

	if(($isInclusive ==1 || $paymentCode=="503509")){
    		$isSurchargeInAmount = true;
    }

  $valresponse= array();
  $valresponse['customerName']       = (!empty($response->name) ?$response->name : $response->info2);
  $valresponse['shortTransactionRef'] = $request->getRequestRef();
  $valresponse['responseCode']     = $responseCode;
  $valresponse['responseMessage']  = $response->respmsg;
  $valresponse['amount']           = $request->getAmount()*100; //api module compliance
  $valresponse['paymentItem']      = "Bill Payment";
  $valresponse['isAmountFixed']    = false;
  $valresponse['surcharge']        = $ourCharge*100; //api module compliance
  $valresponse['excise']           = 0;
  $valresponse['customerId']       = $request->getCustomerId();
  $valresponse['biller']           = $response->biller;
  $valresponse['balance']          = (!empty($response->info4) ?$response->info4 : 0);
  $valresponse['balanceType']      = "display";
  $valresponse['surchargeType']    = $isSurchargeInAmount;
  $valresponse['balanceNarration'] = "<b>OUTSTANDING BALANCE: </b><br> UGX ";

  return $valresponse;
}

public function ezeePay($request){

     $paymentCode  = $request->getPaymentCode();
     $method       = $this->pay_mdl->getRouteMethod($paymentCode); //endpoint in this class
     $response     = Modules::run('ezee/'.$method,$request);
     $responseCode = "90020";
     $msg ="";

     if($response->respcode == "0")
        $responseCode="9000";

   if(in_array($response->respcode,EZEE_09)){
     $responseCode="90009";
     $msg = "Request is being processed, check status to confirm";
   }

     $payresponse= array();
     $payresponse['transferCode']     = (($responseCode == "9000") ?$response->emtxid : $response->respmsg);
     $payresponse['requestReference'] = $request->getRequestRef();
     $payresponse['rechargePIN']      = ($response->info1)? $response->info1:$response->emtxid;
     $payresponse['responseCode']     = $responseCode;
     $payresponse['responseMessage']  = (!empty($response->respmsg))?$response->respmsg." ".$response->receiptno:$msg;

     $payconfirmation = new PaymentResponse();

     $payconfirmation->setFromJSON($payresponse);

    return $payconfirmation;
  }

  public function offlineValidate($request){

    $responseCode="9000";
    $valresponse= array();
    $valresponse['customerName'] = '----------------';
    $valresponse['shortTransactionRef'] = $request->getRequestRef();
    $valresponse['responseCode'] = $responseCode;
    $valresponse['responseMessage'] = 'SUCCESS';
    $valresponse['amount'] = $request->getAmount()*100;
    $valresponse['paymentItem']="Payment";
    $valresponse['isAmountFixed']=false;
    $valresponse['surcharge']=0;
    $valresponse['excise']=0;
    $valresponse['customerId']=$request->getCustomerId();
    $valresponse['biller'] = $response->biller;
    $valresponse['balance'] = 0;
    $valresponse["surchargeType"]=true;
    $valresponse['balanceType'] = "any";

    return $valresponse;
  }
  public function ezeeAirtime($request){
    $data= array(
              'sccode'     => EZEE_CODE,
              'userid'     => EZEE_USERNAME,
              'password'   => EZEE_PASSWORD,
              'sctxnid'    => $request->getRequestRef(),
              'trxtype'    => "1",
              'cardid'     => '',
              'phoneno'    => $request->getCustomerId(),
              'amount'     => $request->getAmount(),
              'pinKeyword' => $request->getItemCode()
          );
      $response= $this->ezeeMoney($data,"AirTimeTopUp");
      $response->info1 = $response->info5;
      return $response;
  }

  public function ezeeServices(){
    $data= array();
      $response= $this->ezeeMoney($data,"GetTelcoList");
      print_r(json_encode($response));
  }

public function validateAirtel($request){

  $data= array(
            'sccode'   => EZEE_CODE,
            'userid'   => EZEE_USERNAME,
            'password' => EZEE_PASSWORD,
            'sctxnid'  => "ZAD".time().mt_rand(100,999),
            'payeecode' => 'WARIDPESA',
            'trxtype'  => 1,
            'phoneno'  => $request->getCustomerId()
        );
        /*
        [respcode] => 0 [respmsg] => OK [name] => Ezeemoney Test
        */
    $response = $this->ezeeMoney($data,"AirtelMoneyCheckPhoneNo");

    $response->biller = "Airtel Money";

    return $response;
}

public function airtelMoney($request){

  $itemCode = $request->getItemCode();

  if($itemCode == "CASHIN"){
    $response  = $this->airtelCashin($request);
  }else{
    $response  = $this->airtelCashout($request);
  }

  return $response;

}

public function airtelCashin($request){
  //airtel cashin
  $data= array(
            'sccode'   => EZEE_CODE,
            'userid'   => EZEE_USERNAME,
            'password' => EZEE_PASSWORD,
            'sctxnid'  => $request->getRequestRef(),
            'trxtype'  => 1,
            'phoneno'  => $request->getCustomerId(),
            'amount'   => $request->getAmount()
        );
  $response = $this->ezeeMoney($data,"AirtelMoneyCashIn");
  return $response;
}



public function getDstvBills(){

   $data= array(
            'sccode'   => EZEE_CODE,
            'userid'   => EZEE_USERNAME,
            'password' => EZEE_PASSWORD,
        );
        /*
        [respcode] => 0 [respmsg] => OK [name] => Ezeemoney Test
        */
    $response = $this->ezeeMoney($data,"GetDSTVProduct");
  
  print_r($response->product->any);
}


public function test(){

  $account="0705596470";
   $data= array(
            'sccode'   => EZEE_CODE,
            'userid'   => EZEE_USERNAME,
            'password' => EZEE_PASSWORD,
            'sctxnid'  => "ZAD".time().mt_rand(100,999),
            'payeecode' => 'WARIDPESA',
            'trxtype'  => 1,
            'phoneno'  => $account
        );
        /*
        [respcode] => 0 [respmsg] => OK [name] => Ezeemoney Test
        */
    $response = $this->ezeeMoney($data,"AirtelMoneyCheckPhoneNo");
  
  print_r($response);
}

public function airtelCashout($request){
  $data= array(
            'sccode'     => EZEE_CODE,
            'userid'     => EZEE_USERNAME,
            'password'   => EZEE_PASSWORD,
            'sctxnid'    => $request->getRequestRef(),
            'trxtype'    => 1,
            'phoneno'    => $request->getCustomerId(),
            'secretcode' => $request->getToken()
        );
    $response = $this->ezeeMoney($data,"AirtelMoneyCashOut");
    return $response;
}

public function validateUmeme($request){

    $data= array(
              'sccode'    => EZEE_CODE,
              'userid'    => EZEE_USERNAME,
              'password'  =>EZEE_PASSWORD,
              'sctxnid'   => "ZAD".time().mt_rand(100,999),
              'payeecode' => 'UMEME',
              'accountno' => $request->getCustomerId(),
              'remark1'   => '',
              'remark2'   => ''
          );
          //info2 Account Name
          /*[sctrxnid] => 1583505717 [respcode] => 0 [respmsg] => SUCCESS [info1] => 04236599102
          [info2] => UMEME TEST [info3] => PREPAID [info4] => 500 [info5] =>*/
      $response= $this->ezeeMoney($data,"ValidateBillAccount");
      $response->biller = "Umeme";
      $response->charge = ($request->getAmount()*0.025)*100;
      return $response;
  }

  public function validateStartimes($request){

      $data= array(
                'sccode'    => EZEE_CODE,
                'userid'    => EZEE_USERNAME,
                'password'  => EZEE_PASSWORD,
                'sctxnid'   =>"ZAD".time().mt_rand(100,999),
                'payeecode' => 'STARTIMES',
                'accountno' => $request->getCustomerId(),
                'remark1'   => '',
                'remark2'   => ''
            );
            
        $response= $this->ezeeMoney($data,"ValidateBillAccount");
        $response->biller = "Startimes";
        return $response;
    }
    public function validateDstv($request){

        $data= array(
                  'sccode'    => EZEE_CODE,
                  'userid'    => EZEE_USERNAME,
                  'password'  =>EZEE_PASSWORD,
                  'sctxnid'   => "ZAD".time().mt_rand(100,999),
                  'payeecode' => 'DSTV',
                  'accountno' => trim($request->getCustomerId()),
                  'remark1'   => '',
                  'remark2'   => ''
              );
              
          $response= $this->ezeeMoney($data,"ValidateBillAccount");
          $response->biller="DSTV Payments";
          return $response;
      }
      public function validateNwsc($request){

          $data= array(
                    'sccode'    => EZEE_CODE,
                    'userid'    => EZEE_USERNAME,
                    'password'  =>EZEE_PASSWORD,
                    'sctxnid'   => "ZAD".time().mt_rand(100,999),
                    'payeecode' => 'NWSC',
                    'accountno' => $request->getCustomerId(),
                    'remark1'   => $request->getItemCode(),
                    'remark2'   => ''
                );
            $response= $this->ezeeMoney($data,"ValidateBillAccount");
            $response->biller = "NWSC payment";
            $response->info4  = $response->info3;
            return $response;
        }
  public function ezeeUmeme($request){
    $payCode=1;

    if($request->getItemCode()=="POSTPAID")
      $payCode=2;

      $data= array(
                'sccode'    => EZEE_CODE,
                'userid'    => EZEE_USERNAME,
                'password'  => EZEE_PASSWORD,
                'sctxnid'   => $request->getRequestRef(),
                'txntype'   => $payCode, //“1”,postpaid 2
                'payeecode' => "UMEME",
                'accountno' => $request->getCustomerId(),
                'phoneno'   => $request->getPhoneNumber(),
                'amount'    => $request->getAmount(),
                'remark1'   => $request->getCustomerName(), //Account Name
                'remark2'   => ''
            );

            /*
            [sctrxnid] => 1583506157 [respcode] => 0 [respmsg] => SUCCESS [emtxid] => 1168660 [txnfee] => 125.00 [receiptno] => 65008900
            [info1] => 12345678901234567890 [info2] => 9.8 [info3] => [info4] => [info5]
            */
        $response= $this->ezeeMoney($data,"PayBill");
  
       if($payCode==1)
          $response->info1 = $response->info1." Units: ".$response->info2;
  
  			if($response->respcode == "1028"){
                $response->respmsg = "Service currently limited, try again later";
            	$response->info1   = $response->respmsg;
            }
  
        return $response;
    }

    public function ezeeDstv($request){

        $data= array(
                  'sccode'    => EZEE_CODE,
                  'userid'    => EZEE_USERNAME,
                  'password'  =>EZEE_PASSWORD,
                  'sctxnid'   => $request->getRequestRef(),
                  'txntype'   => "1",
                  'payeecode' => "DSTV",
                  'accountno' => $request->getCustomerId(),
                  'phoneno'   => $request->getPhoneNumber(),
                  'amount'    => $request->getAmount(),
                  'remark1'   => $request->getCustomerName(), //Account Name
                  'remark2'   => ''
              );

          $response = $this->ezeeMoney($data,"PayBill");
          return $response;
      }

      public function ezeeNwsc($request){

          $data= array(
                    'sccode'    => EZEE_CODE,
                    'userid'    => EZEE_USERNAME,
                    'password'  => EZEE_PASSWORD,
                    'sctxnid'   => $request->getRequestRef(),
                    'txntype'   => "1",
                    'payeecode' => "NWSC",
                    'accountno' => $request->getCustomerId(),
                    'phoneno'   => $request->getPhoneNumber(),
                    'amount'    => $request->getAmount(),
                    'remark1'   => $request->getItemCode(), //Area
                    'remark2'   => $request->getCustomerName()
                );

            $response = $this->ezeeMoney($data,"PayBill");
            return $response;
        }

  public function payBill($request){
    $payCode=1;
    if($request->getItemCode()=="POSTPAID")
      $payCode=2;

      $data= array(
                'sccode'    => EZEE_CODE,
                'userid'    => EZEE_USERNAME,
                'password'  => EZEE_PASSWORD,
                'sctxnid'   => $request->getRequestRef(),
                'txntype'   => $payCode, //“1”,postpaid 2
                'payeecode' => "UMEME",
                'accountno' => $request->getCustomerId(),
                'phoneno'   => $request->getPhoneNumber(),
                'amount'    => $request->getAmount(),
                'remark1'   => $request->getCustomerName(), //Account Name
                'remark2'   => ''
            );

        $response= $this->ezeeMoney($data,"PayBill");
        return $response;
    }

    public function ezeeStartimes($request){

        $data= array(
                  'sccode'    => EZEE_CODE,
                  'userid'    => EZEE_USERNAME,
                  'password'  =>EZEE_PASSWORD,
                  'sctxnid'   => $request->getRequestRef(),
                  'txntype'   => 1, //“1”,postpaid 2
                  'payeecode' => "STARTIMES",
                  'accountno' => $request->getCustomerId(),
                  'phoneno'   => $request->getPhoneNumber(),
                  'amount'    => $request->getAmount(),
                  'remark1'   => $request->getCustomerName(), //Account Name
                  'remark2'   => ''
              );

          $response= $this->ezeeMoney($data,"PayBill");
          return $response;
    }
public function checkBalance(){
        $data= array(
                  'sccode'   => EZEE_CODE,
                  'userid'   => EZEE_USERNAME,
                  'password' =>EZEE_PASSWORD
              );
          $response= $this->ezeeMoney($data,"CheckBalance");
          return $response;
}

public function fetchRemoteStatus($requestRef){

  $data= array(
            'sccode'   => EZEE_CODE,
            'userid'   => EZEE_USERNAME,
            'password' =>EZEE_PASSWORD,
            'sctxnid'  => $requestRef
        );
    $response= $this->ezeeMoney($data,"CheckBillStatus");
    $responseCode="90020";
    $msg ="";
    if($response->respcode=="0")
       $responseCode="9000";

    if(in_array($response->respcode,EZEE_09)){
        $responseCode="90009";
        $msg = "Request is being processed, check status to confirm";
    }

    $payresponse= array();
    $payresponse['transferCode']      = (!empty($response->emtxid) ?$response->emtxid : '');
    $payresponse['requestReference']  =  $requestRef;
    $payresponse['rechargePIN']       = $response->info1."  : ".$response->info2;;
    $payresponse['responseCode']      = $responseCode;
    $payresponse['responseMessage']   = (!empty($response->respmsg))?$response->respmsg." ".$response->receiptno:$msg;

    $payconfirmation = new PaymentResponse();

    $payconfirmation->setFromJSON($payresponse);

   return $payconfirmation;
}

public function ezeeMoney( $ap_param,$endpoint){

        file_put_contents(LOG_FILE, "\n REQ EZEE-MONEY OUT \n".json_encode($ap_param),FILE_APPEND);

        $soapClient = new SoapClient(WEBSERVICE_URL);
        // Prepare SoapHeader parameters
        $sh_param = array();
        $headers = new SoapHeader(WEBSERVICE_URL, 'Request', $sh_param);

        $soapClient->__setSoapHeaders(array($headers));

        $error = 0;
        try {
            $response = $soapClient->__call($endpoint, array($ap_param));
        }
        catch (SoapFault $fault) {
            $error = 1;
            file_put_contents(LOG_FILE, "\n REQ EZEE-MONEY RESPONSE \n".json_encode($fault),FILE_APPEND);
        }
        if ($error == 0) {
            unset($soapClient);

            file_put_contents(LOG_FILE, "\n REQ EZEE-MONEY RESPONSE \n".json_encode($response),FILE_APPEND);
            return $response;
          }
    }
    

    }


?>
