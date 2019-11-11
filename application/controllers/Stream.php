<?php


class Stream extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('UserModel');
		$this->load->model('ClassModel');
		$this->load->helper('url');
		$this->load->database();

		if(!$this->UserModel->isLoggedIn()){
			redirect("/user/login");
			return;
		}
	}

	//通过GET参数读取课程对应的视频
	// src: https://gist.github.com/ranacseruet/9826293
	public function get() {
		if(!isset($_GET['class_id']) || !$this->ClassModel->checkMutual((int)$_GET['class_id'])){
			print("您未选择本课程或课程不存在");
			return;
		}

		$classId = (int)$_GET['class_id'];
		$vid = $this->db
			->select("path")
			->from("stream")
			->where("class_id", $classId)
			->get()->result();

		if(empty($vid) || $vid[0]->path == ""){
			print("该课程没有视频");
			return;
		}

		$path = APPPATH . "../video/" . $vid[0]->path;
		$stream = "";
		$buffer = 102400;
		$start  = -1;
		$end    = -1;
		$size   = 0;

		//OPEN
		if (!($stream = fopen($path, 'rb'))) {
			print("视频读取失败，请联系管理员");
			return;
		}

		ob_get_clean();
		//HEADER
		$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		switch ($ext) {
			case "mp4":
				header("Content-Type: video/mp4");
				break;
			case "webm":
				header("Content-Type: video/webm");
				break;
		}
		header("Cache-Control: max-age=2592000, public");
		header("Expires: ".gmdate('D, d M Y H:i:s', time()+2592000) . ' GMT');
		header("Last-Modified: ".gmdate('D, d M Y H:i:s', @filemtime($path)) . ' GMT' );
		$start = 0;
		$size  = filesize($path);
		$end   = $size - 1;
		header("Accept-Ranges: bytes 0-".$size);

		if (isset($_SERVER['HTTP_RANGE'])) {
			$c_start = $start;
			$c_end = $end;

			list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
			if (strpos($range, ',') !== false) {
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
				header("Content-Range: bytes $start-$end/$size");
				exit;
			}

			if ($range == '-') {
				$c_start = $size - substr($range, 1);
			} else {
				$range = explode('-', $range);
				$c_start = $range[0];

				$c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $c_end;
			}
			$c_end = ($c_end > $end) ? $end : $c_end;
			if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
				header("Content-Range: bytes $start-$end/$size");
				exit;
			}
			$start = $c_start;
			$end = $c_end;
			$length = $end - $start + 1;
			fseek($stream, $start);
			header('HTTP/1.1 206 Partial Content');
			header("Content-Length: ".$length);
			header("Content-Range: bytes $start-$end/".$size);
		} else {
			header("Content-Length: ".$size);
		}

		//STREAM
		$i = $start;
		set_time_limit(0);
		while(!feof($stream) && $i <= $end && !connection_aborted()) {
			$bytesToRead = $buffer;
			if(($i+$bytesToRead) > $end) {
				$bytesToRead = $end - $i + 1;
			}
			$data = fread($stream, $bytesToRead);
			print($data);
			flush();
			$i += $bytesToRead;
		}

		//END
		fclose($stream);
		exit;
	}
}
