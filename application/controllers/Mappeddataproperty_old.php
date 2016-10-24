<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Mappeddataproperty extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		$this->load->model("Mappeddataproperty_model", "mappeddataproperty");		
		$this->load->model("Mappedclass_model", "mappedclass");
		$this->load->model("Datasource_model", "datasource");
		$this->load->model("Mappingspace_model", "mappingspace");
		$this->load->model("Prefix_model", "prefix");	
		$this->load->model("Ontology_model", "ontology");	
		$this->load->model("workspaces_model", "workspaces_model");
		//$this->load->model("Log_model", "mapon_log");
		$this->load->model("Mapping_model", "mapping");
		$this->load->model("Mappedobjectproperty_model", "mappedobjectproperty");
	}
		
	public function index()
	{
	}

	public function addnew($datasource_id, $mappingspace_id, $mappedclass_id, $dataproperty_id = 0)
	{		
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}
		
		$data["datasource_id"] = $datasource_id;
		$data["mappingspace_id"] = $mappingspace_id;
		$data["mappedclass_id"] = $mappedclass_id;
		$data["dataproperty_id"] = $dataproperty_id;
		

		$mapClass = $this->mappedclass->getMappedclass($mappedclass_id);

		$data["uriMappedClass"] = (count($mapClass) > 0) ? $mapClass[0]->class: "";		
		$data["mappedtablecolumn"] = (count($mapClass) > 0) ? $mapClass[0]->mappedtablecolumn: "";		
		$arr = explode ("->", $data["mappedtablecolumn"]);
		//echo "generateSQL: ".$input_table." ".$datasource_id ;
		
		$data["value"] = (count($arr) == 2) ? $arr[0].".".$arr[1] : "";

		$data["table"] = $data["mappedtablecolumn"];
		
		$data["type"] = "xsd:string";
		$data["sourcetable_id"] = 0;
		$data["sourcetable_name"] = "";
		
		$row = $this->datasource->getTableByName($arr[0], $datasource_id);

		$types_int = ["int", "number"];
		$types_decimal = ["decimal"];
		$types_string = ["varchar", "varchar2"];


		if(count($row) > 0) {
			$data["sourcetable_id"] = $row[0]->id;
			$data["sourcetable_name"] = $row[0]->name;
			
			$col = $this->datasource->getColumnByName($arr[1], $row[0]->id);

			if ( in_array( $col[0], $types_int ) ) {
				$data["type"] = "xsd:integer";
			} else if ( in_array( $col[0], $types_decimal ) ) {
				$data["type"] = "xsd:decimal";
			} else if ( in_array( $col[0], $types_string ) ) {
				$data["type"] = "xsd:string";
			} else {
				$data["type"] = "xsd:string";
			}

		}

		
		$data["graph"] = $this->graph($datasource_id, $mappingspace_id, $mapClass[0], $dataproperty_id, true);

		
				
		//////////////////////////////////
		//For the bread crumb
		$datasource = $this->datasource->getDatasource($datasource_id);
		$mapspace = $this->mappingspace->getMappingspace($mappingspace_id);
		$ontology_id = $this->datasource->getOntology($datasource_id);
		$data["class"] = $this->prefix->getQName($data["uriMappedClass"], $ontology_id);
			
		$head["breadcrumb"][] = array("name" => "Data source", "link" => "datasource");
		$head["breadcrumb"][] = array("name" => $datasource[0]->name, "link" => "datasource/view/".$datasource_id);
		$head["breadcrumb"][] = array("name" => $mapspace[0]->name, "link" => "mappingspace/graph/".$datasource_id."/".$mappingspace_id);
		$head["breadcrumb"][] = array("name" => $data["class"] , "link" => "mappedclass/createnew/".$datasource_id."/".$mappingspace_id."/".$mappedclass_id);
		
		//
		//////////////////////////////////
		

		if($dataproperty_id == 0) {
			//new data property
			$head["breadcrumb"][] = array("name" => "Add data property", "link" => "mappeddataproperty/addnew/".$datasource_id."/".$mappingspace_id."/".$mappedclass_id);
		} else {
			//edit a data property
			
			$row = $this->mappeddataproperty->getMappeddataproperty($dataproperty_id);
		
			$data["dataproperty"] = (count($row) > 0) ? $row[0]->dataproperty: "";
			$data["value"] = (count($row) > 0) ? $row[0]->value: "";
			$data["type"] = (count($row) > 0) ? $row[0]->type: "";
			
			$data["dataproperty"] = $this->prefix->getQName($data["dataproperty"], $ontology_id);
			
			$head["breadcrumb"][] = array("name" => "Edit data property", "link" => "mappeddataproperty/addnew/".$datasource_id."/".$mappingspace_id."/".$mappedclass_id);
			
			//$head["logs"] = $this->mapon_log->get("mappeddataproperty", $dataproperty_id, 15);
			$head["logs"] = $this->maponlog->get(15);
		}

		$data['ontology_layout'] = $this->ontology->getOntologyLayout( $datasource_id );
		$data['dbgraph_layout'] = $this->datasource->getDatasourceLayout( $datasource_id );
		$data["prefixes"] = json_encode( $this->prefix->getPrefixes( $ontology_id ) );
		
		$this->load->view('header_s', $head);
		$this->load->view('mappeddataproperty/addnew', $data);
		//$this->load->view('footer_s');
	}
	
	
	public function addnew_post()
	{
		$dataproperty_id = $this->input->post('dataproperty_id');
		
		$dataproperty = $this->input->post('input_dataproperty');
		$value = $this->input->post('input_value');
		$type = $this->input->post('input_type');
		
		$datasource_id = $this->input->post('datasource_id');
		$mappingspace_id = $this->input->post('mappingspace_id');
		$mappedclass_id = $this->input->post('mappedclass_id');
		$input_table = $this->input->post('input_table');
		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			redirect('/mappingspace/graph/'.$datasource_id.'/'.$mappingspace_id);
		}
		
		$ontology_id = $this->datasource->getOntology($datasource_id);

		$dataproperty = $this->prefix->getURI($dataproperty, $ontology_id);

		if($dataproperty_id == 0) {
			//add
			//$this->mapon_log->write("Data property created: <strong>".$this->input->post('input_dataproperty')."</strong>", "new", "mappingspace", $mappingspace_id);
			$this->maponlog->add( "new", "Data property created: <strong>".$this->input->post('input_dataproperty')."</strong>" );

			$mappingspaceid = $this->mappeddataproperty->add($dataproperty, $value, $type, $mappedclass_id);

			$sql = $this->mapping->generateSQL($input_table, $mappedclass_id, $datasource_id);
			$this->mappedclass->updateSQL($mappedclass_id, $sql);

			//$this->mapon_log->write("Data property created: <strong>".$this->input->post('input_dataproperty')."</strong>", "new", "mappeddataproperty", $mappingspaceid);
	
		} else {
			//edit
			
			$row = $this->mappedclass->getMappedclass($mappedclass_id);
			$rowdp = $this->mappeddataproperty->getMappeddataproperty($dataproperty_id);

			//$this->mapon_log->write("Data property modified of the <strong>".$this->prefix->getQName($row[0]->class, $ontology_id)."</strong> mapping. From <strong>".$this->prefix->getQName($rowdp[0]->dataproperty, $ontology_id)."</strong> to <strong>".$this->input->post('input_dataproperty')."</strong>", "mappingspace", $mappingspace_id);
			
			$from = $this->prefix->getQName($rowdp[0]->dataproperty, $ontology_id);
			$actionlog = "";
			
			if($from !== $this->input->post('input_dataproperty'))
				$actionlog = $actionlog.". Data property: <strong>".$this->input->post('input_dataproperty')."</strong>";
			if($row[0]->value !== $this->input->post('input_value'))
				$actionlog = $actionlog.". Value: <strong>".$this->input->post('input_value')."</strong>";
			if($row[0]->type !== $this->input->post('input_type'))
				$actionlog = $actionlog.". Type: <strong>".$this->input->post('input_type')."</strong>";
					
			//$this->mapon_log->write("Data property modified: <strong>".$this->input->post('input_dataproperty')."</strong>".$actionlog, "edit", "mappingspace", $mappingspace_id);
			//$this->mapon_log->write("Data property modified: <strong>".$this->input->post('input_dataproperty')."</strong>".$actionlog, "edit", "mappeddataproperty", $dataproperty_id);
			$this->maponlog->add( "edit", "Data property modified: <strong>".$this->input->post('input_dataproperty')."</strong>".$actionlog );

			$mappingspaceid = $this->mappeddataproperty->update($dataproperty_id, $dataproperty, $value, $type);

			$sql = $this->mapping->generateSQL($input_table, $mappedclass_id, $datasource_id);
			$this->mappedclass->updateSQL($mappedclass_id, $sql);
		}
		
		redirect('/mappingspace/graph/'.$datasource_id.'/'.$mappingspace_id);
	}
	
	public function graph($datasource_id, $mappingspace_id, $mappedclass, $dataproperty_id, $toText = false)
	{
		$datalist = $this->datasource->getDatasource($datasource_id);
		$data["datasource"] = $datalist[0];
		$data["ontologyName"] = $this->ontology->getOntology($data["datasource"]->ontology_id)->name;

		$data["datasource_id"] = $datasource_id;	
		$data["dataproperty_id"] = $dataproperty_id;	
		$data["mappedclass_id"] = $mappedclass->id;
		
		$data["mappings"] = array();
		$data["mapTables"] = array();
		$data["mappingsDP"] = array();
		$data["mappedclass"] = array();
		$data["datapropertiesList"] = array();
		$data["tables"] = $this->datasource->getTables($datasource_id);
		
		foreach($data["tables"] as $row) {
			$data["columns"][$row->id] = $this->datasource->getColumns($row->id);
		}
		
		$store_Mysql = $this->workspaces_model->connect_workspace("ontology_".$data["datasource"]->ontology_id);	
		$annot = $this->ontology->getAnnotationbyClass($store_Mysql, $mappedclass->class);
		$data["mappedclassDescription"] = (count($annot) > 0) ? $annot[0]["comment"]: "";
		
		$mappedclass->class = $this->prefix->getQName($mappedclass->class, $data["datasource"]->ontology_id);
		$data["mapClass"] = $mappedclass;
		
		$tablesonList = $this->mappedclass->getTableson($mappedclass->id);
		$data["tableson"] = array();
		foreach($tablesonList as $row) {
			$data["tableson"][$row->tableid] = "1";
		}
		
		$layoutList = $this->mappedclass->getLayout($mappedclass->id);
		
		$data["layout"] = array();
		
		foreach($layoutList as $row) {
			$data["layout"][$row->nodeid] = array("layoutX" => $row->layoutX, "layoutY" => $row->layoutY );
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
			$this->load->view('mappeddataproperty/graph', $data);
			$this->load->view('footer_s');
		} else {
			return $this->load->view('mappeddataproperty/graph', $data, true);
		}
	}
	
	
	
	public function edit($datasource_id, $mappingspace_id, $mappedclass_id, $dataproperty_id)
	{		
		$data["dataproperty_id"] = $dataproperty_id;
		$data["datasource_id"] = $datasource_id;
		$data["mappingspace_id"] = $mappingspace_id;
		$data["mappedclass_id"] = $mappedclass_id;
		
		$row = $this->mappeddataproperty->getMappeddataproperty($dataproperty_id);
		
		$data["dataproperty"] = (count($row) > 0) ? $row[0]->dataproperty: "";
		$data["value"] = (count($row) > 0) ? $row[0]->value: "";
		$data["type"] = (count($row) > 0) ? $row[0]->type: "";

		
		$row = $this->mappedclass->getMappedclass($mappedclass_id);
		$data["uriMappedClass"] = (count($row) > 0) ? $row[0]->class: "";		
		$data["mappedtablecolumn"] = (count($row) > 0) ? $row[0]->mappedtablecolumn: "";		
		
		$arr = explode ("->", $data["mappedtablecolumn"]);
		
		$row = $this->datasource->getTableByName($arr[0], $datasource_id);
		$data["sourcetable_id"] = 0;
		$data["sourcetable_name"] = "";
		
		if(count($row) > 0) {
			$data["sourcetable_id"] = $row[0]->id;
			$data["sourcetable_name"] = $row[0]->name;
		}
		
		$this->load->view('header');
		$this->load->view('mappeddataproperty/edit', $data);
		$this->load->view('footer');
	}
	
	
	public function edit_post()
	{
		$dataproperty = $this->input->post('input_dataproperty');
		$value = $this->input->post('input_value');
		$type = $this->input->post('input_type');
		$datasource_id = $this->input->post('datasource_id');
		$mappingspace_id = $this->input->post('mappingspace_id');
		$dataproperty_id = $this->input->post('dataproperty_id');
		
		redirect('/mappingspace/graph/'.$datasource_id.'/'.$mappingspace_id);
	}
	
	
	public function delete($datasource_id, $mappingspace_id, $id)
	{		
		$ontology_id = $this->datasource->getOntology($datasource_id);
		$row = $this->mappeddataproperty->getMappeddataproperty($id);
		$from = $this->prefix->getQName($row[0]->dataproperty, $ontology_id);
		
		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			redirect('/mappingspace/graph/'.$datasource_id.'/'.$mappingspace_id);
		}
		
		//$this->mapon_log->write("Data property deleted: <strong>".$from."</strong>", "delete", "mappingspace", $mappingspace_id);
		//$this->mapon_log->write("Data property deleted: <strong>".$from."</strong>", "delete", "mappeddataproperty", $id);
		$this->maponlog->add( "delete", "Data property deleted: <strong>".$from."</strong>" );

		$this->mappeddataproperty->delete($id);
		
		redirect('/mappingspace/graph/'.$datasource_id.'/'.$mappingspace_id);
	}
}

