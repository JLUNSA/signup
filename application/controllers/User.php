<?php


class User extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model("UserModel");
		$this->load->model("CaptchaModel");
		$this->load->helper('url');
		$this->load->helper('form');
	}

	public function register(){
		$data = [];
		$render = function () use (&$data) {
			$this->load->view("templates/header");
			$this->load->view('user/register', $data);
			$this->load->view("templates/footer");
		};

		if($_SERVER['REQUEST_METHOD'] == "GET"){
			$render();
			return;
		}

		$this->load->library('form_validation');
		$out = $this->output->set_content_type('application/json');
		$errTplMsg = [
			'required'      => "请填写%s",
			'regex_match'   => '%s输入有误'
		];
		$this->form_validation->set_rules('studentId', '学号', 'trim|required|regex_match[/^\d{8}$/]', $errTplMsg);
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
			$_POST['studentId'],
			$_POST['captcha'],
			true
		)){
			return $out->set_output(json_encode([
				"status"	=> 2,
				"msg"		=> "验证码错误",
			]));
		}

		if(!$this->UserModel->createUser(
			$_POST['studentId'],
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

		$this->UserModel->setLogin($_POST['studentId'], false);
		return $out->set_output(json_encode([
			"status"	=> 0,
			"msg"		=> "",
		]));
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
