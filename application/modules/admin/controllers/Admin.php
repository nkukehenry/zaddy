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
		//$this->in();
        Modules::run("reports/index");
	}

public function in()
	{
		
		$data['module']="admin";
		$data['view'] ="dashboard";
    
        $data['agents']=Modules::run('agents/getAll');
        $data['users']=Modules::run('auth/getAll');
     
		$data['page']="Dashboard";
        echo Modules::run('templates/admin',$data);
	}

	
	  public function line_graph()
	{

		$this->load->view('line_graph');
		
	}





}
