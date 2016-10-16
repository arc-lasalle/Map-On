<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		$this->load->model("Log_model", "log");
	}
		
	public function index()
	{
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}
		
		 // load pagination class
        $this->load->library('pagination');
		
        $config['base_url'] = site_url("home/index");
        $config['total_rows'] = $this->db->count_all('log');
        $config['per_page'] = '20';
        $config['full_tag_open'] = '<p>';
        $config['full_tag_close'] = '</p>';

        $this->pagination->initialize($config);
		
		$vars['log'] = $this->log->getAll_pagination($config['per_page'],$this->uri->segment(3));
		
		
		$this->load->view('header');
		$this->load->view('home', $vars);
		$this->load->view('footer');
	}
}
