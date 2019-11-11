<?php


class ClassModel extends CI_Model {
	private $errId;
	private $errMsg;

	public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->model('LogModel');
		$this->load->model('UserModel');
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

	public function getClassList(bool $ignoreTime=false){
		$query = $this->db
			->select("*")
			->from("class");
		if(!$ignoreTime){
			$query = $query
				->where("start_select <=", date('Y-m-d H:i:s'))
				->where("end_select >=", date('Y-m-d H:i:s'));
		}
		return $query->get()->result();
	}

	//检查课程是否存在
	private function isClassExist(int $classId, bool $ignoreTime=false){
		$classList = $this->getClassList($ignoreTime);
		foreach($classList as $i){
			if($i->class_id == $classId){
				return true;
			}
		}
		return false;
	}

	public function getSubClassList(int $classId, bool $ignoreTime=false){
		if(!$this->isClassExist($classId, $ignoreTime)) {
			return [];
		}

		$subclassList = $this->db
			->select("*")
			->from("subclass")
			->where("class_id", $classId)
			->get()->result();

		foreach ($subclassList as &$item) {
			$item->selected = $this->db
				->from("selected_class")
				->where("subclass_id", $item->subclass_id)
				->count_all_results();
			$item->capacity = (int)$item->capacity;
		}

		return $subclassList;
	}

	public function selectClass(int $subClassId){
		//检查课程是否存在
		$q = $this->db
			->select("class_id, capacity")
			->from("subclass")
			->where("subclass_id", $subClassId)
			->get()->result();
		if(count($q) == 0){
			$this->setErr(1, "找不到课程");
			return false;
		}
		$q = $q[0];

		$classId = $q->class_id;

		if ($this->checkMutual($classId)) {
			$this->setErr(5, "只能选择一个课时");
			return false;
		}

		//检查是否在选课时间内
		if(!$this->isClassExist($classId)) {
			$this->setErr(2, "当前不在选课时间");
			return false;
		}

		$studentId = $this->UserModel->getLoggedInUser()['studentId'];
		//锁表
		$this->db->trans_start();
		$this->db->query("SELECT * FROM selected_class FOR UPDATE");

		//检查是否重复选课
		$chk = $this->db
			->select("student_id")
			->from("selected_class")
			->where("student_id", $studentId)
			->where("subclass_id", $subClassId)
			->get()->result();
		if(count($chk) > 0){
			$this->db->trans_complete();
			$this->setErr(3, "已选过该课程");
			return false;
		}

		$cnt = $this->db
			->from("selected_class")
			->where("subclass_id", $subClassId)
			->count_all_results();

		if($cnt >= $q->capacity){
			$this->db->trans_complete();
			$this->setErr(4, "选课容量已满");
			return false;
		}

		$this->db->insert("selected_class", [
			"student_id"	=> $studentId,
			"subclass_id"	=> $subClassId,
		]);
		$this->db->trans_complete();
		return true;
	}

	public function getSelectedClassList($detail=false){
		$studentId = $this->UserModel->getLoggedInUser()['studentId'];

		$selectedClassList = $this->db
			->select("subclass_id")
			->from("selected_class")
			->where("student_id", $studentId)
			->get()->result();

		foreach ($selectedClassList as $i) {
			if($detail){
				$subclass = $this->db
					->select("class_id, start_time, end_time")
					->from("subclass")
					->where("subclass_id", $i->subclass_id)
					->get()->result()[0];

				$class = $this->db
					->select("*")
					->from("class")
					->where("class_id", $subclass->class_id)
					->get()->result()[0];

				$video = $this->db
					->select("*")
					->from("stream")
					->where("class_id", $subclass->class_id)
					->get()->result();
				$class->start_time = $subclass->start_time;
				$class->end_time = $subclass->end_time;
				$class->have_stream = !empty($video);
				$result[] = $class;
			} else {
				$result[] = $i->subclass_id;
			}
		}

		return $result;
	}

	/**
	 * 检查是否选择过该课程
	 *
	 * @param $classId
	 * @return bool
	 */
	public function checkMutual($classId) {
		$selected = $this->getSelectedClassList();

		// TODO 修改为多表联查
		$q = $this->db
			->select("subclass_id")
			->from("subclass")
			->where("class_id", $classId)
			->get()->result();

		foreach ($q as $item) {
			if (in_array($item->subclass_id, $selected)) {
				return true;
			}
		}

		return false;
	}
}
