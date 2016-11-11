<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Prefix extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		
		$this->load->model("Workspaces_model", "workspaces_model");
		$this->load->model("ontology_model", "ontology");
		$this->load->model("Prefix_model", "prefix");
//		$this->load->model("Log_model", "log");
	}
		
	public function view($ontology_id)
	{
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}

		
		$data["ontology_id"] = $ontology_id;
		$data["ontology"] = $this->ontology->getOntology($ontology_id);
		$data['prefixes'] = $this->prefix->getPrefixes($ontology_id);
			
		
		$head["breadcrumb"][] = array("name" => "Ontologies", "link" => "ontology");
		$head["breadcrumb"][] = array("name" => $data["ontology"]->name, "link" => "ontology/view/".$ontology_id);
		$head["breadcrumb"][] = array("name" => "Namespaces", "link" => "prefix/view/".$ontology_id);
		
		$this->load->view('header_s', $head);
		$this->load->view('prefix/view', $data);
		$this->load->view('footer_s');
	}
	
	public function view2($ontology_id)
	{
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}
		
		$data["ontology_id"] = $ontology_id;
		
		$data["nprefixes"] = $ontology_id;
		
		
		$data["ontology"] = $this->ontology->getOntology($ontology_id);
		
		$data["modules"] = $this->ontology->getOntologyModules($ontology_id);
		
		$data["createnew"] = $this->addnewmodule($ontology_id, true);
		
		$head["breadcrumb"][] = array("name" => "Ontologies", "link" => "ontology");
		$head["breadcrumb"][] = array("name" => $data["ontology"]->name, "link" => "ontology/view/".$ontology_id);
			
		$this->load->view('header_s', $head);
		$this->load->view('ontology/view', $data);
		$this->load->view('footer_s');
	}
	
	
	
	public function delete($ontology_id, $prefix_id)
	{	
		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			$this->view($ontology_id);
		}
		
		$this->prefix->delete($prefix_id);
		
		$this->view($ontology_id);
	}
	
	
	public function createnew_post()
	{
		
		$ontology_id = $this->input->post('ontology_id');
		$prefix = $this->input->post('input_prefix');
		$iri = $this->input->post('input_iri');

		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			$this->view($ontology_id);
		}
		
		$this->prefix->add($prefix, $iri, $ontology_id);

		$this->view($ontology_id);
	}
	
	
	
	public function edit_post()
	{
		$ontology_id = $this->input->post('ontology_id');
		$prefix_id = $this->input->post('prefix_id');
		$prefix = $this->input->post('input_edit_prefix');
		$iri = $this->input->post('input_edit_iri');
		
		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			$this->view($ontology_id);
		}
		
		$this->prefix->update($prefix_id, $prefix, $iri, $ontology_id);

		$this->view($ontology_id);
	}
	
		
	
	
	
	
	
	function update()
	{
	/*
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}
		*/
		
		
		
		//$vars["version"] = $this->energymodel->getVersion($store_Mysql);

		//$this->energymodel->loadInferencedSuperClasses();
		
		//echo $vars["version"][0]['version'];
		
		
		//redirect(base_url()."index.php/energymodel");
	}

	///test function
	/*
	function inference()
	{
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}
		
		$store_Mysql = $this->workspaces_model->connect_workspace("EnergyModel");

		$this->energymodel->loadInferencedSuperClasses();
				
		//redirect(base_url()."index.php/admin");
	}
	*/
	
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
	/*
	
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
	}*/
}
