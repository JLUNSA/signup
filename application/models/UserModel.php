<?php


class UserModel extends CI_Model {
	private $errId;
	private $errMsg;

	public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->model('LogModel');
	}

	private function setErr(int $id, string $msg){
		$this->errId = $id;
		$this->errMsg = $msg;
	}

	public function lastError(){
		return [
			"errId"		=> $this->errId,
			"errMsg"	=> $this->errMsg
		];
	}

	//创建用户
	//如果返回false，则说明出错，应使用lastError获取错误
	public function createUser(
		string $studentId,
		string $name,
		string $department,
		string $email,
		string $password,
		array $photo //上传的原始照片
	) {
		if(!$this->isStudentIdValid($studentId)){
			$this->setErr(1, "请正确输入学号");
			return false;
		}

		//查重
		$dup = $this->isExist($studentId);
		if($dup){
			$this->setErr(2, "用户已存在");
			return false;
		}

		//转换图片
		$allowedExtension = ["image/jpeg", "image/png"];
		if(!in_array($photo['type'], $allowedExtension)){
			$this->setErr(3, "照片格式不支持");
			return false;
		}

		try{
			switch($photo['type']){
				case "image/jpeg":
					$fp = APPPATH."../upload/$studentId.jpg";
					$image = imagecreatefromjpeg($photo['tmp_name']);
					imagejpeg($image, $fp, 80);
					imagedestroy($image);
					break;
				case "image/png":
					$fp = APPPATH."../upload/$studentId.png";
					$image = imagecreatefrompng($photo['tmp_name']);
					imagepng($image, $fp, 80);
					imagedestroy($image);
					break;
				default:
					$this->setErr(3, "照片格式不支持");
					return false;
			}
		}catch (Exception $e){
			$this->setErr(4, "照片转换出错");
			return false;
		}


		$this->db->insert("user", [
			"student_id"	=> $studentId,
			"name"			=> $name,
			"department"	=> $department,
			"email"			=> $email,
			"password"		=> password_hash($password, PASSWORD_BCRYPT),
			"photo"			=> $fp,
			"verified"		=> 0,
			"is_admin"		=> 0,
		]);

		$this->LogModel->insertLog($studentId, "register", true, [
			"name"			=> $name,
			"department"	=> $department,
			"email"			=> $email,
			"filename"		=> $photo['name']
		]);
		return true;
	}

	//登录
	//如果返回false，则说明出错
	//为了安全起见，不会返回错误信息
	public function login(
		string $studentId,
		string $name,
		string $password
	) {
		if(!$this->isStudentIdValid($studentId)){
			$this->LogModel->insertLog($studentId, "login", false, [
				"name"			=> $name,
				"description"	=> "学号不符合规则"
			]);
			return false;
		}

		$query = $this->db
			->select("student_id, password, is_admin")
			->from("user")
			->where([
				'student_id'	=> $studentId,
				'name'			=> $name
			])
			->get()->result();

		if(empty($query)){
			$this->LogModel->insertLog($studentId, "login", false, [
				"name"			=> $name,
				"description"	=> "用户不存在"
			]);
			return false;
		}

		if(password_verify($password, $query[0]->password)){
			$this->LogModel->insertLog($studentId, "login", true, [
				"name"			=> $name,
			]);
			$this->setLogin($studentId, $name, (bool)$query[0]->is_admin);
			return true;
		}

		$this->LogModel->insertLog($studentId, "login", false, [
			"name"			=> $name,
			"description"	=> "密码错误"
		]);
		return false;
	}

	//查重
	public function isExist(string $studentId){
		$query = $this->db
			->select("student_id")
			->from("user")
			->where("student_id", $studentId)
			->get()
			->result();

		if(count($query) > 0){
			return true;
		}
		return false;
	}

	public function isStudentIdValid(string $studentId){
		if(preg_match("/^\d{2}[12]\d{5}$/", $studentId)){
			return true;
		}
		return false;
	}

	public function setLogin(string $studentId, string $name, bool $isAdmin){
		$_SESSION['isLoggedIn'] = true;
		$_SESSION['studentId'] = $studentId;
		$_SESSION['name'] = $name;
		$_SESSION['isAdmin'] = $isAdmin;
	}

	public function isLoggedIn(){
		if(!isset($_SESSION['isLoggedIn'])){
			return false;
		}else{
			return $_SESSION['isLoggedIn'];
		}
	}

	public function getLoggedInUser() {
		if(!$this->isLoggedIn()){
			return null;
		}

		return [
			"studentId"	=> $_SESSION['studentId'],
			"name"		=> $_SESSION['name'],
			"isAdmin"	=> $_SESSION['isAdmin']
		];
	}
}
