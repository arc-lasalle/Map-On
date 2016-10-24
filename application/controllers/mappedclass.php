<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Mappedclass extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		$this->load->model("Mappedclass_model", "mappedclass");
		$this->load->model("Workspaces_model", "workspaces");	
		$this->load->model("Prefix_model", "prefix");	
		$this->load->model("Ontology_model", "ontology");	
		$this->load->model("Datasource_model", "datasource");	
		$this->load->model("Mappingspace_model", "mappingspace");			
		$this->load->model("Mapping_model", "mapping");
		$this->load->model("Mappedobjectproperty_model", "mappedobjectproperty");
		$this->load->model("Mappeddataproperty_model", "mappeddataproperty");
		//$this->load->model("Log_model", "mapon_log");

		if ( !$this->ion_auth->logged_in() ){
			redirect('auth/login', 'refresh');
		}
		

	}
		
	public function index()
	{
	}
	
		
	public function view($datasource_id, $mappingspace_id, $mappedclass_id)
	{
		$this->maponrouting->set( $datasource_id, $mappingspace_id, $mappedclass_id );

		$data["mappedclass_id"] = $mappedclass_id;
		$data["mappingspace_id"] = $mappingspace_id;
		$data["datasource_id"] = $datasource_id;
		
		$row = $this->mappingspace->getMappingspace($mappedclass_id);
		
		$data["name"] = (count($row) > 0) ? $row[0]->name: "";
		$data["mclasses"] = $this->mappedclass->getMappedclasses($mappedclass_id);
		
		$this->load->view('header');
		$this->load->view('mappingspace/view', $data);
		$this->load->view('footer');
	}
	
	public function createnew($datasource_id, $mappingspace_id, $mappedclass_id = 0)
	{	
		
		$this->maponrouting->set( $datasource_id, $mappingspace_id, $mappedclass_id );
		$data["routes"] = $this->maponrouting->get();


		//$data["datasource_id"] = $datasource_id;
		//$data["mappingspace_id"] = $mappingspace_id;
		//$data["mappedclass_id"] = $mappedclass_id;

		//$data["graph"] = $this->graph($datasource_id, $mappedclass_id, true);

		$data2["mapping_graph"] = $this->_getGraphData( $datasource_id, $mappedclass_id );
		$data["graph"] = $this->load->view('mappedclass/graph', $data2, true);



		//For the bread crumb
		$datasource = $this->datasource->getDatasource($datasource_id);
		$mapspace = $this->mappingspace->getMappingspace($mappingspace_id);
		
		$head["breadcrumb"][] = array("name" => "Data source", "link" => "datasource");
		$head["breadcrumb"][] = array("name" => $datasource[0]->name, "link" => "datasource/view/".$datasource_id);
		$head["breadcrumb"][] = array("name" => $mapspace[0]->name, "link" => "mappingspace/graph/".$datasource_id."/".$mappingspace_id);
		$data['db_type'] = $datasource[0]->type;
		
		$ontology_id = $this->datasource->getOntology($datasource_id);

		if($mappedclass_id == 0) {
			//Create a new mapped class
		
			$head["breadcrumb"][] = array("name" => "New mapping", "link" => "mappedclass/createnew/".$datasource_id."/".$mappingspace_id);
			
		} else {
			///edit a mapped class
			$row = $this->mappedclass->getMappedclass($mappedclass_id);
			
			$data["class"] = (count($row) > 0) ? $row[0]->class: "";
			$data["table"] = (count($row) > 0) ? $row[0]->mappedtablecolumn: "";
			$data["sql"] = (count($row) > 0) ? $row[0]->sql: "";
			$data["uri"] = (count($row) > 0) ? $row[0]->uri: "";

			$data["class"] = $this->prefix->getQName($data["class"], $ontology_id);

			$head["breadcrumb"][] = array("name" => "Edit mapping", "link" => "mappedclass/createnew/".$datasource_id."/".$mappingspace_id."/".$mappedclass_id);

			//$head["logs"] = $this->mapon_log->get("mappedclass", $mappedclass_id, 15);

			$head["logs"] = $this->maponlog->get( 15 );
		}

		$data['ontology_layout'] = $this->ontology->getOntologyLayout( $datasource_id );
		$data['dbgraph_layout'] = $this->datasource->getDatasourceLayout( $datasource_id );
		$data['prefixes'] = json_encode( $this->prefix->getPrefixes( $ontology_id ) );

		$this->load->view('header_s', $head);
		$this->load->view('mappedclass/createnew', $data);
		//$this->load->view('footer_s');
	}


	
	public function createnew_post()
	{
		
	
		$class = $this->input->post('input_class');
		$mappedtablecolumn = $this->input->post('input_table');
		$sql = $this->input->post('input_sql');
		$uri = $this->input->post('input_uri');
		$mappedclass_id = $this->input->post('mappedclass_id');
		$datasource_id = $this->input->post('datasource_id');
		$mappingspace_id = $this->input->post('mappingspace_id');
		
		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			redirect('/mappingspace/graph/'.$datasource_id.'/'.$mappingspace_id);
		}
		
		
		$ontology_id = $this->datasource->getOntology($datasource_id);
		$class = $this->prefix->getURI($class, $ontology_id);
			
		if($mappedclass_id == 0) {
			///Creating a new one
			$user_id = 1;
			
			//$this->mapon_log->write("Mapping created: <strong>".$this->input->post('input_class')."</strong>", "new", "mappingspace", $mappingspace_id);
			
			$mappedclassid = $this->mappedclass->add($class, $sql, $uri, $user_id, $mappingspace_id, $mappedtablecolumn);
			
			//$this->mapon_log->write("Mapping created: <strong>".$this->input->post('input_class')."</strong>", "new", "mappedclass", $mappedclassid);

			$this->maponlog->add( "new", "Mapping created: <strong>".$this->input->post('input_class')."</strong>" );

			
			
		} else {
			//editing an old one
			
			$row = $this->mappedclass->getMappedclass($mappedclass_id);
			$from = $this->prefix->getQName($row[0]->class, $ontology_id);
			$actionlog = "";
			
			if($from !== $this->input->post('input_class'))
				$actionlog = $actionlog.". Class: <strong>".$this->input->post('input_class')."</strong>";
			if($row[0]->mappedtablecolumn !== $this->input->post('input_table'))
				$actionlog = $actionlog.". Table: <strong>".$this->input->post('input_table')."</strong>";
			if($row[0]->sql !== $this->input->post('input_sql'))
				$actionlog = $actionlog.". SQL: <strong>".$this->input->post('input_sql')."</strong>";
			if($row[0]->uri !== $this->input->post('input_uri'))
				$actionlog = $actionlog.". URI: <strong>".$this->input->post('input_uri')."</strong>";
				
			//$this->mapon_log->write("Mapping modified: <strong>".$from."</strong>".$actionlog, "edit", "mappingspace", $mappingspace_id);
			//$this->mapon_log->write("Mapping modified: <strong>".$from."</strong>".$actionlog, "edit", "mappedclass", $mappedclass_id);
			$this->maponlog->add( "edit", "Mapping modified: <strong>".$from."</strong>".$actionlog );
			
			$mappedclassid = $this->mappedclass->update($mappedclass_id, $class, $sql, $uri, $mappedtablecolumn);
		
			//Udpating the mappedclasses which are the domain of the object properties which range is mappingspaceid
			
			$objproperties = $this->mappedobjectproperty->getMappedobjectpropertiesbyRange($mappedclass_id);
			
			foreach ($objproperties  as $objprop) {
				
				//Updating the URI of the object property
				$this->mappedobjectproperty->update($objprop->id, $objprop->objectproperty, $uri, $objprop->domainId, $mappedclass_id);
				
				//Updating the domain class just in case the table had changed.
				$classdomain = $this->mappedclass->getMappedclass($objprop->domainId);
		
				$sql = $this->mapping->generateSQL($classdomain[0]->mappedtablecolumn, $objprop->domainId, $datasource_id);
			
				$this->mappedclass->update($classdomain[0]->id, $classdomain[0]->class, $sql, $classdomain[0]->uri, $classdomain[0]->mappedtablecolumn);
			}
		}
		
		redirect('/mappingspace/graph/'.$datasource_id.'/'.$mappingspace_id);
	}
	
    /*
	public function graph($datasource_id, $mappedclass_id, $toText = false)
	{
		$datalist = $this->datasource->getDatasource($datasource_id);
		$data["datasource"] = $datalist[0];
		$data["ontologyName"] = $this->ontology->getOntology($data["datasource"]->ontology_id)->name;

		$data["datasource_id"] = $datasource_id;	
		$data["mappedclass_id"] = $mappedclass_id;	
		
		//$data["tables"] = $this->datasource->getTables( $datasource_id );

		$mappedclass = $this->mappedclass->getMappedclass($mappedclass_id);
		
		$data["class"] = (count($mappedclass) > 0) ? $this->prefix->getQName($mappedclass[0]->class, $data["datasource"]->ontology_id): "";
		$data["table"] = (count($mappedclass) > 0) ? $mappedclass[0]->mappedtablecolumn: "";

		//foreach($data["tables"] as $row) {
		//	$data["columns"][$row->id] = $this->datasource->getColumns($row->id);
		//}
		
		$tablesonList = $this->mappedclass->getTableson($mappedclass_id);
		$data["tableson"] = array();
		
		foreach($tablesonList as $row) {
			$data["tableson"][$row->tableid] = "1";
		}

		$layoutList = $this->mappedclass->getLayout($mappedclass_id);
		$data["layout"] = array();
		
		foreach($layoutList as $row) {
			$data["layout"][$row->nodeid] = array("layoutX" => $row->layoutX, "layoutY" => $row->layoutY );
		}
			
		if(!$toText) {
			$this->load->view('header_s');
			$this->load->view('mappedclass/graph', $data);
			$this->load->view('footer_s');
		} else {
			return $this->load->view('mappedclass/graph', $data, true);
		}
	}*/

	public function _getGraphData ( $datasource_id, $mappedclass_id ) {

		// DATASOURCE - TABLES & COLUMNS

		$data["tables"] = $this->datasource->getTableTree( $datasource_id );

		$enabledTables = $this->mappedclass->getTableson( $mappedclass_id, true );

        $enabled_tables = [];
		foreach( $enabledTables as $row ) $enabled_tables[] = strtolower($row->tableid);

		foreach( $data["tables"] as $table ) {
			$table->enabled = in_array( strtolower( $table->name ), $enabled_tables);
			//$table->enabled = false;
		}


		return $data;
	}



	public function edit($datasource_id, $mappingspace_id, $mappedclass_id)
	{		
		$data["mappedclass_id"] = $mappedclass_id;
		$data["datasource_id"] = $datasource_id;
		$data["mappingspace_id"] = $mappingspace_id;
		
		$row = $this->mappedclass->getMappedclass($mappedclass_id);
		
		$data["class"] = (count($row) > 0) ? $row[0]->class: "";
		$data["table"] = (count($row) > 0) ? $row[0]->mappedtablecolumn: "";
		$data["sql"] = (count($row) > 0) ? $row[0]->sql: "";
		$data["uri"] = (count($row) > 0) ? $row[0]->uri: "";
		
		$ontology_id = $this->datasource->getOntology($datasource_id);
		$data["class"] = $this->prefix->getQName($data["class"], $ontology_id);

		
		$this->load->view('header');
		$this->load->view('mappedclass/edit', $data);
		$this->load->view('footer');
	}

	public function move( $mappedclass_id, $new_mappingspace_id ) {
		$this->mappedclass->move( $mappedclass_id, $new_mappingspace_id );

		redirect($_SERVER['HTTP_REFERER']);
	}
	
	
	public function edit_post()
	{
		$class = $this->input->post('input_class');
		$mappedtablecolumn = $this->input->post('input_table');
		$sql = $this->input->post('input_sql');
		$uri = $this->input->post('input_uri');
		$datasource_id = $this->input->post('datasource_id');
		$mappingspace_id = $this->input->post('mappingspace_id');
		$mappedclass_id = $this->input->post('mappedclass_id');
				
		$ontology_id = $this->datasource->getOntology($datasource_id);
		
		$row = $this->mappedclass->getMappedclass($mappedclass_id);

		//$this->mapon_log->write("mapping modified from ".$this->prefix->getQName($row[0]->class, $ontology_id)." to ".$class, "mappingspace", $mappingspace_id);
		
		$this->maponlog->add( "edit", "mapping modified from ".$this->prefix->getQName($row[0]->class, $ontology_id)." to ".$class );

		$class = $this->prefix->getURI($class, $ontology_id);
		
		//Updating the mapped class
		$mappingspaceid = $this->mappedclass->update($mappedclass_id, $class, $sql, $uri, $mappedtablecolumn);
		
		//Updating the mapped classes which have an object property pointing to mappingspaceid;
		
		
		

		//redirect('/mappingspace/graph/'.$datasource_id.'/'.$mappingspace_id);
	}
	
	
	public function delete($datasource_id, $mappingspace_id, $mappedclass_id)
	{		
		$ontology_id = $this->datasource->getOntology($datasource_id);
		$row = $this->mappedclass->getMappedclass($mappedclass_id);
		$from = $this->prefix->getQName($row[0]->class, $ontology_id);
		
		
		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			redirect('/mappingspace/graph/'.$datasource_id.'/'.$mappingspace_id);
		}
		
		
		$this->mappedclass->delete($mappedclass_id);

		//$this->mapon_log->write("Mapping deleted: <strong>".$from."</strong>", "delete", "mappingspace", $mappingspace_id);
		//$this->mapon_log->write("Mapping deleted: <strong>".$from."</strong>", "delete", "mappedclass", $mappedclass_id);

		$this->maponlog->add( "delete", "Mapping deleted: <strong>".$from."</strong>" );
		
		redirect('/mappingspace/graph/'.$datasource_id.'/'.$mappingspace_id);
	}

	//////////////////////////////
	
	public function expand($datasource_id, $mappingspace_id, $mappedclass_id)
	{
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}
		
		$data["mappedclass_id"] = $mappedclass_id;
		$data["datasource_id"] = $datasource_id;
		$data["mappingspace_id"] = $mappingspace_id;
		
		$datalist = $this->datasource->getDatasource($datasource_id);
		$mapspace = $this->mappingspace->getMappingspace($mappingspace_id);

		$data["datasource"] = $datalist[0];
		
		$data["ontology_id"] = $data["datasource"]->ontology_id;
		
		$data["ontology"] = $this->ontology->getOntology($data["datasource"]->ontology_id);
		
		$row = $this->mappedclass->getMappedclass($mappedclass_id);

		$source = $row[0]->class;
		
		$data["root"] = $this->prefix->getQName($source, $data["datasource"]->ontology_id);
		
		$head["breadcrumb"][] = array("name" => "Data source", "link" => "datasource");
		$head["breadcrumb"][] = array("name" => $data["datasource"]->name, "link" => "datasource/view/".$datasource_id);
		$head["breadcrumb"][] = array("name" => $mapspace[0]->name, "link" => "mappingspace/graph/".$datasource_id."/".$mappingspace_id);
		$head["breadcrumb"][] = array("name" => "Expand mapping: ".$data["root"], "link" => "mappedclass/expand/".$datasource_id."/".$mappingspace_id."/".$mappedclass_id);

		
		$this->load->view('header_s', $head);
		$this->load->view('mappedclass/expand', $data);
		$this->load->view('footer_s');
	}


	public function expand_post()
	{
		$datasource_id = $this->input->post('datasource_id');
		$mappingspace_id = $this->input->post('mappingspace_id');
		$mappedclass_id = $this->input->post('mappedclass_id');
		$selected_path = $this->input->post('input_select_path');
		
				
		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			redirect('/mappingspace/graph/'.$datasource_id.'/'.$mappingspace_id);
		}
		
		
		$user_id = 1;
		
		$ontology_id = $this->datasource->getOntology($datasource_id);
		$basic_uri = $this->datasource->getBasicUri($datasource_id); 

		$row = $this->mappedclass->getMappedclass($mappedclass_id);

				
		$paths = explode('|', $selected_path);
		$domain_id = $mappedclass_id;
		$target_id = 0;
		
		var_dump($paths);
		echo "<br>------------<br>";
		
		/////////////////////////////
		// We start with the cell 2 which has the first range class.
		//
		for($i = 2; $i < count($paths); $i+=2) {
			echo "Range class ".$paths[$i]."<br>";
			echo "object property ".$paths[$i-1]."<br>";
			echo "class<br>";
					
			$target = $this->prefix->getURI($paths[$i], $ontology_id);
			
			$sql = $this->mapping->generateSQL($row[0]->mappedtablecolumn, $domain_id, $datasource_id);
			
			$uri = $this->mapping->generateURI($datasource_id, $paths[$i], $row[0]->mappedtablecolumn, $basic_uri, $ontology_id); 
			
			$target_id = $this->mappedclass->add($target, $sql, $uri, $user_id, $mappingspace_id, $row[0]->mappedtablecolumn);

			echo "obj<br>";
			
			$objectproperty = $this->prefix->getURI($paths[$i-1], $ontology_id);
			
			$uri = $this->mapping->generateURI($datasource_id, $target, $row[0]->mappedtablecolumn, $basic_uri, $ontology_id); 

			
			echo "uri: ".$uri."<br>";
			$this->mappedobjectproperty->add($objectproperty, $uri, $domain_id, $target_id);

		
			
			//////
			// Once the  object property is added, we are going to re-generate the SQL for the domain class, it might be changed.
			$classdomain = $this->mappedclass->getMappedclass($domain_id);
			
			$sql = $this->mapping->generateSQL($classdomain[0]->mappedtablecolumn, $domain_id, $datasource_id);
				
			$this->mappedclass->update($classdomain[0]->id, $classdomain[0]->class, $sql, $classdomain[0]->uri, $classdomain[0]->mappedtablecolumn);
			
			$domain_id = $target_id;
		}
		
		redirect('/mappingspace/graph/'.$datasource_id.'/'.$mappingspace_id);
	}
	
	public function storepositions()
	{	
		echo "storepositions: ";
		
		
		$mappedclass_id = $this->input->post('mappedclass_id');
		$nodeid = $this->input->post('nodeid');
		$layoutX = $this->input->post('layoutX');
		$layoutY = $this->input->post('layoutY');
		
				
		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if (!$this->ion_auth->in_group('guest')){
					
			$this->mappedclass->updatePosition($mappedclass_id, $nodeid, $layoutX, $layoutY);
		
			echo $mappedclass_id." ".$nodeid." ".$layoutX." ".$layoutY." ";
		}
	}	
	
	public function storetableson()
	{	
		$mappedclass_id = $this->input->post('mappedclass_id');
		$tableid = $this->input->post('tableid');
		$status = $this->input->post('onoff');
		
		if (!$this->ion_auth->in_group('guest')){
			if($status == "true") {
				$this->mappedclass->insertTableson($mappedclass_id, $tableid);
				
			} else {
				//if the status is false then we insert the tableid in the table
				$this->mappedclass->deleteTableson($mappedclass_id, $tableid);
			}	
		}
	}	
}

