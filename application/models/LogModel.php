<?php


class LogModel extends CI_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
	}

	public function insertLog(int $studentId, string $operation, bool $success, array $payload) {
		$this->db->insert('log', [
			"student_id"	=> $studentId,
			"operation"		=> $operation,
			"ip"			=> $_SERVER["REMOTE_ADDR"],
			"result"		=> $success ? "OK" : "FAILED",
			"payload"		=> json_encode($payload)
		]);
	}
}
