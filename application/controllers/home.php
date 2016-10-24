<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		//$this->load->model("Log_model", "log");
	}
		
	public function index()
	{
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}
		
		
		
		 // load pagination class
       
		
		
		$this->load->view('header_s');
		$this->load->view('home');
		$this->load->view('footer_s');
	}
}
