<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Billers extends MX_Controller {

	public function __construct()
        {
            parent::__construct();
            $this->load->model('billers_mdl');
            $this->module="billers";
            //Modules::run("auth/isLegal");
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

   public function refresh(){
		 Modules::run("cache/cleardata");
    	 $msg = "Cache cleared succesfully";
         Modules::run("templates/setFlash",$msg);
         redirect('billers/list');
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
        $config['use_page_numbers'] = FALSE;
        //END CUSTOM LINKS
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        //$pg=$_GET['page'];
		//($pg)? $pg:0; //
        $page = (isset($_GET['page']))? $_GET['page']:0; //($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

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

			echo "Biller successfully Updated";

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
    
    foreach($items as $item):
     $this->db->where('paymentCode',$item->paymentCode);
     $qry = $this->db->get('billeritems');
     $res = $qry->row();
    
     $data = array('itemAmount'=>$item->amount/100,
                  'itemName'=>str_replace("-&#41;","#",str_replace("&#47;",")",str_replace("&#40;","(",$item->paymentitemname))),
                  'itemCode'=>$item->code
                 );
    if(count($res)>0):
      $this->db->where('paymentCode',$item->paymentCode);
      $this->db->update('billeritems',$data);
    else:
      $data['billerId'] = $billerId;
      $data['itemId'] = $item->paymentitemid;
      $data['providerObject'] = json_encode($item);
      $this->db->insert('billeritems',$data);
    endif;
    
    endforeach;
   
	}

	public function saveItems($items){

		foreach ($items as $item) {

		    if($item->categoryid==4 && strpos($item->paymentitemname,"airtime")<1){
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
    
    	$billers = Modules::run('cache/getData',"BILLERS_".$catgeoryId);
    
    	if(is_array($billers)){
        	return $billers;
        }
		$billers=$this->billers_mdl->getCategoryBillers($catgeoryId);
         Modules::run('cache/setData',"BILLERS_".$catgeoryId,$billers);
    
		return $billers;
	}

	public function getBillItems($billerId){
		
        $items = Modules::run('cache/getData',"ITEMS_".$billerId);
    
    	if(is_array($items)){
        	return $items;
        }
    
		$items=$this->billers_mdl->getBillerItems($billerId);
        Modules::run('cache/setData',"ITEMS_".$billerId,$items);
    
		return $items;
	}

	public function getItemName($paymentCode){
		
	    $item=$this->billers_mdl->getItem($paymentCode);

	    return $item->itemName;
	}

	public function createBiller(){

	    $data['module']=$this->module;
	    $data['categories']=$this->billers_mdl->getCategories();
	     $data['page']='Add New Biller';
	    $data['view']='add_biller';

	    echo Modules::run('templates/admin',$data);
	}

  public function saveItemEdit($id){
		$item = $this->input->post(); 
        $updated = $this->billers_mdl->saveItemEdit($id,$item);
  		$msg ="Error occured";
        if($updated):
         $msg ="Item Updated Successfully";
  	    endif;
        Modules::run("templates/setFlash",$msg);
        redirect('billers/showBiller/'.$item['billerId']);
  }

	public function editItem($id){

			$data['module']  = $this->module;
			$data['billers'] = $this->billers_mdl->getAllBillers();
    		$data['item'] = $this->billers_mdl->getItemById($id);
			$data['page'] = 'Edit Item';
			$data['view'] = 'add_item';

			echo Modules::run('templates/admin',$data);
	}

	public function showBiller($id){

	    $data['module']=$this->module;
	    $data['categories']=$this->billers_mdl->getCategories();
	    $data['biller']=$this->billers_mdl->getBillerById($id);
	    $data['items']=$this->billers_mdl->getBillerItems($id);
	    $data['page']='Edit Biller';
	    $data['view']='edit_biller';

	    echo Modules::run('templates/admin',$data);
	}

	public function listBillers(){


	    $this->load->library('pagination');
	    $config = array();

        $config["base_url"] = base_url() . "billers/listBillers/";
        $config["total_rows"] = 75;
        $config["per_page"] = 20;
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
	    $data['billers']=$this->billers_mdl->getAllBillers($config["per_page"],$page);
	    $data['page']='Biller List';
	    $data['view']='billers';

	    echo Modules::run('templates/admin',$data);
	}


  public function	saveBillerEdits($billerId){

      $postData =$this->input->post();

	     if(!empty($_FILES['picture']['tmp_name'])){

      $config['upload_path']   = './assets/img/billers/';
      $config['allowed_types'] = 'gif|jpg|png';
      $config['max_size']      = 15000;
      $config['file_name']      = str_replace(' ', '_', $billerId.time().mt_rand());

      $this->load->library('upload', $config);

	if ( ! $this->upload->do_upload('picture')) {
         $error = $this->upload->display_errors();
         echo strip_tags($error);
      }
      else{

         $data = $this->upload->data();
         $photofile =$data['file_name'];
         $postData['picture']=$photofile;
      }

     }

     $update=$this->billers_mdl->saveBillerUpdate($billerId,$postData);

     	if($update){
			 Modules::run('cache/cleardata');
			$msg= '<i class="icon icon-check  text-main s-18"></i> Biller successfully updated';

		}

		else{

			$msg= "Operation failed, please try again";

		}

            Modules::run("templates/setFlash",$msg);

        redirect('billers/showBiller/'.$billerId);
	}


	public function storeBiller(){

		$postdata=$this->input->post();

		$billerId=mt_rand(20201,date('mdh'));
		$billerData=array(
			'billerId'=>$billerId,
			'billerName'=>$postdata['billerName'],
			'categoryId'=>$postdata['categoryId'],
			'provider'=>$postdata['provider'],
			'categoryName'=>''
		);

		$save=$this->billers_mdl->saveBiller($billerData);


		if($save){

		$msg= '<i class="icon icon-check  text-main s-18"></i> Biller successfully created';

		}

		else{

		$msg= "Operation failed, please try again";

		}
		Modules::run("templates/setFlash",$msg);

		redirect('billers/createBiller');

	}

	public function saveItem(){
    
    //Aids Manual charge configs

		$postData=$this->input->post();

		$providerObject=array(
		"categoryid"=>"",
		"billerid"=>$postData['billerId'],
		"paymentitemname"=>$postData['itemName'],
		"amount"=>$postData['amount'],
		"isAmountFixed"=>($postData['amount']>0)?1:0
		);


		$itemData=array(
			"billerId"=>$postData['billerId'],
			"itemName"=>$postData['itemName'],
			"itemAmount"=>$postData['amount'],
			"itemCode"=>$postData['itemCode'],
			"providerObject"=>json_encode($providerObject)
		);

		$save = $this->billers_mdl->saveItems($itemData);

		if($save){

		$msg= '<i class="icon icon-check  text-main s-18"></i> Item successfully created';

		}

		else{

		$msg= "Operation failed, please try again";

		}
		Modules::run("templates/setFlash",$msg);

		redirect('billers/createItem');

	}

	public function fireFees(){
    
    	$copy_code='90217565';
    
        $this->db->where('paymentcode',$copy_code);
        $qry= $this->db->get('fees');
    
    	$copy= $qry->result();
    
        $data=array();
        

$codes = [	90217552
,90217553
,90217554
,90217555
,90217556
,90217557
,90217558
,90217559
,90217560
,90217561
,90217562
,90217563
,90217564
,90217566
];

		foreach($codes as $code):

          foreach($copy as $copy_row):
				$row = array("paymentcode"=>$code,
                      "lowerlimit" =>$copy_row->lowerlimit,
                      "upperlimit"=>$copy_row->upperlimit ,
                      "ourCharge"=>$copy_row->ourCharge,
                      "isPercentage"=>$copy_row->isPercentage,
                      "billerCharge"=>$copy_row->billerCharge,
                      "toShare"=>$copy_row->toShare,
                      "agent_share"=>$copy_row->agent_share,
                      'toShare_is_percentage'=>$copy_row->toShare_is_percentage,
                      "us"=>$copy_row->us );
                array_push($data,$row);
           endforeach;
		endforeach;
   
       $this->db->insert_batch('fees',$data);
       $this->fireCharges($copy_code, $codes);//insert charges

	}

public function doCharges(){
$copy_code=215649;
$codes = [21517503];

	$this->fireCharges($copy_code, $codes);
}

public function fireCharges($copy,$paycodes){
    
    	$copy_code= $copy;
    
        $this->db->where('paymentcode',$copy_code);
        $qry  = $this->db->get('charges');
    
    	$copy = $qry->result();
    
        $data = array();
        
		$codes = $paycodes;

		foreach($codes as $code):

          foreach($copy as $copy_row):
				$row = array("paymentcode"=>$code,
                      "lowerlimit" =>$copy_row->lowerlimit,
                      "upperlimit"=>$copy_row->upperlimit ,
                      "ourCharge"=>$copy_row->ourCharge,
                      "isPercentage"=>$copy_row->isPercentage,
                      "billerCharge"=>$copy_row->billerCharge,
                      "isinclusive"=>$copy_row->isinclusive,
                      "us"=>$copy_row->us );
    
                array_push($data,$row);
    
           endforeach;
		endforeach;
    
       $this->db->insert_batch('charges',$data);
	}

public function throwItems($billerId){
		$items=$this->billers_mdl->getBillerItems($billerId);
		print_r($items);
	}

function updateEzeeTv(){

///aids a manual catalog update

    $items = json_decode(file_get_contents('dstv.json'));

     //print_r($items[0] );

        $billerId =504;
 		$i=1;
     foreach($items as $item):
     $i++;
     $this->db->where('paymentCode',$billerId.$i);
     $qry = $this->db->get('billeritems');
     $res = $qry->row();

     $data = array('itemAmount'=>$item->productprice,
                  'itemName'=>str_replace("-&#41;","#",str_replace("&#47;",")",str_replace("&#40;","(",$item->productdesc))),
                  'itemCode'=>$item->productcode,
                  'paymentCode'=>$billerId.$i
                 );
    if(count($res)>0):
      $this->db->where('paymentCode',$billerId.$i);
      $this->db->update('billeritems',$data);
    else:
      $data['billerId'] = $billerId;
      $data['itemId']   = $billerId.$i.mt_rand(0,100);
      $data['providerObject'] = json_encode($item);
      $this->db->insert('billeritems',$data);
    endif;
    endforeach;
}




}
