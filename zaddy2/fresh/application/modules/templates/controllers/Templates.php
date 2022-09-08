<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Templates extends MX_Controller {

	

	public function user($data)
	{
		$this->load->view('user',$data);
	}



public function admin($data)
	{
		
        $this->load->view('main',$data);

	}
	
	


	public function test()
	{
		
        $this->load->view('include/footer',$data);

	}
	
	public function setFlash($msg){
	    
        $flash='<div class="alert alert-info alert-dismissable">
              <h3><b>'.$msg.'</b></span> <span class="pull-right" style="margin-top:-25px;" data-dismiss="alert">&times;</span></h3>
              
            </div>';
            
            $this->session->set_flashdata('msg',$flash);
	}


}
