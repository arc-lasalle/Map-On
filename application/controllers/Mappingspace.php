<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Mappingspace extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		$this->load->model("Mappingspace_model", "mappingspace");
		$this->load->model("Mappedclass_model", "mappedclass");
		$this->load->model("Mappeddataproperty_model", "mappeddataproperty");
		$this->load->model("Mappedobjectproperty_model", "mappedobjectproperty");
		$this->load->model("Prefix_model", "prefix");
		$this->load->model("Datasource_model", "datasource");
		$this->load->model("Ontology_model", "ontology");
		$this->load->model("workspaces_model", "workspaces_model");
		//$this->load->model("Log_model", "mapon_log");

		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}

	}
		
	public function index()
	{
		$this->show(1);
	}
		
	public function graph($datasource_id, $mappedspace_id)
	{
		$this->maponrouting->set( $datasource_id, $mappedspace_id );
		$data["routes"] = $this->maponrouting->get();
		
		
		$datalist = $this->datasource->getDatasource($datasource_id);
		$data["datasource"] = $datalist[0];
		$maplist = $this->mappingspace->getMappingspace($mappedspace_id);
		$data["mappingspace"] =  $maplist[0];
		$data["mappingspaces"] = $this->mappingspace->getMappingspaces($datasource_id);

		$data["ontologyName"] = $this->ontology->getOntology($data["datasource"]->ontology_id)->name;
		
		
		
		$ontology_id = $this->datasource->getOntology($datasource_id);



		$mclasses = $this->mappedclass->getMappedclasses($mappedspace_id);
		$data["mappedclass"] = [];
		foreach($mclasses as $row) {
			$data["mappedclass"][] = $this->graphMappedClass($ontology_id, $datasource_id, $mappedspace_id, $row->id);
		}

		
		$data["mapping_graph"] = $this->_getGraphData( $datasource_id, $mappedspace_id, $ontology_id );


		
		$head["logs"] = $this->maponlog->get( 15 );


		
		//////////////////////////////////////////
		//	
		$head["breadcrumb"][] = array("name" => "Data source", "link" => "datasource");
		$head["breadcrumb"][] = array("name" => $data["datasource"]->name, "link" => "datasource/view/".$datasource_id);
		$head["breadcrumb"][] = array("name" => $data["mappingspace"]->name, "link" => "mappingspace/graph/".$datasource_id."/".$mappedspace_id);

		$data['ontology_layout'] = $this->ontology->getOntologyLayout( $datasource_id );
		$data['dbgraph_layout'] = $this->datasource->getDatasourceLayout( $datasource_id );
		$data["tables"] = $this->datasource->getTableTree( $datasource_id );
		$data["prefixes"] = json_encode( $this->prefix->getPrefixes( $ontology_id ) );

		$this->load->view('header_s', $head);
		$this->load->view('mappingspace/graph', $data);
		//$this->load->view('footer_s');
	}

	public function _getGraphData ( $datasource_id, $mappingspace_id, $ontology_id ) {

        // LAYOUT
		$layout = $this->mappingspace->getLayout( $mappingspace_id, true );
        //$layout = array();
        //$mappingSpaceLayout = $this->mappingspace->getLayout( $mappingspace_id );
        //foreach( $mappingSpaceLayout as $row ) $layout[strtolower($row->nodeid)] = $row;


		// ONTOLOGY - CLASSES with ObjectProperties and DataProperties.
        $store_Mysql = $this->workspaces_model->connect_workspace( "ontology_".$ontology_id );
        $enabled_tables = array();
        $data["data_properties"] = array();

        $data["classes"] = $this->mappedclass->getMappedclasses( $mappingspace_id );

        foreach( $data["classes"] as $i => $classNode ) {

            $annotation = $this->ontology->getAnnotationbyClass( $store_Mysql, $classNode->class );
            $classNode->description = (count($annotation) > 0) ? $annotation[0]["comment"] : "";
            $classNode->uri = $classNode->class;
            $classNode->qname = $this->prefix->getQName($classNode->class, $ontology_id);

            unset( $classNode->layoutX );
			unset( $classNode->layoutY );

            $qn = strtolower($classNode->qname);
            if ( isset($layout[ $qn ]) ) {
                $classNode->layoutX = $layout[ $qn ]->layoutX;
                $classNode->layoutY = $layout[ $qn ]->layoutY;
            }

            $classNode->objectProperties = $this->mappedobjectproperty->getMappedobjectproperties( $classNode->id );

            foreach( $classNode->objectProperties as $op ) {

                foreach( $data["classes"] as $range ) {

                    if ( $range->class == $op->target ) {
                        $range->objectProperty = new stdClass();
                        $range->objectProperty->domainUri = $classNode->uri;
                        $range->objectProperty->domainQname = $classNode->qname;
                        $range->objectProperty->uri = $op->objectproperty;
                        $range->objectProperty->qname = $this->prefix->getQName( $op->objectproperty, $ontology_id );
                    }
                }

            }

            $classNode->dataProperties = $this->mappeddataproperty->getMappeddataproperties( $classNode->id );

            foreach( $classNode->dataProperties as $dp ) {


                $dp->domainUri = $classNode->uri;
                $dp->domainQname = $classNode->qname;
                $dp->uri = $dp->dataproperty;
                $dp->qname = $this->prefix->getQName($dp->dataproperty, $ontology_id);

                $qn = strtolower($dp->qname);
                if ( isset($layout[ $qn ]) ) {
                    $dp->layoutX = $layout[ $qn ]->layoutX;
                    $dp->layoutY = $layout[ $qn ]->layoutY;
                }

                $arr = explode ( ".", $dp->value );
                if( count($arr) == 2 ) $dp->rangeTable = $arr[0]."_".$arr[1];

                $data["data_properties"][] = $dp;

            }


            // Link Class with Table
            $classNode->tableLink = "";

            $arr = explode ( "->", $classNode->mappedtablecolumn );

            if( count($arr) == 2 ) {
                $classNode->tableLink = $arr[0]."_".$arr[1];
                $enabled_tables[] = strtolower( $arr[0] ); //to know if a table has been mapped or not.
            }

            $sqlTables = $this->getTablesfromSQL( $classNode->sql );
            foreach( $sqlTables as $table ) $enabled_tables[] = strtolower($table);
            
        }


        // DATASOURCE - TABLES & COLUMNS

		$data["tables"] = $this->datasource->getTableTree( $datasource_id, $layout );
        
		foreach( $data["tables"] as $i => $table ) {
			$table->enabled = in_array( strtolower( $table->name ), $enabled_tables);
		}


        return $data;
	}
	
	//This function retrieves the view of a mapped class for a graph view
	private function graphMappedClass($ontology_id, $datasource_id, $mappedspace_id, $mappedclass_id) 
	{
		$subdata["mappedclass_id"] = $mappedclass_id;
		$subdata["datasource_id"] = $datasource_id;
		$subdata["mappedspace_id"] = $mappedspace_id;

		$row = $this->mappedclass->getMappedclass($mappedclass_id);
		
		$subdata["class"] = (count($row) > 0) ? $this->prefix->getQName($row[0]->class, $ontology_id): "";
		$subdata["table"] = (count($row) > 0) ? str_replace("->", ".", $row[0]->mappedtablecolumn): "";
		$subdata["sql"] = (count($row) > 0) ? $row[0]->sql: "";
		$subdata["uri"] = (count($row) > 0) ? $row[0]->uri: "";
		$subdata["dataproperties"] = $this->mappeddataproperty->getMappeddataproperties($mappedclass_id);
		$subdata["objectproperties"] = $this->mappedobjectproperty->getMappedobjectproperties($mappedclass_id);

		// Replace only for visual aspects. Not necessary.
		$subdata["sql"] = str_replace("SELECT", "<br>SELECT<br>&nbsp;&nbsp;", $subdata["sql"]);
		$subdata["sql"] = str_replace(",", ",<br>&nbsp;&nbsp;", $subdata["sql"]);
		$subdata["sql"] = str_replace("FROM", "<br>FROM<br>&nbsp;&nbsp;", $subdata["sql"]);
		$subdata["sql"] = str_replace("AND", "AND<br>&nbsp;&nbsp;", $subdata["sql"]);

		$store_Mysql = $this->workspaces_model->connect_workspace("ontology_".$ontology_id);		
		$row = $this->ontology->getAnnotationbyClass($store_Mysql, $row[0]->class);
		$subdata["description"] = (count($row) > 0) ? $row[0]["comment"]: "";
			
		foreach($subdata["dataproperties"] as $obj) {
			$obj->dataproperty = $this->prefix->getQName($obj->dataproperty, $ontology_id);
		}
		foreach($subdata["objectproperties"] as $obj) {
			$row = $this->ontology->getAnnotationbyClass($store_Mysql, $obj->target);
			$subdata["targetDescription"][$obj->id] = (count($row) > 0) ? $row[0]["comment"]: "";
			
			$obj->target = $this->prefix->getQName($obj->target, $ontology_id);
		}
		$subdata["mappingspaces"] = $this->mappingspace->getMappingspaces($datasource_id);

		return $this->load->view('mappedclass/viewbox', $subdata, true);
	}

	//
	/////////////////////////////////////////////////////////////////////
	
	public function show($datasource_id)
	{

		$data["mpsaces"] = $this->mappingspace->getMappingspaces($datasource_id);
		$data["datasource_id"] = $datasource_id;
		
		$this->load->view('header');
		$this->load->view('mappingspace/index', $data);
		$this->load->view('footer');
	}
		
	public function view($datasource_id, $mappedspace_id)
	{
		$data["mappedspace_id"] = $mappedspace_id;
		$data["datasource_id"] = $datasource_id;
		
		$row = $this->mappingspace->getMappingspace($mappedspace_id);
		
		$data["name"] = (count($row) > 0) ? $row[0]->name: "";	
		$data["mclasses"] = $this->mappedclass->getMappedclasses($mappedspace_id);
		$ontology_id = $this->datasource->getOntology($datasource_id);

		foreach($data["mclasses"] as $row) {
			$row->class = $this->prefix->getQName($row->class, $ontology_id);
			$data["dataproperties"][$row->id] = $this->mappeddataproperty->getMappeddataproperties($row->id);
			$data["objectproperties"][$row->id] = $this->mappedobjectproperty->getMappedobjectproperties($row->id);
			
			foreach($data["objectproperties"][$row->id] as $obj) {
				$obj->target = $this->prefix->getQName($obj->target, $ontology_id);
			}
		}
		
		$this->load->view('header');
		$this->load->view('mappingspace/view', $data);
		$this->load->view('footer');
	}
	
	public function createnew($datasource_id)
	{		
		$data["datasource_id"] = $datasource_id;
		
		$this->load->view('header');
		$this->load->view('mappingspace/createnew', $data);
		$this->load->view('footer');
	}
	
	
	public function createnew_post()
	{
		$name = $this->input->post('input_name');
		$datasource_id = $this->input->post('datasource_id');
		$user_id = 1;
		
		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			redirect('/datasource/view/'.$datasource_id);
		}
		
		$mappingspace_id = $this->mappingspace->add($name, $user_id, $datasource_id);
	
		//Copying the layout of the data source to the mapping.
		$data["tables"] = $this->datasource->getTables($datasource_id);
		
		foreach($data["tables"] as $row) {
			$this->mappingspace->updatePosition($mappingspace_id, $row->name, $row->layoutX, $row->layoutY);
		}
		
		redirect('/datasource/view/'.$datasource_id);
	}

	
	public function edit_post()
	{
		$name = $this->input->post('input_edit_name');
		$mappingspace_id = $this->input->post('mappingspace_id');
		$datasource_id = $this->input->post('datasource_id');
		
		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			redirect('/datasource/view/'.$datasource_id);
		}
		
		$mappingspaceid = $this->mappingspace->update($mappingspace_id, $name);
	
		//echo "Name: ".$name." Id: ".$id."<br>";
		redirect('/datasource/view/'.$datasource_id);
	}
	
	
	public function delete($datasource_id, $mappingspace_id)
	{		
		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			redirect('/datasource/view/'.$datasource_id);
		}
		
		$this->mappingspace->delete($mappingspace_id);
		
		redirect('/datasource/view/'.$datasource_id);
	}
	
	///////////////////////////////////////////////////////////////
	
	private function getTablesfromSQL($sql) 
	{
		if( ($pos = strpos($sql, "FROM", 0)) !== false) {
			
			$pos2 = strpos($sql, "WHERE", $pos+1);
			
			$tables = substr($sql, $pos+5, $pos2-$pos-5);
			$tables = str_replace(" ", "", $tables);
			
			return explode (",", $tables);
		}
		return array();
	}
	
	///////////////////////////////////////////////////////////////
	
	public function storepositions()
	{
        if ( $this->ion_auth->in_group('guest') ) return;

		$mappingspace_id = $this->input->post('mappingspaceid');
		$nodeid = $this->input->post('nodeid');
		$layoutX = $this->input->post('layoutX');
		$layoutY = $this->input->post('layoutY');
        $unpin = $this->input->post('unpin');

        if ( $unpin !== null ) {
            $this->mappingspace->deletePosition($mappingspace_id, $nodeid);
        } else {
            $this->mappingspace->updatePosition($mappingspace_id, $nodeid, $layoutX, $layoutY);
        }


	}
	
	//////////////////////////////
	// Search for a mapping (class name and table->column name) and returns them for the suggestion list
	public function searchmapping() 
	{
		$substring = $this->input->post('string');
		$mappingspace_id = $this->input->post('mappingspace_id');
		$datasource_id = $this->input->post('datasource_id');
		$ontology_id = $this->datasource->getOntology($datasource_id);
		$found = false;

		$mclasses = $this->mappedclass->getMappedclasses($mappingspace_id);
		foreach($mclasses as $row) {
			$row->class = $this->prefix->getQName($row->class, $ontology_id);
			$pos1 = stripos($row->class, $substring);
			$pos2 = stripos($row->mappedtablecolumn, $substring);
			if($pos1 !== false || $pos2 !== false || $substring == "") {
				$strPrintValue = str_ireplace($substring, "<strong>".$substring."</strong>", $row->class. " -- ".$row->mappedtablecolumn);
				
				echo '<span style="font-size:11px; color: rgb(79, 98, 40); cursor: pointer; cursor: hand;" onclick="add_search_box_Mapping(\''.$row->class.'\')">'.$strPrintValue.'</span><br /><br />';
				$found = true;
			}
			
		}

		if ( !$found ) {
			echo 'Nothing found.';
		}

	}
}

