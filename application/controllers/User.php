<?php


class User extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model("UserModel");
		$this->load->model("CaptchaModel");
		$this->load->helper('url');
		$this->load->helper('form');
	}

	public function index(){
		if(!$this->UserModel->isLoggedIn()){
			redirect("/user/login");
			return;
		}
		$this->load->view("templates/header");
		$user = $this->UserModel->getLoggedInUser();
		$this->load->view('user/index', [
			"name"		=>	$user['name'],
			"verified"	=>	$user['verified'],
		]);
		$this->load->view("templates/footer");
	}

	public function login() {
		if($_SERVER['REQUEST_METHOD'] == "GET"){
			$this->load->view("templates/header");
			$this->load->view('user/login');
			$this->load->view("templates/footer");
			return;
		}

		$this->load->library('form_validation');
		$out = $this->output->set_content_type('application/json');
		$errTplMsg = [
			'required'      => "请填写%s",
			'regex_match'   => '%s输入有误'
		];
		$this->form_validation->set_rules('student_id', '学号', 'trim|required|regex_match[/^\d{8}$/]', $errTplMsg);
		$this->form_validation->set_rules('name', '姓名', 'trim|required', $errTplMsg);
		$this->form_validation->set_rules('password', '密码', 'trim|required', $errTplMsg);
		$this->form_validation->set_rules('captcha', '验证码', 'trim|required', $errTplMsg);

		if ($this->form_validation->run() == false) {
			return $out->set_output(json_encode([
				"status"	=> 1,
				"msg"		=> validation_errors(),
			]));
		}

		if(!$this->CaptchaModel->verify(
			$_POST['student_id'],
			$_POST['captcha'],
			true
		)){
			return $out->set_output(json_encode([
				"status"	=> 2,
				"msg"		=> "验证码错误",
			]));
		}

		if(!$this->UserModel->login($_POST['student_id'], $_POST['name'], $_POST['password'])){
			return $out->set_output(json_encode([
				"status"	=> 3,
				"msg"		=> "用户名或密码错误",
			]));
		}

		return $out->set_output(json_encode([
			"status"	=> 0,
			"msg"		=> "",
		]));
	}

	public function register(){
		if($_SERVER['REQUEST_METHOD'] == "GET"){
			$this->load->view("templates/header");
			$this->load->view('user/register');
			$this->load->view("templates/footer");
			return;
		}

		$this->load->library('form_validation');
		$out = $this->output->set_content_type('application/json');
		$errTplMsg = [
			'required'      => "请填写%s",
			'regex_match'   => '%s输入有误'
		];
		$this->form_validation->set_rules('student_id', '学号', 'trim|required|regex_match[/^\d{8}$/]', $errTplMsg);
		$this->form_validation->set_rules('name', '姓名', 'trim|required', $errTplMsg);
		$this->form_validation->set_rules('department', '学院', 'trim|required', $errTplMsg);
		$this->form_validation->set_rules('email', '邮箱', 'trim|required|valid_email', $errTplMsg);
		$this->form_validation->set_rules('password', '密码', 'trim|required', $errTplMsg);
		$this->form_validation->set_rules('captcha', '验证码', 'trim|required', $errTplMsg);

		if ($this->form_validation->run() == false) {
			return $out->set_output(json_encode([
				"status"	=> 1,
				"msg"		=> validation_errors(),
			]));
		}

		if(!$this->CaptchaModel->verify(
			$_POST['student_id'],
			$_POST['captcha'],
			true
		)){
			return $out->set_output(json_encode([
				"status"	=> 2,
				"msg"		=> "验证码错误",
			]));
		}

		if(!isset($_FILES['file'])){
			return $out->set_output(json_encode([
				"status"	=> 1,
				"msg"		=> "参数错误",
			]));
		}

		if(!$this->UserModel->createUser(
			$_POST['student_id'],
			$_POST['name'],
			$_POST['department'],
			$_POST['email'],
			$_POST['password'],
			$_FILES['file']
		)){
			return $out->set_output(json_encode([
				"status"	=> 3,
				"msg"		=> $this->UserModel->lastError()[1],
			]));
		}

		$this->UserModel->setLogin($_POST['student_id']);
		return $out->set_output(json_encode([
			"status"	=> 0,
			"msg"		=> "",
		]));
	}

	public function logout(){
		$this->UserModel->logout();
		redirect("/user/login");
	}

	public function isExist(){
		$out = $this->output->set_content_type('application/json');
		if(!isset($_GET['id'])){
			return $out->set_output(json_encode([
				"status"	=> 1,
				"msg"		=> "参数错误",
			]));
		}

		$id = $_GET['id'];
		if(!$this->UserModel->isStudentIdValid($id)){
			return $out->set_output(json_encode([
				"status"	=> 2,
				"msg"		=> "学号格式错误",
			]));
		}

		if($this->UserModel->isExist($id)){
			return $out->set_output(json_encode([
				"status"	=> 3,
				"msg"		=> "用户已注册",
			]));
		}

		return $out->set_output(json_encode([
			"status"	=> 0,
			"msg"		=> "",
		]));
	}

}
