<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agents extends MX_Controller {

	public function __construct()
        {
            parent::__construct();
            $this->load->model('agents_mdl');
            $this->module="agents";
            Modules::run("auth/isLegal");
        }

     public function agentLogin($username,$password)
	{
		
		$agent=$this->agents_mdl->getAgentLogin($username,$password);
		return $agent;
		
	}
	
	 public function profileUpdate($request)
	{
		
		//$update=$this->agents_mdl->getAgentLogin($request);
		return $request;
		
	}
	
	public function setTranPin($request)
	{
		
		$setpin=$this->agents_mdl->setAgentTranPin($request);
		
		if($setpin)
		return 'SUCCESS';
		
	}
	
	public function setPassword($request)
	{
		
		$setpassword=$this->agents_mdl->setAgentPassword($request);
		
		return $setpassword;
		
	}
	
	public function getAll($limit=null,$end=null)
	{
		
		$agents=$this->agents_mdl->getAll($limit,$end);
		return $agents;
		
	}
		public function getAgent($agentNo)
	{
		
		$agent=$this->agents_mdl->getByAgentNo($agentNo);

		return $agent;
		
	}

	public function countAgents(){

		return $this->agents_mdl->count();
	}
	
	
	public function getByUserId($uid){
	    
	    return $this->agents_mdl->getByUserId($uid);
	}

  public function add()
	    {
		$data['module']=$this->module;
		$data['view']="add_agent";
		$data['page']="Add Agent";
		$data['agentNo']=$this->agents_mdl->genAgentNo();
		echo Modules::run("templates/admin",$data);
	}
	
	public function edit($agentNo)
	 {
		$data['module']=$this->module;
		$data['view']="edit_agent";
		$data['page']="Edit Agent";
		$data['agent']=$this->getAgent($agentNo);
		echo Modules::run("templates/admin",$data);
	}
	
	public function merchantProfile($agentNo)
	 {
	        $agentNo=rawurldecode($agentNo);
		$data['module']=$this->module;
		$data['view']="agentprofile";
		$data['page']="My Profile";
		$data['agent']=$this->getAgent($agentNo);
		echo Modules::run("templates/user",$data);
	}
	
		public function createLogin($agentNo)
	 {
		$data['module']=$this->module;
		$data['view']="agent_login";
		$data['page']="Create Agent Login";
		$agent=$this->getAgent($agentNo);
		$data['user'] = (!empty($agent->userId))?$this->agents_mdl->getAgentUser($agent->userId): [];
		$data['agent']=$agent;
		echo Modules::run("templates/admin",$data);
	}

    public function saveAgentLogin($agentNo){
        
        $userdata=$this->input->post();
        
        $username=$userdata['username'];
        
        $user=$this->getAgentUser($username);
        
        if(!empty($user))
        {
            
            $msg="<font color='red'>This username <b>".$username."</b> already exists <b>For ".$user->names." - ".$user->agentNo."</b>";
            
            if($user->agentNo == $agentNo) //user already has
             $msg.=" ,try resetting the password";
             
             if($user->agentNo !== $agentNo) //not for current user
             $msg.=" ,try another username";
            
            Modules::run("templates/setFlash",$msg."</font> ");
            redirect('agents/createLogin/'.$agentNo);
        }
        
        $pass=mt_rand(1000,9999);
        $userdata['password']=md5($pass);
        $userdata['userType']='2';
        $userdata['status']='1';
        $userdata['tranPin']=$pass;
        $this->db->insert('users',$userdata);
        
        $newuser=$this->getUser($username);
        
        if(!empty($newuser))
        {
             
             $agent=$this->getAgent($agentNo);
             
              $message="Hello ".strtoupper($agent->names)." welcome to ELLY PAY. Your username is ".$username." and password is ".$pass. " and Transaction PIN is ".$pass." You can change later";
              
             // $this->sendSms($agent->phoneNumber,$message); 
              
              $this->db->where('agentNo',$agentNo);
              $this->db->update('agents',array('userId'=>$newuser->user_id));
              
            Modules::run("templates/setFlash",$message);
             redirect('agents/list');
        }
        
        else{
            
            $message="Operation failed, try again";
            
            Modules::run("templates/setFlash",$message);
             redirect('agents/list');
        }
        
    }

    public function resetAgent($userId,$agentNo,$isPin=""){
        
       
        $pass=mt_rand(1000,9999);
        
        if(empty($isPin))
         $userdata['password']=md5($pass);

        $userdata['status']='1';

        if(!empty($isPin))
        $userdata['tranPin']=$pass;

    	$this->db->where('user_id',$userId);
        $saved = $this->db->update('users',$userdata);
        
        if(!empty($saved))
        {
        		$phrase ="transaction pin ";
        		if(empty($isPin))
        			 $phrase ="password";
             
              $message="Successful, the new  ".$phrase." is <b>".$pass."</b> agent should login afresh";

            Modules::run("templates/setFlash",$message);
             redirect('agents/createLogin/'.$agentNo);
        }
        
        else{
            
            $message="Operation failed, try again";
            Modules::run("templates/setFlash",$message);
             redirect('agents/list');
        }
        
    }
    
    function getAgentUser($username){
        
        $this->db->join('agents','agents.userId=users.user_id');
        $this->db->where('username',$username);
       $qry= $this->db->get('users');
        
        return $qry->row();
        
    }
    
    function getUser($username){
        
        $this->db->where('username',$username);
       $qry= $this->db->get('users');
        
        return $qry->row();
        
    }


	public function list()
	    {
	        
	   $searchData=array();
         
         if($this->input->post()!==null){
             $searchData=$this->input->post();
         }
         
         
        $data['search']=$searchData;
        

	    $this->load->library('pagination');
	    $config = array();

        $config["base_url"] = base_url() . "agents/list/";
        $config["total_rows"] = $this->agents_mdl->count($searchData);
        $config["per_page"] = 15;
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
		$data['view']="agents";
		$data['agents']=$this->agents_mdl->getAll($config["per_page"],$page,$searchData);
		$data['page']="Manage Agents";
		
		echo Modules::run("templates/admin",$data);
	}


    	public function commissionlist()
	    {
	        
	   $searchData=array();
         if($this->input->post()!==null){
             $searchData=$this->input->post();
         }
        $data['search']=$searchData;
        
        $this->load->model('reports/reports_mdl');
        
		$data['module']=$this->module;
		$data['view']="pay_commission";
		$data['comagents']=null;
		$data['page']=" Agent Payments- Commission";
		
	 if(!empty($this->input->post())){
		    
		  if(!empty($this->input->post('pay'))){
		  $comagents = $this->reports_mdl->getCommissionList($searchData);
		  
		   echo  Modules::run("payments/payCommission", $comagents,$searchData);
		    
		    return;
		  }
		  
		  else{
		      
		  $data['comagents'] = $this->reports_mdl->getCommissionList($searchData);
		  }
		}
		  echo Modules::run("templates/admin",$data);
	
	}

    
    
    public function payComms()
	    {
        
        $searchData=$this->input->post();
        $this->load->model('reports/reports_mdl');
		$comagents = $this->reports_mdl->getCommissionList($searchData);
		
		echo Modules::run("payment/payCommission", $comagents,$searchData);
	}


	public function saveAgent()
	    {
		
		$postData=$this->input->post();

  if(!empty($_FILES['kyc']['tmp_name']) || !empty($_FILES['photo']['tmp_name'])){

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


     if(!empty($_FILES['kyc']['tmp_name'])){

      $config['upload_path']   = './uploads/kyc/'; 
      $config['allowed_types'] = 'gif|jpg|png|pdf'; 
      $config['max_size']      = 15000;
      $config['file_name']      = $postData['phoneNumber'].time().mt_rand();
     
      $this->load->library('upload', $config);

	if ( ! $this->upload->do_upload('kyc')) {
         $error = $this->upload->display_errors(); 
         echo strip_tags($error);
      }
      else{ 

         $data1 = $this->upload->data();
         $kycfile =$data1['file_name'];
         $postData['kyc_attached']=$kycfile;
      } 

     }

     $res=$this->agents_mdl->saveAgent($postData);

    } //if uploads
  else{
  		//no files at all
		$res=$this->agents_mdl->saveAgent($postData);

	  }

		if($res=='ok'){

			$msg= "Agent successfully Added";

		}

		else{

			$msg= "Operation failed, please try again";

		}

            Modules::run("templates/setFlash",$msg);
			redirect('agents/list');
	}
	
	
	public function saveAgentEdit($agentNo,$flag=0){
	   
	  $postData=$this->input->post();

  if(!empty($_FILES['kyc']['tmp_name']) || !empty($_FILES['photo']['tmp_name'])){

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


     if(!empty($_FILES['kyc']['tmp_name'])){

      $config['upload_path']   = './uploads/kyc/'; 
      $config['allowed_types'] = 'gif|jpg|png|pdf'; 
      $config['max_size']      = 15000;
      $config['file_name']      = $postData['phoneNumber'].time().mt_rand();
     
      $this->load->library('upload', $config);

	if ( ! $this->upload->do_upload('kyc')) {
         $error = $this->upload->display_errors(); 
         echo strip_tags($error);
      }
      else{ 

         $data1 = $this->upload->data();
         $kycfile =$data1['file_name'];
         $postData['kyc_attached']=$kycfile;
      } 

     }

     $res=$this->agents_mdl->updateAgent($agentNo,$postData);

    } //if uploads
  else{
  		//no files at all
		$res=$this->agents_mdl->updateAgent($agentNo,$postData);

	  }

		if($res=='ok'){

			$msg= "Agent Updated successfully ";
			
			if($flag==1)
			 $msg= "Profile Updated successfully ";

		}

		else{

			$msg= "Operation failed, please try again";

		}

        
        Modules::run("templates/setFlash",$msg);
        
        if($flag==1){
		  redirect('user/merchant');
        }else{
		  redirect('agents/list');
        }
	    
	    
	}

    public function getByAgentNo($agentNo)
	{
		$agent=$this->agents_mdl->getByAgentNo($agentNo);
		return $agent;
		
	}
	
	 public function checkAgent($agentNo)
	{
		$agent=$this->getByAgentNo($agentNo);
		echo json_encode($agent);
	}

	public function addAgent()
	    {
		$data['module']=$this->module;
		$data['view']="add_team";
		$data['page']="Manage Agents";

		echo Modules::run("templates/admin",$data);
	}
	
	public function paidCommission()
	 {
		$data['module']=$this->module;
		$data['view']="paid_commission";
		$data['page']="Paid Commissions";
		$data['commissions']=$this->agents_mdl->getPaidCommissions();
		echo Modules::run("templates/admin",$data);
	}
	
	public function recordReceived(){
	    
	   $data= $this->input->post();
	   $save=$this->agents_mdl->saveReceivedCommission($data);
	   $msg="Operation Successful";
	   Modules::run("templates/setFlash",$msg);
	   redirect('agents/paidCommission');
	}


	public function updateAgent()
	    {
		
		$postData=$this->input->post();

		$res=$this->agents_mdl->updateAgent($postData);

		if($res=='ok'){

			echo "Agent successfully Updated";

		}

		else{

			echo "Operation failed, please try again";

		}
	}
	


	public function getAge($birthDate){

     $birthDate = explode("/", $birthDate);
     
     $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[2], $birthDate[1], $birthDate[0]))) > date("md")
    ? ((date("Y") - $birthDate[0]) - 1)
    : (date("Y") - $birthDate[0]));

     return $age;
	}
	
	public function getAgentBalance($agentNo){

		$balance = $this->agents_mdl->getBalance($agentNo);
		return number_format((float)$balance, 1, '.', '');

	}
	
	public function getAgentCommission($agentNo){

		$commission= $this->agents_mdl->getCommission($agentNo);

		return number_format((float)$commission, 1, '.', '');
	}
	
	public function redeemCommission($agentNo,$amount){
	    
	    $comms=getAgentCommission($agentNo);
	    
	    if($comms<100)
	      return 'failed';
	      $redeem= Modules::run("payment/redeemCommission",$agentNo,$amount);
	      

	      if ($redeem) {
	      	return "SUCCESSFUL";
	      }
	      return "FAILED";
	    
	}
	
	public function getAgentTotalBalance(){

		$balance = $this->agents_mdl->getTotalBalance();

		return $balance;

	}
	
	public function restBalance($agentNo){

		$balance = $this->getAgentBalance($agentNo);
		$commission =$this->getAgentCommission($agentNo);

		$res =array(
				"balance" => number_format($balance),
				"commission" => number_format($commission)
		);
		echo json_encode($res);

	}
	
	
	public function getAgentHistory($agentNo){

		$history = $this->agents_mdl->getAgentHistory($agentNo);
		
		$data=array();
		
		for($i=0;$i<count($history); $i++){
		    if(strpos($history[$i]->requestRef,"COMS") !==false)
                $history[$i]->itemName ="REDEEMED COMMISSION";
		    $history[$i]->commission=$history[$i]->agent_fee;
		}

		return $history;

	}
	
	
	function getItemName($paymentCode){
	    $this->db->select('itemName');
	    $this->db->where('paymentCode',$paymentCode);
	    
	    $qry=$this->db->get('billeritems');
	    
	    if($paymentCode=='SHARE')
	        return "FLOAT SHARE";
	   if($paymentCode=='COMMS')
	        return "PAID COMMISSION";
	        
	    return $qry->row()->itemName;
	}
	
	public function sendSms($contact="",$message=""){
	    
    //$message=urlencode($message);
        /*
     $response=file_get_contents("https://www.easysendsms.com/sms/bulksms-api/bulksms-api?username=henrhenr2020&password=esm37008&from=0704878224&to=".$contact."&text=".$message."&type=0");
     echo $response;*/
     
     $apiKey = urlencode('xdjoEAftX4Y-ORWrrtXJ4eQNV837Szk1xtAO9UuDR5');
	
	// Message details
	$numbers = array('+256705596470');
	$sender = urlencode('ELLY PAY');
	$message = rawurlencode('This is your message');
 
	$numbers = implode(',', $numbers);
 
	// Prepare data for POST request
	$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
 
	// Send the POST request with cURL
	$ch = curl_init('https://api.txtlocal.com/send/');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	curl_close($ch);
	
	// Process your response here
	echo $response;
}


public function getText(){

print_r($this->agents_mdl->getCommission("54000"));

}


}
