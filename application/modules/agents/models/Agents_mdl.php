<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Agents_mdl extends CI_Model
{

	public function __construct()
	{
		parent::__construct();

		$this->table = "agents";
		// $this->user=$this->session->userdata['user_id'];
	}


	public function getAgentLogin($username, $password)
	{

		$this->db->where('username', $username);
		$this->db->where('password', md5($password));
		$this->db->where_in('agents.status', [1, 2]);
		$this->db->join('agents', 'agents.userId=users.user_id');
		$qry = $this->db->get('users');
		return $qry->row();
	}
	public function getTable()
	{

		$table = "agents";
		return $table;
	}

	public function count($searchData = null)
	{

		if (!empty($searchData['agentNo']))
			$this->db->where('agentNo', $searchData['agentNo']);

		if (!empty($searchData['names']))
			$this->db->where("names like '%" . $searchData['names'] . "%'");
		$table = $this->getTable();
		$this->db->select('id');
		$rows = $this->db->get($table)->result_array();
		return count($rows);
	}

	public function getAll($limit = 10, $start = 0, $searchData = null)
	{
		$table = $this->getTable();

		if (!empty($searchData['agentNo']))
			$this->db->where('agentNo', $searchData['agentNo']);

		if (!empty($searchData['names']))
			$this->db->where("names like '%" . $searchData['names'] . "%'");

		$this->db->limit($limit, $start);
		$this->db->order_by("id", "desc");
		$query = $this->db->get($table);

		return $query->result();
	}





	public function getByAgentNo($agentNo)
	{
		$table = $this->table;
		$this->db->where('agentNo', $agentNo);
		$query = $this->db->get($table);

		return $query->row();
	}

	public function genAgentNo()
	{

		$qry = $this->db->query("SELECT max(id) as agentNo FROM `agents`");

		$agentNo = $qry->row()->agentNo;
		$agentNo = $agentNo + 1;

		if ($agentNo < 10)
			$agentNo = "0" . $agentNo;

		$prefix = "";
		if (strlen($agentNo) < 3)
			$prefix = "000";

		if (strlen($agentNo) == 3)
			$prefix = "00";
		if (strlen($agentNo) == 4)
			$prefix = "0";
		if (strlen($agentNo) > 4)
			$prefix = "";

		return AGENT_PREFIX . $prefix . ($agentNo);
	}


	public function saveAgent($postdata)
	{
		$table = $this->getTable();

		$saved = $query = $this->db->insert($table, $postdata);

		if ($saved) {

			return "ok";
		} else {

			return "failed";
		}
	}

	public function updateAgent($agentNo, $postdata)
	{
		$table = $this->getTable();

		$this->db->where('agentNo', $agentNo);
		$saved = $this->db->update($table, $postdata);

		if ($saved) {

			return "ok";
		} else {

			return "failed";
		}
	}

	public function deleteAgent($agentNo)
	{

		$this->db->where('agentNo', $agentNo);
		$done = $this->db->delete($this->table);

		if ($done) {

			return 'ok';
		} else {

			return 'failed';
		}
	}




	public function getBalance($agentNo)
	{

		$this->db->select("sum(impact) as balance");
		$this->db->where("(
	( tranStatus in ('SUCCESSFUL','PENDING') AND impact<0)
	OR ( tranStatus in ('SUCCESSFUL','PENDING') AND impact>0 and paymentDate<'2019-12-30 03:24')
	OR ( tranStatus in ('SUCCESSFUL') AND impact>0 and paymentDate>='2019-12-30 03:24'))
	");  //date refers to when pending withdraws stopped being credits

		$this->db->where(" (agentNo='" . $agentNo . "' OR (customerNo='" . $agentNo . "' AND agentNo ='" . LOAD_TERMIANL . "'))");


		$qry = $this->db->get("transactions");
		return $qry->row()->balance;
	}

	public function getCommission($agentNo)
	{

		$this->db->select("sum(agent_fee) as commission");
		$this->db->where("tranStatus in ('SUCCESSFUL','PENDING')");  //date refers to when pending withdraws stopped being credits
		$this->db->where("agentNo", $agentNo);
		$this->db->where('commissionState', 0);

		$qry = $this->db->get("transactions");
		return $qry->row()->commission;
	}

	public function getRefferalCommission($agentNo)
	{

		$this->db->select("sum(referralComms) as commission");
		$this->db->where("tranStatus in ('SUCCESSFUL','PENDING')");  //date refers to when pending withdraws stopped being credits
		$this->db->where("referralAgent", $agentNo);
		$this->db->where('refferalCommState', 0);
		$qry = $this->db->get("transactions");
		return $qry->row()->commission;
	}

	public function getReffererCommission($refferal)
	{

		$this->db->select("sum(referralComms) as commission");
		$this->db->where("tranStatus in ('SUCCESSFUL','PENDING')");  //date refers to when pending withdraws stopped being credits
		$this->db->where("agentNo", $refferal);
		$this->db->where('refferalCommState', 0);
		$qry = $this->db->get("transactions");
		return $qry->row()->commission;
	}


	public function getBalance2($agentNo)
	{

		$this->db->select("sum(impact) as balance");
		$this->db->where(" (agentNo='" . $agentNo . "' OR (customerNo='" . $agentNo . "' AND agentNo ='" . LOAD_TERMIANL . "'))");
		$qry = $this->db->get("transactions");
		return $this->db->last_query();
	}

	public function getTotalBalance()
	{

		$this->db->select("sum(impact) as balance");
		$this->db->where("(
	( tranStatus in ('SUCCESSFUL','PENDING') AND impact<0)
	OR ( tranStatus in ('SUCCESSFUL','PENDING') AND impact>0 and paymentDate<'2019-12-30 03:24')
	OR ( tranStatus in ('SUCCESSFUL') AND impact>0 and paymentDate>='2019-12-30 03:24'))
	");  //date refers to when pending withdraws stopped being credits

		$this->db->where(" (agentNo in (SELECT agentNo from agents) OR (customerNo in (SELECT agentNo from agents)  AND agentNo ='" . LOAD_TERMIANL . "'))");

		$qry = $this->db->get("transactions");
		echo  number_format($qry->row()->balance);
	}


	public function getAgentHistory($agentNo)
	{

		// $this->db->where("agentNo",$agentNo);
		$this->db->where(" (agentNo='" . $agentNo . "' or(agentNo='" . LOAD_TERMIANL . "' AND customerNo='" . $agentNo . "'))");
		$this->db->where("paymentDate BETWEEN (CURRENT_DATE() - INTERVAL 1 MONTH) AND (CURRENT_DATE() + INTERVAL 1 DAY)");
		$this->db->order_by("id", 'DESC');
		$qry = $this->db->get("transactions");
		return $qry->result();
	}


	public function setAgentTranPin($request)
	{

		$agentNo = $request->agentNo;
		$tranPin = $request->oldPin;
		if (!empty($request->newPin))
			$tranPin = $request->newPin;

		$agent = $this->getByAgentNo($agentNo);

		$data = array('tranPin' => $tranPin);

		$this->db->where('user_id', $agent->userId);
		$update = $this->db->update('users', $data);

		return $update;
	}


	public function setAgentPassword($request)
	{

		$agentNo = $request->agentNo;

		$oldPin = md5($request->oldPin);

		$newPin = md5($request->newPin);

		$agent = $this->getByAgentNo($agentNo);

		$data = array('password' => $newPin, "pwd_changed" => 1);

		$this->db->where('user_id', $agent->userId);
		$qry = $this->db->get("users");

		$user = $qry->row();

		if ($user->password == $oldPin) {

			$this->db->where('user_id', $agent->userId);
			$update = $this->db->update('users', $data);
			$response = "SUCCESS";
		} else {
			$response = "INCORRECT OLD PASSWORD";
		}

		return $response;
	}

	public function getRefferals($agentNo)
	{
		$this->db->select('names as name,agentNo');
		$this->db->where('refferalAgent', $agentNo);
		$qry = $this->db->get($this->table);
		return $qry->result();
	}
}