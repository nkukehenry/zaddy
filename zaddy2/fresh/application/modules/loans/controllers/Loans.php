<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loans extends MX_Controller {

	public function __construct()
        {
            parent::__construct();
            $this->load->model('loans_model','loans_mdl');
            $this->module="loans";
            Modules::run("auth/isLegal");
        }

	
	public function getAll()
	{
		
		$customers=$this->loans_mdl->getAll();
		return $customers;
		
	}
		public function getCustomer($id)
	{
		
		$customer=$this->loans_mdl->getCustomer($id);

		return $customer;
		
	}

	public function countCustomers(){

		return $this->loans_mdl->count();
	}
	

    public function loanOut()
	    {
		$data['module']=$this->module;
		$data['view']="new_loan";
		$data['page']="New Loan";
		$data['customers']=$this->loans_mdl->getAll();
		echo Modules::run("templates/admin",$data);
	}
	
	 public function loanPayment()
	    {
		$data['module']=$this->module;
		$data['view']="repay";
		$data['page']="New Payment";
		$data['customers']=$this->loans_mdl->getAll();
		echo Modules::run("templates/admin",$data);
	}
	
	public function saveLoan(){
	    
	    $postdata=$this->input->post();
	    $save=$this->loans_mdl->saveLoan($postdata);
	    
	    $msg="Opreation Failed";
	    if($save){
	        $msg="Loan recorded successfully";
	    }
	    Modules::run("templates/setFlash",$msg);
	    redirect('loans/customers');
	}
	
	
	public function repay($id=null){
	    
	       $postdata=$this->input->post();
	  
	     $pay=$this->loans_mdl->repayLoan($postdata);
	    
	    $msg="Opreation Failed";
	    
	    if($pay){
	        $msg="Repayment recorded successfully";
	    }
	     Modules::run("templates/setFlash",$msg);
	    redirect('loans/customers');
	}
  
    public function getPaidAmount($loanid){
        $paid=$this->loans_mdl->getPaidAmount($loanid);
        
        return $paid;
    }
    
    public function getBorrowedAmount($id){
        $borrowed=$this->loans_mdl->getCustomerLoanAmount($id);
        
        return $borrowed;
    }
    
  
	public function addCustomer()
	    {
		$data['module']=$this->module;
		$data['view']="add_customer";
		$data['page']="Add  Customer";
		echo Modules::run("templates/admin",$data);
	}

	public function customerEdit($id)
	 {
		$data['module']=$this->module;
		$data['view']="edit_customer";
		$data['page']="Edit Customer";
		$data['customer']=$this->getByCustomerNo($id);
		echo Modules::run("templates/admin",$data);
	}

	public function customers()
	    {
	        
	   $searchData=array();
         
         if($this->input->post()!==null){
             $searchData=$this->input->post();
         }
         
         
        $data['search']=$searchData;
        

	    $this->load->library('pagination');
	    $config = array();

        $config["base_url"] = base_url() . "loans/customers/";
        $config["total_rows"] = $this->loans_mdl->count($searchData);
        $config["per_page"] = 50;
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
		$data['view']="customers";
		$data['customers']=$this->loans_mdl->getAll($config["per_page"],$page,$searchData);
		$data['page']="Loan Customers";
		
		echo Modules::run("templates/admin",$data);
	}

    	public function loanList()
	    {
	        
	   $searchData=array();
         
         if($this->input->post()!==null){
             $searchData=$this->input->post();
         }
         
         
        $data['search']=$searchData;
        

	    $this->load->library('pagination');
	    $config = array();

        $config["base_url"] = base_url() . "loans/loanList/";
        $config["total_rows"] = $this->loans_mdl->count($searchData);
        $config["per_page"] = 50;
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

        $page = (!empty($pg))? $pg:0; //($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        $data["links"] = $this->pagination->create_links();
		$data['module']=$this->module;
		$data['view']="loans";
		$data['loans']=$this->loans_mdl->getAllLoans($config["per_page"],$page,$searchData);
		$data['page']="Loans";
		
		echo Modules::run("templates/admin",$data);
	}


    	public function repayments()
	    {
	        
	   $searchData=array();
         
         if($this->input->post()!==null){
             $searchData=$this->input->post();
         }
         
         
        $data['search']=$searchData;
        

	    $this->load->library('pagination');
	    $config = array();

        $config["base_url"] = base_url() . "loans/repayments/";
        $config["total_rows"] = $this->loans_mdl->countRepayments($searchData);
        $config["per_page"] = 50;
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

        $page = (!empty($pg))? $pg:0; //($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        $data["links"] = $this->pagination->create_links();
		$data['module']=$this->module;
		$data['view']="repayments";
		$data['payments']=$this->loans_mdl->getAllRepayments($config["per_page"],$page,$searchData);
		$data['page']="Repayments";
		
		echo Modules::run("templates/admin",$data);
	}



	public function saveCustomer()
	    {
		
		$postData=$this->input->post();

  if(!empty($_FILES['photo']['tmp_name'])){

	 if(!empty($_FILES['photo']['tmp_name'])){

      $config['upload_path']   = './assets/img/people/'; 
      $config['allowed_types'] = 'gif|jpg|png'; 
      $config['max_size']      = 15000;
      $config['file_name']      = str_replace(' ', '_', $postData['phoneNumber'].time().mt_rand());
     
      $this->load->library('upload', $config);

	if ( ! $this->upload->do_upload('photo')) {
         $error = $this->upload->display_errors(); 
         echo strip_tags($error);
      }
      else{ 

         $data = $this->upload->data();
         $photofile =$data['file_name'];
         $postData['photo']=$photofile;
      } 

     }

     $res=$this->loans_mdl->saveCustomer($postData);

    } //if uploads
  else{
  		//no files at all
		$res=$this->loans_mdl->saveCustomer($postData);

	  }

		if($res=='ok'){

			$msg= "Customer successfully Added";

		}

		else{

			$msg= "Operation failed, please try again";

		}

             Modules::run("templates/setFlash",$msg);
			redirect('loans/customers');
	}
	
	
 public function saveCustomerEdit($id){
	   
	  $postData=$this->input->post();

  if( !empty($_FILES['photo']['tmp_name'])){

	 if(!empty($_FILES['photo']['tmp_name'])){

      $config['upload_path']   = './assets/img/people/'; 
      $config['allowed_types'] = 'gif|jpg|png'; 
      $config['max_size']      = 15000;
      $config['file_name']      = str_replace(' ', '_', $postData['phoneNumber'].time().mt_rand());
     
      $this->load->library('upload', $config);

	if ( ! $this->upload->do_upload('photo')) {
         $error = $this->upload->display_errors(); 
         echo strip_tags($error);
      }
      else{ 

         $data = $this->upload->data();
         $photofile =$data['file_name'];
         $postData['photo']=$photofile;
      } 

     }



     $res=$this->loans_mdl->updateCustomer($id,$postData);

    } //if uploads
  else{
  		//no files at all
		$res=$this->loans_mdl->updateCustomer($id,$postData);

	  }

		if($res=='ok'){

			$msg= "Customer Updated successfully ";

		}

		else{

			$msg= "Operation failed, please try again";

		}

         Modules::run("templates/setFlash",$msg);
		redirect('loans/customers');
	    
	    
	}

    public function getByCustomerNo($id)
	{
		$customer=$this->loans_mdl->getCustomer($id);
		return $customer;
		
	}
	
	public function getCustomerBalance($id=null)
	{
		$bal=$this->loans_mdl->getCustomerBalance($id);
		return $bal;
		
	}


	public function getAge($birthDate){

     $birthDate = explode("/", $birthDate);
     
     $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[2], $birthDate[1], $birthDate[0]))) > date("md")
    ? ((date("Y") - $birthDate[0]) - 1)
    : (date("Y") - $birthDate[0]));

     return $age;
	}


}
