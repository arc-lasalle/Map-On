<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class R2rml extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		$this->load->model("Mappingspace_model", "mappingspace");
		$this->load->model("Mappedclass_model", "mappedclass");
		$this->load->model("Mappeddataproperty_model", "mappeddataproperty");
		$this->load->model("Mappedobjectproperty_model", "mappedobjectproperty");
		$this->load->model("Workspaces_model", "workspaces");	
		$this->load->model("Ontology_model", "ontology");	
		$this->load->model("Mapping_model", "mapping");	
		$this->load->model("Prefix_model", "prefix");
		$this->load->model("Datasource_model", "datasource");
		
		$this->load->model("R2rml_model", "r2rml");
	}
		
	public function index()
	{

	}
		
	public function export($datasource_id)
	{		
		$str = $this->r2rml->export($datasource_id);
		
		$prefixes = $this->r2rml->generatePrefixes();
		
		$part = $this->r2rml->getR2RMLPart($datasource_id);
		
		$str = $prefixes.$part.$str;		
		
		$outputfilename = "./download/".$this->team->dir()."/".$datasource_id."_R2RML.ttl";
		
		$fp=fopen($outputfilename,'w');
		fwrite($fp, $str, strlen($str));

		fclose($fp);
		
		
		$datalist = $this->datasource->getDatasource($datasource_id);
		$data["datasource"] = $datalist[0];
		$data["ontologyName"] = $this->ontology->getOntology($data["datasource"]->ontology_id)->name;
		
		$data["datasource_id"] = $datasource_id;
		
		$data["filename"] = $outputfilename;
		$data["r2rmlcode"] = $str;

		//////////////////////////////////////////////////
		// Bread crumb
		$head["breadcrumb"][] = array("name" => "Data source", "link" => "datasource");
		$head["breadcrumb"][] = array("name" => $data["datasource"]->name, "link" => "datasource/view/".$datasource_id);
		$head["breadcrumb"][] = array("name" => "R2RML exporting", "link" => "r2rml/export/".$datasource_id);
		
		$this->load->view('header_s', $head);
		$this->load->view('r2rml/view', $data);
		$this->load->view('footer_s');
	}
	
	public function import($datasource_id)
	{
		$datalist = $this->datasource->getDatasource($datasource_id);
		$data["datasource"] = $datalist[0];
		$data["ontologyName"] = $this->ontology->getOntology($data["datasource"]->ontology_id)->name;
		
		$data["datasource_id"] = $datasource_id;
	
	
		$head["breadcrumb"][] = array("name" => "Data source", "link" => "datasource");
		$head["breadcrumb"][] = array("name" => $data["datasource"]->name, "link" => "datasource/view/".$datasource_id);
		$head["breadcrumb"][] = array("name" => "R2RML exporting", "link" => "r2rml/export/".$datasource_id);
		
		$this->load->view('header_s', $head);
		$this->load->view('r2rml/import', $data);
		$this->load->view('footer_s');
	}
	
	public function import_post()
	{
		$datasource_id = $this->input->post('datasource_id');
		$datasource_name = $this->datasource->getDatasource($datasource_id)[0]->name;
        $datasource_path = "upload/".$this->team->dir()."/datasources/" . $datasource_id."_".$datasource_name . "/source/";
		$user_id = 1;
		
		if (is_uploaded_file($_FILES['input_r2rmlfile']['tmp_name'])) {

            if ( !is_dir($datasource_path) ) mkdir($datasource_path, 0777, true);

			$targetfile = $datasource_path.$_FILES['input_r2rmlfile']['name'];
			move_uploaded_file($_FILES['input_r2rmlfile']['tmp_name'], $targetfile);
			
			$this->r2rml->loadR2RML($targetfile, $datasource_id);			
		}
		
		$this->export($datasource_id);
	}
		
		
	function edit($datasource_id)
	{
		$str = $this->r2rml->export($datasource_id);
		
		$prefixes = $this->r2rml->generatePrefixes();
		
		$output = $prefixes.$str;		
		
		$outputfilename = "./download/".$this->team->dir()."/".$datasource_id."_R2RML.ttl";
		
		$fp=fopen($outputfilename,'w');
		fwrite($fp, $output, strlen($output));

		fclose($fp);
		
		
		$datalist = $this->datasource->getDatasource($datasource_id);
		$data["datasource"] = $datalist[0];
		$data["ontologyName"] = $this->ontology->getOntology($data["datasource"]->ontology_id)->name;
		
		$data["datasource_id"] = $datasource_id;
		
		$data["filename"] = $outputfilename;
		$data["r2rmlprefixes"] = $prefixes;
		$data["r2rmlcode"] = strlen($str) == 0 ? " ": $str;
		
		$data["r2rmlpart"] = $this->r2rml->getR2RMLPart($datasource_id);
		
		$head["breadcrumb"][] = array("name" => "Data source", "link" => "datasource");
		$head["breadcrumb"][] = array("name" => $data["datasource"]->name, "link" => "datasource/view/".$datasource_id);
		$head["breadcrumb"][] = array("name" => "R2RML exporting", "link" => "r2rml/export/".$datasource_id);
		
		$this->load->view('header_s', $head);
		$this->load->view('r2rml/edit', $data);
		$this->load->view('footer_s');
	}
	
	public function edit_post()
	{
		$input_r2rmlpart = $this->input->post('input_r2rmlpart');
		$datasource_id = $this->input->post('datasource_id');
		$user_id = 1;
		
		var_dump ($input_r2rmlpart);
		
		$str = $this->r2rml->updateR2RMLPart($input_r2rmlpart, $user_id, $datasource_id);
		//$mappingspaceid = $this->mappingspace->add($name, $user_id, $datasource_id);
	
		$this->export($datasource_id);
	}
	
	

	public function load($datasource_id) {
	
		//echo "Loading a R2RML file<br>";
		
		$file = "download/".$this->team->dir()."/sigkdd_putative_sicilia.r2rml";
		
		$this->r2rml->loadR2RML($file, $datasource_id);
		
		
		
		$datalist = $this->datasource->getDatasource($datasource_id);
		$data["datasource"] = $datalist[0];
		
		//////////////////////////////////////////////////
		// Bread crumb
		$head["breadcrumb"][] = array("name" => "Data source", "link" => "datasource");
		$head["breadcrumb"][] = array("name" => $data["datasource"]->name, "link" => "datasource/view/".$datasource_id);
		$head["breadcrumb"][] = array("name" => "R2RML loading", "link" => "r2rml/load/".$datasource_id);
		
		$this->load->view('header_s', $head);
		//$this->load->view('r2rml/view', $data);
		$this->load->view('footer_s');
	}
	
	
}


