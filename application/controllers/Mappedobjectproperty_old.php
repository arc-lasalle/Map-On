<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Mappedobjectproperty extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		$this->load->model("Mappedobjectproperty_model", "mappedobjectproperty");
		$this->load->model("Mappeddataproperty_model", "mappeddataproperty");		

		$this->load->model("Mappedclass_model", "mappedclass");
		$this->load->model("Prefix_model", "prefix");	
		$this->load->model("Ontology_model", "ontology");	
		
		$this->load->model("Mapping_model", "mapping");
		$this->load->model("Datasource_model", "datasource");	
		$this->load->model("Mappingspace_model", "mappingspace");
		//$this->load->model("Log_model", "mapon_log");

	}
		
	public function index()
	{
	}

	public function addnew($datasource_id, $mappingspace_id, $mappedclassdomain_id)
	{		
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}
		
		$data["datasource_id"] = $datasource_id;
		$data["mappingspace_id"] = $mappingspace_id;
		$data["mappedclassdomain_id"] = $mappedclassdomain_id;
		
		$row = $this->mappedclass->getMappedclass($mappedclassdomain_id);

		$data["uriMappedClass"] = (count($row) > 0) ? $row[0]->class: "";		
		$data["mappedtablecolumn"] = (count($row) > 0) ? $row[0]->mappedtablecolumn: "";		
		
		
		$data["graph"] = $this->graph($datasource_id, $mappingspace_id, $row[0], true);

		
		//////////////////////////////////
		//For the bread crumb
		$datasource = $this->datasource->getDatasource($datasource_id);
		$mapspace = $this->mappingspace->getMappingspace($mappingspace_id);
		$ontology_id = $this->datasource->getOntology($datasource_id);
		$data["class"] = $this->prefix->getQName($data["uriMappedClass"], $ontology_id);
			
		$head["breadcrumb"][] = array("name" => "Data source", "link" => "datasource");
		$head["breadcrumb"][] = array("name" => $datasource[0]->name, "link" => "datasource/view/".$datasource_id);
		$head["breadcrumb"][] = array("name" => $mapspace[0]->name, "link" => "mappingspace/graph/".$datasource_id."/".$mappingspace_id);
		$head["breadcrumb"][] = array("name" => $data["class"] , "link" => "mappedclass/createnew/".$datasource_id."/".$mappingspace_id."/".$mappedclassdomain_id);
		$head["breadcrumb"][] = array("name" => "Add object property", "link" => "mappedobjectproperty/addnew/".$datasource_id."/".$mappingspace_id."/".$mappedclassdomain_id);
	
		//
		//////////////////////////////////

		$data['ontology_layout'] = $this->ontology->getOntologyLayout( $datasource_id );
		$data['dbgraph_layout'] = $this->datasource->getDatasourceLayout( $datasource_id );
		$data["prefixes"] = json_encode( $this->prefix->getPrefixes( $ontology_id ) );

		$this->load->view('header_s', $head);
		$this->load->view('mappedobjectproperty/addnew', $data);
		//$this->load->view('footer_s');
	}
	
	
	public function addnew_post()
	{
		$objectproperty = $this->input->post('input_objectproperty');
		$uri = $this->input->post('input_uri');
		$input_target = $this->input->post('input_target');
		$input_table = $this->input->post('input_table');

		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			redirect('/mappingspace/graph/'.$datasource_id.'/'.$mappingspace_id);
		}
		
		$datasource_id = $this->input->post('datasource_id');
		$mappingspace_id = $this->input->post('mappingspace_id');
		$mappedclassdomain_id = $this->input->post('mappedclassdomain_id');
		$mappedclassrange_id = $input_target;
		$user_id = 1;
		$ontology_id = $this->datasource->getOntology($datasource_id);
		$basic_uri = $this->datasource->getBasicUri($datasource_id); 
		
		echo "input_target :". $input_target."<br>";	
		
		if(strpos($mappedclassrange_id, "New:#:", 0) === 0) {
			//We have to create a new classmap before creating the object property.
			
			$targetQname = str_replace("New:#:", "", $mappedclassrange_id);
			
//			var_dump($mappedclassdomain_id);

			$sql = $this->mapping->generateSQL($input_table, $mappedclassdomain_id, $datasource_id);
			
			$uri = $this->mapping->generateURI($datasource_id, $targetQname, $input_table, $basic_uri, $ontology_id); 
		
			$class = $this->prefix->getURI($targetQname, $ontology_id);
			
			$mappedclassrange_id = $this->mappedclass->add($class, $sql, $uri, $user_id, $mappingspace_id, $input_table);
		}
			
		$objectproperty = $this->prefix->getURI($objectproperty, $ontology_id);
		$mappingspaceid = $this->mappedobjectproperty->add($objectproperty, $uri, $mappedclassdomain_id, $mappedclassrange_id);
		
		//$this->mapon_log->write("Object property created: <strong>".$this->input->post('input_objectproperty')."</strong>", "new", "mappingspace", $mappingspace_id);
		//$this->mapon_log->write("Object property created: <strong>".$this->input->post('input_objectproperty')."</strong>", "new", "mappedclass", $mappedclassdomain_id);
		//$this->mapon_log->write("Object property created: <strong>".$this->input->post('input_objectproperty')."</strong>", "new", "mappedclass", $mappedclassrange_id);
		$this->maponlog->add( "new", "Object property created: <strong>".$this->input->post('input_objectproperty')."</strong>" );



		//////
		// Once the new object property is added, we are going to re-generate the SQL for the domain class, it might be changed.
		$classdomain = $this->mappedclass->getMappedclass($mappedclassdomain_id);
		
		$sql = $this->mapping->generateSQL($classdomain[0]->mappedtablecolumn, $mappedclassdomain_id, $datasource_id);
			
		$this->mappedclass->update($classdomain[0]->id, $classdomain[0]->class, $sql, $classdomain[0]->uri, $classdomain[0]->mappedtablecolumn);

		redirect('/mappingspace/graph/'.$datasource_id.'/'.$mappingspace_id);
	}
	
	
	public function graph($datasource_id, $mappingspace_id, $mappedclass, $toText = false)
	{
		$datalist = $this->datasource->getDatasource($datasource_id);
		$data["datasource"] = $datalist[0];
		$data["ontologyName"] = $this->ontology->getOntology($data["datasource"]->ontology_id)->name;

		$data["datasource_id"] = $datasource_id;
		$data["mappingspace_id"] = $mappingspace_id;
		$data["mappedclass_id"] = $mappedclass->id;
		
		$data["mappings"] = array();
		$data["mapTables"] = array();
		$data["mappingsDP"] = array();
		$data["mappedclass"] = array();
		$data["datapropertiesList"] = array();
		$data["layout"] = array();
		$data["tableson"] = array();
		
		$data["tables"] = $this->datasource->getTables($datasource_id);
		
		foreach($data["tables"] as $row) {
			$data["columns"][$row->id] = $this->datasource->getColumns($row->id);
			//The default layout comes from the data source
			$data["layout"][$row->name] = array("layoutX" => $row->layoutX, "layoutY" => $row->layoutY );
		}
		
		$mappedclass->class = $this->prefix->getQName($mappedclass->class, $data["datasource"]->ontology_id);
		$data["mapClass"] = $mappedclass;
		
		$tablesonList = $this->mappedclass->getTableson($mappedclass->id);
		
		foreach($tablesonList as $row) {
			$data["tableson"][$row->tableid] = "1";
		}
		
		$layoutList = $this->mappedclass->getLayout($mappedclass->id);
		//$data["layout"] = array();
		
		foreach($layoutList as $row) {
			$data["layout"][$row->nodeid] = array("layoutX" => $row->layoutX, "layoutY" => $row->layoutY );
		}
		
		//Object properties
		$data["objectproperties"] = $this->mappedobjectproperty->getMappedobjectproperties($mappedclass->id);
		
		foreach($data["objectproperties"] as $obj) {
			$obj->target = $this->prefix->getQName($obj->target, $data["datasource"]->ontology_id);
			$obj->objectproperty = $this->prefix->getQName($obj->objectproperty, $data["datasource"]->ontology_id);
			
			$row = $this->mappedclass->getMappedclass($obj->targetId);
			
			//Mappings object properties
			$arr = explode ("->", $row[0]->mappedtablecolumn);		
			if(count($arr) == 2) {
				$data["mappings"][$row[0]->id] = $arr[0]."_".$arr[1];
				$data["mapTables"][$arr[0]] = 1;  //to know if a table has been mapped or not.
			} else {
				$data["mappings"][$row[0]->id] = "";
			}
		}
		
		$data["dataproperties"][$mappedclass->id] = $this->mappeddataproperty->getMappeddataproperties($mappedclass->id);
			
		foreach($data["dataproperties"][$mappedclass->id] as $obj) {
			$obj->dataproperty = $this->prefix->getQName($obj->dataproperty, $data["datasource"]->ontology_id);
			
			//a Hash map to insert only a data property node.
			$data["datapropertiesList"][] = array("dp" => $obj->dataproperty, "id" => $obj->id);
			
			//Mappings data proprties
			$arr = explode (".", $obj->value);		
			if(count($arr) == 2) {
				$data["mappingsDP"][] = array("dp" => $obj->dataproperty, "id" => $obj->id, "table" => $arr[0]."_".$arr[1]);
			}
		}
		
		//Mappings object properties
		$arr = explode ("->", $mappedclass->mappedtablecolumn);		
		if(count($arr) == 2) {
			$data["mappings"][$mappedclass->id] = $arr[0]."_".$arr[1];
			$data["mapTables"][$arr[0]] = 1;  //to know if a table has been mapped or not.
		} else {
			$data["mappings"][$mappedclass->id] = "";
		}

		if(!$toText) {
			$this->load->view('header_s');
			$this->load->view('mappedobjectproperty/graph', $data);
			$this->load->view('footer_s');
		} else {
			return $this->load->view('mappedobjectproperty/graph', $data, true);
		}
	}
	
	
	
	
	public function delete($datasource_id, $mappingspace_id, $id)
	{		
	
		$ontology_id = $this->datasource->getOntology($datasource_id);
		$row = $this->mappedobjectproperty->getMappedobjectproperty($id);
		$from = $this->prefix->getQName($row[0]->objectproperty, $ontology_id);
		
		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			redirect('/mappingspace/graph/'.$datasource_id.'/'.$mappingspace_id);
		}
		
		//$this->mapon_log->write("Object property deleted: <strong>".$from."</strong>", "delete", "mappingspace", $mappingspace_id);
		//$this->mapon_log->write("Object property deleted: <strong>".$from."</strong>", "delete", "mappedclass", $row->mappedclassdomain_id);
		//$this->mapon_log->write("Object property deleted: <strong>".$from."</strong>", "delete", "mappedclass", $row->mappedclassrange_id);
		$this->maponlog->add( "delete", "Object property deleted: <strong>".$from."</strong>" );


		$this->mappedobjectproperty->delete($id);
		
		redirect('/mappingspace/graph/'.$datasource_id.'/'.$mappingspace_id);
	}
}

