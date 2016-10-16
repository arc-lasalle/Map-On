<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Mapping extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		$this->load->model("Mappingspace_model", "mappingspace");
		$this->load->model("Mappedclass_model", "mappedclass");
		$this->load->model("Mappeddataproperty_model", "mappeddataproperty");
		$this->load->model("Mappedobjectproperty_model", "mappedobjectproperty");
		$this->load->model("Workspaces_model", "workspaces");	
		$this->load->model("Ontology_model", "ontology");	
		$this->load->model("Prefix_model", "prefix");
		$this->load->model("Datasource_model", "datasource");
		
		
		$this->load->model("Mapping_model", "mapping");
		
	}
		
	public function index()
	{
		
	}
		
	public function show($datasource_id)
	{
		
	}
	
	public function createnew($datasource_id, $mappingspace_id)
	{		
		$data["datasource_id"] = $datasource_id;
		$data["mappingspace_id"] = $mappingspace_id;
		
		$this->load->view('header');
		$this->load->view('mapping/createnew', $data);
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
			$this->show($datasource_id);
		}
		
		$mappingspaceid = $this->mappingspace->add($name, $user_id, $datasource_id);
	
		$this->show($datasource_id);
	}

	
	public function delete($datasource_id, $mappingspace_id)
	{		
		///////////////////////////////////////////////////////////////		
		// If it is a guest user then he/she is automatically redirected without modifying the mapping
		if ($this->ion_auth->in_group('guest')){
			$this->show($datasource_id);
		}
		
		$this->mappingspace->delete($mappingspace_id);
		
		$this->show($datasource_id);
	}
	
	
	
	function suggestclass()
	{
		$substring = $this->input->post('string');
		$datasource_id = $this->input->post('datasource_id');
		$ontology_id = $this->datasource->getOntology($datasource_id); 
		$found = false;
		

	//	$substring = "building_name";
		
		//echo "sumo:Building<br>semano:Building<br>";

		//IT has been hardcoded, to change it.
		$store_Mysql = $this->workspaces->connect_workspace("ontology_".$ontology_id); 

		//Get all classes
		$classes = $this->ontology->getClassesAndAnnotations($store_Mysql);
		echo '<div style="line-height: 1.4em;" class="gl_clickOut_hide">';
		for($i=0; $i < count($classes); $i++){
			$qName = $this->prefix->getQName($classes[$i]['class'], $ontology_id);
			$strComment = isset ($classes[$i]['comment']) ? '<span><i>'.$classes[$i]['comment'].'</i></span><br />' : "";
			
			$pos1 = stripos($qName, $substring);
			if($pos1 !== false || $substring == "") {
				$strPrintValue = str_ireplace($substring, "<strong>".$substring."</strong>", $qName);
				$strComment = str_ireplace($substring, "<strong>".$substring."</strong>", $strComment);

				echo '<span style="font-size:11px; color: rgb(79, 98, 40); cursor: pointer; cursor: hand;" onclick="add_search_box_Class(\''.$qName.'\')">'.$strPrintValue.'</span><br />';
				echo $strComment.'<br />';
				$found = true;
			} else {
				if(isset ($classes[$i]['comment']) ){
					$pos2 = stripos($classes[$i]['comment'], $substring);
					
					if($pos2 !== false) {
						$strComment = str_ireplace($substring, "<strong>".$substring."</strong>", $strComment);
						echo '<span style="font-size:11px; color: rgb(79, 98, 40); cursor: pointer; cursor: hand;" onclick="add_search_box_Class(\''.$qName.'\')">'.$qName.'</span><br/>';
						echo $strComment.'<br />';
						$found = true;
					}
				}
			}	
		}

		if ( !$found ) {
			echo "Nothing found.";
		}

		echo "</div>\n";
	}

	function checkClass( ) {

		$class = $this->input->post('class');
		$datasource_id = $this->input->post('datasource_id');

		$ontology_id = $this->datasource->getOntology($datasource_id);

		$store_Mysql = $this->workspaces->connect_workspace("ontology_".$ontology_id);

		$class_uri = $this->prefix->getURI($class, $ontology_id);

		$found = $this->ontology->checkClass($store_Mysql, $class_uri);

		if ( $found ) {
			echo "true";
		} else {
			echo "false";
		}

	}

	function suggesttable()
	{
		$substring = $this->input->post('string');
		$datasource_id = $this->input->post('datasource_id');
		$target = $this->input->post('target');
		
		
//		$substring = "Heat";
//		$datasource_id = 1;
		
		$tables = $this->datasource->getTables($datasource_id);
		
		echo '<div style="line-height: 1.4em;">';
		for($i=0; $i < count($tables); $i++){

		//foreach($tables as $rowT){
			$pos1 = stripos($tables[$i]->name, $substring);
			$tableMatch = false;
			$printTable = $tables[$i]->name;
			if($pos1 !== false || $substring == "") {
				$tableMatch = true;
				$printTable = str_ireplace($substring, "<strong>".$substring."</strong>", $printTable);
			}
			echo '<span style="font-size:11px; color: #999999;" title="Table">'.$printTable."</span><br />\n";
			
			$columns = $this->datasource->getcolumns($tables[$i]->id);
			for($j=0; $j < count($columns); $j++){
			
				$pos1 = stripos($columns[$j]->name, $substring);
				if($pos1 !== false || $tableMatch) {
					$printColumn = str_ireplace($substring, "<strong>".$substring."</strong>", $columns[$j]->name);
					echo '<span style="font-size:11px; margin-left:10px; color: rgb(79, 98, 40);cursor: pointer; cursor: hand" onclick="add_search_box_Table(\''.$tables[$i]->name.'->'.$columns[$j]->name.'\')" title="Column">- '.$printColumn.' ('.$columns[$j]->type.')</span><br />';
				} 
			}
		}
		
		echo "</div><br>\n";
	}

	function checkTable( ) {
		$table = $this->input->post('table');
		$datasource_id = $this->input->post('datasource_id');

		$tbl = explode("->", $table);
		if ( !isset($tbl[0]) || !isset($tbl[1]) ) return;

		$db_table = $this->datasource->getTableByName( $tbl[0], $datasource_id );
		if ( !isset($db_table[0]) ) return;
		$db_col = $this->datasource->getColumnByName( $tbl[1], $db_table[0]->id );
		//function getTableByName($tablename, $datasource_id)
		//function getColumnByName($columnname, $sourcetable_id)
		$aux = count($db_col);
		if ( count($db_col) > 0 ) {
			echo "true";
		} else {
			echo "false";
		}

	}

	function suggestcolumn()
	{
		$substring = $this->input->post('string');
		$datasource_id = $this->input->post('datasource_id');
		$sourcetable_id = $this->input->post('sourcetable_id');
		$sourcetable_name = $this->input->post('sourcetable_name');

//		$substring = "Heat";
//		$datasource_id = 1;
	
		echo '<div style="line-height: 1.4em;">';
		echo '<span style="font-size:11px; color: #999999;" title="Table">'.$sourcetable_name."</span><br />\n";
		$columns = $this->datasource->getcolumns($sourcetable_id);
		for($j=0; $j < count($columns); $j++){
			$printColumn = str_ireplace($substring, "<strong>".$substring."</strong>", $columns[$j]->name);
			echo '<span style="font-size:11px; margin-left:20px; color: rgb(79, 98, 40);cursor: pointer; cursor: hand" onclick="add_search_box_Table(\''.$sourcetable_name.'->'.$columns[$j]->name.'\')" title="Column">- '.$printColumn.' ('.$columns[$j]->type.')</span><br />';			 
		}
		echo "</div><br>\n";
	}
	
	function suggestdataproperty()
	{
		$substring = $this->input->post('string');
		$class = $this->input->post('class');
		$datasource_id = $this->input->post('datasource_id');
		$found = false;
		
		$ontology_id = $this->datasource->getOntology($datasource_id); 
		//IT has been hardcoded, to change it.
		$store_Mysql = $this->workspaces->connect_workspace("ontology_".$ontology_id); 

		//$class = "http://www.semanco-project.eu/2012/5/SEMANCO.owl#Space_Cooling_Fraction_Of_Cold";
		//Get all dataproperties
		$dp = $this->ontology->getDatapropertiesbyDomain($store_Mysql, $class);
		
		echo '<div style="line-height: 1.4em;">';
		for($i=0; $i < count($dp); $i++){
			$qName = $this->prefix->getQName($dp[$i]['datatype'], $ontology_id);
			
			$strPrintValue = str_ireplace($substring, "<strong>".$substring."</strong>", $qName);

			echo '<span style="font-size:11px; color: rgb(79, 98, 40); cursor: pointer; cursor: hand;" onclick="add_search_box_Dataproperty(\''.$qName.'\')">'.$strPrintValue.'</span><br />';
			$found = true;
		}		
		$dp2 = $this->ontology->getDatapropertiesWithoutDomain($store_Mysql);
		
		$n = 0;
		for($i=0; $i < count($dp2); $i++){
			if($dp2[$i]) {
				$qName = $this->prefix->getQName($dp2[$i]['datatype'], $ontology_id);
				$pos1 = stripos($qName, $substring);
				if($pos1 !== false || $substring == "") {
					if($n++ == 0) echo "---<br />";
					
					$strPrintValue = str_ireplace($substring, "<strong>".$substring."</strong>", $qName);
					echo '<span style="font-size:11px; color: rgb(79, 98, 40); cursor: pointer; cursor: hand;" onclick="add_search_box_Dataproperty(\''.$qName.'\')">'.$strPrintValue.'</span><br />';
					$found = true;
				}
			}
		}

		if ( !$found ) {
			echo "Nothing found.";
		}
		echo "</div>\n";
	}

	function checkDataProperty () {
		$dp_name = $this->input->post('dp_name');
		$datasource_id = $this->input->post('datasource_id');

		$ontology_id = $this->datasource->getOntology($datasource_id);

		$store_Mysql = $this->workspaces->connect_workspace("ontology_".$ontology_id);

		$data_property_uri = $this->prefix->getURI($dp_name, $ontology_id);

		$found = $this->ontology->checkDataProperty($store_Mysql, $data_property_uri);

		if ( $found ) {
			echo "true";
		} else {
			echo "false";
		}

	}

	function suggestobjectproperty()
	{
		$substring = $this->input->post('string');
		$class = $this->input->post('class');
		$datasource_id = $this->input->post('datasource_id');

		$ontology_id = $this->datasource->getOntology($datasource_id); 
		
		//IT has been hardcoded, to change it.
		$store_Mysql = $this->workspaces->connect_workspace("ontology_".$ontology_id); 

		//$class = "http://www.semanco-project.eu/2012/5/SEMANCO.owl#Space_Cooling_Fraction_Of_Cold";
		//Get all dataproperties
		$dp = $this->ontology->getObjectpropertiesbyDomain($store_Mysql, $class);
		
		echo '<div style="line-height: 1.4em;">';
		echo '<span style="font-size:11px; color: rgb(40, 40, 40);"><i>Object properties whose domain is '.$this->prefix->getQName($class, $ontology_id).': </i>';
		
		if(count($dp) == 0)
			echo "<strong><i>not found!</i></strong></span><br>";
		else 
			echo "</span><br>";
		
		for($i=0; $i < count($dp); $i++){
			$qName = $this->prefix->getQName($dp[$i]['datatype'], $ontology_id);
			
			$strPrintValue = str_ireplace($substring, "<strong>".$substring."</strong>", $qName);

			echo '<span style="font-size:11px; color: rgb(79, 98, 40); cursor: pointer; cursor: hand;" onclick="add_search_box_Objectproperty(\''.$qName.'\')">'.$strPrintValue.'</span><br />';
		}		
		$dp2 = $this->ontology->getObjectpropertiesWithoutDomain($store_Mysql);
		
		$n = 0;
		for($i=0; $i < count($dp2); $i++){
			if($dp2[$i]) {
				$qName = $this->prefix->getQName($dp2[$i]['datatype'], $ontology_id);
				$pos1 = stripos($qName, $substring);
				if($pos1 !== false) {
					if($n++ == 0) echo '<br /><span style="font-size:11px; color: rgb(40, 40, 40);"><i>Other object properties of the ontology: </i></span><br/>';
					
			
					$strPrintValue = str_ireplace($substring, "<strong>".$substring."</strong>", $qName);
					echo '<span style="font-size:11px; color: rgb(79, 98, 40); cursor: pointer; cursor: hand;" onclick="add_search_box_Objectproperty(\''.$qName.'\')">'.$strPrintValue.'</span><br />';
				}
			}
		}	
		echo "</div>\n";
	}

	function checkObjectProperty () {
		$op_name = $this->input->post('op_name');
		$datasource_id = $this->input->post('datasource_id');

		$ontology_id = $this->datasource->getOntology($datasource_id);

		$store_Mysql = $this->workspaces->connect_workspace("ontology_".$ontology_id);

		$data_property_uri = $this->prefix->getURI($op_name, $ontology_id);

		$found = $this->ontology->checkObjectProperty($store_Mysql, $data_property_uri);

		if ( $found ) {
			echo "true";
		} else {
			echo "false";
		}

	}
	
	function generateSQL()
	{
		$input_table = $this->input->post('input_table');
		$mappedclass_id = $this->input->post('mappedclass_id');
		$datasource_id = $this->input->post('datasource_id');
		
		$this->load->model("Mappeddataproperty_model", "mappeddataproperty");
		
		echo $this->mapping->generateSQL($input_table, $mappedclass_id, $datasource_id); 
	}
	
	function generateDatapropertyValue()
	{
		$input_table = $this->input->post('input_table');
		
		echo $this->mapping->generateDatapropertyValue($input_table); 
	}
	
	function generateURI()
	{
		$input_class = $this->input->post('input_class');
		$input_table = $this->input->post('input_table');
		$datasource_id = $this->input->post('datasource_id');
		$ontology_id = $this->datasource->getOntology($datasource_id); 
		$basic_uri = $this->datasource->getBasicUri($datasource_id); 
		
		echo $this->mapping->generateURI($input_class, $input_table, $basic_uri, $ontology_id); 
	}
		
	function generateObjectpropertyURI()
	{
		$input_object = $this->input->post('input_object');
		$datasource_id = $this->input->post('datasource_id');
		$input_table = $this->input->post('input_table');
		$ontology_id = $this->datasource->getOntology($datasource_id); 
		$basic_uri = $this->datasource->getBasicUri($datasource_id); 
		$path = $this->input->post('path'); 
		
		echo $this->mapping->generateObjectpropertyURI($input_object, $input_table, $basic_uri, $ontology_id, $path); 
	}	
	
	function generateObjectpropertyTarget()
	{
		$input_object = $this->input->post('input_object');
		$mappingspace_id = $this->input->post('mappingspace_id');
		$datasource_id = $this->input->post('datasource_id');
		$ontology_id = $this->datasource->getOntology($datasource_id); 
		
		echo $this->mapping->generateObjectpropertyTarget($input_object, $mappingspace_id, $ontology_id); 
	}
	
	
	
	
	
}

