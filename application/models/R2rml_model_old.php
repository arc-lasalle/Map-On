<?php
class R2rml_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
        $this->load->library('parsers/r2rml_parser', 'r2rml_parser');
    }
	
	///////////////////////////////////////
	
	
	function getR2RMLPart($datasource_id)
	{
		$this->team->db->where('datasource_id', $datasource_id);

		$query = $this->team->db->get("r2rmlparts");
		$ret = "";
		if(count($query->result()) > 0 ) {
			$rows = $query->result();
			$ret = $rows[0]->text;
		}
		
		return $ret;
	}
	
	function updateR2RMLPart($input_r2rmlpart, $user_id, $datasource_id)
	{
		$this->team->db->where('datasource_id', $datasource_id);

		$query = $this->team->db->get("r2rmlparts");
		
		if(count($query->result()) > 0 ) {
			$rows = $query->result();
			
			$this->team->db->where('id', $rows[0]->id);
			$this->team->db->update('r2rmlparts', array('text' => $input_r2rmlpart,'user_id' => $user_id,'datasource_id' => $datasource_id, 'date' => date("Y-m-d")));
		} else {
			$this->team->db->insert('r2rmlparts', array('text' => $input_r2rmlpart,'user_id' => $user_id,'datasource_id' => $datasource_id, 'date' => date("Y-m-d")));
		}

	}
	
	///////////////////////////////////////
	
	function export($datasource_id)
	{	
//		echo "Exporting data source: ".$datasource_id."<br><br>";
		
		///Version 1. All mapped clases of all mapping spaces are exported. No overlappings are checked
		
		$outputText = "";
		
		$mappings = $this->mappingspace->getMappingspaces($datasource_id);
				
		foreach($mappings as $map) {
//			echo " - Exporting mapping space: ".$map->name."<br><br>";
			
			$classes = $this->mappedclass->getMappedclasses($map->id);
		
			foreach($classes as $class) {
//				echo " - Exporting classes: ".$class->class."<br><br>";
				
				$dataproperties = $this->mappeddataproperty->getMappeddataproperties($class->id);
				$objectproperties = $this->mappedobjectproperty->getMappedobjectproperties($class->id);

				$outputText = $outputText.$this->generateR2RMLClass($class, $dataproperties, $objectproperties);
			}
		}
		
		return $outputText;
	}
	
	///////////////////////////////////////
	// This function generates the R2RML output of a class

	function generateR2RMLClass($map, $dataproperties, $objectproperties)
	{
		$ret = "\n\n";
		$ret .= "################################################\n";
		$ret .= "# TripleMap for ".$map->id.": ".$map->class."\n\n";
		
		$ret .= "<mapping1_".$map->id."> a rr:TriplesMap;\n";
		$ret .= "	rr:logicalTable [ rr:sqlQuery \"".$map->sql."\" ];\n";

		$ret .= "	rr:subjectMap [	rr:template \"".$map->uri."\";\n";
		$ret .= "			rr:class <".$map->class.">\n";
		$ret .= "	];\n\n";

		foreach($dataproperties as $dp) {
			$ret .= "	rr:predicateObjectMap [\n";
			$ret .= "		rr:predicate 	<".$dp->dataproperty."> ;\n";
			$ret .= "		rr:objectMap [ rr:column \"".strtolower(str_replace(".", "", $dp->value))."\" ]\n";
			$ret .= "	];\n";
		}
		
		foreach($objectproperties as $op) {
			$ret .= "	rr:predicateObjectMap [\n";
			$ret .= "		rr:predicate 	<".$op->objectproperty."> ;\n";
			$ret .= "		rr:objectMap [ rr:template \"".$op->uri."\" ]\n";
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
		//1. getting mapping spaces to delete them
		$mapping_spaces = $this->mappingspace->getMappingspaces($datasource_id);
				
		foreach($mapping_spaces as $ms) {
			$this->mappingspace->delete($ms->id);
		}
		
		//2. Creating an empty mapping space
		$mappingspace_id = $this->mappingspace->add("automatic", 0, $datasource_id);
		
		//3. Reading the mappings.
		include_once("public/arc2/ARC2.php");
		require_once "public/easyrdf/EasyRdf.php";
	
		//Getting "selected" classes	
		$ontology_id = $this->datasource->getOntology($datasource_id);
		$store_Mysql = $this->workspaces->connect_workspace("ontology_".$ontology_id);	

        $this->r2rml_parser->parse( $file );

		$graph = new EasyRdf_Graph();
				
		$ntriples = $graph->parseFile($file, "turtle");
		
		///////////////////////////////
		//First we create all the subject mappings with the data properties
		foreach ($graph->allOfType('rr:TriplesMap') as $triplemap) {
			
			$logicaltable = $this->obtainLogicalTableFromTripleMap($triplemap);
            $query = $logicaltable['value'];
			//$query = str_replace('"', "", $query);
//			echo "<br>SQLQUERY: ".$query."<br>";

            if ( $logicaltable['query'] === false ) {
                $table = $logicaltable['value'];
            } else {
                $table = $this->obtainTableFromQuery($query);
            }

//			echo "<br>TABLE: ".$table."<br>";
			
			$subjecttemplate = $this->obtainSubjectTemplateFromTripleMap($triplemap);
//			echo "SubjectMap Template: ".$subjecttemplate."<br>";

            $column = $this->obtainColumnFromTemplate($subjecttemplate);
//			echo "SubjectMap column: ".$column."<br>";

            if ( $logicaltable['query'] === false ) {
                $subjecttemplate = str_replace($column, $table.$column, $subjecttemplate);
                $column = $table . "." . $column;
            } else {
                $column = $this->regex_get_name_from_alias($query, $column);
            }

			$subjectclass = $this->obtainSubjectClassFromTripleMap($triplemap);
//			echo "SubjectMap Class: ".$subjectclass."<br>";
			
			$sql = $this->obtainSQL($query, $column, $table); 
			$mappedtablecolumn = $this->obtainMappedTableColumn($column, $table); 
			
			//3.1 Adding mappings to class ($class, $sql, $uri, $user_id, $mappingspace_id, $mappedtablecolumn = "")
			$mappedclass_id = $this->mappedclass->add($subjectclass, $sql, $subjecttemplate, 0, $mappingspace_id, $mappedtablecolumn);
			
//			echo "PredicateMaps...<br>";
			
			foreach ($triplemap->all('rr:predicateObjectMap') as $predicatemap) {
				$predicate = ''.$this->obtainPredicateFromPredicateMap($predicatemap);
//				echo "<br>Predicate: ".$predicate."<br>";
				
				$objectMapTemplate = ''.$this->obtainObjectMapFromPredicateMap($predicatemap);
				$objectMapColumn = $this->obtainColumnFromTemplate($objectMapTemplate);


                $objectMapColumn = $this->regex_get_name_from_alias($query, $objectMapColumn);

                if ( $logicaltable['query'] === false ) {
                    $objectMapColumn = $table . "." . $objectMapColumn;
                }

///				echo "objectMapTemplate: ".$objectMapTemplate."<br>";
//				echo "ObjectMap: ".$objectMapColumn."<br>";		
				
				if($this->ontology->isDataproperty( $store_Mysql , $predicate)) {
					//3.2 adding data properties  add($dataproperty, $value, $type, $mappedclass_id)
					
					$this->mappeddataproperty->add($predicate, $objectMapColumn, "", $mappedclass_id);
				}					
			}
		}
		
//		echo "<br><br><br>####################################################<br>";
		///////////////////////////////
		// Then we create the object properties.
		foreach ($graph->allOfType('rr:TriplesMap') as $triplemap) {
			
			$subjecttemplate = $this->obtainSubjectTemplateFromTripleMap($triplemap);
//			echo "SubjectMap Template: ".$subjecttemplate."<br>";
//			echo "PredicateMaps...<br>"; 
			
			foreach ($triplemap->all('rr:predicateObjectMap') as $predicatemap) {
				$predicate = ''.$this->obtainPredicateFromPredicateMap($predicatemap);
//				echo "<br>Predicate: ".$predicate."<br>";
				
				$objectMap = ''.$this->obtainObjectMapFromPredicateMap($predicatemap);
//				echo "ObjectMap: ".$objectMap."<br>";
				
				if(!$this->ontology->isDataproperty( $store_Mysql , $predicate)) {
					//3.3 adding objectproperties   add($objectproperty, $uri, $mappedclassdomain_id, $mappedclassrange_id)
					
					$domain = $this->mappedclass->getMappedclassByURI(''.$subjecttemplate, $mappingspace_id);
					$range = $this->mappedclass->getMappedclassByURI($objectMap, $mappingspace_id);
									
					if(count($domain) >0 && count($range) > 0) {
						$this->mappedobjectproperty->add($predicate, $objectMap, $domain[0]->id, $range[0]->id);
						
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
	
	private function obtainLogicalTableFromTripleMap($triplemap) {

        $ret['query'] = "";
        $ret['value'] = "";

		$logictable = $triplemap->get('rr:logicalTable');
		
		if ( $logictable === null) return $ret;

		$tablename = $logictable->getLiteral('rr:tableName');

		if ( $tablename != null ) {
            $ret['query'] = false;
            $ret['value'] = $tablename->getValue();
        }

		$sqlquery = $logictable->getLiteral('rr:sqlQuery');

		if ( $sqlquery != null ){
            $ret['query'] = true;
            $ret['value'] = $sqlquery->getValue();
        }

		return $ret;
	}
	
	private function obtainTableFromQuery($query) {
		
		$table = "";
				
		$inx = stripos($query, " from ");
		if ($inx !== false) {
			$inx+= 6;
			$inx2 = stripos($query, "where");
			$tot= strlen($query);
			
			if ($inx2 !== false) 
				$tot = $inx2;
			$inx3 = stripos($query, "inner");
			if ($inx3 !== false && $inx3 < $inx2) 
				$tot = $inx3;						
			
			$table = substr($query, $inx, $tot -$inx-1);
		}
		
		return ''.$table;
	}
	
	private function obtainSubjectTemplateFromTripleMap($triplemap) {
		$subjecttemplate = "";
		
		$subjectmap = $triplemap->get('rr:subjectMap');
	
		if($subjectmap != null) {
			$template = $subjectmap->get('rr:template');
			
			if($template != null) {
				$subjecttemplate = $template->getValue();
			}
		}
		
		return ''.$subjecttemplate;
	}
	
	private function obtainColumnFromTemplate ($template) {
		$inx = stripos($template, "{");
		if ($inx !== false) {
			$template = substr($template, $inx+1, stripos($template, "}") -$inx-1);
		}
		
		return $template;
	}
	
	private function obtainSubjectClassFromTripleMap($triplemap) {
		$subjectclass = "";
		
		$subjectmap = $triplemap->get('rr:subjectMap');
	
		if($subjectmap != null) {
			$class = $subjectmap->get('rr:class');
			
			if($class != null) 
				$subjectclass = ''.$class;
			
		}
		return $subjectclass;
	}
			
	private function obtainPredicateFromPredicateMap($predicatemap) {
		$predicate = "";
		
		$predicate = $predicatemap->get('rr:predicate');
	
		
		return $predicate;
	}
	
	private function obtainObjectMapFromPredicateMap($predicatemap) {
		$column = "";
		
		$objectMap = $predicatemap->get('rr:objectMap');
		if($objectMap != null) {
			$column = $objectMap->get("rr:template");
			
			$rrcolumn = $objectMap->get("rr:column");
			
			if($rrcolumn != null) 
				$column = $rrcolumn;
		}
		
		return ''.$column;
	}

	private function obtainMappedTableColumn($column, $table)
	{
		$inx = stripos($column, ".");
		if ($inx !== false) {
			return str_replace (".", "->", $column);
		}
		
		return $table."->".$column;
	}
	
	private function obtainSQL($query, $column, $table)
	{
		if(0 == strcmp($query, $table)) 
		{
			return 'SELECT '.$column.' FROM '.$table;
		}
		
		return $query;		
	}

	private function regex_get_query_parts($query) {
		$matches = [];
		$regex = '@SELECT (?<select>.*) FROM (?<from>.*)@i';
		preg_match($regex, $query, $matches);
		return $matches;
	}

	private function regex_get_query_alias_list( $query ) {

		$query_parts = $this->regex_get_query_parts($query);
		if ( empty($query_parts['select']) ) return [];

		$matches = [];
		$regex = '@\\s*(?P<original>[a-zA-Z0-9_.]+) AS (?P<alias>[a-zA-Z0-9_]+),?\\s?@i';

		preg_match_all($regex, $query_parts['select'], $matches, PREG_SET_ORDER);

		return $matches;
	}

    private function regex_get_name_from_alias($query, $alias) {

        $alias_list = $this->regex_get_query_alias_list($query);

        foreach ( $alias_list as $a ) {
            if ( $a['alias'] == $alias ) return $a['original'];
        }

        return $alias;
    }

}
	

	
	
?>