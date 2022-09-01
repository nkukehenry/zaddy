<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MX_Controller {

	public function __construct()
        {
            parent::__construct();
            $this->load->model('reports_mdl');
            //$this->load->model('agents/agents_mdl','agents_mdl');
            date_default_timezone_set("Africa/Kampala");
            $this->module="reports";
            Modules::run("auth/isLegal");
        }


        

	public function transactions($params="")
	    {
	        
         $searchData=array();
         
         if($this->input->post()!==null){
             $searchData=$this->input->post();
         }
         
         if(empty($searchData)){
             $searchData['startDate']=date('Y-m-d');
             $searchData['endDate']=date('Y-m-d');
         }
         
         $params="";
        foreach($searchData as $value){
           $params .= $value."-";
        }
        
        $params = rtrim($params, "-");
         
        $data['search']=$searchData;
        $data['key']=$searchData['key'];
        
	    $this->load->library('pagination');
	    $config = array();

        $config["base_url"] = base_url() . "reports/transactions/";
        $config["total_rows"] = $this->reports_mdl->countTransactions($searchData);
        $config["per_page"] = 200;
        $config["uri_segment"] = 3;
       
	    //CUSTOM LINKS
	    $config['full_tag_open'] = '<nav class="pt-3" aria-label="Page navigation"><ul class="pagination">';
        $config['full_tag_close'] = '</nav></ul>';

        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['use_page_numbers'] = TRUE;
        //END CUSTOM LINKS
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        $pg=@$_GET['page'];

        $page = ($pg)? $pg:0; //($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        $data["links"] = $this->pagination->create_links();
		$data['module']=$this->module;
		$data['view']="transactions";
		$data['transactions']=$this->reports_mdl->getTransactions($config["per_page"],$page,$searchData);
		$data['page']="Transactions";
		

		echo Modules::run("templates/admin",$data);
	}
	
	
	public function exportExcelTrans($start,$stop,$agent=null,$customer=null,$status=null){
	    
	    
	    $searchData=array();
	    $searchData['startDate']=$start;
	    $searchData['endDate']=$end;
	    $searchData['agentNo']=$agent;
	    $searchData['customerNo']=$customer;
	    $searchData['tranStatus']='';
	    
	      if(empty($start))
             $searchData['startDate']=date('Y-m-d');
        if(empty($end))
             $searchData['endDate']=date('Y-m-d');
           if(empty($agent))  
             $searchData['agentNo']='';
             
        if(empty($status))  
             $searchData['tranStatus']='';
             
        if(empty($customer))  
             $searchData['customerNo']='';
             
             

		$transactions=$this->reports_mdl->getTransactions(0,0,$searchData);

		$data=array();

        $balance=0;
        
		foreach ($transactions as $tran) {

			$row=array();
			
			$balance += $tran->impact;

			$row['DATE']=date('d/m/Y', strtotime($tran->paymentDate));
			
			 $tran =json_decode($tran->requestObject);

                if(!empty($tranData->itemName)){
                   $row['ITEM NAME'] = $tranData->itemName;
                }
                else if($tran->paymentCode=="SHARE"){ $row['ITEM NAME'] = "FLOAT SHARE"; } 
                else if($tran->paymentCode=="COMMS"){ $row['ITEM NAME'] = $tran->narration; } 
                else if ($tran->paymentCode=="LOAD"){ $row['ITEM NAME'] =  "WALLET LOAD";}
                else { 
                  $row['ITEM NAME'] =(empty($tran->itemName))?$tran->itemName:$tran->narration; 
                }
                                         
            $row['CUSTOMER_ID']=$tran->customerNo;
            $row['NAME']=$tran->customerName;
			$row['REF']=$tran->requestRef;
			$row['STATUS']=$tran->finalStatus;
			$row['IMPACT/AMOUNT']=$tran->amount;

			$data[]=$row;
		}

		$filename="searched_transactions".$agentNo."_".time().".xls";
		$this->exportToExcel($data,$filename);

	}
	
    
	public function myTransactions($params="")
	    {
	        
	        $user=$this->session->userdata();
	        
         $searchData=array();
         
         if($this->input->post()!==null){
             $searchData=$this->input->post();
         }
         
         if(empty($searchData)){
             $searchData['startDate']=date('Y-m-d');
             $searchData['endDate']=date('Y-m-d');
         }
         
         $searchData['agentNo']=$user['agentNo'];
         
         $params="";
        foreach($searchData as $value){
           $params .= $value."-";
        }
        
        $params = rtrim($params, "-");
         
        $data['search']=$searchData;
        
	    $this->load->library('pagination');
	    $config = array();

        $config["base_url"] = base_url() . "reports/myTransactions/";
        $config["total_rows"] = $this->reports_mdl->countTransactions($searchData);
        $config["per_page"] = 200;
        $config["uri_segment"] = 3;
       
	    //CUSTOM LINKS
	    $config['full_tag_open'] = '<nav class="pt-3" aria-label="Page navigation"><ul class="pagination">';
        $config['full_tag_close'] = '</nav></ul>';

        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['use_page_numbers'] = TRUE;
        //END CUSTOM LINKS
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        $pg=@$_GET['page'];

        $page = ($pg)? $pg:0; //($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        $data["links"] = $this->pagination->create_links();
		$data['module']=$this->module;
		$data['view']="mytransactions";
		$data['transactions']=$this->reports_mdl->getTransactions($config["per_page"],$page,$searchData);
		$data['page']="My Transactions";
		

		echo Modules::run("templates/user",$data);
	}
	
	
	public function statement($agentNo="")
	    {
	      
	      $searchData=array();
         
         if($this->input->post()!==null){
             $searchData=$this->input->post();
         }
	     if(isset($_POST['agentno']))  
	      $agentNo=$this->input->post('agentno');
	      
	       if(empty($searchData)){
             $searchData['startDate']=date('Y-m-d');
             $searchData['endDate']=date('Y-m-d');
         }
         
         
         $data['search']=$searchData;

	    $this->load->library('pagination');
	    $config = array();

        $config["base_url"] = base_url() . "reports/statement/".$agentNo."/";
        $config["total_rows"] = $this->reports_mdl->countStatement($agentNo);
        $config["per_page"] = 10;
        $config["uri_segment"] = 4;
       
	    //CUSTOM LINKS
	    $config['full_tag_open'] = '<nav class="pt-3" aria-label="Page navigation"><ul class="pagination">';
        $config['full_tag_close'] = '</nav></ul>';

        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['use_page_numbers'] = TRUE;
        //END CUSTOM LINKS
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        $pg=@$_GET['page'];

        $page = ($pg)? $pg:0; //($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        $data["links"] = $this->pagination->create_links();
		$data['module']=$this->module;
		$data['view']="statement";
		$transactions=array();
		
		 if(isset($_POST['agentno'])) {
		   $transactions=$this->reports_mdl->getStatement($agentNo,$searchData,$config["per_page"],$page);
		
		$data['transactions']=$transactions['statement'];
		$data['startingBalance']=$transactions['startingBal'];
		 }
		$data['agent']=Modules::run('agents/getByAgentNo',$agentNo);
		
		if(empty($transactions))
		  $data['transactions']=array();
		
		$data['page']="Agent Statement";
		$data['agentNo']=$agentNo;
		
		//print_r($transactions);

		echo Modules::run("templates/admin",$data);
	}

	public function getByAgent($agentNo)
	{
		
		$reports=$this->reports_mdl->getByAgent($agentNo);

		return $reports;
		
	}
	
	
	public function getPending(){
	    
	    $transactions=$this->reports_mdl->getPendingTransactions();
	    
	    return $transactions;
	}
	
	public function  getPdfStatement($agentNo,$start,$end){
	    
	    $searchdata=array();
	    
	    $searchdata['startDate']=$start;
	    $searchdata['endDate']=$end;

		$data['heading']=$agentNo." Agent Statement";
	    $transactions=$this->reports_mdl->getStatement($agentNo,$searchdata,0,0);
	    
	    $data['transactions']=$transactions['statement'];
	    $data['startingBalance']=$transactions['startingBal'];
	    
	    $data['agent']=Modules::run('agents/getByAgentNo',$agentNo);
		$html=$this->load->view("statement_pdf",$data,true);

		$filename=$agentNo.time().".pdf";
		$this->makeLpdf($html,$filename,"I");
	}

	public function exportExcelStatement($agentNo){

		$transactions=$this->reports_mdl->getExcelStatement($agentNo,0,0);

		$data=array();

        $balance=0;
        
		foreach ($transactions as $tran) {

			$row=array();
			
			$balance += $tran->impact;

			$row['DATE']=date('d/m/Y', strtotime($tran->paymentDate));
			
             $tran =json_decode($tran->requestObject);

                if(!empty($tranData->itemName)){
                   $row['ITEM NAME'] = $tranData->itemName;
                }
                else if($tran->paymentCode=="SHARE"){ $row['ITEM NAME'] = "FLOAT SHARE"; } 
                else if($tran->paymentCode=="COMMS"){ $row['ITEM NAME'] = $tran->narration; } 
                else if ($tran->paymentCode=="LOAD"){ $row['ITEM NAME'] =  "WALLET LOAD";}
                else { 
                  $row['ITEM NAME'] =(empty($tran->itemName))?$tran->itemName:$tran->narration; 
                }

             
             if($tran->impact>0){
                        $row['FROM']=$tran->customerNo;
                      } 
             else if($tran->impact<0 ){
                          
                         $row['FROM']=$tran->agentNo;
                    }
                    
            if($tran->impact>0){
                $row['TO']=$tran->agentNo;
            } 
             else if($tran->impact<0 ){
                                                  
              $row['TO']=$tran->customerNo;
            }
            
            $row['NAME']=$tran->customerName;
			$row['REF']=$tran->requestRef;
			$row['STATUS']=$tran->finalStatus;
			$row['IMPACT/AMOUNT']=$tran->impact;
			$row['BALANCE']=$balance;

			$data[]=$row;
		}

		$filename="agent_statement_".$agentNo."_".time().".xls";
		$this->exportToExcel($data,$filename);

	}
	
	public function getFees($amt,$paycode){
	    
	    $fees=$this->reports_mdl->getFees($amt,$paycode);
	    
	    return number_format((float)$fees, 2, '.', '');
	    
	}
   
   public function getTotalAgentCommission(){
	    
	    $fees=$this->reports_mdl->getAllAgentCommission();
	    
	    $f= number_format((float)$fees, 2, '.', '');
	    
	    echo number_format($f,2);
	    
	}
	
	public function getTotalPaidAgentCommission(){
	    
	    $fees=$this->reports_mdl->getAllPaidCommission();
	    
	    $f= number_format((float)$fees, 2, '.', '');
	    
	    echo number_format($f,2);
	    
	}
	
	
    public function getTotalEarned(){
	    
	    $paidCommissions=$this->reports_mdl->getPaidCommissions();
	    
	    $fees=$this->reports_mdl->getOurCommission();
	    
	    $final=$fees-$paidCommissions;
	    
        $f= number_format((float)$final, 2, '.', '');
	    
	     echo number_format($f,2);
	    
	}
	
	public function calcComms(){
	    
	    //$trans=$this->reports_mdl->getTransactions(1000,0);
	    $this->db->where('requestRef','ELI54465E74BEF1C63DB');
	    $trans=$this->db->get('transactions')->result();
	    $row=0;
	    foreach($trans as $tran){
	        
	        $ourfee=number_format((float)$this->getFees($tran->amount,$tran->paymentCode), 1, '.', '');
	        
	        if(!empty($ourfee)){
	            
	                 $row+=1;
	             $comms=number_format((float)$ourfee*COMMISSION, 1, '.', '');
	             
	             
	            $data=array("our_fee"=>$ourfee,"agent_fee"=>$comms);
	            $this->db->where("requestRef",$tran->requestRef);
	            $this->db->update("transactions",$data);
	        }
	    }
	    
	    echo $row;
	    
	}
	

	
	//UTILITY/SHARED FUNCTION BELOW
	
Public function makePdf($html,$filename,$action)
	{	

	$this->load->library('M_pdf');  //or i used ML_pdf for landscape

	 ini_set('max_execution_time',0);
	 $PDFContent = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
	 

	 ini_set('max_execution_time',0);

	$this->m_pdf->pdf->WriteHTML($PDFContent); //ml_pdf because we loaded the library ml_pdf for landscape format not m_pdf

	/*
	header('Content-Type: application/pdf'); //mime type
	header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
	 */
	//download it D save F.
	$this->m_pdf->pdf->Output($filename,$action);

	}

	//function that makes all landscape pdfs
public function makeLpdf($html,$filename,$action)
	{	

	@$this->load->library('ML_pdf');  //or i used ML_pdf for landscape

	 ini_set('max_execution_time',0);
	 $PDFContent = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
	 
	 ini_set('max_execution_time',0);

	$this->ml_pdf->pdf->WriteHTML($PDFContent); //ml_pdf because we loaded the library ml_pdf for landscape format not m_pdf
	 
	//download it D save F.
	$this->ml_pdf->pdf->Output($filename,$action);
	}
	
 public function exportToExcel($exportData,$filename) {
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        if(is_array($exportData))
         $exportData=json_decode(json_encode($exportData), true);
        
        $isPrintHeader = false;
        
        foreach ($exportData as $row) {
            if (! $isPrintHeader) {
                echo implode("\t", array_keys($row)) . "\n";
                $isPrintHeader = true;
            }
            echo implode("\t", array_values($row)) . "\n";
        }
        exit();
    }
    
  public function testTransactions($params="")
	    {
	        
         $searchData=array();
         
         if($this->input->post()!==null){
             $searchData=$this->input->post();
         }
         
         if(empty($searchData)){
             $searchData['startDate']=date('Y-m-d');
             $searchData['endDate']=date('Y-m-d');
         }
         
         $params="";
        foreach($searchData as $value){
           $params .= $value."-";
        }
        
        $params = rtrim($params, "-");
         
        $data['search']=$searchData;
        
	    $this->load->library('pagination');
	    $config = array();

        $config["base_url"] = base_url() . "reports/transactions/";
        $config["total_rows"] = $this->reports_mdl->countTransactions($searchData);
        $config["per_page"] = 200;
        $config["uri_segment"] = 3;
       
	    //CUSTOM LINKS
	    $config['full_tag_open'] = '<nav class="pt-3" aria-label="Page navigation"><ul class="pagination">';
        $config['full_tag_close'] = '</nav></ul>';

        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['use_page_numbers'] = TRUE;
        //END CUSTOM LINKS
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        $pg=@$_GET['page'];

        $page = ($pg)? $pg:0; //($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        $data["links"] = $this->pagination->create_links();
		$data['module']=$this->module;
		$data['view']="test_transactions";
		$data['transactions']=$this->reports_mdl->getTestTransactions($config["per_page"],$page,$searchData);
		$data['page']="Transactions";
		

		echo Modules::run("templates/admin",$data);
	}
	
	
	public function exportTestExcelTrans($start,$stop,$agent=null,$customer=null,$status=null){
	    
	    
	    $searchData=array();
	    $searchData['startDate']=$start;
	    $searchData['endDate']=$end;
	    $searchData['agentNo']=$agent;
	    $searchData['customerNo']=$customer;
	    $searchData['tranStatus']='';
	    
	      if(empty($start))
             $searchData['startDate']=date('Y-m-d');
        if(empty($end))
             $searchData['endDate']=date('Y-m-d');
           if(empty($agent))  
             $searchData['agentNo']='';
             
        if(empty($status))  
             $searchData['tranStatus']='';
             
        if(empty($customer))  
             $searchData['customerNo']='';
             
             

		$transactions=$this->reports_mdl->getTestTransactions(100,0,$searchData);

		$data=array();

        $balance=0;
        
		foreach ($transactions as $tran) {

			$row=array();
			
			$balance += $tran->impact;

			$row['DATE']=date('d/m/Y', strtotime($tran->paymentDate));
			
			if ($tran->paymentCode=="LOAD"){
			    
			       if($tran->impact>0)
                    $row['ITEM NAME']=  "WALLET LOAD";
                    if($tran->impact<0)
                     $row['ITEM NAME']=  "WALLET DEBIT";
			    
			} 
             else if($tran->paymentCode=="SHARE"){
                 $row['ITEM NAME']="FLOAT SHARE"; 
             } 
             else if($tran->paymentCode=="COMMS"){
                 $row['ITEM NAME']=$tran->customerNo->narration; 
             } 
             else { 
                 $row['ITEM NAME']=Modules::run("billers/getItemName",$tran->paymentCode);
             }
             
                                         
            $row['CUSTOMER_ID']=$tran->customerNo;
            $row['NAME']=$tran->customerName;
			$row['REF']=$tran->requestRef;
			$row['STATUS']=$tran->finalStatus;
			$row['IMPACT/AMOUNT']=$tran->amount;
			$row['RESPONSE_CODE']=$tran->responseCode;

			$data[]=$row;
		}

		$filename="searched_transactions".$agentNo."_".time().".xls";
		$this->exportToExcel($data,$filename);

	}
	
	
	
public function africellBalance()
  {
    $transaction_data = array(
      "vendorNo" => AFRICELL_VENDORNO,
      "agentNo" => "ELLYPAYAPP",
      "login" => base64_encode(AFRICELL_USER),
      "password" => base64_encode(AFRICELL_PASS),
      "proof" => str_replace("==","",base64_encode("ELLYPAYAPP"))
     );
     
     $resourceUrl="http://104.37.191.18/connect/index.php/africellBal";
     $trans_data=json_encode($transaction_data);

      $request_headers = array();
      $request_headers[] = "Content-Type: application/json;charset=UTF-8";
 
      $ch = curl_init($resourceUrl);

      curl_setopt($ch,CURLOPT_POSTFIELDS,$trans_data);
      // Option to Return the Result, rather than just true/false
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      // Set Request Headers 
      curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
      //time to wait while waiting for connection...indefinite
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);

      curl_setopt($ch, CURLOPT_HTTP_VERSION, '1.1');

      curl_setopt($ch,CURLOPT_POST,1);
      //set curl time..processing time out
      curl_setopt($ch, CURLOPT_TIMEOUT, PROCESS_TIMEOUT);
      curl_setopt($ch, CURLINFO_HEADER_OUT, true);
      // Perform the request, and save content to $result
      ini_set("max_execution_time",EXEC_TIMEOUT);
      
      $result = curl_exec($ch);
        //curl error handling
        $curl_errno = curl_errno($ch);
                $curl_error = curl_error($ch);
                if ($curl_errno > 0) {
                    echo "CURL Error ($curl_errno): $curl_error\n";
                    }
          $info = curl_getinfo($ch);
          $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

          curl_close($ch);
          
         $response=json_decode($result);
        
      

      print_r(number_format($response->bal));
  }


  public function balancesReport(){

  	    ini_set("max_execution_time", 0);

  	    $data['agentNo'] = $this->input->post('agentNo');
  	    $force = $this->input->post('dataSource');

	  	$balances = json_decode(file_get_contents("balances".date('Ymdh').".txt"));

	 	if(empty($balances) || !empty($data['agentNo']) || $force==1){

	 	   $balances = $this->reports_mdl->balancesReport($data['agentNo']);

	 	if(empty($data['agentNo'])){
	 	  file_put_contents("balances".date('Ymdh').".txt",json_encode($balances));
	 	  $dirname = "balances";
	 	  array_map('unlink', glob("$dirname/*.*"));
	 	  }
	 	}

     	$data['balances'] = $balances;
     	$data['module']=$this->module;
		$data['view']="float_report";
		$data['agentNo'] = "";

		echo Modules::run("templates/admin",$data);
  	  }
	
 public function exportExcelBalances($agentNo=""){


	  	$balances = json_decode(file_get_contents("balances".date('Ymdh').".txt"));

	 	if(empty($balances) || !empty($agentNo)){
	 	  $balances = $this->reports_mdl->balancesReport($agentNo);

	 	if(empty($agentNo)){
	 	  file_put_contents("balances".date('Ymdh').".txt",json_encode($balances));
	 	  $dirname = "balances";
	 	  array_map('unlink', glob("$dirname/*.*"));
	 	 }
	 	}

 	  $this->exportToExcel($balances,"agent_balances".date('ymd').".xls");

 }
    


}
