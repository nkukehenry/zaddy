<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MX_Controller {


	public function __construct()
        {
                parent::__construct();

                Modules::run("auth/adminLegal");
                Modules::run("auth/isLegal");
        }

	
	public function index()
	{
		//$this->load->view('sign_in');
		$this->in();
	}



public function in()
	{
		
		$data['module']="admin";
		$data['view'] ="dashboard";
		$data['agents']=Modules::run('agents/getAll',5,0);
		$data['users']=Modules::run('auth/getAll');
		$data['widgets'] = $this->getWidgetData();
		$data['page']="Dashboard";

        echo Modules::run('templates/admin',$data);
	}

	
	  public function line_graph()
	{

		$this->load->view('line_graph');
		
	}


public function getWidgetData(){

	$qry = $this->db->query("SELECT 
		(SELECT count(id) from agents) as agents,
	    (SELECT count(id) from transactions where finalStatus='SUCCESSFUL') as success, 
	    (SELECT count(id) from transactions where finalStatus='FAILED') as failures,
	    (SELECT count(id) from transactions where finalStatus='PENDING') as pending");

	return $qry->row();


}




}
