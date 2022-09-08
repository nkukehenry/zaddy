<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MX_Controller {


	public function __construct()
        {
                parent::__construct();
                Modules::run("auth/isLegal");
        }

public function merchant()
	{
		
		$data['module']="user";
		$data['view'] ="home";
		$data['categories']=$this->getCategories();
		$data['page']="Dashboard";
        echo Modules::run('templates/user',$data);
	}
	
public function merchantBillers($id,$hint="")
	{
		
		if(!empty($hint))
		    $hint=="ANY";
		$data['module']="user";
		$data['view'] ="billers";
		$data['billers']=Modules::run("billers/getCategoryBillers",$id);
		$data['page']="Services";
		$data['hint']=$hint;
        echo Modules::run('templates/user',$data);
	}

public function merchantForm($id)
	{
		
		$data['module']="user";
		$data['view'] ="payment";
		$data['items']=Modules::run("billers/getBillItems",$id);
		$data['page']="Services";
        echo Modules::run('templates/user',$data);
	}
		

public function getCategories(){

		$categories=$this->db->get("categories")->result();

		return $categories;
	}



}
