<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Teams_mdl extends CI_Model {

	public function __construct()
        {
                parent::__construct();

                $this->table="teams";

                //$this->user=$this->session->userdata['user_id'];
                //$this->team_id=$this->session->userdata['team_id'];
        }

	
	public function getTable()
	{
		
		$table="teams";
		return $table;
	}


	public function getAll($limit=10,$start=0)
	{
		
		$table=$this->getTable();

		$this->db->limit($limit,$start);
		$query=$this->db->get($table);
		
		return $query->result();
	}

	public function count(){

		$table=$this->getTable();
		$this->db->select('id');
		$rows=$this->db->get($table)->result_array();
		return count($rows);
	}


	public function getBy_id($team_id)
	{
		$table=$this->getTable();
		$this->db->where('team_id',$cat_id);
		$query=$this->db->get($table);
		
		return $query->row_array();
	}

		public function saveTeam($postdata)
	{
		$table=$this->getTable();
		
		$saved=$query=$this->db->insert($table,$postdata);

		if($saved){

			return "ok";
		}
		else{

			return "failed";
		}
	}

	public function deleteTeam($team_id){

			$this->db->where('team_id',$team_id);
			$done=$this->db->delete($this->table);

		if($done){

			return 'ok';
		}

		else{

			return 'failed';
		}



	}


public function updateTeam($postdata){

	$id=$postdata['team_id'];
	$this->db->where("team_id",$id);
	$done=$this->db->update($this->table,$data);

	if($done){

		return "ok";
	}
	else{

		return "failed";
	}

}




}
