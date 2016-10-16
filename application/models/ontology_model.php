<?php
class Ontology_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
	}
	
	function getOntologies() {
	
		$query = $this->db->get("ontology");
	
		return($query->result());	
	}
	
	function getOntology($id)
	{
		$this->db->where('id', $id);

		$query = $this->db->get("ontology");
		
		$ret = $query->result();
		return($ret[0]);	
	}

	function add($name, $user_id)
	{
		$this->db->insert('ontology', array('name' => $name,'date' => date("Y-m-d"),'user_id' => $user_id));
		
		return($this->db->insert_id());
	}

	function update($id, $name, $url)
	{
		$this->db->where('id', $id);
		$this->db->update('ontology', array('name' => $name,'date' => date("Y-m-d")));
	}

	function delete($id)
	{
		$this->db->where("ontology_id", $id);
		$this->db->delete("prefix");
		
		$this->db->where("ontology_id", $id);
		$this->db->delete("ontology_modules");
		
		$this->db->where("id", $id);
		$this->db->delete("ontology");
	}
	
	
	function addModule($name, $file, $url, $ontology_id)
	{
		$this->db->insert('ontology_modules', array('name' => $name,'file' => $file,'url' => $url,'ontology_id' => $ontology_id));
		
		return($this->db->insert_id());
	}

	
	
	
	
	function getOntologyModules($ontology_id)
	{
		$this->db->where('ontology_id', $ontology_id);

		$query = $this->db->get("ontology_modules");
		
		return($query->result());	
	}

	function getOntologyLayout( $datasource_id )
	{
		$this->db->where('datasource_id', $datasource_id);

		$query = $this->db->get("ontology_layout");

		return($query->result());
	}

	function setOntologyLayout( $datasource_id, $nodeid, $insert, $layoutX, $layoutY )
	{
		// Delete if exist.
		$this->db->where("datasource_id", $datasource_id);
		$this->db->where("nodeid", $nodeid);
		$this->db->delete("ontology_layout");

		if ( !$insert ) return;

		$this->db->insert('ontology_layout', array('nodeid' => $nodeid, 'layoutX' => $layoutX, 'layoutY' => $layoutY, 'datasource_id' => $datasource_id));

	}

	////////////////////////////////////////////////////////
	//
	
	function loadOntology($store_Mysql, $file, $ontology_id, $base_prefix)
	{
		set_time_limit(0);                   // ignore php timeout
		ignore_user_abort(true);             
		
		
		include_once("public/arc2/ARC2.php");
 
		require_once "public/easyrdf/EasyRdf.php";
		
	
		$graph = new EasyRdf_Graph();

		$ntriples = $graph->parseFile($file, "guess");


		$array = $graph->resources();


		$base_url = dirname($_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);


		$parser = ARC2::getRDFParser();


        //$parser->parse("http://localhost/mapon/upload/ontology.ttl");
		$parser->parse($base_url . "/" . $file);


		$easyRdf_initialNamespaces = EasyRdf_Namespace::namespaces();
		$file_namespaces_ttl = empty($parser->parser->prefixes) ? [] : $parser->parser->prefixes;
        $file_namespaces_xml = empty($parser->parser->nsp) ? [] : $parser->parser->nsp;

		$ns = $easyRdf_initialNamespaces;


        foreach( $file_namespaces_ttl as $prefix => $namespaceUri ) {
            $prefix = rtrim($prefix, ":"); //Eliminamos ':' del final del string.
            if ( $prefix == "" ) $prefix = $base_prefix;

            if ( !array_key_exists( $prefix, $ns ) ) {
                $ns[$prefix] = $namespaceUri;
            }
        }


        foreach( $file_namespaces_xml as $namespaceUri => $prefix ) {
            if ( $prefix == "" ) $prefix = $base_prefix;

            if ( !array_key_exists( $prefix, $ns ) ) {
                $ns[$prefix] = $namespaceUri;
            }
        }





		foreach(array_keys($ns) as $prefix){
			//echo "prefix ".$prefix.": ".$ns[$prefix]."<br>";
			if($this->prefix->getByIRI($ns[$prefix], $ontology_id) == ""){
			
				$this->prefix->add($prefix, $ns[$prefix], $ontology_id);
			}
		}

		$newtriples = array();

		  				
		foreach(array_keys($array) as $key){
			$props = $graph->properties($key);
			foreach($props as $prop){
			
				$reslit = $graph->all ($key, $prop);
				
				foreach($reslit as $v){
					
					//This is done to store the properties in URI format and not as QName.
					$prop = $this->prefix->getURI($prop, $ontology_id);
					 
					//echo "K: ".$key."| ".$prop."| ".$v."<br>";
					
					$nt["s"] = $key;
					$nt["p"] = $prop;
					$nt["o"] = $v;
					$nt["s_type"] = "uri";
					$nt["o_type"] = "uri";
					$nt["o_datatype"] = "";
					$nt["o_lang"] = "";
					
					array_push($newtriples, $nt);
				}
			}
		}
		
		
		//echo "tripoes. ".$ntriples."<br>";
		//var_dump($ns);
		
		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}
		
		$store_Mysql->insert($newtriples, 'rows');  
		
		
		
		//var_dump($newtriples);
		
		
		
		///////////////////////////////////////////////////////////////////////////////////
		// We are going to find and add domain and ranges for objectproperties
		
		$qDomain = 'SELECT  ?objproperties ?domain WHERE {
					?objproperties <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#ObjectProperty>.
					?b1 ?y ?objproperties .
					?b1 <http://www.w3.org/2000/01/rdf-schema#subClassOf> ?domain.
			}';
					
		$qRange = 'select ?objproperties ?range where { 
					?objproperties <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#ObjectProperty>.
					?b1 <http://www.w3.org/2002/07/owl#inverseOf> ?objproperties .
					?b2 <http://www.w3.org/2002/07/owl#onProperty> ?b1.
					?b2 <http://www.w3.org/2000/01/rdf-schema#subClassOf> ?range.
			}';	
		
		$ret = $store_Mysql->query($qRange,'rows');

		$newtriples = array();
		
			echo "RANGE: Triples: ".count($ret)."<br>";
		
		foreach($ret as $t){
			$nt["s"] = $t["objproperties"];
			$nt["p"] = "http://www.w3.org/2000/01/rdf-schema#range";
			$nt["o"] = $t["range"];
			$nt["s_type"] = "uri";
			$nt["o_type"] = "uri";
			$nt["o_datatype"] = "";
			$nt["o_lang"] = "";
			
			array_push($newtriples, $nt);
			
//				echo "RANGE: - ".$t["objproperties"]." -> ".$t["range"]. "<br>";
		}
		$store_Mysql->insert($newtriples, 'rows');    				


		$ret = $store_Mysql->query($qDomain,'rows');

		
			echo "DOMAIN: Triples: ".count($ret)."<br>";
		
		$newtriples = array();
		
		foreach($ret as $t){
			$nt["s"] = $t["objproperties"];
			$nt["p"] = "http://www.w3.org/2000/01/rdf-schema#domain";
			$nt["o"] = $t["domain"];
			$nt["s_type"] = "uri";
			$nt["o_type"] = "uri";
			$nt["o_datatype"] = "";
			$nt["o_lang"] = "";
			
			array_push($newtriples, $nt);
			
//				echo "DOMAIN: - ".$t["objproperties"]." -> ".$t["domain"]. "<br>";
		}
		$store_Mysql->insert($newtriples, 'rows');    				
		//
		///////////////////////////////////////////////////////////////////////////////////
		
		
		// TO ThiNK ABOUT prEfiXES
		//$this->updatePrefix($file);
	}
	
	function updatePrefix($baseprefix, $file, $ontology_id)
	{
		$str =file_get_contents($file);
	
		//echo "updatePrefix<br><br>";
		//var_dump($str);
	
		$doc = new DOMDocument();
		$doc->loadXML ( $str );//xml file loading here

		$context = $doc->documentElement;

		$xpath = new DOMXPath($doc);
		foreach( $xpath->query('namespace::*', $context) as $node ) {
			
			
			$nodeName = str_replace(":", "", trim($node->nodeName));
			$nodeName = str_replace("xmlns", "", trim($nodeName));
			
			if($nodeName == "") 
				$nodeName = $baseprefix;
			//echo   $nodeName . ": ".$node->nodeValue, "\n";
			
			if($this->prefix->getByIRI($node->nodeValue, $ontology_id) == ""){
			
				$this->prefix->add($nodeName, $node->nodeValue, $ontology_id);
			}
		}
	}

	function getClasses ($store_Mysql)
	{
		include_once("public/arc2/ARC2.php");
		
		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}
		
		$q = 'SELECT DISTINCT ?class WHERE { ?class <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#Class>.} order by ?class'; 
		
		$class = $store_Mysql->query($q, 'rows');
		
		return ($class);
	}
	
	function getClassesAndAnnotations ($store_Mysql)
	{
		include_once("public/arc2/ARC2.php");
		
		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}
		
		$q = 'SELECT DISTINCT ?class ?comment WHERE {
					{ ?class ?y <http://www.w3.org/2002/07/owl#Class> }
						UNION
					{ ?class ?y <http://www.w3.org/2000/01/rdf-schema#Class> }
					OPTIONAL 
					{ ?class <http://www.w3.org/2000/01/rdf-schema#comment> ?comment. }
				}'; 
		
		$class = $store_Mysql->query($q, 'rows');
		
		return ($class);
	}

	function checkClass ($store_Mysql, $class_uri)
	{
		include_once("public/arc2/ARC2.php");

		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}

		$q = 'SELECT COUNT(?x) as total_rows WHERE { ?x ?y ?z. <' . $class_uri . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#Class>.}';

		$class = $store_Mysql->query($q, 'rows');

		if ( isset($class[0]['total_rows']) && $class[0]['total_rows'] != "0" ) return true;
		return false;
	}

	function getAnnotationbyClass ($store_Mysql, $classname)
	{
		include_once("public/arc2/ARC2.php");
		
		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}
		
		$q = 'SELECT DISTINCT ?comment WHERE {
					<'.$classname.'> <http://www.w3.org/2000/01/rdf-schema#comment> ?comment. 
				}'; 
		
		$class = $store_Mysql->query($q, 'rows');
		
		return ($class);
	}

	function getSubClasses ($store_Mysql, $superclass)
	{
		include_once("public/arc2/ARC2.php");
		
		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}
		
		$q = 'SELECT DISTINCT ?class WHERE { 
				?class <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#Class>.
				?class <http://www.w3.org/2000/01/rdf-schema#subClassOf> <'.$superclass.'>.
			} order by ?class'; 
		
		//echo "Qes: ".$q."<br>";
		$class1 = $store_Mysql->query($q, 'rows');
		
		//var_dump($class1);
				
		$q = 'SELECT DISTINCT ?class WHERE { 
				?class <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#Class>.
				?class <http://www.w3.org/2000/01/rdf-schema#subClassOf> ?x.
				?x <http://www.w3.org/2000/01/rdf-schema#subClassOf> <'.$superclass.'>.				
		} order by ?class'; 
		
		//echo "Qes2: ".$q."<br>";
		$class2 = $store_Mysql->query($q, 'rows');
		
		//var_dump($class2);
		
		$q = 'SELECT DISTINCT ?class WHERE { 
				?class <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#Class>.
				?class <http://www.w3.org/2000/01/rdf-schema#subClassOf> ?y.
				?y <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#Class>.
				?y <http://www.w3.org/2000/01/rdf-schema#subClassOf> ?x.
				?x <http://www.w3.org/2000/01/rdf-schema#subClassOf> <'.$superclass.'>.				
		} order by ?class'; 
		
		//echo "Qes2: ".$q."<br>";
		$class3 = $store_Mysql->query($q, 'rows');
		
		//var_dump($class2);
		
		
		$class = array_merge($class1, $class2, $class3);
		return ($class);
	}		
	
	
	function getClassesByObjectProperties ($store_Mysql, $classname)
	{
		include_once("public/arc2/ARC2.php");
		
		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}

		$q = 'SELECT * WHERE { 
				?prop <http://www.w3.org/2000/01/rdf-schema#domain> <'.$classname.'>. 
				?prop <http://www.w3.org/2000/01/rdf-schema#range> ?class. 
			}'; 
		
		$class = $store_Mysql->query($q, 'rows');
		
		return ($class);
	}
		
	function getDataproperties ($store_Mysql)
	{
		include_once("public/arc2/ARC2.php");
		
		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}
		
		$q = 'SELECT DISTINCT ?datatype WHERE { ?datatype <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#DatatypeProperty>.}'; 
		
		$class = $store_Mysql->query($q, 'rows');
		
		return ($class);
	}
	/*
	function getDatapropertiesbyDomain ($store_Mysql, $class)
	{
		include_once("public/arc2/ARC2.php");
		
		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); 
		}
		
		$q = 'SELECT DISTINCT * WHERE { ?datatype <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#DatatypeProperty>.
		?datatype <http://www.w3.org/2000/01/rdf-schema#domain> <'.$class.'> .}'; 
		
		$class = $store_Mysql->query($q, 'rows');
		
		return ($class);
	}
	*/
	
	function getDatapropertiesbyDomain ($store_Mysql, $class)
	{
		include_once("public/arc2/ARC2.php");
		
		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}
		
		$q = 'SELECT DISTINCT * WHERE { ?datatype <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#DatatypeProperty>. 
		?datatype <http://www.w3.org/2000/01/rdf-schema#domain> <'.$class.'> .}
		'; 
	
		$class = $store_Mysql->query($q, 'rows');
		
		return ($class);
	}

	function checkDataProperty ($store_Mysql, $data_property_uri)
	{
		include_once("public/arc2/ARC2.php");

		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}

		$q = 'SELECT COUNT(?x) as total_rows WHERE { ?x ?y ?z. <' . $data_property_uri . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#DatatypeProperty>.}';

		$class = $store_Mysql->query($q, 'rows');

		if ( isset($class[0]['total_rows']) && $class[0]['total_rows'] != "0" ) return true;
		return false;
	}
		
	function getDatapropertiesWithoutDomain ($store_Mysql)
	{
		include_once("public/arc2/ARC2.php");
		
		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}
		
		$q = 'SELECT DISTINCT ?datatype WHERE { ?datatype <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#DatatypeProperty>. 
		}'; 
	    
		$class = $store_Mysql->query($q, 'rows');
		
		return ($class);
	}
	
	function isDataproperty ($store_Mysql, $dataproperty)
	{
		include_once("public/arc2/ARC2.php");
		
		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}
		
		$q = 'SELECT DISTINCT ?class WHERE { <'.$dataproperty.'> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?class. 
		}'; 
	
		$class = $store_Mysql->query($q, 'rows');

		for ( $i = 0; $i < count($class); $i++ ) {
			if( 0 == strcmp($class[$i]['class'], "http://www.w3.org/2002/07/owl#DatatypeProperty"))
				return true;
		}

		// This query returns two rows:
		// SELECT DISTINCT ?class WHERE { <http://xmlns.com/foaf/0.1/firstName> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?class }
		//if(count($class) >0)
		//	if(0== strcmp($class[0]['class'], "http://www.w3.org/2002/07/owl#DatatypeProperty"))
		//		return true;
		
		return false;
	}
	
	function getObjectpropertiesbyDomain ($store_Mysql, $class)
	{
		include_once("public/arc2/ARC2.php");
		
		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}
		
		$q = 'SELECT DISTINCT * WHERE { ?datatype <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#ObjectProperty>. 
		?datatype <http://www.w3.org/2000/01/rdf-schema#domain> <'.$class.'> .}
		'; 
		
		/*$q = 'prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
		SELECT DISTINCT * WHERE { ?datatype <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#ObjectProperty>. 
		?datatype ?x <'.$class.'> .
		FILTER(?x = "rdfs:domain").}
		';*/ 
		
		$class = $store_Mysql->query($q, 'rows');
		
		
	/*	var_dump($class);
		var_dump($store_Mysql->getErrors());
		*/
		return ($class);
	}
		
	function getObjectpropertiesWithoutDomain ($store_Mysql)
	{
		include_once("public/arc2/ARC2.php");
		
		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}
		
		$q = 'SELECT DISTINCT ?datatype WHERE { ?datatype <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#ObjectProperty>. 
		
		}'; 
		//FILTER NOT EXISTS {  ?datatype <http://www.w3.org/2000/01/rdf-schema#domain> ?u}
		$class = $store_Mysql->query($q, 'rows');
		
		return ($class);
	}
	
	function getRangeOfObjectProperty ($store_Mysql, $objectproperty)
	{
		include_once("public/arc2/ARC2.php");
		
		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}

		$q = 'SELECT * WHERE { 
				<'.$objectproperty.'> <http://www.w3.org/2000/01/rdf-schema#range> ?range. 
				OPTIONAL {
					?class <http://www.w3.org/2000/01/rdf-schema#subClassOf> ?range.
				}
			}'; 
			
		/*$q = 'prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
				SELECT DISTINCT * WHERE { 
				<'.$objectproperty.'> ?x ?range. 
				FILTER(?x = "rdfs:range").}
				'; 
		*/
		$class = $store_Mysql->query($q, 'rows');
		
		return ($class);
	}

	function checkObjectProperty ($store_Mysql, $object_property_uri)
	{
		include_once("public/arc2/ARC2.php");

		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}

		$q = 'SELECT COUNT(?x) as total_rows WHERE { ?x ?y ?z. <' . $object_property_uri . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#ObjectProperty>.}';

		$class = $store_Mysql->query($q, 'rows');

		if ( isset($class[0]['total_rows']) && $class[0]['total_rows'] != "0" ) return true;
		return false;
	}
	
	
	function getXSDDatatypes ($store_Mysql)
	{
		$ret = array();
		
		$ret[] = "xsd:string";
		$ret[] = "xsd:float";
		$ret[] = "xsd:double";
		$ret[] = "xsd:anyURI";
		$ret[] = "xsd:integer";
		$ret[] = "xsd:decimal";
		$ret[] = "xsd:language";
		$ret[] = "xsd:byte";
		$ret[] = "xsd:boolean";
		$ret[] = "xsd:dateTime";
		
		return ($ret);
	}

	
	function getVersion($store_Mysql)
	{
		include_once("public/arc2/ARC2.php");
		
		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}
		
		$q = 'SELECT DISTINCT ?version WHERE { ?o <http://www.w3.org/2002/07/owl#versionInfo> ?version.}'; 
		
		$version = $store_Mysql->query($q, 'rows');
		
		return ($version);
	}
	
	function getStatistics($store_Mysql)
	{
		include_once("public/arc2/ARC2.php");
		
		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}
		
		$data = array(); 
		
		$r = $store_Mysql->query('SELECT distinct ?s { ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#Class> }', 'rows');
		$data["nclasses"] = count ($r);

		$r = $store_Mysql->query('SELECT distinct ?s { ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#ObjectProperty> }', 'rows');
		$data["nobjprop"] = count ($r);
	
		$r = $store_Mysql->query('SELECT distinct ?s { ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#DatatypeProperty> }', 'rows');
		$data["ndataprop"] = count ($r);
		
		return ($data);
	}
	
	function getOwl($store_Mysql, $ontology_id)
	{
		include_once("public/arc2/ARC2.php");
		
		if (!$store_Mysql->isSetUp()) {
			$store_Mysql->setUp(); /* create MySQL tables */
		}

		$arc2 = new ARC2_Class(array(), new stdClass);

		$ont = $this->getOntology($ontology_id);
		 
		//$path = "upload/owl/".$ontology_id."_".$ont->name.".owl";
		//$pathSPOG = "upload/owl/".$ontology_id."_".$ont->name.".spog";


        $path = "upload/ontologies/". $ontology_id."_".$ont->name . "/";
        $parhOwl = $path . $ont->name.".owl";
        $pathSPOG = $path . $ont->name.".spog";

		/*
		$ret = $store_Mysql->query("CONSTRUCT { ?s ?p ?o } WHERE { ?s ?p ?o }");
		
		 var_dump($ret);
		*/
		
		$ret = $store_Mysql->createBackup($pathSPOG);
		//var_dump($ret);
		
		$parser = ARC2::getRDFParser();

		$base_url = dirname($_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);


		$parser->parse($base_url . "/" . $pathSPOG);
		$triples = $parser->getTriples();

		$rdfxml_doc = $parser->toRDFXML($triples);
		
		var_dump($rdfxml_doc);

		$fp = fopen($parhOwl, 'w');
            fwrite($fp, $rdfxml_doc);
			
		fclose($fp);
			
			
		
		//var_dump($store_Mysql->dump());
		
		//toRDFXML
		//$arc2->backupStoreData($store_Mysql, $path);
	}		
	
			
	function getPath($sourceClass, $targetClass)
	{
		$path = "";
		
		$this->db->where("SourceClass", $sourceClass);
		$this->db->where("TargetClass", $targetClass);
		$query = $this->db->get("EnergyModel_Paths");
		
		$retarray = $query->result();
		
		if(count($retarray) > 0) {
			$path = $retarray[0]->Path;
		}
		
		return $path;
	}
	///////////////////////////////////////////////
	// This function reads a previously generated CSV file which contains for each Energy Model class its superclasses.
	function loadInferencedSuperClasses()
	{
		$rows = file('http://arcdev.housing.salle.url.edu/sem/Semanco_merged_Sumo_superclasses.txt');
		
		foreach ($rows as $n => $row) {
			
			$pos = strpos($row, ',');
			
			if ($pos !== false) {
				echo substr($row, 0, $pos)."<br>";
				
				$data = array('Class' => substr($row, 0, $pos), 'Superclasses' => substr($row, $pos+1, strlen($row)-$pos-1));

				$this->db->insert('EnergyModel_SuperClasses', $data);
			}
		}
	}
}
	
?>