<?php

//选课页面
class ClassSelection extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('UserModel');
		$this->load->model('ClassModel');
		$this->load->helper('url');

		if(!$this->UserModel->isLoggedIn()){
			redirect("/user/login");
		}
	}

	public function index(){
		$this->load->view("templates/header");
		$this->load->view('classSelection/index');
		$this->load->view("templates/footer");
	}

	public function getClassList(){
		$r = $this->ClassModel->getClassList();
		return $this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				"status"	=> 0,
				"msg"		=> "",
				"data"		=> $r
			]));
	}

	public function getSubClassList(){
		$out = $this->output->set_content_type('application/json');
		if(!isset($_POST['class_id'])){
			return $out->set_output(json_encode([
				"status"	=> 1,
				"msg"		=> "参数错误",
			]));
		}
		$classId = (int)$_POST['class_id'];
		$r = $this->ClassModel->getSubClassList($classId);
		return $out->set_output(json_encode([
			"status"	=> 0,
			"msg"		=> "",
			"data"		=> $r
		]));
	}

	public function selectClass(){
		$_POST['subclass_id'] = $_GET['subclass_id'];
		$out = $this->output->set_content_type('application/json');
		if(!isset($_POST['subclass_id'])){
			return $out->set_output(json_encode([
				"status"	=> 1,
				"msg"		=> "参数错误",
			]));
		}


		$subclassId = $_GET['subclass_id'];
		$r = $this->ClassModel->selectClass($subclassId);
		if($r){
			return $out->set_output(json_encode([
				"status"	=> 0,
				"msg"		=> "",
			]));
		}else{
			return $out->set_output(json_encode([
				"status"	=> 2,
				"msg"		=> $this->ClassModel->lastError()['errMsg'],
			]));
		}

	}
}
