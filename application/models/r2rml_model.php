<?php
class R2rml_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
        $this->load->library('parsers/r2rml_parser', 'r2rml_parser');
        $this->load->model("mapping_model", "mapping");

    }
	
	///////////////////////////////////////
	
	
	function getR2RMLPart($datasource_id)
	{
		$this->db->where('datasource_id', $datasource_id);

		$query = $this->db->get("r2rmlparts");
		$ret = "";
		if(count($query->result()) > 0 ) {
			$rows = $query->result();
			$ret = $rows[0]->text;
		}
		
		return $ret;
	}
	
	function updateR2RMLPart($input_r2rmlpart, $user_id, $datasource_id)
	{
		$this->db->where('datasource_id', $datasource_id);

		$query = $this->db->get("r2rmlparts");
		
		if(count($query->result()) > 0 ) {
			$rows = $query->result();
			
			$this->db->where('id', $rows[0]->id);
			$this->db->update('r2rmlparts', array('text' => $input_r2rmlpart,'user_id' => $user_id,'datasource_id' => $datasource_id, 'date' => date("Y-m-d")));
		} else {
			$this->db->insert('r2rmlparts', array('text' => $input_r2rmlpart,'user_id' => $user_id,'datasource_id' => $datasource_id, 'date' => date("Y-m-d")));
		}

	}
	
	///////////////////////////////////////
	
	function export($datasource_id)
	{	
//		echo "Exporting data source: ".$datasource_id."<br><br>";
		
		///Version 1. All mapped clases of all mapping spaces are exported. No overlappings are checked



		$outputText = "";
		
		$mappings = $this->mappingspace->getMappingspaces( $datasource_id );
				
		foreach( $mappings as $map ) {
//			echo " - Exporting mapping space: ".$map->name."<br><br>";
			
			$classes = $this->mappedclass->getMappedclasses( $map->id );
		
			foreach( $classes as $class ) {
//				echo " - Exporting classes: ".$class->class."<br><br>";

                //$newSql = $this->mapping->generateSQL($class->mappedtablecolumn, $class->id, $datasource_id, true);
                //$class->sql = $newSql;

				$dataproperties = $this->mappeddataproperty->getMappeddataproperties($class->id);
				$objectproperties = $this->mappedobjectproperty->getMappedobjectproperties($class->id);

				$outputText = $outputText.$this->generateR2RMLClass($class, $dataproperties, $objectproperties);
			}
		}
		
		return $outputText;
	}
	
	///////////////////////////////////////
	// This function generates the R2RML output of a class

	function generateR2RMLClass( $map, $dataproperties, $objectproperties )
	{
		$ret = "\n\n";
		$ret .= "################################################\n";
		$ret .= "# TripleMap for ".$map->id.": ".$map->class."\n\n";
		
		$ret .= "<mapping1_".$map->id."> a rr:TriplesMap;\n";
		$ret .= "	rr:logicalTable [ rr:sqlQuery \"".addslashes($map->sql)."\" ];\n";

		$ret .= "	rr:subjectMap [	rr:template \"".addslashes($map->uri)."\";\n";
		$ret .= "			rr:class <".$map->class.">\n";
		$ret .= "	];\n\n";

		foreach ( $dataproperties as $dp ) {
			$ret .= "	rr:predicateObjectMap [\n";
			$ret .= "		rr:predicate 	<".$dp->dataproperty."> ;\n";
			$ret .= "		rr:objectMap [ rr:column \"".strtolower(str_replace(".", "", $dp->value))."\" ]\n";
			$ret .= "	];\n";
		}
		
		foreach ( $objectproperties as $op ) {
			$ret .= "	rr:predicateObjectMap [\n";
			$ret .= "		rr:predicate 	<".$op->objectproperty."> ;\n";
			$ret .= "		rr:objectMap [ rr:template \"".addslashes($op->uri)."\" ]\n";
			$ret .= "	];\n";
		}
		
		$ret .= "	.\n";

		return $ret;
	}
	///////////////////////////////////////

	function generatePrefixes()
	{
		$ret = "\n";
		$ret .= "@prefix rr: <http://www.w3.org/ns/r2rml#> .\n";
		$ret .= "@prefix xsd: <http://www.w3.org/2001/XMLSchema#> .\n";
		$ret .= "@prefix : <http://www.benchmark.com/quest/> .\n";
		$ret .= "@base <http://example.com/base/> .\n";
		$ret .= "\n";
		
		return $ret;
	}
	
	///////////////////////////////////////

	
	function loadR2RML($file, $datasource_id)
	{
		// 1. getting mapping spaces to delete them
		$mapping_spaces = $this->mappingspace->getMappingspaces( $datasource_id );

		foreach ( $mapping_spaces as $ms ) {
			$this->mappingspace->delete( $ms->id );
		}
		
		// 2. Creating an empty mapping space
		$mappingspace_id = $this->mappingspace->add( "automatic", 0, $datasource_id );
		
		// 3. Reading the mappings.
		require_once "public/easyrdf/EasyRdf.php";
	
		// Getting "selected" classes
		$ontology_id = $this->datasource->getOntology( $datasource_id );
		$store_Mysql = $this->workspaces->connect_workspace( "ontology_".$ontology_id );

        $r2rml = $this->r2rml_parser->parse( $file );

		///////////////////////////////
		//First we create all the subject mappings with the data properties
		foreach ( $r2rml['triplesMap'] as $tripleMap ) {

			$query = $tripleMap['logicalTable']['query'];
			$table = $tripleMap['logicalTable']['table'];
			$column = $tripleMap['subjectMap']['column'];

			$subjectMapTemplate = $tripleMap['subjectMap']['template'];
			$subjectMapClass = $tripleMap['subjectMap']['class'];

			$tablePcolumn = $table . "." . $column;
			$tableRcolumn = $table . "->" . $column;

			$sql = $query;
			if ( empty($sql) ) $sql = 'SELECT ' . $tablePcolumn . ' FROM ' . $table;

			//3.1 Adding mappings to class ($class, $sql, $uri, $user_id, $mappingspace_id, $mappedtablecolumn = "")
			$mappedclass_id = $this->mappedclass->add( $subjectMapClass, $sql, $subjectMapTemplate, 0, $mappingspace_id, $tableRcolumn );


			foreach ( $tripleMap['predicateObjectMap'] as $predicatemap ) {
				$predicate = $predicatemap['predicate'];
				$objectMapColumn = $table . "." . $predicatemap['column'];

				if( $this->ontology->isDataproperty( $store_Mysql , $predicate ) ) {

					//3.2 adding data properties  add($dataproperty, $value, $type, $mappedclass_id)
					$this->mappeddataproperty->add( $predicate, $objectMapColumn, "", $mappedclass_id );

				}					
			}
		}

		///////////////////////////////
		// Then we create the object properties.
		foreach ( $r2rml['triplesMap'] as $tripleMap ) {

			$subjecttemplate = $tripleMap['subjectMap']['template'];

			foreach ( $tripleMap['predicateObjectMap'] as $predicatemap ) {
				$predicate = $predicatemap['predicate'];
                $objectMap = $predicatemap['template'];
				if( empty($objectMap) ) $objectMap = $table . "." . $predicatemap['column'];
				
				if( !$this->ontology->isDataproperty( $store_Mysql , $predicate) ) {

					//3.3 adding objectproperties   add($objectproperty, $uri, $mappedclassdomain_id, $mappedclassrange_id)
					$domain = $this->mappedclass->getMappedclassByURI( $subjecttemplate, $mappingspace_id );
					$range = $this->mappedclass->getMappedclassByURI( $objectMap, $mappingspace_id );
									
					if ( count($domain) > 0 && count($range) > 0 ) {

						$this->mappedobjectproperty->add( $predicate, $objectMap, $domain[0]->id, $range[0]->id );
						
					}
				} 
			}
		}
		
		///////////////////////////////
		// Update the SQL part of the mapped classes to take into account the object properties
		
		$mappedclasses = $this->mappedclass->getMappedclasses($mappingspace_id);
		foreach ($mappedclasses as $mappedclass) {
			
			$sql = $this->mapping->generateSQL($mappedclass->mappedtablecolumn,  $mappedclass->id, $datasource_id);
			
			$this->mappedclass->update($mappedclass->id, $mappedclass->class, $sql, $mappedclass->uri, $mappedclass->mappedtablecolumn);
		}
	}


}
	

	
	
?>