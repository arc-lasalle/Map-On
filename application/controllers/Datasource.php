<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Datasource extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		$this->load->model("Datasource_model", "datasource");
		$this->load->model("Mappingspace_model", "mappingspace");
		$this->load->model("Ontology_model", "ontology");
		$this->load->model("Mappedclass_model", "mappedclass");
		$this->load->model("Prefix_model", "prefix");
		$this->load->model("Mappedobjectproperty_model", "mappedobjectproperty");

		if ( !$this->ion_auth->logged_in() ){
			redirect( 'auth/login', 'refresh' );
		}

	}

	public function index()
	{

//		$vars['datasources'] = $this->datasource->getDatasources();
		if ( $this->team->connected() ) {
			$vars['datasources'] = $this->datasource->getDatasourcesAndOntologies();
		} else {
			$vars['datasources'] = [];
		}


		$vars["createnew"] = $this->createnew(true);

		$head["breadcrumb"][] = array("name" => "Data source", "link" => "datasource");

		$this->load->view('header_s', $head);
		$this->load->view('datasource/list', $vars);
		$this->load->view('footer_s');
	}

	public function view($datasource_id)
	{

		$this->maponrouting->set( $datasource_id );
		$data["routes"] = $this->maponrouting->get();

		$row = $this->datasource->getDatasource($datasource_id);

		$data["datasource_id"] = $datasource_id;

		$data["name"] = (count($row) > 0) ? $row[0]->name: "";
		$data["sqlfile"] = (count($row) > 0) ? $row[0]->sqlfile: "";
		$data["stringconnection"] = (count($row) > 0) ? $row[0]->stringconnection: "";
		$data["xmlfile"] = (count($row) > 0) ? $row[0]->xmlfile: "";
		$data["basicuri"] = (count($row) > 0) ? $row[0]->basicuri: "";
		$data["ontologyId"] = $row[0]->ontology_id;
		$data["ontologyName"] = $this->ontology->getOntology($row[0]->ontology_id)->name;
		$data["date"] = (count($row) > 0) ? $row[0]->date: "";

		$data["mspaces"] = $this->mappingspace->getMappingspaces($datasource_id);

		$ontology_id = $this->datasource->getOntology($datasource_id);


		///Getting mappings and tables used in the mappings spaces
		foreach($data["mspaces"] as $row) {
			$list = $this->mappedclass->getMappedclasses($row->id);
			$data["mappings"][$row->id] = "";
			$data["tables"][$row->id] = "";

			foreach($list as $rowmp) {
				if (false === strpos($data["mappings"][$row->id], $this->prefix->getQName($rowmp->class, $ontology_id)))
					$data["mappings"][$row->id] = $data["mappings"][$row->id]. "<strong>".$this->prefix->getQName($rowmp->class, $ontology_id)."</strong>, ";

				if (false === strpos($data["tables"][$row->id], $rowmp->mappedtablecolumn))
					$data["tables"][$row->id] = $data["tables"][$row->id]. "<strong>".$rowmp->mappedtablecolumn."</strong>, ";

			}
		}

		$graphData["routes"] = $this->maponrouting->get();
		$graphData["mapping_graph"] = $this->getGraphData( $datasource_id );

		$data["graph"] = $this->load->view('datasource/graph', $graphData, true);



		$head["breadcrumb"][] = array("name" => "Data source", "link" => "datasource");
		$head["breadcrumb"][] = array("name" => $data["name"], "link" => "datasource/view/".$datasource_id);

		$this->load->view('header_s', $head);
		$this->load->view('datasource/view', $data);
		$this->load->view('footer_s');
	}

	public function getGraphData ( $datasource_id ) {
		$data["datasource_id"] = $datasource_id;

		$data["tables"] = $this->datasource->getTables($datasource_id);

		foreach( $data["tables"] as $i => $table ) {

			$table->columns = $this->datasource->getColumns( $table->id );

		}

		return $data;
	}

	public function graph( $datasource_id )
	{
		$graphData["mapping_graph"] = $this->getGraphData($datasource_id);

		$this->load->view('header_s');
		$this->load->view('datasource/graph', $graphData);
		$this->load->view('footer_s');
	}
	
	public function createnew($toText = false)
	{
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}
		if ( $this->team->connected() ) {
			$data['ontologies'] = $this->ontology->getOntologies();
		} else {
			$data['ontologies'] = [];
		}
		if(!$toText) {
			$this->load->view('header');
			$this->load->view('datasource/createnew', $data);
			$this->load->view('footer');
		} else {
			return $this->load->view('datasource/createnew', $data, true);
		}
	}
	
	
	public function createnew_post()
	{
		$name = $this->input->post('input_name');
		
		$basicuri = $this->input->post('input_basicuri');
		$basicuri = trim($basicuri);
		$ontology_id = $this->input->post('input_ontology');
		$file_type = $this->input->post('file_type');
		$user_id = 1;
		
		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			$this->index();
		}

		if ( $name == "" ) {
			$this->session->set_flashdata('error_message', [false, "Invalid datasource name" ]);
			redirect("datasource");
		}

		if ( $basicuri == "" ) {
			$this->session->set_flashdata('error_message', [false, "Invalid Base URI" ]);
			redirect("datasource");
		}

		$last_baseuri_char = $basicuri[strlen($basicuri)-1];
		if ( $last_baseuri_char != "/" && $last_baseuri_char != "#" ) {
			$this->session->set_flashdata('error_message', [false, "The Base URI must end with one of these symbols: '/', '#'" ]);
			redirect("datasource");
		}



		if (is_uploaded_file($_FILES['input_attachment']['tmp_name'])) {

			$file_name = $_FILES['input_attachment']['name'];

			if ( $file_type == "schema" ) {
				// Schema generated by the java application.
				if ( !$this->endsWith($file_name, ".txt") && !$this->endsWith($file_name, ".xml") ) {
					$this->session->set_flashdata('error_message', [false, "Not an .xml file." ]);
					redirect("datasource");
				}

				$datasource_id = $this->datasource->add($name, $file_name, "", "", $basicuri, $user_id, $ontology_id);

				$datasource_path = "upload/".$this->team->dir()."/datasources/" . $datasource_id . "_" . $name . "/source/";
				if ( !is_dir($datasource_path) ) mkdir($datasource_path, 0777, true);
				move_uploaded_file($_FILES['input_attachment']['tmp_name'], $datasource_path.$file_name );

				$this->loadSchemaFile($datasource_id, $datasource_path.$file_name);

			} else {

				if ( !$this->endsWith($file_name, ".sql") ) {
					$this->session->set_flashdata('error_message', [false, "Not an .sql file." ]);
					redirect("datasource");
				}

				$datasource_id = $this->datasource->add($name, $file_name, "", "", $basicuri, $user_id, $ontology_id);

				$datasource_path = "upload/".$this->team->dir()."/datasources/" . $datasource_id . "_" . $name . "/source/";
				if ( !is_dir($datasource_path) ) mkdir($datasource_path, 0777, true);
				move_uploaded_file($_FILES['input_attachment']['tmp_name'], $datasource_path.$file_name );

				$this->loadSQLfile($datasource_id, $datasource_path.$file_name);
			}


		} else {
			$this->session->set_flashdata('error_message', [false, "File not uploaded." ]);
			redirect("datasource");
		}

		$this->index();
	}

	function endsWith($haystack, $needle) {
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
	}
	
	public function edit_post()
	{
		$name = $this->input->post('input_edit_name');
		$datasource_id = $this->input->post('datasource_id');
		$basicuri = $this->input->post('input_edit_basicuri');
		$ontology_id = $this->input->post('input_edit_ontology');
		
		$row = $this->datasource->getDatasource($datasource_id);
		
		if(count($row) > 0) {
			if (strcmp($row[0]->basicuri, $basicuri) !== 0) {
				$this->changeUri($datasource_id, $row[0]->basicuri, $basicuri);
			}
		}
		
		$this->datasource->update($datasource_id, $name, $basicuri, $ontology_id);
		
		redirect($_SERVER['HTTP_REFERER']);
		
//		$this->index();
	}
	
	public function delete($datasource_id, $redirect = true)
	{		
		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			$this->index();
		}

		$datasource_name = $this->datasource->getDatasource($datasource_id);

		if ( !empty($datasource_name[0]) ) {

			$datasource_name = $datasource_name[0]->name;
			$datasource_dir = getcwd() . "/upload/".$this->team->dir()."/datasources/" . $datasource_id . "_" . $datasource_name;

			if ( is_dir($datasource_dir) ){
				if (PHP_OS === 'WINNT') {
					exec('rd /s /q "' . $datasource_dir . '"');
				} else {
					exec('rm -rf "' . $datasource_dir . '"');
				}
			}

			$this->datasource->delete($datasource_id);

		}

		if ( isset($redirect) && $redirect === true ) {
			$this->index();
		}
	}		
	
	
	public function storepositions()
	{
		if ( $this->ion_auth->in_group('guest') ) return;

		$datasource_id = $this->input->post('datasourceid');
		$nodeid = $this->input->post('nodeid');
		$layoutX = $this->input->post('layoutX');
		$layoutY = $this->input->post('layoutY');
		$unpin = $this->input->post('unpin');

		if ( $unpin !== null ) {
			$this->datasource->deletePosition($datasource_id, $nodeid, 0, 0);
		} else {
			$this->datasource->updatePosition($datasource_id, $nodeid, $layoutX, $layoutY);
		}

	}




	// Save the position of a node of the vowl graph.
	public function saveDbgraphLayout( $datasource_id ) {
		$save = $this->input->post('save');
		$tableid = $this->input->post('table_id');
		$layoutX = $this->input->post('pos_x');
		$layoutY = $this->input->post('pos_y');

		if (!$this->ion_auth->in_group('guest')) {
			$insert = ($save === "true");
			$this->datasource->setDatasourceLayout($datasource_id, $tableid, $insert, $layoutX, $layoutY);
		}
	}

	/*
	function loadSQLfile_new($datasource_id, $SQLfile = "")
	{
		echo "sql: ".$SQLfile."<br>";
		error_reporting(E_ALL);
		ini_set("display_errors", 1);
		set_time_limit(0); // For running the script with no time limit
		$sql_statements = file_get_contents ($SQLfile);

		$included = require_once('./application/third_party/PHPsql-Parser/vendor/autoload.php');

		$parser = new PHPSQLParser\PHPSQLParser();
		$parsed = $parser->parse($sql_statements);

		echo("<pre>");
		print_r($parsed);
		echo("</pre>");
	}*/

	function loadSchemaFile( $datasource_id, $XMLfile = "" ) {

		set_time_limit(0); // For running the script with no time limit
		$schema_encoded = file_get_contents ($XMLfile);

		$schema = simplexml_load_string( $schema_encoded );
		$schema = json_decode(json_encode((array)$schema), TRUE); //Object to array

		if ( isset($schema['type']) ) {
			$this->datasource->setDbType( $datasource_id, $schema['type'] );
		}

		if ( !isset($schema['tables']) ) {
			//Invalid file.
			$this->session->set_flashdata('error_message', [false, "Invalid file, must be created with 'Schema Creator' tool.", "Download: <a href='" . $this->config->item('schema_creator_tool_url') . "'>Schema Creator tool.</a>" ]);
			$this->delete( $datasource_id, false );
			redirect("datasource");
		}

		$tables = $schema['tables']['model.Table'];
		if ( isset( $tables['name'] )) $tables = $schema['tables'];

		if ( !is_array($tables) ) {
			$this->session->set_flashdata('error_message', [false, "Malformed file, there are no tables." ]);
			$this->delete( $datasource_id, false );
			redirect("datasource");
		}

		foreach ( $tables as $table ) {

			$idTable = $this->datasource->addTable( $datasource_id, $table['name'] );

			$columns = Array();
			if ( isset($table['columns']['model.Column']) )  $columns = $table['columns']['model.Column'];
			if ( count($columns) > 0 && !isset($columns[0]) ) $columns = Array( $columns );

			foreach ( $columns as $column ) {
				if ( !isset($column['type']) ) $column['type'] = "string";

				$this->datasource->addColumn( $idTable, $column['name'], $column['type'] );

			}

			$pks = Array( );
			if ( isset( $table['primaryKeys']['string'] )) $pks = $table['primaryKeys']['string'];
			if ( !is_array($pks) ) $pks = Array( $pks );

			if( is_array($pks) ) foreach ( $pks as $pk ) {

				$this->datasource->updateColumnPrimaryKey( $pk, true, $idTable );

			}

			$fks = Array( );
			if ( isset( $table['foreginKeys']['model.foreginKey'] )) $fks = $table['foreginKeys']['model.foreginKey'];
			if( count($fks) > 0 && !isset($fks[0]) ) $fks = Array( $fks );

			foreach ( $fks as $fk ) {

				$this->datasource->updateColumnForeignKey( $fk['fkColumnName'], $fk['pkColumnName'], $fk['pkTableName'], $idTable );

			}

		}


	}

	function loadSQLfile($datasource_id, $SQLfile = "")
	{
		echo "sql: ".$SQLfile."<br>";
		
		set_time_limit(0); // For running the script with no time limit
		$sql_statements = file_get_contents ($SQLfile);
		
		$pos = 0;
		
		///CREATE TABLE statement
		while( ($pos = strpos($sql_statements, "CREATE TABLE", $pos)) !== false) {
			
			$pos2 = strpos($sql_statements, ";", $pos+1);
			
			$create = substr($sql_statements, $pos, $pos2-$pos);
			
			$pos = $pos2;
			
			$this->processCreateStatement($datasource_id, $create);
		}
		
		///ALTER TABLE statement with foreign key
		while( ($pos = strpos($sql_statements, "ALTER TABLE", $pos)) !== false) {
			$pos2 = strpos($sql_statements, ";", $pos+1);
			
			$create = substr($sql_statements, $pos, $pos2-$pos);
			
			$pos = $pos2;
			
			$this->processAlterStatement($datasource_id, $create);
		}
	}
	
	function processCreateStatement($datasource_id, $create) {
	
//		echo "Create: ".$create."<br><br>..........<br>";
		
		
		//Table name
		if( ($pos = strpos($create, "`", 0)) !== false) {
			
			$pos2 = strpos($create, "`", $pos+1);
			
			$tableName = substr($create, $pos+1, $pos2-$pos-1);
	
//			echo "----------------------------<br>name: ".$tableName."<br>";
		}
		
		$idTable = $this->datasource->addTable($datasource_id, $tableName);
		
		//Columns
		if( ($pos = strpos($create, "(", 0)) !== false) {
			$pos2 = strlen ($create);
			
			$colss = substr($create, $pos+1, $pos2-$pos-1);
			$colss = str_replace("`", "", $colss);
			$cols = str_replace("`", "", $colss);
			
			//Removing ()
			$exit = false;
			$pos = 0;
			while(!$exit) {
				if( ($pos = strpos($cols, "(", $pos)) !== false) {
					if( ($pos2 = strpos($cols, ")", $pos)) !== false) {
						$cols = substr_replace ($cols, "", $pos, $pos2-$pos+1); 
					}
				}
				else 
					$exit = true;
			}
			
			var_dump($cols);
			$cols = str_replace("\r", "", $cols);
			$cols = str_replace("\n", "", $cols);
			$cols = str_replace("  ", " ", $cols);
			$cols = str_replace(")", "", $cols);
			var_dump($cols);
			
			//parsing columns definitions
			$cols_arr = explode (",", trim($cols));
			
			foreach($cols_arr as $col) {
			
				$col = trim(str_replace("`", "", $col));
				
				if(strpos($col, "PRIMARY KEY") === 0 || strpos($col, "KEY") === 0 || strpos($col, "UNIQUE KEY") === 0) {
					//Primary key definition and key definition

				} else 	{
					//A column definition
					$col_details = explode (" ", $col);

//					echo "<br>COL: ".$col."<br><br>";
					if(count($col_details) > 1) {
						$columnName = $col_details[0];
						$columnType = $col_details[1];
						
						$columnName = str_replace("`", "", $columnName);
						$columnName = str_replace("'", "", $columnName);
						$columnName = str_replace("\"", "", $columnName);
						$columnName = str_replace("(", "", $columnName);
						
						$columnType = str_replace("`", "", $columnType);
						$columnType = str_replace("'", "", $columnType);
						$columnType = str_replace("\"", "", $columnType);
						$columnType = str_replace("(", "", $columnType);
						//var_dump($col_details);
						
//						echo "<br>columnName: ".$columnName."<br><br>";
						$this->datasource->addColumn($idTable, $columnName, $columnType);
					}
				}
			}

			// Primary key statement
			$pos = strpos($colss, "PRIMARY KEY", 0);
			$pos = strpos($colss, "(", $pos);
			$pos2 = strpos($colss, ")", $pos+1);
	
			$primarykey = substr($colss, $pos+1, $pos2-$pos-1);
			
//			echo "Primary: ".$primarykey."<br>";

			$this->datasource->updateColumnPrimaryKey($primarykey, true, $idTable);			
		}	
	}
	
	
	function processAlterStatement($datasource_id, $alter) {
	
		//echo "Alter: ".$alter."<br>..........<br>";
		
		//Table name
		if( ($pos = strpos($alter, "ADD CONSTRAIN", 0)) !== false) {
			
			$tableName = substr($alter, 0, $pos-1);
			$tableName = str_replace("`", "", $tableName);
			$tableName = str_replace("'", "", $tableName);
			$tableName = str_replace("\"", "", $tableName);
			$tableName = str_replace("ALTER TABLE", "", $tableName);
			$tableName = trim($tableName);
	
			//echo "name: ".$tableName."<br>";

			$pos = 0;
			while( ($pos = strpos($alter, "ADD CONSTRAIN", $pos)) !== false) {
				//echo "pos: ".$pos."<br>";
				if( ($pos2 = strpos($alter, ",", $pos)) !== false) {
					$add = substr($alter, $pos, $pos2-$pos);
					
					$pos = $pos2;
				}
				else  {
					$add = substr($alter, $pos, strlen($alter)-$pos);
					
					$pos = strlen($alter);
				}
				
//				echo "name1: ".$add."<br>";
				///////////////////////////////////
				//Looking for the column of the Foreign key
				$columnName  = "";
				$foreignkey = "";
				$foreigntable = "";
				
				if( ($fpos = strpos($add, "FOREIGN KEY", 0)) !== false) {
					$fpos = strpos($add, "(", 0);
					$fpos2 = strpos($add, ")", $fpos+1);
			
					$columnName = substr($add, $fpos , $fpos2-$fpos);
					$columnName = str_replace("`", "", $columnName);
					$columnName = str_replace("'", "", $columnName);
					$columnName = str_replace("\"", "", $columnName);
					$columnName = str_replace("(", "", $columnName);
//					echo "column: ".$columnName."<br>";
				

					///////////////////////////////////
					//Looking for TAble and foreign key
					
					if( ($fpos = strpos($add, "REFERENCES", 0)) !== false) {
						$fpos2 = strpos($add, "(", $fpos+1);
				
						$foreigntable = substr($add, $fpos + strlen("REFERENCES"), $fpos2-$fpos-strlen("REFERENCES"));
						$foreigntable = str_replace("`", "", $foreigntable);
						$foreigntable = str_replace("'", "", $foreigntable);
						$foreigntable = str_replace("\"", "", $foreigntable);
						$foreigntable = trim(str_replace("(", "", $foreigntable));	//Table names cannot have spaces
	//					echo "table: ".$foreigntable."<br>";
						
						
						$fpos = $fpos2;
						$fpos2 = strpos($add, ")", $fpos+1);
						$foreignkey = substr($add, $fpos+1, $fpos2-$fpos-1);
						$foreignkey = str_replace("`", "", $foreignkey);

	//					echo "foreign key: ".$foreignkey."<br>";

					}
					
					if($columnName != "" && $foreignkey != "" && $foreigntable != "" ) 
					{
						$tablelist = $this->datasource->getTableByName($tableName, $datasource_id);
						$table = $tablelist[0];
						
	//					var_dump($tablelist);
	//					var_dump($table);
	//					echo "table: ".$tableName." < column|".$columnName."|<br>";
						//$collist = $this->datasource->getColumnByName($columnName, $table->id);
						//$column = $collist[0];
						
						$this->datasource->updateColumnForeignKey($columnName, $foreignkey, $foreigntable, $table->id);	
					}
				}
				else if( ($fpos = strpos($add, "PRIMARY KEY", 0)) !== false) {
					$fpos = strpos($add, "(", 0);
					$fpos2 = strpos($add, ")", $fpos+1);
			
					$columnName = substr($add, $fpos , $fpos2-$fpos);
					$columnName = str_replace("`", "", $columnName);
					$columnName = str_replace("'", "", $columnName);
					$columnName = str_replace("\"", "", $columnName);
					$columnName = str_replace("(", "", $columnName);
					
					if($columnName != "")
					{
						$tablelist = $this->datasource->getTableByName($tableName, $datasource_id);
													
						$arr = explode(',', $columnName);
						if(count($arr) > 1) {
							
							$this->datasource->updateColumnPrimaryKey($arr[0], true, $tablelist[0]->id);			
							$this->datasource->updateColumnPrimaryKey($arr[1], true, $tablelist[0]->id);			
						}
						else {
							$this->datasource->updateColumnPrimaryKey($columnName, true, $tablelist[0]->id);			
						}
					}
				}
			}
		
		}
		
	}

	///////////////////////////////
	// This function changes the URI of a data sources, it goes through all mappings
	//
	public function changeUri($datasource_id, $basicuriFrom, $basicuriTo)
	{
		$datasource = $this->datasource->getDatasource($datasource_id);
		
	
		$mappingspaces = $this->mappingspace->getMappingspaces($datasource_id);
		
		foreach($mappingspaces as $row) {
		
		
			//Updating mapping class
			$query ="UPDATE mappedclass SET uri = REPLACE(uri, '".$basicuriFrom."', '".$basicuriTo."') WHERE mappingspace_id = ".$row->id;
						
			$this->team->db->query ($query);
			
			$classlist = $this->mappedclass->getMappedclasses($row->id);
			
			foreach($classlist as $rowClass) {
				
				//Updating object properties
				$query ="UPDATE mappedobjectproperty SET uri = REPLACE(uri, '".$basicuriFrom."', '".$basicuriTo."') WHERE mappedclassdomain_id = ".$rowClass->id. " OR mappedclassrange_id= ".$rowClass->id;
						
				$this->team->db->query ($query);
			}
		}
		
		//Updating R2RML parts
		
		$query ="UPDATE r2rmlparts SET text = REPLACE(text, '".$basicuriFrom."', '".$basicuriTo."') WHERE datasource_id = ".$datasource_id;
						
		$this->team->db->query ($query);
	}
	
	///////////////////////////////
	// This function changes the Prefix of a ontology for the mappings of a data sources
	//
	public function changePrefix($datasource_id)
	{
		$prefixFrom = "http://semanco02.hs-albsig.de/repository/ontology-releases/eu/semanco/ontology/SEMANCO/SEMANCO.owl#";
		$prefixTo = "http://semanco-tools.eu/ontology-releases/eu/semanco/ontology/SEMANCO/SEMANCO.owl#";
		
		echo "Prefix from: ".$prefixFrom."<br>";
		echo "Prefix to: ".$prefixTo."<br>";
		
		$mappingspaces = $this->mappingspace->getMappingspaces($datasource_id);
		
		foreach($mappingspaces as $row) {
		
		
			//Updating mapping class
			$query ="UPDATE mappedclass SET class = REPLACE(class, '".$prefixFrom."', '".$prefixTo."') WHERE mappingspace_id = ".$row->id;
						
			$this->team->db->query ($query);
			
			$classlist = $this->mappedclass->getMappedclasses($row->id);
			
			foreach($classlist as $rowClass) {
				
				//Updating object properties
				$query ="UPDATE mappedobjectproperty SET objectproperty = REPLACE(objectproperty, '".$prefixFrom."', '".$prefixTo."') WHERE mappedclassdomain_id = ".$rowClass->id. " OR mappedclassrange_id= ".$rowClass->id;
						
				$this->team->db->query ($query);
				
				//Updating data properties
				$query ="UPDATE mappeddataproperty SET dataproperty = REPLACE(dataproperty, '".$prefixFrom."', '".$prefixTo."') WHERE mappedclass_id = ".$rowClass->id;
						
				$this->team->db->query ($query);
			}
		}
		
		//Updating R2RML parts
		
		$query ="UPDATE r2rmlparts SET text = REPLACE(text, '".$prefixFrom."', '".$prefixTo."') WHERE datasource_id = ".$datasource_id;
						
		$this->team->db->query ($query);
		

		
	}
}

