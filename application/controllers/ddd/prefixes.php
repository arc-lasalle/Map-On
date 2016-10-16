<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Prefixes extends CI_Controller {
    
    public function index(){
        $this->load->helper('form');
        
        // load pagination class
        $this->load->library('pagination');
        $config['base_url'] = site_url("prefixes/index");
        $config['total_rows'] = $this->db->count_all('prefixes');
        $config['per_page'] = '20';
        $config['full_tag_open'] = '<p>';
        $config['full_tag_close'] = '</p>';

        $this->pagination->initialize($config);

        //load the model and get results
        $this->load->model('prefixes_model');
        $data['results'] = $this->prefixes_model->get_prefixes($config['per_page'],$this->uri->segment(3));

        // load the view
		$this->load->view('header');
        $this->load->view('prefix/index', $data);
		$this->load->view('footer');
	}
    
    public function add(){
        
        $this->load->model('prefixes_model');
        $this->prefixes_model->add($this->input->post('pf_uri'), $this->input->post('pf_prefix'), $this->input->post('pf_color'));
        
        redirect('prefixes/index','refresh');
    }
    
    public function modify(){
        
        $this->load->model('prefixes_model');
        $this->prefixes_model->modify($this->input->post('pf_modify_id'), $this->input->post('pf_modify_uri'), $this->input->post('pf_modify_prefix'), $this->input->post('pf_modify_color'));
        
        redirect('prefixes/index','refresh');
    }
    
    public function delete($id){
        
        $this->load->model('prefixes_model');
        $this->prefixes_model->delete($id);
        
        redirect('prefixes/index','refresh');
    }
    
}

