<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Treemaps extends CI_Controller {
    
    public function treemap(){
        
        $workspace = $this->input->post('workspace');
        $id = $this->input->post('id');
        
        $this->load->model('workspaces_model');
        //
        
        $type = $this->input->post('type');
        if( $type == null ){
            $type = 'navigable';
        }
        $data['type'] = $type;
        
        $this->load->model('treemaps_model');
        
        if($type == 'navigable'){
			$workspace_id = $this->workspaces_model->get_workspace_id($workspace);
            $treemap = $this->treemaps_model->get_cache($workspace_id);
            if($treemap == '0'){
                //function that create the json variable for the treemap
                $treemap = $this->treemaps_model->create_var_treemap_navigable($workspace);
            }
        }else{

            $exist = $this->treemaps_model->exist_treemap($workspace);
            
            if(!$exist){
                $treemap = $this->treemaps_model->add_treemap_nodes($workspace);
            }
            
            $offset = $this->input->post('offset');
            if($offset == null){
                $offset = 0;                
            }
            $data['offset'] = $offset;
            
            $treemap_classes = $this->treemaps_model->get_classes_treemap_onelevel($workspace,$offset);
            
            $treemap = $this->create_var_treemap_onelevel($workspace, $treemap_classes);
            //$this->treemaps_model->add_treemap_nodes($workspace);
            
        }
        
        $data['treemap'] = $treemap;
        $data['id'] = $id;
        $data['workspace'] = $workspace;
        
        $height = $this->input->post('height');
        if( $height == null){
            $height = "600px";
        }
        $data['height'] = $height;
        
        $width = $this->input->post('width');
        if( $width == null ){
            $width = "600px";
        }
        $data['width'] = $width;
        
        $node_color = $this->input->post('node_color');
        if($node_color == null){
            $node_color = "#555555";
        }
        $data['node_color'] = $node_color ;
        
        $tip_color = $this->input->post('tip_color');
        if($tip_color == null){
            $tip_color = "#000000";
        }
        $data['tip_color'] = $tip_color;
        
        $data['onclick'] = $this->input->post('onclick');
        $data['onmouseover'] = $this->input->post('onmouseover');
        $data['onload'] = $this->input->post('onload');
        
        $this->load->view('treemap/treemap',$data);
    }
    
    public function admin(){
        
        // revisar la paginacio.
        //http://stackoverflow.com/questions/4262721/codeigniter-pagination-for-a-query
        $this->load->helper('form');
        
        $workspace_name = $this->input->post('workspace');
        if($workspace_name == null){
            $workspace_name = $this->uri->segment(3);
        }
        
        $this->load->database();
        $this->load->model('treemaps_model');
        
        $exist = $this->treemaps_model->exist_treemap($workspace_name);

        if(!$exist){
            $treemap = $this->treemaps_model->add_treemap_nodes($workspace_name);
        }
        
        // load pagination class
        $this->load->library('pagination');
        $config['base_url'] = site_url("treemaps/admin/".$workspace_name."/");
        $config['total_rows'] = $this->db->count_all($workspace_name.'_treemaps');
        $config['per_page'] = '20';
        $config['full_tag_open'] = '<p>';
        $config['full_tag_close'] = '</p>';
        $config['uri_segment'] = 4;

        $this->pagination->initialize($config);

        $data['results'] = $this->treemaps_model->get_classes_treemap_paginate($workspace_name,$config['per_page'],$this->uri->segment(4));
        $data['workspace'] = $workspace_name;

        // load the view
		$this->load->view('header');
        $this->load->view('treemap/admin', $data);
		$this->load->view('footer');
    }
    
    public function create_var_treemap_onelevel($workspace, $treemap_classes){
        
        $treemap = '{        
            "id": "root", 
            "name": "<b>Ontology '.strtoupper($workspace).'</b>",
            "data": {},
            "children": [';

        $this->load->model('prefixes_model');
        foreach($treemap_classes as $class){
            $class_info = $this->prefixes_model->get_prefix_and_uri($class->class);
            
            $color = "";
            if($class->color != ''){
                $color = ', "$color":"'.$class->color.'"'; 
            }
            
            $treemap .= '{
                "id": "'.$class->class.'", 
                "name": "'.$class_info['class'].'",
                "data": {
                        "$area": '.$class->area.'
                        '.$color.'
                        },
                "children": []
                },';
        }

        $treemap .= ']}';


        return $treemap;
    }
    
    function modify_class(){
        
        $id = $this->input->post('ct_modify_id');
        $workspace_name = $this->input->post('ct_modify_workspace');
        $class = $this->input->post('ct_modify_uri');
        $visible = $this->input->post('ct_modify_visible');
        $area = $this->input->post('ct_modify_area');
        $color = $this->input->post('ct_modify_color');
        
        $this->load->model('treemaps_model');
        $this->treemaps_model->modify_class($id, $workspace_name, $class, $visible, $area, $color);
        
        redirect('treemaps/admin/'.$workspace_name,'refresh');
    }
    
}
     