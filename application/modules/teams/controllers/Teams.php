<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Teams extends MX_Controller {

	public function __construct()
        {
                parent::__construct();

                $this->load->model('teams_mdl');
                $this->module="teams";

                Modules::run("auth/isLegal");

               
        }

     public function countTeams(){

		return $this->teams_mdl->count();
	}

	public function list()
	    {

	    $this->load->library('pagination');
	    $config = array();

        $config["base_url"] = base_url() . "teams/list/";
        $config["total_rows"] = $this->teams_mdl->count();
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
		$data['view']="teams";
		$data['teams']=$this->teams_mdl->getAll($config["per_page"],$page);
		$data['page']="Manage Teams";

		echo Modules::run("templates/admin",$data);
	}

	public function add()
	    {
		$data['module']=$this->module;
		$data['view']="add_team";
		$data['page']="Manage Teams";

		echo Modules::run("templates/admin",$data);
	}

	public function saveTeam()
	    {
		
		$postData=$this->input->post();

		$postData['tournaments']=json_encode($postData['tournaments']);

	 if(!empty($_FILES['logo']['tmp_name'])){

      $config['upload_path']   = './assets/img/clubs/'; 
      $config['allowed_types'] = 'gif|jpg|png'; 
      $config['max_size']      = 15000;
      $config['file_name']      = str_replace(' ', '_', $postData['team_name']);
     
      $this->load->library('upload', $config);

	if ( ! $this->upload->do_upload('logo')) {
         $error = $this->upload->display_errors(); 
         echo strip_tags($error);
      }
      else{ 

         $data = $this->upload->data();
         $photofile =$data['file_name'];
         $path=$config['upload_path'].$photofile;

         $postData['logo']=$photofile;
         $res=$this->teams_mdl->saveTeam($postData);
      } 

     }
     else{

		$res=$this->teams_mdl->saveTeam($postData);

	  }

		if($res=='ok'){

			echo "Category successfully Added";

		}

		else{

			echo "Operation failed, please try again";

		}
	}


	public function getAll()
	{
		$teams=$this->teams_mdl->getAll();

		return $teams;
		
	}


	
    public function getBy_id($cat_id)
	{
		$data['item']=$this->teams_mdl->getBy_id($cat_id);

		return $data;
		
	}


	public function updateTeam()
	    {
		
		$postData=$this->input->post();

		$res=$this->teams_mdl->updateTeam($postData);

		if($res=='ok'){

			echo "Category successfully Updated";

		}

		else{

			echo "Operation failed, please try again";

		}
	}
	
	

	public function deleteTeam($team_id)
	    {
	    	$res=$this->teams_mdl->deleteTeam($team_id);

		if($res=='ok'){

			echo "Deletion Complete";

		}

		else{

			echo "Operation failed, please try again";

		}
	}
	
	



}
