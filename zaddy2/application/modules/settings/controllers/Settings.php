<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MX_Controller {

	public function __construct()
        {
                parent::__construct();

                $this->load->model('settings_mdl');
                $this->module="settings";

                
        }

	
	public function getAll()
	{
		$settings=$this->settings_mdl->getAll();

		return $settings;
		
	}


	
    public function getBy_id($loc_id)
	{
		$data['settings']=$this->settings_mdl->getBy_id($set_id);

		return $data;
		
	}

 public function configure()
	    {
		$data['module']=$this->module;
		$data['view']="configure";
		$data['page']="General Settings";

		echo Modules::run("templates/admin",$data);
	}


public function saveSettings()
	    {
		
		$postData=$this->input->post();

	if(!empty($_FILES['logo']['tmp_name'])){


      $config['upload_path']   = './assets/images/sm/'; 

      $config['allowed_types'] = 'gif|jpg|png'; 

      $config['max_size']      = 15000;
      $config['file_name']      = 'main_logo';

      $this->load->library('upload', $config);

	
	if ( ! $this->upload->do_upload('logo')) {

         $error = $this->upload->display_errors(); 

         echo strip_tags($error);

      }else { 

         $data = $this->upload->data();

         $main_logo =$data['file_name'];

         $path=$config['upload_path'].$main_logo;

         //water mark the photo
         $this->photoMark($path);

         $postdata['logo']=$main_logo;

         $res=$this->settings_mdl->saveSettings($postData);

      } 


     }//user uploaded with a photo
    else
	{
			$res=$this->settings_mdl->saveSettings($postData);
	}
		
	if($res=='ok'){

			echo "Settings successfully updated";

		}

		else{

			echo "Operation failed, please try again";

		}
	}

public function cronNow()
	    {

	    	file_put_contents('cron_logs.txt',"HELLO AT".date('Y-m-d h:i:a'));

	    }





}
