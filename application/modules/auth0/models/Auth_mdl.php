<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_mdl extends CI_Model {

	public function __construct()
        {
                parent::__construct();

                $this->table="users";
        }

	
public function loginChecker(){

	$postdata=$this->input->post();

	$username=$postdata['username'];
	$password=md5($postdata['pass']);

	$this->db->where("username",$username);
	$this->db->where("password",$password);
	$this->db->where("users.status",1);
	$this->db->where("users.userType",1);
   // $this->db->where_in("agents.status", [1,2]);
	//$this->db->join("agents","users.user_id=agents.userId",'left');
	$this->db->join("usertypes","users.userType=usertypes.id",'left');
	$qry=$this->db->get($this->table);

	$rows=$qry->num_rows();

	if($rows!==0){

	$person=$qry->row();
	
	if(empty($person->photo))
	 $person->photo="avatar.jpg";
	return $person;

   }

   else{

   	return "failed";

   }
}

public function unlock($pass){

	$uid=$this->session->userdata['user_id'];
	$username=$this->session->userdata['username'];

$this->db->where("user_id",$uid);
$this->db->where("username",$username);
$this->db->where("password",md5($pass));

$qry=$this->db->get($this->table);

$rows=$qry->num_rows();

if($rows==1){

	return "ok";
}


}

public function getUser($id){

$this->db->where("user_id",$id);
$qry=$this->db->get($this->table);

return $qry->row();


}

public function getAll(){

//$this->db->where("inc",0);

//$this->db->join("locations","locations.location_id=users.location_id");
$qry=$this->db->get($this->table);

return $qry->result();

}

public function addUser($postdata){

	$postdata['password']=md5($postdata['password']);

	$postdata['state']=1;

	$qry=$this->db->insert($this->table,$postdata);
	$rows=$this->db->affected_rows();

	if($rows>0){

		return "User has been Added";
	}

	else{

		return "Operation failed";
	}

}

// update user's details

public function updateUser($postdata){

    $uid=$postdata['user_id'];

	$this->db->where('user_id',$uid);

	$this->db->update($this->table,$postdata);
	$rows=$this->db->affected_rows();

	if($rows>0){

		return "User details for".$postdata['lastname']." ".$postdata['firstname']." have been updated";
	}

	else{

		return "No Operation made, seems like no changes made";
	}

}


// change password
public function changePass($postdata){

$oldpass=md5($postdata['oldpass']);
$newpass=md5($postdata['newpass']);
$uid=$postdata['uid'];


$this->db->select('password');
$this->db->where('user_id',$uid);
$qry=$this->db->get($this->table);

$user=$qry->row();

if($user->password==$oldpass){
// change the password

$data=array("password"=>$newpass,"pass_change"=>1);
$this->db->where('user_id',$uid);
$this->db->update($this->table,$data);
$rows=$this->db->affected_rows();

if($rows==1){
	return "ok";
} else{
	return "Operation failed for an unknown reason, try again";
}

}

else{
	return "The old password you provided is wrong";
}



}




public function updateProfile($postdata){

$uid=$postdata['user_id'];

$this->db->where('user_id',$uid);
$done=$this->db->update($this->table,$postdata);
$rows=$this->db->affected_rows();

if($rows==1){

	return "ok";


} else{



	return "Nothing done, changes deemed to be Null";
}





}


//reset user's password

public function resetPass($postdata){

$uid=$postdata['user_id'];
$password=md5($postdata['password']);

$data=array("password"=>$password,"pass_change"=>0);

$this->db->where('user_id',$uid);
$done=$this->db->update($this->table,$data);
$rows=$this->db->affected_rows();

if($rows==1){

	return "User's password has been reset";


} else{



	return "Failed, Try Again";
}
}

//block

public function blockUser($postdata){

$uid=$postdata['user_id'];

$data=array("state"=>0);

$this->db->where('user_id',$uid);
$done=$this->db->update($this->table,$data);
$rows=$this->db->affected_rows();

if($rows==1){

	return "User has been blocked";

} else{



	return "Failed, Try Again";
}


}


//unblock user

public function unblockUser($postdata){

$uid=$postdata['user_id'];

$data=array("state"=>1);

$this->db->where('user_id',$uid);
$done=$this->db->update($this->table,$data);
$rows=$this->db->affected_rows();

if($rows==1){

	return "User has been Unblocked";


} else{



	return "Failed, Try Again";
}


}







}
