<?php
class Mapping_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('generators/sql_generator', 'sql_generator');
	}
	
	///////////////////////////////////////
	
	///////////////////////////////////////
	
	function generateSQL($tableRcolumn, $mappedclass_id, $datasource_id, $quotes = true)
	{	
		$sql = "";
		$classTableArr = explode ("->", $tableRcolumn);
		
		if(count($classTableArr) == 2) {
		
			$tables = Array();
			$joins = Array();
			$selects = array();
			
			$selects[strtolower($classTableArr[0].".".$classTableArr[1])] = 1;
			$tables[strtolower($classTableArr[0])] = 1;

			// Actual
			$classrange = $this->mappedclass->getMappedclass($mappedclass_id);
			if ( !empty($classrange[0]->mappedtablecolumn) ) {
				$path = $this->getPath($tableRcolumn, $classrange[0]->mappedtablecolumn, $datasource_id);

				$this->getSQLComponents($tableRcolumn, $classrange[0]->mappedtablecolumn, $path, $selects, $tables, $joins, $datasource_id);
			}


			$objectproperties = $this->mappedobjectproperty->getMappedobjectproperties($mappedclass_id);

			foreach ($objectproperties  as $objprop) {
				$classrange = $this->mappedclass->getMappedclass($objprop->targetId);
				
				$path = $this->getPath($tableRcolumn, $classrange[0]->mappedtablecolumn, $datasource_id);
				
				$this->getSQLComponents($tableRcolumn, $classrange[0]->mappedtablecolumn, $path, $selects, $tables, $joins, $datasource_id);

			}




			$dataproperties = $this->mappeddataproperty->getMappeddataproperties($mappedclass_id);
			
			foreach ($dataproperties  as $dataprop){
				
				if (!array_key_exists(strtolower($dataprop->value), $selects)) $selects[strtolower($dataprop->value)] = 1;
			}
			
			/*
			echo "<br>selects: <br>";
			var_dump($selects);
			echo "<br>tables: <br>";
			var_dump($tables);
			echo "<br>joins: <br>";			
			var_dump($joins);

			echo "<br>sql: <br>";			
			*/

			// We store the data in a clean array.

			$selkeys = array_keys($selects);
			$tabkeys = array_keys ($tables);
			$joikeys = array_keys ($joins);


			$sqlParts['select'] = [];

			foreach( $selkeys as $select_tablePcolumn ) {
				$select_tablePcolumn = explode( ".", strtolower($select_tablePcolumn) );

				$sel['table'] = $select_tablePcolumn[0];
				$sel['column'] = $select_tablePcolumn[1];

				$sqlParts['select'][] = $sel;
			}

			$sqlParts['from'] = [];

			if ( count($tabkeys) > 0 ) {
                $from_graphPtable = explode( ".", strtolower($tabkeys[0]) );

                if ( count($from_graphPtable) > 1 ) {
                    $from['postgresGraph'] = $from_graphPtable[0];
                    $from['table'] = $from_graphPtable[1];
                } else {
                    $from['table'] = $from_graphPtable[0];
                }

				$sqlParts['from'][] = $from;
			}

			$sqlParts['join'] = [];

			for ( $i = 1; $i < count($tabkeys); $i++ ) {
				//if ( empty($joikeys[$i-1]) ) continue;
				if ( empty($joikeys[$i-1]) ) {
					echo "<span style='color: red;'>";
					echo "Error creating join, not correcly saved in bd or table (" . $tabkeys[$i] . ") not exist.<br>";
					echo "</span>";
					continue;
				}

                $on = explode( "=", strtolower(str_replace(" ", "", $joikeys[$i-1])) );
				$join_tablePcolumn1 = explode( ".", $on[0] );
				$join_tablePcolumn2 = explode( ".", $on[1] );

				$join['table'] = strtolower($tabkeys[$i]);

				$join['table1'] = $join_tablePcolumn1[0];
				$join['column1'] = $join_tablePcolumn1[1];
				$join['table2'] = $join_tablePcolumn2[0];
				$join['column2'] = $join_tablePcolumn2[1];

				$sqlParts['join'][] = $join;
			}

			$sqlParts['where'] = [];

			for($i = 0; $i < count($selkeys); $i++) {
				$where_tablePcolumn = explode( ".", strtolower($selkeys[$i]) );

				$where['table'] = $where_tablePcolumn[0];
				$where['column'] = $where_tablePcolumn[1];

				$sqlParts['where'][] = $where;
			}


			$quote = $quotes ? '"' : "";

			$sql = $this->sql_generator->generateSql( $sqlParts, $quote );

		}
		
		
		return $sql;
	}
	
	///////////////////////////////////////
	
	function getSQLComponents($domain, $range, $path, &$selects, &$tables, &$joins, $datasource_id)
	{
		$pathkeys = array_keys($path);
		
		for($i = 0; $i < count($pathkeys) -1; $i++) {
			if (!array_key_exists(strtolower($pathkeys[$i]), $tables)) $tables[strtolower($pathkeys[$i])] = 1;

			$join = $this->getJoinBetween($pathkeys[$i], $pathkeys[$i+1], $selects, $datasource_id);
			
			if (!array_key_exists(strtolower($join), $joins)) $joins[strtolower($join)] = 1;
		}
		
		$strdomain = explode ("->", $domain);	
		$strrange = explode ("->", $range);	

		if (!array_key_exists(strtolower($strdomain[0]), $tables)) $tables[strtolower($strdomain[0])] = 1;
		if (!array_key_exists(strtolower($strrange[0]), $tables)) $tables[strtolower($strrange[0])] = 1;

		if (!array_key_exists(strtolower($strdomain[0].".".$strdomain[1]), $selects)) $selects[strtolower($strdomain[0].".".$strdomain[1])] = 1;
		if (!array_key_exists(strtolower($strrange[0].".".$strrange[1]), $selects)) $selects[strtolower($strrange[0].".".$strrange[1])] = 1;
	}
	
	///////////////////////////////////////

	function getJoinBetween($table1, $table2, &$selects, $datasource_id)
	{
		$col1 = $this->datasource->getColumnsByForeignTable($table2, $table1, $datasource_id );
		$col2 = $this->datasource->getColumnsByForeignTable($table1, $table2, $datasource_id );
		
		if(count($col1) > 0) {
			
			if (!array_key_exists(strtolower($table1.".".$col1[0]->foreignkey), $selects)) $selects[strtolower($table1.".".$col1[0]->foreignkey)] = 1;
			if (!array_key_exists(strtolower($table2.".".$col1[0]->columnname), $selects)) $selects[strtolower($table2.".".$col1[0]->columnname)] = 1;
			
			return $table1.".".$col1[0]->foreignkey." = ".$table2.".".$col1[0]->columnname;
			
		} else if(count($col2) > 0) {

			if (!array_key_exists(strtolower($table1.".".$col2[0]->columnname), $selects)) $selects[strtolower($table1.".".$col2[0]->columnname)] = 1;
			if (!array_key_exists(strtolower($table2.".".$col2[0]->foreignkey), $selects)) $selects[strtolower($table2.".".$col2[0]->foreignkey)] = 1;

			return $table1.".".$col2[0]->columnname." = ".$table2.".".$col2[0]->foreignkey;
		}
	}
	///////////////////////////////////////
	
	function getPath($domain, $target, $datasource_id) {
		//$path = "";
		
		$path = Array();
		
		$visited = Array();
		
		$domainTableArr = explode ("->", $domain);
		$targetTableArr = explode ("->", $target);		
		
		/*
		echo "1 Domain: ".$domain."<br>";
		echo "1 target: ".$target."<br>";
				
		echo "Domain: ".$domainTableArr[0]."<br>";
		echo "target: ".$targetTableArr[0]."<br><br>";
		*/
		$ret = $this->recursivePathDFS ($domainTableArr[0], $targetTableArr[0], $datasource_id, $visited, $path);
		//$ret = $this->recursivePathDFS ($domain, $target, $datasource_id, $visited, $path);
		
		
		//$this->breadh_first($domainTableArr[0], $targetTableArr[0], $datasource_id, $visited, $path);
		//$path = $path.$target;
		if (!array_key_exists(strtolower($targetTableArr[0]), $path)) 
			$path[strtolower($targetTableArr[0])] = 1;
		
		return $path;
	}
	
	function breadh_first($domain, $target, $datasource_id, $visited, &$path) 
	{
		$q = array();
		
		array_push($q, $domain);
		$visited[$domain] = 1;
		echo $domain . "\n";
	
		$path[] = $domain;
		
		while (count($q)) {
			$t = array_shift($q);
	 
			//We obtain the foreign keys to and back to the target.
			$domainTable = $this->datasource->getTableByName($domain, $datasource_id);
			$rangeTable = $this->datasource->getTableByName($target, $datasource_id);
			
			$nextTables = Array();
			//These are the direct connections of the target table through their foreign keys definitions.
			$foreignTables = $this->datasource->getForeignKeycolumns($rangeTable[0]->id);
				
			foreach($foreignTables as $foreigner) {
				if (!array_key_exists($foreigner->foreigntable."->".$foreigner->foreignkey, $nextTables)) 
					$nextTables[$foreigner->foreigntable."->".$foreigner->foreignkey] = 1;
			}
					
			// we are looking the other way around
			$pkColumn = $this->datasource->getPrimaryKeyColumn($rangeTable[0]->id);
			
			if(count($pkColumn) > 0) {
				$foreignTables = $this->datasource->getColumnsByForeignKey($pkColumn[0]->name, $target, $datasource_id);
			
				//var_dump($foreignTables);
				
				foreach($foreignTables as $foreigner) {
					if (!array_key_exists($foreigner->tablename."->".$foreigner->columnname, $nextTables)) 
						$nextTables[$foreigner->tablename."->".$foreigner->columnname] = 1;
				}
			}
	 
			foreach(array_keys($nextTables) as $ntable) {
				$ntableArr = explode ("->", $ntable);
				
				if (!array_key_exists($ntableArr[0], $visited)) 
				{
					$visited[$ntableArr[0]] = 1;
					array_push($q, $ntableArr[0]);
				
					echo $ntableArr[0] . "\t";
				}
				
			}
			
			/*
			foreach ($graph[$t] as $key => $vertex) {
				if (!$visited[$key] && $vertex == 1) {
					$visited[$key] = 1;
					array_push($q, $key);
					echo $key . "\t";
				}
			}
			
			*/
			 echo "\n";
        }
   	}
	
	
	function recursivePathDFS($domain, $target, $datasource_id, &$visited, &$path)
	{
		$visited[strtolower($target)] = 1;
		$ret = "";
	
		if(strcasecmp ($domain, $target) == 0) {
			//we found it
			//$path = $target;
		
			return '>'.$target;
		}
		
		//We obtain the foreign keys to and back to the target.
		$domainTable = $this->datasource->getTableByName($domain, $datasource_id);
		$rangeTable = $this->datasource->getTableByName($target, $datasource_id);
		
		$nextTables = Array();
		
		if(count($rangeTable) > 0) {
			//These are the direct connections of the target table through their foreign keys definitions.
			$foreignTables = $this->datasource->getForeignKeycolumns($rangeTable[0]->id);
				
			foreach($foreignTables as $foreigner) {
				if (!array_key_exists($foreigner->foreigntable."->".$foreigner->foreignkey, $nextTables)) 
					$nextTables[$foreigner->foreigntable."->".$foreigner->foreignkey] = 1;
			}
					
			// we are looking the other way around
			$pkColumn = $this->datasource->getPrimaryKeyColumn($rangeTable[0]->id);
			
			if(count($pkColumn) > 0) {
				$foreignTables = $this->datasource->getColumnsByForeignKey($pkColumn[0]->name, $target, $datasource_id);
			
				//var_dump($foreignTables);
				
				foreach($foreignTables as $foreigner) {
					if (!array_key_exists($foreigner->tablename."->".$foreigner->columnname, $nextTables)) 
						$nextTables[$foreigner->tablename."->".$foreigner->columnname] = 1;
				}
			}
		}
		
		/*
		echo "<br>Next Tables for ".$target."<br>";
		var_dump($nextTables);
		echo "<br><br>";
		*/
		
		//We iterate for the text tables to inspect		
		foreach(array_keys($nextTables) as $ntable) {
			$ntableArr = explode ("->", $ntable);
			
			if (array_key_exists(strtolower($ntableArr[0]), $visited) && $ret == "") {
			} else {
		
				$retRec = $this->recursivePathDFS($domain , $ntableArr[0], $datasource_id, $visited, $path) ;

				if( $retRec != "") {
					$ret = $retRec . '>' . $ntable;
						
				//	$path[] = $ntableArr[0];
					
					if (!array_key_exists(strtolower($ntableArr[0]), $path)) 
						$path[strtolower($ntableArr[0])] = 1;
					
			//		echo "<br>Path: ".$ntable."<br>";
			//		var_dump($path);
				}
			}
		}
		
		return $ret;
	}	
	
	/*
	selectPath = function (nodeid) {

		var visited = new Array();
		
		var pathHtml = '';
		var outCompletePath =  { text :  ''};
		
		
		console.log("root: " + '<?php echo $mappings[$mapClass->id]; ?>');
		console.log("target: " + nodeid);
							
		pathHtml = recursivePathHTMLDFS (nodeid, visited, graph, 1, outCompletePath);

		//console.log(visited);
//			    $('#select_path').html(pathHtml);
//				$('#input_select_path').value = pathHtml;
		
		
		//console.log("input_select_path: " + outCompletePath.text);
		//document.getElementById('input_select_path').value = outCompletePath.text;
		
		return outCompletePath.text + '|' +nodeid;
	}
	*/
		/*		
		recursivePathHTMLDFS = function (nodeid, visited, graph, backlinktype, outCompletePath) {
			visited[nodeid] = 1;
			var ret = "";
			
			if(nodeid == '<?php echo $mappings[$mapClass->id]; ?>') {
				//console.log("found: " + nodeid);
				outCompletePath.text = nodeid;
				return  '>'+nodeid;
			}
			
			graph.forEachLinkedNode(nodeid, function(nodeTo, link){
				if(nodeTo.id in visited && ret == "") {
				} else {
					retRec = recursivePathHTMLDFS(nodeTo.id, visited, graph, link.data.type, outCompletePath);

					if( retRec != "") {
						
					
						if(backlinktype == 2 || backlinktype == 5) {
							ret = retRec + '>'+nodeid;
							
							outCompletePath.text = outCompletePath.text + '|' + nodeid;
						}
					}
				}
			});
			
			return ret;
		}
		*/
	///////////////////////////////////////
	
	function generateDatapropertyValue($input_table)
	{
		$arr = explode ("->", $input_table);
		
		if(count($arr) == 2) {
			return $arr[0].".".$arr[1];
		} else {
			return "";
		}
	}
	
	///////////////////////////////////////
		
	//Intern function
	function generateURI($input_class, $input_table, $basic_uri, $ontology_id, $quotes = true)
	{
		$qname = $this->prefix->getQName($input_class, $ontology_id);
		$arr = explode ("->", $input_table);
		$arr2 = explode (":", $qname);


		$q = $quotes ? '"' : "";

		if(count($arr2) == 2) {
			if(count($arr) == 2) {
				return $basic_uri.strtolower($arr2[1]).'/{'. $q . strtolower($arr[0].$arr[1]) . $q .'}';
			} else {
				return $basic_uri.strtolower($arr2[1]).'/{\"...\"}';
			}
		} else {
			return '';
		}
	}
	
	///////////////////////////////////////
	
	
	function generateObjectpropertyURI($input_object, $input_table, $basic_uri, $ontology_id)
	{
		$uriclass = $this->prefix->getURI($input_object, $ontology_id);
		
		$store_Mysql = $this->workspaces->connect_workspace("ontology_".$ontology_id); 
	
		$dp = $this->ontology->getRangeOfObjectProperty($store_Mysql, $uriclass);
		
		$qname = $this->prefix->getQname($dp[0]["range"], $ontology_id);
		
		$arr2 = explode (":", $qname);
		
		$input_table = str_replace ('->', '',$input_table);

		$ret = '';
		
		if(count($arr2) == 2) 
			$ret = $basic_uri.strtolower($arr2[1]).'/{'.$input_table.'}';

		return $ret;
	}
	
	///////////////////////////////////////
	
	function generateObjectpropertyTarget($input_object, $mappingspace_id, $ontology_id)
	{
		$ret = '';
		
		$uriclass = $this->prefix->getURI($input_object, $ontology_id);
		
		$store_Mysql = $this->workspaces->connect_workspace("ontology_".$ontology_id); 
	
	
		$dp = $this->ontology->getRangeOfObjectProperty($store_Mysql, $uriclass);
		
		//var_dump($uriclass);
		//var_dump($dp);
		
		if(count($dp) > 0) {
			$qname = $this->prefix->getQname($dp[0]["range"], $ontology_id);
		
			//Looking for Sub Classes
			$subclasses = $this->ontology->getSubClasses($store_Mysql, $dp[0]["range"]);
			$optionsSub = "";
			foreach($subclasses as $row){
				$qnameSub = $this->prefix->getQname($row["class"], $ontology_id);
				//$optionsSub = $optionsSub. '	<option value="New:#:'.$qnameSub.'">Subclass: '.$qnameSub.'</option>';
				$optionsSub = $optionsSub. '	<div class="item" data-value="New:#:'.$qnameSub.'">Subclass: '.$qnameSub.'</div>';
			}
			
			//Looking for Existig mappings
			$mclasses = $this->mappedclass->getMappedclassByClass($dp[0]["range"], $mappingspace_id);

			$options = "";

			foreach($mclasses as $row) {

				//$options = $options. '	<option value="'.$row->id.'">'. $this->prefix->getQname($row->class, $ontology_id).' mapped to: '.$row->mappedtablecolumn.'</option>';
				$options = $options. '	<div class="item" data-value="'.$row->id.'">'.$this->prefix->getQname($row->class, $ontology_id).' mapped to: '.$row->mappedtablecolumn.'</div>';
			}
			
				/*	
			$ret = '';
			$ret = $ret. '<select name="input_target" id="input_target" onChange="onChangeTarget();">';
			$ret = $ret. '	<option value="New:#:'.$qname.'">New mapping to: \''.$qname.'\'</option>';
			$ret = $ret. '	<option value="---">---</option>';
			$ret = $ret. '	'.$optionsSub;
			$ret = $ret. '	<option value="---">---</option>';
			$ret = $ret. '	'.$options;
			$ret = $ret. '</select>';
			*/
			
			$ret = '';
			$ret = $ret. ' 	<div class="item" data-value="New:#:'.$qname.'">New mapping to: '.$qname.'</div>';
			if($optionsSub != "") {
				$ret = $ret. '	<div class="item" data-value="---">---</div>';
				$ret = $ret. '	'.$optionsSub;
			}
			if($options != "") {
				$ret = $ret. '	<div class="item" data-value="---">---</div>';
				$ret = $ret. '	'.$options;
			}
		}
		
		return $ret;
	}
	
	///////////////////////////////////////
}
	

	
	
?>