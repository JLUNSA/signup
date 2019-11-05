<?php



class Baoming extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Baoming_model');
        $this->load->library('session');
    }

    public function index()
    {
        $this->load->view("templates/header");
        $this->load->view("baoming/index");
        $this->load->view("templates/footer");
    }

    public function check_duplicate()
    {
        $out = $this->output->set_content_type('application/json');
        if (!isset($_GET['jxh']) || !is_numeric($_GET['jxh'])) {
            $out->set_output(json_encode([
                "status"    => 2,
                "message"   => "无效输入"
            ]));
            return $out;
        }
        $jxh = (int)$_GET['jxh'];

        $r = $this->Baoming_model->check_duplicate($jxh);   //1

        $out->set_output(json_encode([
            "status"    => $r ? 0 : 1,
            "msg"       => ""
        ]));
        return $out;
    }

    public function login()
    {
        $this->load->helper('form');

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->load->view("templates/header");
            $data['err']=false;
            $this->load->view('baoming/login', $data);
            $this->load->view("templates/footer");
            return;
        }

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $errTplMsg = [
            'required'      => "请填写%s",
            'regex_match'   => '%s输入有误'
        ];
        $this->form_validation->set_rules('jxh', '教学号', 'trim|required|regex_match[/^\d{8}$/]', $errTplMsg);
        $this->form_validation->set_rules('name', '姓名', 'trim|required', $errTplMsg);
        $this->form_validation->set_rules('password', '密码', 'trim|required', $errTplMsg);



        if ($this->form_validation->run() == false) {
            $this->load->view("templates/header");
            $this->load->view('baoming/login');
            $this->load->view("templates/footer");
            return;
        }

        if (!$this->Baoming_model->login($_POST['jxh'], $_POST['name'], $_POST['password'])) {   //2
            $data = [];
            $data['err'] = true;
            $data['msg'] = "用户名或密码错误";
            $this->load->view("templates/header");
            $this->load->view('baoming/login', $data);
            $this->load->view("templates/footer");
            return;
        }

        $_SESSION['name'] = $_POST['name'];
        $_SESSION['jxh'] = $_POST['jxh'];

        http_response_code(302);
        header("Location: status");             // 5. 删除"banming/"
    }

    public function status()
    {
        $this->load->helper('form');
        $this->load->helper('url');

        $data = [];

        if (!isset($_SESSION['jxh'])) {
            // 未登录
            http_response_code(302);
            header("Location: /baoming/login");
            return;
        }

        $r = $this->Baoming_model->query($_SESSION['jxh']);   //3
        $data['name'] = $r->name;
        $data['jxh'] = $r->jxnum;
        $data['reserve_time'] = $r->reserve_time;
        $data['verified'] = $r->verified != 0 ? '是' : '否';

        $this->load->view("templates/header");
        $this->load->view('baoming/status', $data);
        $this->load->view("templates/footer");
    }

    public function query()
    {
        if (!isset($_SESSION['jxh'])) {
            http_response_code(403);
            return;
        }

        $r = $this->Baoming_model->query($_SESSION['jxh']);  //4
        $j = [
            "name"          => $r->name,
            'jxh'           => $r->jxnum,
            'reserve_time'  => $r->reserve_time,
            'verified'      => $r->verified != 0
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($j));
    }
}
