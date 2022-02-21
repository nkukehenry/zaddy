<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agents extends MX_Controller {

	public function __construct(){
            parent::__construct();
            $this->load->model('agents_mdl');
            $this->module="agents";
           // Modules::run("auth/isLegal");
   }

  public function agentLogin($username,$password){
		$agent=$this->agents_mdl->getAgentLogin($username,$password);
		return $agent;
	}

  public function profileUpdate($request){
		//$update=$this->agents_mdl->getAgentLogin($request);
		return $request;
	}

  public function setTranPin($request){
		$setpin=$this->agents_mdl->setAgentTranPin($request);
		if($setpin)
		return 'SUCCESS';
	}

  public function setPassword($request){
		$setpassword=$this->agents_mdl->setAgentPassword($request);
		return $setpassword;
	}

  public function getAll(){
		$agents=$this->agents_mdl->getAll();
		return $agents;
	}
  public function getAgent($agentNo){
		$agent=$this->agents_mdl->getByAgentNo($agentNo);
		return $agent;
	}

  public function countAgents(){
		return $this->agents_mdl->count();
	}


  public function add(){
		$data['module']=$this->module;
		$data['view']="add_agent";
		$data['page']="Add Agent";
		$data['agentNo']=$this->agents_mdl->genAgentNo();
		echo Modules::run("templates/admin",$data);
	}

	public function genNo(){

		print_r($this->agents_mdl->genAgentNo());
	}

	public function edit($agentNo){
    
		$data['module']=$this->module;
		$data['view']="edit_agent";
		$data['page']="Edit Agent";
		$data['agent']=$this->getAgent($agentNo);
		echo Modules::run("templates/admin",$data);
	}

   public function createLogin($agentNo){
		$data['module']=$this->module;
		$data['view']="agent_login";
		$data['page']="Create Agent Login";
		$agent = $this->getAgent($agentNo);;
		$data['agent']=$agent;
		$data['login'] =$this->getUser($agent->phoneNumber);
		echo Modules::run("templates/admin",$data);
	}

    public function saveAgentLogin($agentNo){

        $userdata=$this->input->post();

        $username=$userdata['username'];

        $user=$this->getUser($username);

        if(count($user)>0)
        {

					$userdata['password']=md5($userdata['password']);
					$userdata['userType']='1';
					$userdata['status']='1';

					$this->db->where('user_id',$user->user_id);
					$this->db->update('users',$userdata);

            redirect('agents/list');
        }

        $userdata['password']=md5($userdata['password']);
        $userdata['userType']='1';
        $userdata['status']='1';

        $this->db->insert('users',$userdata);

        $newuser=$this->getUser($username);

        if(count($newuser)>0)
        {
              $this->db->where('agentNo',$agentNo);
              $this->db->update('agents',array('userId'=>$newuser->user_id));
             redirect('agents/list');
        }

    }

   function getUser($username){

        $this->db->where('username',$username);
       $qry= $this->db->get('users');

        return $qry->row();

    }


	public function list(){

	   $searchData=array();

         if($this->input->post()!==null){
             $searchData=$this->input->post();
         }

        $data['search']=$searchData;

	    $this->load->library('pagination');
	    $config = array();

        $config["base_url"] = base_url() . "agents/list/";
        $config["total_rows"] = $this->agents_mdl->count($searchData);
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


public function saveAgentEdit($agentNo){

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
		}

		else{

			$msg= "Operation failed, please try again";

		}

        Modules::run("templates/setFlash",$msg);
		redirect('agents/list');
	}

    public function getByAgentNo($agentNo)
	{
		$agent=$this->agents_mdl->getByAgentNo($agentNo);
		return $agent;

	}

	public function addAgent()
	    {
		$data['module']=$this->module;
		$data['view']="add_team";
		$data['page']="Manage Agents";

		echo Modules::run("templates/admin",$data);
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

  public function getRefferalCommission($agentNo){

		$commission= $this->agents_mdl->getRefferalCommission($agentNo);
		return number_format((float)$commission, 1, '.', '');
	}

	public function getAgentTotalBalance(){

		$balance = $this->agents_mdl->getTotalBalance();

		return $balance;

	}

	public function getAgentHistory($agentNo){

    	$cached = Modules::run('cache/getData','HISTORY_'.$agentNo);
    
       if(!empty($cached) && count($cached)>0){ //returned cached history if available
         return $cached;
        }
    
		$history = $this->agents_mdl->getAgentHistory($agentNo);
		
        $data=array();

		for($i=0;$i<count($history); $i++){
		    $history[$i]->itemName=$this->getItemName($history[$i]->paymentCode);
		    $history[$i]->commission=$history[$i]->agent_fee;
            $history[$i]->amount = ($history[$i]->impact>0)? $history[$i]->impact: $history[$i]->impact*-1;
            $history[$i]->surcharge =  $history[$i]->charges;
        	$history[$i]->total = ($history[$i]->impact>0)? $history[$i]->impact: $history[$i]->impact*-1;
		}
    
    	 Modules::run('cache/setData','HISTORY_'.$agentNo,$history);
    
		return $history;
	}

   public function historyTest($agentNo){
       $count =count($this->getAgentHistory($agentNo));
        $cached = $this->getAgentHistory($agentNo);//
       print_r($cached);
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

public function getReffererCommission($referral){

		$commission= $this->agents_mdl->getReffererCommission($referral);
		return number_format((float)$commission, 1, '.', '');
}

public function getRefferals($agentNo){

  $refs = $this->agents_mdl->getRefferals($agentNo);

	foreach($refs as $referral){
        $referral->commission = $this->getReffererCommission($referral->agentNo);
      }
  //return $refs;

  return $refs;
}

public function agentsExport(){

		$agents = $this->db->get("agents")->result();
		$data = array();
		foreach ($agents  as $row) {
             $row->balance     =  $this->getAgentBalance($row->agentNo);
             $row->commission =  $this->getAgentCommission($row->agentNo);
			 $data[]=$row;
		}

		$filename="agent_list_".time().".xls";
		Modules::run("reports/exportToExcel",$data,$filename);

	}

public function testBalance($agentNo){

		$balance = $this->agents_mdl->getBalance($agentNo);

		print_r($balance);

	}


}
