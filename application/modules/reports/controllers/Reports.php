<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MX_Controller {

	public function __construct()
        {
            parent::__construct();
            $this->load->model('reports_mdl');
            $this->module="reports";
            Modules::run("auth/isLegal");
        }

public function index(){
  $this->transactions();
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

public function mobileStatement($agentNo){
       $transactions = $this->reports_mdl->getMobileStatement($agentNo,0,0);
    	return $transactions;
    }
   public function searchTran($key){
      $result = $this->reports_mdl->searchTran($key);
     return $result;
   }

public function floatLoans($params="")
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

        $config["base_url"] = base_url() . "reports/floatLoans/";
        $config["total_rows"] = $this->reports_mdl->countFloatLoans($searchData);
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
		$data['view']="float_loans";
		$data['transactions']=$this->reports_mdl->getFloatLoans($config["per_page"],$page,$searchData);
		$data['page']="Float Loans";
		

		echo Modules::run("templates/admin",$data);
	}
	
	
	public function statement($agentNo="")
	    {
	      
	      
	     if(isset($_POST['agentno']))  
	      $agentNo=$this->input->post('agentno');

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
		$data['transactions']=$this->reports_mdl->getStatement($agentNo,$config["per_page"],$page);
		$data['agent']=Modules::run('agents/getByAgentNo',$agentNo);
		
		if(empty($agentNo))
		$data['transactions']=array();
		
		$data['page']="Agent Statement";
		$data['agentNo']=$agentNo;

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

    public function showPending(){
	    
	    $transactions=$this->reports_mdl->getPendingTransactions();
	    
	    print_r($transactions);
	}
	
	public function  getPdfStatement($agentNo){

		$data['heading']=$agentNo." Agent Statement";
	    $data['transactions']=$this->reports_mdl->getStatement($agentNo,0,0);
	    $data['agent']=Modules::run('agents/getByAgentNo',$agentNo);
		$html=$this->load->view("statement_pdf",$data,true);

		$filename=$agentNo.time().".pdf";
		$this->makeLpdf($html,$filename,"I");
	}

	public function exportExcelStatement($agentNo){

		$transactions=$this->reports_mdl->getStatement($agentNo,0,0);

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
	
    public function getTotalEarned(){
	    
	    $fees=$this->reports_mdl->getOurCommission();
        $f= number_format((float)$fees, 2, '.', '');
	    
	     echo number_format($f,2);
	    
	}
	
	public function calcComms($paymentCode){
	    
	    $trans=$this->reports_mdl->getCommTransactions(1000,0,$paymentCode);
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
	    
	    return true;
	    
	}

public function sortFees(){
  $codes = ['50410',
'50411',
'50412',
'50413',
'50414',
'50415',
'50416',
'50417',
'50418',
'50419',
'5042',
'5043',
'5044',
'5045',
'5046',
'5047',
'5048',
'5049'];
$count=0;
foreach($codes as $code):
$count+=1;
 $this->calcComms($code);
endforeach;

echo $count;

}
	
		
    public function test($amount,$paymentCode){
        
        echo $this->getFees($amount,$paymentCode);
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


}
