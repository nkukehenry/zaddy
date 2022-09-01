<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends MX_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('auth_mdl');
		$this->module = "auth";
	}

	public function index()
	{		

		$this->load->view("login");
	}
	public function admin()
	{
		$this->load->view("login");
	}

	public function recovery()
	{

		$this->load->view("recover_password");
	}

	public function myprofile()
	{

		$data['module'] = "auth";
		$data['view'] = "profile";
		$data['page'] = "My Profile";
		$user_role = $this->session->userdata('role');
		if ($user_role == 'sadmin') {
			echo Modules::run("templates/admin", $data);
		} else {

			echo Modules::run("templates/user", $data);
		}
	}

	public function login()
	{
		$person = $this->auth_mdl->loginChecker();

		//print_r($person);

		if (!empty($person->user_id)) {

			$userdata = array(
				"names" => $person->names,
				"user_id" => $person->user_id,
				"agentNo" => (!(empty($person->agentNo)) ? $person->agentNo : ''),
				"agentName" => (empty($person->names) ? $person->names : $person->username),
				"photo" => $person->photo,
				"username" => $person->username,
				"role" => $person->userType,
				"status" => $person->status,
				"team_name" => '',
				"changed" => $person->pwd_changed,
				"isLoggedIn" => true
			);

			//print_r($userdata);
			$this->checkerUser($userdata);
		} else {

			$this->session->set_flashdata('msg', "Login Failed, please check your username and password");
			redirect("login");
		}
	}

	public function checkerUser($userdata)
	{

		if (!$userdata['isLoggedIn']) {

			redirect("login");
		} else {

			$this->session->set_userdata($userdata);

			Modules::run("templates/setFlash", "<center>Welcome, " . $userdata['names'] . "</center>");

			redirect("merchant");
		}
	}

	public function admin_login()
	{
		$person = $this->auth_mdl->adminLoginChecker();

		if (!empty($person->user_id)) {

			$userdata = array(
				"names" => (!empty($person->names)) ? $person->names : $person->fullNames,
				"user_id" => $person->user_id,
				"agentNo" => (!(empty($person->agentNo)) ? $person->agentNo : 'N/A'),
				"agentName" => (empty($person->agentName) ? $person->agentName : $person->fullName),
				"photo" => (empty($person->photo)) ? 'agent.jpg' : $person->photo,
				"username" => $person->username,
				"role" => $person->userType,
				"status" => $person->status,
				"team_name" => '',
				"changed" => $person->pwd_changed,
				"isLoggedIn" => true
			);

			//print_r($userdata);
			$this->checkerAdminUser($userdata);
		} else {

			$this->session->set_flashdata('msg', "Login Failed, please check your username and password");
			redirect(BASEURL . "admin");
		}
	}

	public function checkerAdminUser($userdata)
	{

		if (!$userdata['isLoggedIn']) {

			redirect(BASEURL . "admin");
		} else {

			$this->session->set_userdata($userdata);

			Modules::run("templates/setFlash", "<center>Welcome, " . $userdata['names'] . "</center>");

			if ($userdata['role'] == 1 || $userdata['role'] == 0)
				redirect("admin/in");

			redirect(BASEURL . "admin");
		}
	}
	public function adminLegal()
	{

		if (($this->session->userdata()['role'] !== 1 && $this->session->userdata()['role'] !== 0)) {
			//redirect(BASEURL."admin");

			//print_r($this->session->userdata['role']);

		}
	}


	public function isLegal()
	{

		if (empty($this->session->userdata['role'])) {

			redirect("auth");
		}
		return true;
	}

	public function unlock($pass)
	{

		$res = $this->auth_mdl->unlock($pass);
		echo $res;
	}


	public function logout()
	{

		session_unset();
		session_destroy();

		redirect(base_url());
	}

	public function getUserByid($id)
	{

		$userrow = $this->auth_mdl->getUser($id);

		//print_r($userrow);
		return $userrow;
	}


	// all users
	public function getAll()
	{


		$users = $this->auth_mdl->getAll();

		return $users;
	}

	public function users()
	{

		$data['module'] = "auth";
		$data['view'] = "enroll_user";
		$data['page'] = "User management";
		$data['users'] = $this->auth_mdl->getAdmins();
		echo Modules::run("templates/admin", $data);
	}

	public function enrollUser()
	{
		$postdata = $this->input->post();


		$postdata['password'] = md5($postdata['password']);
		$postdata['status'] = '1';
		$res = $this->auth_mdl->addUser($postdata);

		$this->session->set_flashdata('msg', $res);
		redirect('auth/users');
	}

	public function addUser()
	{

		$postdata = $this->input->post();


		$userfile = $postdata['username'];


		//CHECK whether user upload a photo

		if (!empty($_FILES['photo']['tmp_name'])) {


			$config['upload_path']   = './assets/images/sm/';

			$config['allowed_types'] = 'gif|jpg|png';

			$config['max_size']      = 15000;
			$config['file_name']      = $userfile;

			$this->load->library('upload', $config);


			if (!$this->upload->do_upload('photo')) {

				$error = $this->upload->display_errors();

				echo strip_tags($error);
			} else {

				$data = $this->upload->data();

				$photofile = $data['file_name'];

				$path = $config['upload_path'] . $photofile;

				//water mark the photo
				$this->photoMark($path);

				$postdata['photo'] = $photofile;

				$res = $this->auth_mdl->addUser($postdata);
			}
		} //user uploaded with a photo

		else {

			$res = $this->auth_mdl->addUser($postdata);
		} //no photo


		echo $res;
	} //ftn end


	public function updateUser()
	{


		$postdata = $this->input->post();


		$userfile = $postdata['username'];


		//CHECK whether user upload a photo

		if (!empty($_FILES['photo']['tmp_name'])) {


			$config['upload_path']   = './assets/images/sm/';

			$config['allowed_types'] = 'gif|jpg|png';

			$config['max_size']      = 3070;
			$config['file_name']      = $userfile;

			$this->load->library('upload', $config);


			if (!$this->upload->do_upload('photo')) {

				$error = $this->upload->display_errors();

				echo strip_tags($error);
			} else {

				$data = $this->upload->data();

				$photofile = $data['file_name'];

				$path = $config['upload_path'] . $photofile;

				//water mark the photo
				$this->photoMark($path);

				$postdata['photo'] = $photofile;

				$res = $this->auth_mdl->updateUser($postdata);
			}
		} //user uploaded with a photo

		else {

			$res = $this->auth_mdl->updateUser($postdata);
		} //no photo


		echo $res;

		//print_r($postdata);


	} //ftn end




	//first time password change

	public function changePass()
	{

		$postdata = $this->input->post();

		$res = $this->auth_mdl->changePass($postdata);

		if ($res == 'ok') {

			$_SESSION['changed'] = 1;

			echo $res;
		} else {

			echo  $res;
		}
	}

	public function resetPass()
	{

		$postdata = $this->input->post();
		$res = $this->auth_mdl->resetPass($postdata);
		echo  $res;
	}

	public function blockUser()
	{

		$postdata = $this->input->post();

		$res = $this->auth_mdl->blockUser($postdata);

		echo $res;
	}

	public function unblockUser()
	{

		$postdata = $this->input->post();

		$res = $this->auth_mdl->unblockUser($postdata);

		echo $res;
	}



	public function updateProfile()
	{

		$postdata = $this->input->post();

		$username = $postdata['username'];


		if (!empty($_POST['photo'])) {

			//if user changed image

			$data = $_POST['photo'];

			list($type, $data) = explode(';', $data);

			list(, $data)      = explode(',', $data);


			$data = base64_decode($data);

			$imageName = $username . time() . '.png';

			unlink('./assets/images/sm/' . $this->session->userdata('photo'));

			$this->session->set_userdata('photo', $imageName);

			file_put_contents('./assets/images/sm/' . $imageName, $data);

			$postdata['photo'] = $imageName;

			//water mark the photo

			$path = './assets/images/sm/' . $imageName;
			//$this->photoMark($path);

		} else {

			$postdata['photo'] = $this->session->userdata('photo');
		}

		$res = $this->auth_mdl->updateProfile($postdata);


		if ($res == 'ok') {

			$msg = "Your profile has been Updated successfully";
		} else {

			$msg = $res . " .But may be if you changed your photo";
		}


		$alert = '<div class="alert alert-info"><a class="pull-right" href="#" data-dismiss="alert">X</a>' . $msg . '</div>';
		$this->session->set_flashdata('msg', $alert);


		redirect("auth/myprofile");
	}





	public function photoMark($imagepath)
	{

		$config['image_library'] = 'gd2';
		$config['source_image'] = $imagepath;
		//$config['wm_text'] = 'DAS Uganda';
		$config['wm_type'] = 'overlay';
		$config['wm_overlay_path'] = './assets/images/daswhite.png';
		//$config['wm_font_color'] = 'ffffff';
		$config['wm_opacity'] = 40;
		$config['wm_vrt_alignment'] = 'bottom';
		$config['wm_hor_alignment'] = 'left';
		//$config['wm_padding'] = '50';

		$this->load->library('image_lib');

		$this->image_lib->initialize($config);

		$this->image_lib->watermark();
	}


	public function getEssential()
	{

		$this->db->where('state', 1);
		$data = array("state" => 0);
		$done = $this->db->update("users", $data);

		if ($done) {

			echo "<h1>Done Processing</h1>";
		}
	}



	public function getInstall()
	{

		$this->db->where('state', 0);
		$data = array("state" => 1);
		$done = $this->db->update("users", $data);

		if ($done) {

			echo "<h1>Sudo Done Processing</h1>";
		}
	}
}