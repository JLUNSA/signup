<?php


class CaptchaModel extends CI_Model {
	private $studentId;
	private $inputCode;
	private $correctCode;

	public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->model('LogModel');
	}

	//生成验证码，返回验证码图像，并将验证码存在session中
	public function generate() {
		$w = 120; //设置图片宽和高
		$h = 32;
		$str = array(); //用来存储随机码
		$string = "ABCDEFGHJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789";//随机挑选其中4个字符，也可以选择更多，注意循环的时候加上，宽度适当调整
		$vcode = '';
		for ($i = 0; $i < 4; $i++) {
			$str[$i] = $string[rand(0, strlen($string) - 1)];
			$vcode .= $str[$i];
		}

		$_SESSION["captcha"] = $vcode;
		$im = imagecreatetruecolor($w, $h);
		$white = imagecolorallocate($im, 255, 255, 255); //第一次调用设置背景色
		$black = imagecolorallocate($im, 0, 0, 0); //边框颜色
		imagefilledrectangle($im, 0, 0, $w, $h, $white); //画一矩形填充
		imagerectangle($im, 0, 0, $w-1, $h-1, $black); //画一矩形框
		//生成雪花背景
		for ($i = 1; $i < 200; $i++) {
			$x = mt_rand(1, $w-9);
			$y = mt_rand(1, $h-9);
			$color = imagecolorallocate($im, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
			imagechar($im, 1, $x, $y, "*", $color);
		}
		//将验证码写入图案
		for ($i = 0; $i < count($str); $i++) {
			$x = 13 + $i * ($w - 15)/4;
			$y = mt_rand(3, $h / 3);
			$color = imagecolorallocate($im, mt_rand(0, 225), mt_rand(0, 150), mt_rand(0, 225));
			imagechar($im, 5, $x, $y, $str[$i], $color);
		}

		for ($i = 0; $i < 3; $i++) {
			$linecolor = imagecolorallocate($im, rand(80, 220), rand(80, 220), rand(80, 220));
			imageline($im, rand(1, 99), rand(1, 29), rand(1, 99), rand(1, 29), $linecolor);
		}

		ob_start();
		imagepng($im);
		$imagedata = ob_get_contents();
		ob_end_clean();
		imagedestroy($im);

		return $imagedata;
	}

	//验证验证码是否正确，并插入log
	//$studentId for log
	public function verify(int $studentId, string $code, bool $unset=false) {
		$this->studentId = $studentId;
		$this->correctCode = null;
		$this->inputCode = $code;
		if(!isset($_SESSION["captcha"])){
			return $this->insertLog(false);
		}
		$this->correctCode = $_SESSION["captcha"];
		$result = strtolower($code) == strtolower($_SESSION["captcha"]);
		if($unset){
			unset($_SESSION["captcha"]);
		}
		return $this->insertLog($result);
	}

	private function insertLog(bool $is_success){
		$this->LogModel->insertLog($this->studentId, "captcha_verify", $is_success, [
			"input"		=> $this->inputCode,
			"correct"	=> $this->correctCode,
		]);
		return $is_success;
	}
}
