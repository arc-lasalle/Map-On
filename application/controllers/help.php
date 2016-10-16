<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Help extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
	}
		
	public function index()
	{
		
		
		 // load pagination class
       
		$head["breadcrumb"][] = array("name" => "Help", "link" => "help");
		
		$this->load->view('header_s', $head);
		$this->load->view('help/home');
		$this->load->view('footer_s');
	}
}
