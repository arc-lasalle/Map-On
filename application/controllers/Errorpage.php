<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Errorpage extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		
	}

	function index()
	{
		$data['heading'] = "Error";
		$data['message'] = "<p>The page you requested was not found.</p>";
		
		$this->load->view('errors/html/error_404.php', $data);
	
	}
	
}