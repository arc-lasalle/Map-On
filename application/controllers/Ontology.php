<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ontology extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		
		$this->load->model("Workspaces_model", "workspaces_model");
		$this->load->model("ontology_model", "ontology");
		$this->load->model("Prefix_model", "prefix");
		$this->load->model("Datasource_model", "datasource");
		$this->load->model("Vowl_model", "vowl");

		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}
		
	}
		
	public function index()
	{
		$this->maponrouting->setO();

		
		if ( $this->team->connected() ) {
			$vars['ontologies'] = $this->ontology->getOntologies();
		} else {
			$vars['ontologies'] = [];
		}

		$vars["createnew"] = $this->createnew(true);

		
		
		$head["breadcrumb"][] = array("name" => "Ontologies", "link" => "ontology");
		
		$this->load->view('header_s', $head);
		$this->load->view('ontology/list', $vars);
		$this->load->view('footer_s');
	}
	
	public function view($ontology_id)
	{
		if ( !isset($ontology_id) ) redirect("ontology");
		
		$this->maponrouting->setO( $ontology_id );
		
		$data["ontology_id"] = $ontology_id;
		
		$data["nprefixes"] = count($this->prefix->getPrefixes($ontology_id));
		
		$data["ontology"] = $this->ontology->getOntology($ontology_id);

		if ( empty($data["ontology"]) ) {
			$this->session->set_flashdata('error_message', [false, "Ontology not found." ]);
			redirect("ontology");
		}

		$data["modules"] = $this->ontology->getOntologyModules($ontology_id);
		
		$store_Mysql = $this->workspaces_model->connect_workspace("ontology_".$ontology_id);
		$data["statistics"] = $this->ontology->getStatistics($store_Mysql);
	
		$data["createnew"] = $this->addnewmodule($ontology_id, true);
		
		$head["breadcrumb"][] = array("name" => "Ontologies", "link" => "ontology");
		$head["breadcrumb"][] = array("name" => $data["ontology"]->name, "link" => "ontology/view/".$ontology_id);
			
		$this->load->view('header_s', $head);
		$this->load->view('ontology/view', $data);
		$this->load->view('footer_s');
	}
	
	/////////////////////////////////////////////
	// This method return an OWL version of the ontology generated from the store.
	public function viewowl($ontology_id)
	{
		
		$store_Mysql = $this->workspaces_model->connect_workspace("ontology_".$ontology_id);
		
		$owl = $this->ontology->getOwl($store_Mysql, $ontology_id); 
		
		return $owl;
	}
	
	public function graph($ontology_id)
	{
		$data["ontology_id"] = $ontology_id;
		
		$data["ontology"] = $this->ontology->getOntology($ontology_id);
		
		$source = "http://www.ontologyportal.org/SUMO.owl#Building";
		
		$q = ' 	SELECT distinct ?c1  WHERE {
					?objp1 <http://www.w3.org/2000/01/rdf-schema#domain> <'.$source.'>.
					?objp1 <http://www.w3.org/2000/01/rdf-schema#range> ?c1.
				}';
