<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MX_Controller {


	public function __construct()
        {
                parent::__construct();

                //Modules::run("auth/adminLegal");
                Modules::run("auth/isLegal");
        }



	
	public function index()
	{
		$this->load->view('sign_in');
	}



public function in()
	{
		
		$data['module']="user";
		$data['view'] ="home";
		$data['categories']=Modules::run('category/getAll');
		$data['page']="Dashboard";

        echo Modules::run('templates/user',$data);
	}




}
