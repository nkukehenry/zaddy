<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tournaments_mdl extends CI_Model {

	public function __construct()
        {
                parent::__construct();

                $this->table="tournaments";

                $this->user=$this->session->userdata['user_id'];
                $this->team_id=$this->session->userdata['team_id'];
        }

	
	public function getTable()
	{
		
		$table="tournaments";
		return $table;
	}


	public function getAll()
	{
		$table=$this->getTable();
		$query=$this->db->get($table);
		
		return $query->result();
	}


	public function getBy_id($tournament_id)
	{
		$table=$this->getTable();
		$this->db->where('id',$tournament_id);
		$query=$this->db->get($table);
		
		return $query->row_array();
	}

		public function saveTournament($postdata)
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

	public function deleteTournament($tournament_id){

			$this->db->where('tournament_id',$tournament_id);
			$done=$this->db->delete($this->table);

		if($done){

			return 'ok';
		}

		else{

			return 'failed';
		}



	}


public function updateTournament($postdata){

	$id=$postdata['tournament_id'];
	$this->db->where("tournament_id",$id);
	$done=$this->db->update($this->table,$data);

	if($done){

		return "ok";
	}
	else{

		return "failed";
	}

}




}
