<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Billers extends MX_Controller {

	public function __construct()
        {
            parent::__construct();
            $this->load->model('billers_mdl');
            $this->module="billers";
            Modules::run("auth/isLegal");
            include('scripts/InterswitchAuth.php');
        }

	
	public function getAll()
	{
	   $billers=$this->billers_mdl->getAll();
		return $billers;
	}

	public function countBillers(){
		return $this->billers_mdl->count();
	}

  public function add()
	    {
		$data['module']=$this->module;
		$data['view']="add_biller";
		$data['page']="Add Biller";

		echo Modules::run("templates/admin",$data);
	}

	public function saveBiller($postData)
	 {

		$res=$this->billers_mdl->saveBiller($postData);

		return $res;
	}
	
	public function blockBiller($billerId)
	 {

		$res=$this->billers_mdl->blockBiller($billerId);

        redirect('settings/configure');
	}
	
	public function unblockBiller($billerId)
	 {

		$res=$this->billers_mdl->unblockBiller($billerId);

        redirect('settings/configure');
	}


	public function list()
	    {

	    $this->load->library('pagination');
	    $config = array();

        $config["base_url"] = base_url() . "billers/list/";
        $config["total_rows"] = $this->billers_mdl->count();
        $config["per_page"] = 10;
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
		$data['view']="billers";
		$data['billers']=$this->billers_mdl->getAll($config["per_page"],$page);
		$data['page']="Manage Billers";

		echo Modules::run("templates/admin",$data);
	}


    
	public function addBiller()
	    {
		$data['module']=$this->module;
		$data['view']="add_team";
		$data['page']="Manage Billers";

		echo Modules::run("templates/admin",$data);
	}


	public function updateBiller()
	    {
		
		$postData=$this->input->post();

		$res=$this->billers_mdl->updateBiller($postData);

		if($res=='ok'){

			echo "Category successfully Updated";

		}

		else{

			echo "Operation failed, please try again";

		}
	}
	
	

	public function deleteBiller($billerId)
	    {
	    	$res=$this->billers_mdl->deleteBiller($team_id);

		if($res=='ok'){

			echo "Deletion Complete";

		}

		else{

			echo "Operation failed, please try again";

		}
	}


	public function fetchBillers($categoryId){

		$httpMethod = "GET";
		$resourceUrl = QT_BASE_URL."billers";
		$clientId = CLIENT_ID;
		$clientSecretKey = CLIENT_SECRET;
		$signatureMethod = SIGNATURE_REQ_METHOD;

         $interswitchAuth=new InterswitchAuth();

		$AuthData=$interswitchAuth->generateInterswitchAuth($httpMethod, $resourceUrl, $clientId,$clientSecretKey, "", $signatureMethod);

//request headers
           $request_headers = array();

			$request_headers[] = "Authorization:".$AuthData['AUTHORIZATION'];
			$request_headers[] = "Timestamp:".$AuthData['TIMESTAMP'];
			$request_headers[] = "Nonce:".$AuthData['NONCE'];
			$request_headers[] = "Signature:".$AuthData['SIGNATURE'];
			$request_headers[] = "SignatureMethod:".$AuthData['SIGNATURE_METHOD'];
			$request_headers[] = 'Content-Type: application/json';
					    // Initialize cURL session
			$ch = curl_init($resourceUrl);
			// Option to Return the Result, rather than just true/false
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// Set Request Headers 
			curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
			//time to while waiting for connection...indefinite
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
			//set curl time..processing time out
			curl_setopt($ch, CURLOPT_TIMEOUT, 400);
			// Perform the request, and save content to $result
			$result = curl_exec($ch);
				//curl error handling
				$curl_errno = curl_errno($ch);
                $curl_error = curl_error($ch);

                if ($curl_errno > 0) {

                echo "CURL Error ($curl_errno): $curl_error\n";
                    }

			   curl_close($ch);
			   // Close the cURL resource, and free up system resources!

              $billers=json_decode($result)->billers;
               
              // print_r($result);
              
              $unallowedBillers=array(
                  '190','203','151','101','217','211','220','226','233','240','243','247','248','251','254','276','284','325','326','329','331',
                  '377','378','395','396','297','298','100','239','207','237','206','249','214','208'
              );
               
              
            foreach ($billers as $biller){

                    if($biller->categoryid==$categoryId && !in_array($biller->billerid, $unallowedBillers)){
        	               	$billerData=array(
        	               		'billerId'=>$biller->billerid,
        	               		'billerName'=>($biller->billerid!=='238')? $biller->billername :'AIRTIME PURCHASE',
        	               		'categoryId'=>$biller->categoryid,
        	               		'categoryName'=>$biller->categoryname
        	               	);
        
                       	 $this->saveBiller($billerData);
                    }
               }
			

	}

	public function updateBillerItems(){

		$billers=$this->billers_mdl->getAll();
		
		ini_set('max_execution_time', 0);

		foreach ($billers as $biller) {
			//$this->billers_mdl->trashBillerItems($biller->billerId);
			$this->getBillerItems($biller->billerId);
		}
		
	}

	public function getBillerItems($billerId){

		$httpMethod = "GET";
		$resourceUrl = QT_BASE_URL."billers/".$billerId."/paymentitems";
		$clientId = CLIENT_ID;
		$clientSecretKey = CLIENT_SECRET;
		$signatureMethod = SIGNATURE_REQ_METHOD;

         $interswitchAuth=new InterswitchAuth();
		$AuthData=$interswitchAuth->generateInterswitchAuth($httpMethod, $resourceUrl, $clientId,$clientSecretKey, "", $signatureMethod);
//request headers
           $request_headers = array();

			$request_headers[] = "Authorization:".$AuthData['AUTHORIZATION'];
			$request_headers[] = "Timestamp:".$AuthData['TIMESTAMP'];
			$request_headers[] = "Nonce:".$AuthData['NONCE'];
			$request_headers[] = "Signature:".$AuthData['SIGNATURE'];
			$request_headers[] = "SignatureMethod:".$AuthData['SIGNATURE_METHOD'];
			$request_headers[] = 'Content-Type: application/json';
					    // Initialize cURL session
			$ch = curl_init($resourceUrl);
			// Option to Return the Result, rather than just true/false
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// Set Request Headers 
			curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
			//time to while waiting for connection...indefinite
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
			//set curl time..processing time out
			curl_setopt($ch, CURLOPT_TIMEOUT, 400);
			// Perform the request, and save content to $result
			$result = curl_exec($ch);
				//curl error handling
				$curl_errno = curl_errno($ch);
                $curl_error = curl_error($ch);

                if ($curl_errno > 0) {

                      echo "CURL Error ($curl_errno): $curl_error\n";
                    }

			   curl_close($ch);
			   // Close the cURL resource, and free up system resources!

              $items=json_decode($result)->paymentitems;
                
              if(!empty($items))
               $this->saveItems($items);
               
	}

	public function saveItems($items){

		foreach ($items as $item) {
		    
		    if($item->categoryid==4 && strpos($item->paymentitemname,"irtime")<1){
		        //do nothing
		    }
		    else{
		        
          
			$itemData=array(
				"billerId"=>$item->billerid,
				"itemId"=>$item->paymentitemid,
				"itemName"=>str_replace("_"," ",html_entity_decode($item->paymentitemname)),
				"itemAmount"=>$item->amount/100,
				"paymentCode"=>$item->paymentCode,
				"itemCode"=>$item->code,
				"isFixedAmount"=>$item->isAmountFixed,
				"providerObject"=>json_encode($item)

			);

			$this->billers_mdl->saveBillerItem($itemData);
			
		    }
		}

		
	}
	
    public function updateCategoryBillers(){

		$categories=$this->billers_mdl->getCategories();
		
		foreach ($categories as $category) {
			$this->billers_mdl->trashBillers($category->providerId);
			$this->fetchBillers($category->providerId);
		}
		
	}

	public function getCategoryBillers($catgeoryId){

		$billers=$this->billers_mdl->getCategoryBillers($catgeoryId);

		return $billers;
	}

	public function getBillItems($billerId){

		$items=$this->billers_mdl->getBillerItems($billerId);
		return $items;
	}
	
	public function getItemName($paymentCode){
	    
	    $item=$this->billers_mdl->getItem($paymentCode);
	    
	    return $item->itemName;
	}




}