/*
					?objp2 <http://www.w3.org/2000/01/rdf-schema#range> ?c2.
					?objp3 <http://www.w3.org/2000/01/rdf-schema#domain> ?c2.
					?objp3 <http://www.w3.org/2000/01/rdf-schema#range> ?c3.
					?objp4 <http://www.w3.org/2000/01/rdf-schema#domain> ?c3.
					*/
		
		//<'.$objectproperty.'> <http://www.w3.org/2000/01/rdf-schema#range> ?range
		
		//echo "QUER: ".$q."<br>";
		$store_Mysql = $this->workspaces_model->connect_workspace("ontology_".$ontology_id); 

        $classes = $store_Mysql->query($q, 'rows');
		
		//var_dump($classes);
		
		$data["root"] = $this->prefix->getQName($source, $ontology_id);

		foreach($classes as $row) {
			
			$data["nodes"][] = $this->prefix->getQName($row["c1"], $ontology_id);
			
			$subclass = $this->ontology->getSubClasses($store_Mysql, $row["c1"]);
			
			foreach($subclass as $subc) {
			
				$data["nodes"][] = $this->prefix->getQName($subc["class"], $ontology_id);
			}
		}		
		
		$this->load->view('header_s');
		$this->load->view('ontology/graph', $data);
		$this->load->view('footer_s');
	}
	
	public function loadclass()
	{
		//$name = $this->input->post('input_name');
		$ontology_id = $this->input->post('ontologyid');
		$class = $this->input->post('class');
		
		$source = $this->prefix->getURI($class, $ontology_id);

		// Loading the properties where the source is the domain	
		$q = ' 	SELECT ?objp1 ?c1  WHERE {
					?objp1 <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#ObjectProperty>.
					?objp1 <http://www.w3.org/2000/01/rdf-schema#domain> <'.$source.'>.
					?objp1 <http://www.w3.org/2000/01/rdf-schema#range> ?c1.
				}';

		$store_Mysql = $this->workspaces_model->connect_workspace("ontology_".$ontology_id); 

        $classes = $store_Mysql->query($q, 'rows');

		$arr = array();

		foreach($classes as $row) {
			
			$arr[$this->prefix->getQName($row["c1"], $ontology_id)] = $this->prefix->getQName($row["objp1"], $ontology_id);			
		}
		
		////////////////
		// Loading the properties where the source is the range	
		// Too many nodes in the graph --> it is disable for the moment.
		/*$q = ' 	SELECT  ?c1   WHERE {
					?objp1 <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#ObjectProperty>.
					?objp1 <http://www.w3.org/2000/01/rdf-schema#domain> ?c1.
					?objp1 <http://www.w3.org/2000/01/rdf-schema#range> <'.$source.'>.
				}';
		$classes = $store_Mysql->query($q, 'rows');

		foreach($classes as $row) {
			$arr[$this->prefix->getQName($row["c1"], $ontology_id)] = -1;
		}
		*/
		  
		////////////////
		// Loading the subclasses
		$subclass = $this->ontology->getSubClasses($store_Mysql, $source);
		//echo "Subclasses de ".$row["c1"]. ": ".count($subclass)."<br>";
		foreach($subclass as $subc) {
			
			$arr[$this->prefix->getQName($subc["class"], $ontology_id)] = "http://www.w3.org/2000/01/rdf-schema#subClassOf";
		}
		

				
		echo json_encode($arr);

	}
	
	public function createnew($toText = false)
	{
		if(!$toText) {
			$this->load->view('header_s');
			$this->load->view('ontology/createnew');
			$this->load->view('footer_s');
		} else {
		
			return $this->load->view('ontology/createnew', null, true);
		}
	}
	
	public function createnew_post()
	{
		$name = $this->input->post('input_name');
		$user_id = 1;
		
		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			$this->index();
		}
		
		$ontology_id = $this->ontology->add($name, $user_id);
			
		$this->index();
	}
	
	public function addnewmodule($ontology_id, $toText = false) 
	{			
		$data["ontology_id"] = $ontology_id;
		if(!$toText) {
			$this->load->view('header');
			$this->load->view('ontology/addnewmodule', $data);
			$this->load->view('footer');
		} else {
			return 	$this->load->view('ontology/addnewmodule', $data, true);
		}
	}


	public function addnewmodule_post()
	{
		$ontology_id = $this->input->post('ontology_id');
		$module_name = $this->input->post('input_name');
		$prefix = $this->input->post('input_prefix');



		//////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			$this->view($ontology_id);
		}
		
		if (is_uploaded_file($_FILES['input_attachment']['tmp_name'])) {

            $ontology_name = $this->ontology->getOntology($ontology_id)->name;
            $ontology_dir = "upload/".$this->team->dir()."/ontologies/" . $ontology_id . "_" . $ontology_name . "/source/";
            $file_name = $_FILES['input_attachment']['name'];

            if ( !is_dir($ontology_dir) ) mkdir($ontology_dir, 0777, true);
            move_uploaded_file($_FILES['input_attachment']['tmp_name'], $ontology_dir.$file_name );

			
			$store_Mysql = $this->workspaces_model->connect_workspace("ontology_".$ontology_id);
			
			try {
				$this->ontology->loadOntology($store_Mysql, $ontology_dir.$file_name, $ontology_id, $prefix);
			} catch (Exception $e) {
				$this->maponrouting->showMessage(false, "ERROR CO260: Error uploading file.", $e->getMessage());
				redirect('ontology/view/' . $ontology_id);
			}
			
			
			//$this->ontology->updatePrefix($prefix, $targetfile, $ontology_id);
			
			$this->ontology->addModule($module_name, $_FILES['input_attachment']['name'], '', $ontology_id);
			
			//to generate a new owl file
			$owl = $this->ontology->getOwl($store_Mysql, $ontology_id); 

			// Delete old Vowl file if exist. This file is automatically generated when we request for vowl file.
			$vowlFile = $ontology_dir . "../" . $ontology_name . ".json";
			if ( file_exists($vowlFile) ) unlink($vowlFile);

			$this->maponrouting->showMessage(true, "File uploaded successfully.");
			
		} else {
			$this->maponrouting->showMessage(false, "ERROR CO277: Error uploading file.");
		}
		
			
		//$this->view($ontology_id);
		redirect('ontology/view/' . $ontology_id);
	}

	
	public function delete($ontology_id)
	{	
		//////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			$this->index();
		}

        $ontology_name = $this->ontology->getOntology($ontology_id)->name;
        $ontology_dir = getcwd() . "/upload/".$this->team->dir()."/ontologies/" . $ontology_id . "_" . $ontology_name;

        if ( is_dir($ontology_dir) ){
            if (PHP_OS === 'WINNT') {
                exec('rd /s /q "' . $ontology_dir . '"');
            } else {
                exec('rm -rf "' . $ontology_dir . '"');
            }
        }


		$this->ontology->delete($ontology_id);


		$store_Mysql = $this->workspaces_model->connect_workspace("ontology_".$ontology_id);
		$store_Mysql->drop();


		$this->index();
	}

	// Returns the ontology in vowl format for the graph.
	public function getVowl( $datasource_id ) {
		$ontology_id = $this->datasource->getOntology($datasource_id);
		$vowl_file = $this->vowl->getVowlFile( $ontology_id );

		echo $vowl_file["vowl"];
	}

	public function getVowlOntology( $ontology_id ) {
		$vowl_file = $this->vowl->getVowlFile( $ontology_id );

		echo $vowl_file["vowl"];
	}

	// Save the position of a node of the vowl graph.
	public function saveOntologyLayout( $datasource_id ) {

		$save = $this->input->post('save');
		$nodeid = $this->input->post('node_uri');
		$layoutX = $this->input->post('pos_x');
		$layoutY = $this->input->post('pos_y');

		$insert = ($save === "true");
		$this->ontology->setOntologyLayout( $datasource_id, $nodeid, $insert, $layoutX, $layoutY );
	}

	//// Get the position of the nodes for the vowl graph.
	//public function getOntologyLayout( $mapping_space_id ) {
	//	return $this->ontology->getOntologyLayout( $mapping_space_id );
	//}
	
	
	
	
	
	
	
	
	
	
	
	function update()
	{
		//$vars["version"] = $this->energymodel->getVersion($store_Mysql);
		//$this->energymodel->loadInferencedSuperClasses();
		//echo $vars["version"][0]['version'];
		//redirect(base_url()."index.php/energymodel");
	}

	///test function
	/*
	function inference()
	{
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
