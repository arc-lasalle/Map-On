<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shortestpath extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		
		$this->load->model("Workspaces_model", "workspaces");
		$this->load->model("ontology_model", "ontology");
		$this->load->model("Prefix_model", "prefix");
//		$this->load->model("Log_model", "log");
	}
		
	public function index()
	{
		$vars['ontologies'] = $this->ontology->getOntologies();

		
		$this->load->view('header');
		$this->load->view('ontology/list', $vars);
		$this->load->view('footer');
	}
	
	public function view($ontology_id)
	{
		$data["ontology_id"] = $ontology_id;
		
		$store_Mysql = $this->workspaces->connect_workspace("ontology_".$ontology_id); 

		$source = "http://www.ontologyportal.org/SUMO.owl#Building";
		$target = "http://www.semanco-project.eu/2012/5/SEMANCO.owl#Electrical_appliances";
		$target = "http://www.semanco-project.eu/2012/5/SEMANCO.owl#Energy_consumption_and_energy_saving_related_to_building_services";
		
		
        //Get all classes
			
		/*$q = ' 	SELECT DISTINCT * WHERE {
					?objp1 <http://www.w3.org/2000/01/rdf-schema#domain> <'.$source.'>.
					?objp1 <http://www.w3.org/2000/01/rdf-schema#range> ?c1.
					?objp2 <http://www.w3.org/2000/01/rdf-schema#domain> ?c1.
					?objp2 <http://www.w3.org/2000/01/rdf-schema#range> ?c2.
					?objp3 <http://www.w3.org/2000/01/rdf-schema#domain> ?c2.
					?objp3 <http://www.w3.org/2000/01/rdf-schema#range> ?c3.
					?objp4 <http://www.w3.org/2000/01/rdf-schema#domain> ?c3.
					?objp4 <http://www.w3.org/2000/01/rdf-schema#range> <'.$target.'>.
					
				}';
				*/
		$q = ' 	SELECT DISTINCT * WHERE {
					
					?objp1 <http://www.w3.org/2000/01/rdf-schema#range> <'.$target.'>.
					
				}';
		
		//<'.$objectproperty.'> <http://www.w3.org/2000/01/rdf-schema#range> ?range
		
		echo "QUER: ".$q."<br>";
        $classes = $store_Mysql->query($q, 'rows');
		
		
		
		var_dump($classes);
		
		
		//$this->load->view('header');
		//$this->load->view('ontology/view', $data);
		//$this->load->view('footer');
	}
	
		

	

}
