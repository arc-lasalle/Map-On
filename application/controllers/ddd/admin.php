<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		$this->load->model("Workspaces_model", "workspaces_model");
		$this->load->model("Energymodel_model", "energymodel");
		$this->load->model("Log_model", "log");
	}
		
	public function index()
	{
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}
		elseif (!$this->ion_auth->is_admin())
		{
			//redirect them to the home page because they must be an administrator to view this
			redirect($this->config->item('base_url'), 'refresh');
		}

		//set the flash data error message if there is one
		$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

		//list the users
		$this->data['users'] = $this->ion_auth->users()->result();
		foreach ($this->data['users'] as $k => $user)
		{
			$this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
		}

	
		$this->load->view('header');
				
		$this->load->view('auth/index', $this->data);
		$this->load->view('footer');
	}
}
