<?php


class Baoming_model extends CI_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
	}

	public function check_duplicate(int $jxh) {
		$query = $this->db->get_where('baoming', [
			'jxnum'	=> $jxh,
		]);
		$r = $query->result();
		return empty($r);
	}

	public function check_vcode(string $code) {
		if(!isset($_SESSION['vcode'])){
			return false;
		}

		if(strtolower($_SESSION['vcode']) === strtolower($code)){
			return true;
		}
		return false;
	}

	public function login($jxh, $name, $pw){
		//$pw = password_hash($pw, PASSWORD_BCRYPT);
		$query = $this->db->get_where('baoming', [
			'jxnum'		=> $jxh,
			'name'		=> $name
		]);
		$r = $query->result();
		if(empty($r)){
			return false;
		}

		if(password_verify($pw, $r[0]->passwd)){
			return true;
		}
		return false;
	}

	public function query(int $jxh){
		$query = $this->db->get_where('baoming', [
			'jxnum'		=> $jxh
		]);

		return $query->result()[0];
	}
}
