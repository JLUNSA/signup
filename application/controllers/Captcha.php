<?php


class Captcha extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('CaptchaModel');
	}

	public function get(){
		$img = $this->CaptchaModel->generate();
		return $this->output
			->set_content_type('image/png')
			->set_output($img);
	}

	public function check() {
		$out = $this->output->set_content_type('application/json');
		if(!isset($_GET['code'])){
			return $out->set_output(json_encode([
				"status"	=> 1,
				"msg"		=> "参数错误",
			]));
		}
		$is_success = $this->CaptchaModel->verify(0, $_GET['code'], false);
		if($is_success){
			return $out->set_output(json_encode([
				"status"	=> 0,
				"msg"		=> "",
			]));
		}else{
			return $out->set_output(json_encode([
				"status"	=> 2,
				"msg"		=> "验证码错误",
			]));
		}
	}
}
