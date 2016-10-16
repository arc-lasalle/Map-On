<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Energymodel extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		
		$this->load->model("Workspaces_model", "workspaces_model");
		$this->load->model("Energymodel_model", "energymodel");
		$this->load->model("Prefixes_model", "prefixes_model");
		$this->load->model("Log_model", "log");
	}
		
	public function index()
	{
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}

		$store_Mysql = $this->workspaces_model->connect_workspace("EnergyModel");
		
		$this->load->helper('form');
        
        //if is the first call save workspace
        //$selected_workspace = $this->input->post('selected_workspace');
        
        //call function to create graph
        //$selected_class = $this->input->post('selected_class');
        
        $vars['focused_class'] = "http://www.owl-ontologies.com/SUMO155.owl#Entity";
        $vars['color'] = false;
        $vars['subclassof'] = true;
        $vars['workspace'] = "EnergyModel";
		
		$this->load->view('header');
		$this->load->view('energymodel', $vars);
		$this->load->view('footer');
	}
	
	function update()
	{
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}
		
		$store_Mysql = $this->workspaces_model->connect_workspace("EnergyModel");

		$this->energymodel->update($store_Mysql);

		$this->energymodel->updatePrefix();
		
		//$vars["version"] = $this->energymodel->getVersion($store_Mysql);

		//$this->energymodel->loadInferencedSuperClasses();
		
		//echo $vars["version"][0]['version'];
		
		$this->log->write("has updated the Energy Model", "");

		//redirect(base_url()."index.php/energymodel");
	}

	///test function
	function inference()
	{
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}
		
		$store_Mysql = $this->workspaces_model->connect_workspace("EnergyModel");

		$this->energymodel->loadInferencedSuperClasses();
				
		//redirect(base_url()."index.php/admin");
	}
	
	function suggest()
	{
		$substring = $this->input->post('string');

		if($substring == "")
			return;
		
		//load all class of the first workspace
        $this->load->model('workspaces_model');
        $store_Mysql = $this->workspaces_model->connect_workspace("EnergyModel"); 

        //Get all classes
        $q = 'SELECT DISTINCT ?datatype ?comment WHERE {
                 ?datatype <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#DatatypeProperty>. 
				 OPTIONAL { ?datatype  <http://www.w3.org/2000/01/rdf-schema#comment>  ?comment. }
            }';
        $classes = $store_Mysql->query($q, 'rows');
        
        $this->load->model('prefixes_model');

        //put prefixes 
        for($i=0; $i < count($classes); $i++){
            $prefix_uri = $this->prefixes_model->get_prefix_and_uri($classes[$i]['datatype']);
            
            if($prefix_uri != null){
                
				$pos1 = stripos($prefix_uri['class'], $substring);
				
				
				if($pos1 !== false) {
				   $strPrintValue = str_replace($substring, "<strong>".$substring."</strong>", $prefix_uri['class']);
				   $strComment = isset ($classes[$i]['comment']) ? ": ".$classes[$i]['comment']: "" ;
                   echo '<div class="item"><span style="font-size:12px; color: #4F6228;" onclick="add_search_box_EMDatatype(\''.$prefix_uri['class'].'\',\''.$prefix_uri['prefix'].":".$prefix_uri['class'].'\')">'.$strPrintValue.'</span>'.$strComment.'</div>';
                } else {
					if(isset ($classes[$i]['comment']) ){
						$pos2 = stripos($classes[$i]['comment'], $substring);
						
						if($pos2 !== false) {
						   $strPrintValue = $prefix_uri['class'];
						   $strComment = ": ".str_replace($substring, "<strong>".$substring."</strong>", $classes[$i]['comment']);
						   echo '<div class="item"><span style="font-size:12px; color: #4F6228;" onclick="add_search_box_EMDatatype(\''.$prefix_uri['class'].'\',\''.$prefix_uri['prefix'].":".$prefix_uri['class'].'\')">'.$strPrintValue.'</span>'.$strComment.'</div>';
						}
					}
				}
            }
        }
	}
	
	
	function loadontology_post()
	{
		if ((($_FILES["owl_file_input"]["type"] == "application/octet-stream") || ($_FILES["owl_file_input"]["type"] == "application/rdf+xml")) && ($_FILES["owl_file_input"]["size"] < 6000000)){
			// upload file
			$target_path = "./upload/globalontology.owl";
			
			if(move_uploaded_file($_FILES['owl_file_input']['tmp_name'], $target_path)) {
				$this->update();
			}
		}
		
		$this->index();
	}
}
