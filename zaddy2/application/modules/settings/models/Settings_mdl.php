<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_mdl extends CI_Model {

	public function __construct()
        {
                parent::__construct();

                $this->table="settings";
        }

	

	public function getAll()
	{
		$table=$this->table;
		$query=$this->db->get($table);
		
		return $query->row();
	}



public function saveSettings($postdata)
	{
		$table=$this->table;

		$this->db->where('set_id',1);
		
		$saved=$query=$this->db->update($table,$postdata);

		if($saved){

			return "ok";
		}
		else{

			return "failed";
		}
	}





}
