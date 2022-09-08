<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tournaments extends MX_Controller {

	public function __construct()
        {
                parent::__construct();

                $this->load->model('tournaments_mdl');
                $this->module="tournaments";

                Modules::run("auth/isLegal");

               
        }

	
	public function getAll()
	{
		$tournaments=$this->tournaments_mdl->getAll();

		return $tournaments;
		
	}


	
    public function getBy_id($tournament_id)
	{
		$data=$this->tournaments_mdl->getBy_id($tournament_id);

		return $data;
		
	}

	public function addTournament()
	    {
		$data['module']=$this->module;
		$data['view']="add_tournament";
		$data['page']="Manage Tournaments";

		echo Modules::run("templates/admin",$data);
	}

	public function saveTournament()
	    {
		
		$postData=$this->input->post();

		

		$res=$this->tournaments_mdl->saveTournament($postData);

		if($res=='ok'){

			echo "Category successfully Added";

		}

		else{

			echo "Operation failed, please try again";

		}
	}


	public function updateTournament()
	    {
		
		$postData=$this->input->post();

		$res=$this->tournaments_mdl->updateTournament($postData);

		if($res=='ok'){

			echo "Category successfully Updated";

		}

		else{

			echo "Operation failed, please try again";

		}
	}
	
	

	public function deleteTournament($tournament_id)
	    {
	    	$res=$this->tournaments_mdl->deleteTournament($tournament_id);

		if($res=='ok'){

			echo "Deletion Complete";

		}

		else{

			echo "Operation failed, please try again";

		}
	}
	
	



}
