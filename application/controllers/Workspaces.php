<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Workspaces extends CI_Controller {
    
    public function read_owl_file_form(){
        //echo "FUNCTION read_owl_file_form() Workspaces.php<br />";
        
        $this->load->helper('form');
        
        $data['id'] = $this->input->post('id');
        $data['url'] = $this->input->post('url');
        
        $this->load->view('workspace/owl_file', $data);
        
        //echo "END FUNCTION read_owl_file_form() Workspaces.php<br />";
    }
    
    public function read_owl_file(){
        //echo "FUNCTION read_owl_file_form() Workspaces.php<br />";
        
        $id = $this->input->post('id');
        
        // upload file
        $target_path = "./upload/".$this->team->dir()."/";
        $target_path = $target_path . basename( $_FILES[$id.'_owl_file_input']['name']); 
        
        if ((($_FILES[$id."_owl_file_input"]["type"] == "application/octet-stream") || ($_FILES[$id."_owl_file_input"]["type"] == "application/rdf+xml")) && ($_FILES[$id."_owl_file_input"]["size"] < 6000000)){
        
            if(move_uploaded_file($_FILES[$id.'_owl_file_input']['tmp_name'], $target_path)) {
                $this->load->model('workspaces_model');                
                $name = strtolower($this->input->post($id.'_input_name'));
                $workspaces = $this->workspaces_model->find_data_workspace($name);        
        
                if($workspaces == null){
                    $this->workspaces_model->add($name, $_FILES[$id.'_owl_file_input']['name']); 
                }else{
                    $this->workspaces_model->modify($workspaces[0]->id, $name, $_FILES[$id.'_owl_file_input']['name']); 
                }
                
                echo "The file ".basename( $_FILES[$id.'_owl_file_input']['name'])." has been uploaded";
                
                //read owl/rdf file
                include_once("public/arc2/ARC2.php");
                
                $parser = ARC2::getRDFParser();
                $parser->parse(base_url('upload/'.$this->team->dir()."/".$_FILES[$id.'_owl_file_input']['name']));
                $triples = $parser->getTriples();
                
                //config of mysql and add triples to it.
                
                $store_Mysql = $this->workspaces_model->connect_workspace($name);
                $store_Mysql->insert($triples, 'rows');
                
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


                foreach($ret as $t){
                        $nt["s"] = $t["objproperties"];
                        $nt["p"] = "http://www.w3.org/2000/01/rdf-schema#range";
                        $nt["o"] = $t["range"];
                        $nt["s_type"] = "uri";
                        $nt["o_type"] = "uri";
                        $nt["o_datatype"] = "";
                        $nt["o_lang"] = "";

                        array_push($newtriples, $nt);
                }
                $store_Mysql->insert($newtriples, 'rows');    				


                $ret = $store_Mysql->query($qDomain,'rows');

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
                }
                $store_Mysql->insert($newtriples, 'rows');    				
                //
                ///////////////////////////////////////////////////////////////////////////////////
                
                $this->load->model('treemaps_model');
        
                $exist = $this->treemaps_model->exist_treemap($name);

                if(!$exist){
                    $treemap = $this->treemaps_model->add_treemap_nodes($name);
                }
                
                $url = $this->input->post('url');
                redirect($url,'location');
            }else{
                echo "There was an error uploading the file, please try again!";
            }
        }else{
            echo "Type or size file was invalid.";
        }
        //echo "END FUNCTION read_owl_file_form() Workspaces.php<br />";     
    }
    
    public function read_sparql_endpoint_form(){
        //echo "FUNCTION read_sparql_endpoint_form() Workspaces.php<br />";   
        
        $this->load->helper('form');
        
        $data['id'] = $this->input->post('id');
        $data['url'] = $this->input->post('url');
        
        $this->load->view('workspace/sparql_endpoint', $data);
        
        //echo "END FUNCTION read_sparql_endpoint_form() Workspaces.php<br />"; 
    }
    
    public function read_sparql_endpoint(){
        //echo "FUNCTION read_sparql_endpoint() Workspaces.php<br />";
        
        $name = strtolower($this->input->post('name'));
        $graph = $this->input->post('graph');
        $endpoint = $this->input->post('endpoint');
        
        if($graph != null ){
            $from = 'FROM <'.$graph.'>';
        }else{
            $from = '';
        }
        
        $this->load->model('workspaces_model');
        $workspaces = $this->workspaces_model->find_data_workspace($name);        
        
        if($workspaces == null){
            $this->workspaces_model->add($name, $endpoint); 
        }else{
            $this->workspaces_model->modify($workspaces[0]->id, $name, $endpoint); 
        }
        
        $store_Mysql = $this->workspaces_model->connect_workspace($name); 
        
        include_once("public/arc2/ARC2.php");
        
        $config_endpoint = array(
            // remote endpoint 
            'remote_store_endpoint' => $endpoint,
        ); 
        // instantiation 
        $store = ARC2::getRemoteStore($config_endpoint);
        
        
        // PROPIETATS
        // There is a bug if the variables isn't named ?s ?p ?o
        $properties = 'SELECT DISTINCT ?s ?p ?o '.$from.' WHERE {
            { ?s ?p ?o . 
                FILTER ( ?o = <http://www.w3.org/2002/07/owl#ObjectProperty>) .
            }
                UNION
            { ?s ?p ?o . 
                FILTER ( ?o = <http://www.w3.org/1999/02/22-rdf-syntax-ns#Property> ) .
            }
        }
        ORDER BY ?s
        ';
        $this->read_triples_in_pieces($properties,$store,$store_Mysql);
        
        
        // Data Properties
        $dataproperties = 'SELECT DISTINCT ?s ?p ?o '.$from.' WHERE {
            { ?s ?p ?o . 
                FILTER ( ?o = <http://www.w3.org/2002/07/owl#DatatypeProperty>) .
            }
                UNION
            { ?s ?p ?o . 
                FILTER ( ?o = <http://www.w3.org/2000/01/rdf-schema#Datatype> ) .
            } 
        }
        ORDER BY ?s';
        $this->read_triples_in_pieces($dataproperties,$store,$store_Mysql);
        
        
        // Annotation Properties
        $annotation = 'SELECT distinct ?s ?p ?o '.$from.' WHERE {
            ?s ?p ?o .
            FILTER ( ?o = <http://www.w3.org/2002/07/owl#AnnotationProperty>) .
        }
        ORDER BY ?s';
        $this->read_triples_in_pieces($annotation,$store,$store_Mysql);
        
        
        // CLASSES
        $class = 'SELECT DISTINCT ?s ?p ?o '.$from.' WHERE {
            {   ?s ?p ?o .
                FILTER ( ?o = <http://www.w3.org/2002/07/owl#Class>) .
            }
                UNION
            {   ?s ?p ?o .
                FILTER ( ?o = <http://www.w3.org/2000/01/rdf-schema#Class>) .
            }    
        }
        ORDER BY ?s';
        $this->read_triples_in_pieces($class,$store,$store_Mysql);
        
        
        // Annotation Properties data
        $annotation_data = 'SELECT DISTINCT ?s ?p ?o '.$from.' WHERE {
            ?s ?y <http://www.w3.org/2002/07/owl#AnnotationProperty> .
            ?s ?p ?o.
        }
        ORDER BY ?s';
        $this->read_triples_in_pieces($annotation_data,$store,$store_Mysql);
        
        
        //specific annotations
        $annotation_label = 'SELECT DISTINCT ?s ?p ?o '.$from.' WHERE {
            ?s ?p ?o .
            FILTER ( ?p = <http://www.w3.org/2000/01/rdf-schema#label>) .
        }
        ORDER BY ?s';
        $this->read_triples_in_pieces($annotation_label,$store,$store_Mysql);
        
        
        //annotation isdefinedby
        $annotation_isDefinedBy = 'SELECT DISTINCT ?s ?p ?o '.$from.' WHERE {
            ?s ?p ?o .
            FILTER ( ?p = <http://www.w3.org/2000/01/rdf-schema#isDefinedBy>) .
        }
        ORDER BY ?s';
        $this->read_triples_in_pieces($annotation_isDefinedBy,$store,$store_Mysql);
        
        
        //annotation seeAlso
        $annotation_seealso='SELECT DISTINCT ?s ?p ?o '.$from.' WHERE {
            ?s ?p ?o .
            FILTER ( ?p = <http://www.w3.org/2000/01/rdf-schema#seeAlso>) .
        }
        ORDER BY ?s';
        $this->read_triples_in_pieces($annotation_seealso,$store,$store_Mysql);
        
        
        //annotation comment
        $annotation_comment='SELECT DISTINCT ?s ?p ?o '.$from.' WHERE {
            ?s ?p ?o .
            FILTER ( ?p = <http://www.w3.org/2000/01/rdf-schema#comment>) .
        }
        ORDER BY ?s';
        $this->read_triples_in_pieces($annotation_comment,$store,$store_Mysql);

        
        //RELACIONS DOMAIN
        $domain = 'SELECT DISTINCT ?s ?p ?o '.$from.' WHERE { 
            ?s ?p ?o .
            FILTER ( ?p = <http://www.w3.org/2000/01/rdf-schema#domain>).
        }
        ORDER BY ?s';
        $this->read_triples_in_pieces($domain,$store,$store_Mysql);
        
        
        //RELACIONS RANGE
        $range = 'SELECT DISTINCT ?s ?p ?o '.$from.' WHERE { 
            ?s ?p ?o .
            FILTER (?p = <http://www.w3.org/2000/01/rdf-schema#range>) .
        }
        ORDER BY ?s';
        $this->read_triples_in_pieces($range,$store,$store_Mysql);

        
        //RELACIONS SUBCLASSOF
        $subclassof = 'SELECT DISTINCT ?s ?p ?o '.$from.' WHERE {
            ?s ?p ?o .
            FILTER (?p = <http://www.w3.org/2000/01/rdf-schema#subClassOf>) .
        }
        ORDER BY ?s';
        $this->read_triples_in_pieces($subclassof,$store,$store_Mysql);
        
        
        $equivalent = 'SELECT DISTINCT ?s ?p ?o '.$from.' WHERE {
            ?s ?p ?o .
            FILTER (?p = <http://www.w3.org/2002/07/owl#equivalentClass> ) .
        }
        ORDER BY ?s';
        $this->read_triples_in_pieces($equivalent,$store,$store_Mysql);
        
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


        foreach($ret as $t){
                $nt["s"] = $t["objproperties"];
                $nt["p"] = "http://www.w3.org/2000/01/rdf-schema#range";
                $nt["o"] = $t["range"];
                $nt["s_type"] = "uri";
                $nt["o_type"] = "uri";
                $nt["o_datatype"] = "";
                $nt["o_lang"] = "";

                array_push($newtriples, $nt);
        }
        $store_Mysql->insert($newtriples, 'rows');    				


        $ret = $store_Mysql->query($qDomain,'rows');

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
        }
        $store_Mysql->insert($newtriples, 'rows');    				
        //
        ///////////////////////////////////////////////////////////////////////////////////
        
        $this->load->model('treemaps_model');

        $exist = $this->treemaps_model->exist_treemap($name);

        if(!$exist){
            $treemap = $this->treemaps_model->add_treemap_nodes($name);
        }
        
        $this->load->helper('form');
        $data['id'] = $this->input->post('id');
        $data['msg'] = ucfirst($this->input->post('name'))." load is completed.";
        $data['url'] = $this->input->post('url');
        $data['redirect'] = $this->input->post('url');
        $this->load->view('workspace/sparql_endpoint', $data);
        
        //echo "END FUNCTION read_sparql_endpoint() Workspaces.php<br />";
    }
    
    // 100 rows per round
    private function read_triples_in_pieces($query_pre,$store,$store_Mysql){
        //echo "FUNCTION read_triple_in_pieces(query_pre,store,store_mysql) Workspaces.php<br />";
        
        $i = 0;
        $query = $query_pre.' LIMIT 100 OFFSET '.$i;
        $rows = $store->query($query, 'rows');
        
        while($rows != array()){
            
            $store_Mysql->insert($rows, 'rows');
           
            $i=$i+100;
            $query = $query_pre.' LIMIT 100 OFFSET '.$i;
            
            $rows = $store->query($query, 'rows');
            
        }
        //unset($rows); //save memory to read big ontologies.
        sleep(1);
        
        //echo "END FUNCTION read_triple_in_pieces(query_pre,store,store_mysql) Workspaces.php<br />";
    }
    
    function view_all_data_workspace($name){
        //echo "FUNCTION view_all_data_workspaces() Workspaces.php<br />";
        
        $this->load->model('workspaces_model');
        $store_Mysql = $this->workspaces_model->connect_workspace($name);

        $all = 'SELECT * WHERE { ?s ?p ?o}';

        $all_result = $store_Mysql->query($all, 'rows');
        echo "<pre>";
        var_dump($all_result);
        echo "</pre>";
        
        //echo "END FUNCTION view_all_data_workspaces() Workspaces.php<br />";
    }
    
    //ajax
    function annotations_ontology(){
        //echo "FUNCTION annotations_ontology() Workspaces.php<br />";
        
        $this->load->model('workspaces_model');
        $this->load->model('prefixes_model');
        
        $store_Mysql = $this->workspaces_model->connect_workspace($this->input->post('workspace'));
        $selected_class_uri = $this->prefixes_model->get_prefix_and_uri($this->input->post('selected_class'));
        
        $selected_ontology = substr($selected_class_uri['uri'], 0, strlen($selected_class_uri['uri'])-1);
        
        $ontology_annotations_query = 'SELECT DISTINCT ?property ?literal WHERE{
            <'.$selected_ontology.'> ?property ?literal .
        }';
        
        $ontology_annotations = $store_Mysql->query($ontology_annotations_query,'rows');
        
        for($i=0;$i<count($ontology_annotations);$i++){
            $annotation_data = $this->prefixes_model->get_prefix_and_uri($ontology_annotations[$i]['property']);
            $ontology_annotations[$i]['annotation_class'] = ucfirst($annotation_data['class']);
        }
        $data['ontology_annotations'] = $ontology_annotations;
        $data['ontology_prefix'] = ucfirst($selected_class_uri['prefix']);
        
        $this->load->view('workspace/ontology_annotations',$data);
        
        //echo "END FUNCTION annotations_ontology() Workspaces.php<br />";
    }
    
    //ajax
    function annotations_class(){
        //echo "FUNCTION annotations_class() Workspaces.php<br />";
        
        $this->load->model('workspaces_model');
        $store_Mysql = $this->workspaces_model->connect_workspace($this->input->post('workspace'));
        
        $this->load->model('prefixes_model');
        $selected_class = $this->prefixes_model->get_prefix_and_uri($this->input->post('selected_class'));
        $data['ontology_prefix'] = $selected_class['prefix'];
        $data['class_name'] = $selected_class['class']; 
        
        //get all annotations properties of a class
        $annotation_properties = 'SELECT DISTINCT ?class ?property ?literal WHERE{
            {
                ?class ?property ?literal .
                FILTER ( ?class = <'.$this->input->post('selected_class').'> ) .
                FILTER ( ?property = <http://www.w3.org/2000/01/rdf-schema#comment>
                || ?property = <http://www.w3.org/2000/01/rdf-schema#seeAlso> 
                || ?property = <http://www.w3.org/2000/01/rdf-schema#isDefinedBy> 
                || ?property = <http://www.w3.org/2000/01/rdf-schema#label> ) .
            }
            UNION
            {
                ?class ?property ?literal .
                ?property ?y <http://www.w3.org/2002/07/owl#AnnotationProperty> .
                FILTER (?class = <'.$this->input->post('selected_class').'>) .
            }
        }';
        
        $class_annotations = $store_Mysql->query($annotation_properties, 'rows');
        
        for($i=0;$i<count($class_annotations);$i++){
            $annotation_data = $this->prefixes_model->get_prefix_and_uri($class_annotations[$i]['property']);
            $class_annotations[$i]['annotation_class'] = ucfirst($annotation_data['class']);
        }
        $data['class_annotations'] = $class_annotations;
        
        $annotation_dataproperty = 'SELECT DISTINCT ?class ?relation WHERE {
            {
                ?relation <http://www.w3.org/2000/01/rdf-schema#domain> <'.$this->input->post('selected_class').'>.
                ?relation <http://www.w3.org/2000/01/rdf-schema#range> ?class .
            }
            UNION
            {
                ?relation <http://www.w3.org/2000/01/rdf-schema#range> <'.$this->input->post('selected_class').'> .
                ?relation <http://www.w3.org/2000/01/rdf-schema#domain> ?class .
            }
            ?relation <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?type .
            FILTER isIRI( ?class ) .
            FILTER ( ?type = <http://www.w3.org/2002/07/owl#DatatypeProperty> || ?type = <http://www.w3.org/2000/01/rdf-schema#Datatype> ) .    
        }'; 
        
        $class_dataproperties = $store_Mysql->query($annotation_dataproperty, 'rows');
        
        $new_array_dataproperty = array();
        for($i=0;$i<count($class_dataproperties);$i++){
            $class_data = $this->prefixes_model->get_prefix_and_uri($class_dataproperties[$i]['class']);
            $relation_data = $this->prefixes_model->get_prefix_and_uri($class_dataproperties[$i]['relation']);
            $new_array_dataproperty[$i]['class'] = ucfirst($class_data['class']);
            $new_array_dataproperty[$i]['relation'] = ucfirst($relation_data['class']);
        }
        
        $data['class_dataproperties'] = $new_array_dataproperty;
        
        $this->load->view('workspace/class_annotations', $data);
        
        //echo "END FUNCTION annotations_class() Workspaces.php<br />";
    }
    
    //ajax to fill the tooltips
    function annotation(){
        //echo "FUNCTION annotation() Workspaces.php<br />";
        
        $this->load->model('workspaces_model');
        $store_Mysql = $this->workspaces_model->connect_workspace($this->input->post('workspace'));
        
        //get all annotations properties of a class
        $annotation_properties = 'SELECT DISTINCT ?class ?property ?literal WHERE{
            {
                ?class ?property ?literal .
                FILTER ( ?class = <'.$this->input->post('selected_class').'> ) .
                FILTER ( ?property = <http://www.w3.org/2000/01/rdf-schema#comment> 
                || ?property = <http://www.w3.org/2000/01/rdf-schema#seeAlso> 
                || ?property = <http://www.w3.org/2000/01/rdf-schema#isDefinedBy> 
                || ?property = <http://www.w3.org/2000/01/rdf-schema#label>) .
            }
            UNION
            {
                ?class ?property ?literal .
                ?property ?y <http://www.w3.org/2002/07/owl#AnnotationProperty> .
                FILTER (?class = <'.$this->input->post('selected_class').'>) .
            }
        }';
        
        $class_annotations = $store_Mysql->query($annotation_properties, 'rows');
        
        $this->load->model('prefixes_model');
        for($i=0;$i<count($class_annotations);$i++){
            $annotation_data = $this->prefixes_model->get_prefix_and_uri($class_annotations[$i]['property']);
            $class_annotations[$i]['annotation_class'] = ucfirst($annotation_data['class']);
        }
        $data['class_annotations'] = $class_annotations;
        
        $annotation_dataproperty = 'SELECT DISTINCT ?class ?relation WHERE {
            {
                ?relation <http://www.w3.org/2000/01/rdf-schema#domain> <'.$this->input->post('selected_class').'>.
                ?relation <http://www.w3.org/2000/01/rdf-schema#range> ?class .
            }
            UNION
            {
                ?relation <http://www.w3.org/2000/01/rdf-schema#range> <'.$this->input->post('selected_class').'> .
                ?relation <http://www.w3.org/2000/01/rdf-schema#domain> ?class .
            }
            ?relation <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?type .
            FILTER isIRI( ?class ) .
            FILTER ( ?type = <http://www.w3.org/2002/07/owl#DatatypeProperty> || ?type = <http://www.w3.org/2000/01/rdf-schema#Datatype> ) .    
        }'; 
        
        $class_dataproperties = $store_Mysql->query($annotation_dataproperty, 'rows');
        
        $new_array_dataproperty = array();
        for($i=0;$i<count($class_dataproperties);$i++){
            $class_data = $this->prefixes_model->get_prefix_and_uri($class_dataproperties[$i]['class']);
            $relation_data = $this->prefixes_model->get_prefix_and_uri($class_dataproperties[$i]['relation']);
            $new_array_dataproperty[$i]['class'] = ucfirst($class_data['class']);
            $new_array_dataproperty[$i]['relation'] = ucfirst($relation_data['class']);
        }
        
        $data['class_dataproperties'] = $new_array_dataproperty;
            
        $selected_class = $this->prefixes_model->get_prefix_and_uri($this->input->post('selected_class'));
        $data['class_name'] = $selected_class['class'];
        $data['ontology_prefix'] = $selected_class['prefix'];
        
        $this->load->view('workspace/tooltip_annotations',$data);
        
        //echo "END FUNCTION annotation() Workspaces.php<br />";
    }
    
    //ajax 
    function relations(){
        //echo "FUNCTION relations() Workspaces.php<br />";
        
        $this->load->model('workspaces_model');
        $this->load->model('prefixes_model');
        
        $store_Mysql = $this->workspaces_model->connect_workspace($this->input->post('workspace'));
        $selected_class_uri = $this->input->post('selected_class');
        $subclassOf = $this->input->post('subclassof');
        $aggregation = $this->input->post('aggregation');
        $id = $this->input->post('id');
        
        $data_focused = $this->prefixes_model->get_prefix_and_uri($selected_class_uri);
        
        $domain_relations = array();
        $range_relations = array();
        $subClassOf_domain_relations = array();
        $subClassOf_range_relations = array();
        
        if($aggregation){
            $range = 'SELECT DISTINCT ?class_uri ?relation_uri WHERE {
                ?relation_uri <http://www.w3.org/2000/01/rdf-schema#range> <'.$selected_class_uri.'> .
                ?relation_uri <http://www.w3.org/2000/01/rdf-schema#domain> ?class_uri .
                ?relation_uri <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?type .
                FILTER isIRI(?class_uri) .
                FILTER ( ?type != <http://www.w3.org/2002/07/owl#DatatypeProperty> ) . 
                FILTER ( ?type != <http://www.w3.org/2000/01/rdf-schema#Datatype> ) .
            }';

            $range_relations = $store_Mysql->query($range, 'rows');

            for($i=0;$i<count($range_relations);$i++){
                $data_class = $this->prefixes_model->get_prefix_and_uri($range_relations[$i]['class_uri']);
                $range_relations[$i]['class'] = $data_class['class'];
                $data_relation = $this->prefixes_model->get_prefix_and_uri($range_relations[$i]['relation_uri']);
                $range_relations[$i]['relation_class'] = $data_relation['class'];
            }

            $domain = 'SELECT DISTINCT ?class_uri ?relation_uri WHERE {
                ?relation_uri <http://www.w3.org/2000/01/rdf-schema#domain> <'.$selected_class_uri.'>.
                ?relation_uri <http://www.w3.org/2000/01/rdf-schema#range> ?class_uri .
                ?relation_uri <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?type .
                FILTER isIRI(?class_uri) .
                FILTER ( ?type != <http://www.w3.org/2002/07/owl#DatatypeProperty> ) .
                FILTER ( ?type != <http://www.w3.org/2000/01/rdf-schema#Datatype> ) .
            }'; 

            $domain_relations = $store_Mysql->query($domain, 'rows');

            for($i=0;$i<count($domain_relations);$i++){
                $data_class = $this->prefixes_model->get_prefix_and_uri($domain_relations[$i]['class_uri']);
                $domain_relations[$i]['class'] = $data_class['class'];
                $data_relation = $this->prefixes_model->get_prefix_and_uri($domain_relations[$i]['relation_uri']);
                $domain_relations[$i]['relation_class'] = $data_relation['class'];
            }
        }
        
        $data['range_relations'] = $range_relations;
        $data['domain_relations'] = $domain_relations;
        
        if($subclassOf){

            $subClassOf_domain = 'SELECT DISTINCT ?class_uri ?relation_uri WHERE {
                <'.$selected_class_uri.'> ?relation_uri ?class_uri .
                FILTER ( ?relation_uri = <http://www.w3.org/2000/01/rdf-schema#subClassOf> || ?relation_uri = <http://www.w3.org/2002/07/owl#equivalentClass> ) .
                FILTER isIRI(?class_uri) .
            }
            ORDER BY ?relation_uri ?class_uri
            ';
            
            $subClassOf_domain_relations = $store_Mysql->query($subClassOf_domain,'rows');
            
            for($i=0;$i<count($subClassOf_domain_relations);$i++){
                $data_class = $this->prefixes_model->get_prefix_and_uri($subClassOf_domain_relations[$i]['class_uri']);
                $subClassOf_domain_relations[$i]['class'] = $data_class['class'];
                $data_relation = $this->prefixes_model->get_prefix_and_uri($subClassOf_domain_relations[$i]['relation_uri']);
                $subClassOf_domain_relations[$i]['relation_class'] = $data_relation['class'];
            }
            
            $subClassOf_range = 'SELECT DISTINCT ?class_uri ?relation_uri WHERE{
                ?class_uri ?relation_uri <'.$selected_class_uri.'> .
                FILTER ( ?relation_uri = <http://www.w3.org/2000/01/rdf-schema#subClassOf> || ?relation_uri = <http://www.w3.org/2002/07/owl#equivalentClass> ) .
                FILTER isIRI(?class_uri) .
            }
            ORDER BY ?relation_uri ?class_uri
            ';
            
            $subClassOf_range_relations = $store_Mysql->query($subClassOf_range, 'rows');
            
            for($i=0;$i<count($subClassOf_range_relations);$i++){
                $data_class = $this->prefixes_model->get_prefix_and_uri($subClassOf_range_relations[$i]['class_uri']);
                $subClassOf_range_relations[$i]['class'] = $data_class['class'];
                $data_relation = $this->prefixes_model->get_prefix_and_uri($subClassOf_range_relations[$i]['relation_uri']);
                $subClassOf_range_relations[$i]['relation_class'] = $data_relation['class'];
            }
            
        }
        
        $data['subClassOf_domain_relations'] = $subClassOf_domain_relations;
        $data['subClassOf_range_relations'] = $subClassOf_range_relations;
        
        $data['focused_class']=ucfirst($data_focused['class']);
        $data['ontology_prefix'] = $data_focused['prefix'];
        
        $data['subclassOf'] = $subclassOf;
        $data['id'] = $id;
        
        $this->load->view('workspace/relations',$data);
        
        //echo "END FUNCTION relations() Workspaces.php<br />";
    }
    
    function suggest(){
        //echo "FUNCTION suggest() Workspaces.php<br />";
        
        $id = $this->input->post('id');
        
        $substring = $this->input->post('string');
        
        $selected_workspace = $this->input->post('workspace');

        //load all class of the first workspace
        $this->load->model('workspaces_model');
        $store_Mysql = $this->workspaces_model->connect_workspace($selected_workspace); 

        //Get all classes
        $q = 'SELECT DISTINCT ?class_uri ?comment WHERE {
                { ?class_uri ?y <http://www.w3.org/2002/07/owl#Class> }
                    UNION
                { ?class_uri ?y <http://www.w3.org/2000/01/rdf-schema#Class> }
                OPTIONAL { ?class_uri <http://www.w3.org/2000/01/rdf-schema#comment> ?comment. }
            }';
        $classes = $store_Mysql->query($q, 'rows');
        
        $this->load->model('prefixes_model');

        //array to return
        $selected_classes= array();
        //put prefixes 
        for($i=0; $i < count($classes); $i++){
            $prefix_uri = $this->prefixes_model->get_prefix_and_uri($classes[$i]['class_uri']);
            
            if($prefix_uri != null){
                $classes[$i]['class'] = $prefix_uri['class']; 
                
                $pos1 = stripos($classes[$i]['class'], $substring);
               
                if($pos1 !== false) {
                   $strPrintValue = str_ireplace($substring, "<strong>".$substring."</strong>", $classes[$i]['class']);
                   $strComment = isset ($classes[$i]['comment']) ? ": ".$classes[$i]['comment']: "" ;
                   $classes[$i]['strPrintValue'] = $strPrintValue;
                   $classes[$i]['strComment'] = $strComment;
                   $classes[$i]['Comment'] = isset ($classes[$i]['comment']) ? $classes[$i]['comment']: "" ;
                   
                   array_push($selected_classes,$classes[$i]);
                } else {
                    if(isset ($classes[$i]['comment']) ){
                        $pos2 = stripos($classes[$i]['comment'], $substring);
                       
                        if($pos2 !== false) {
                           $strPrintValue = $classes[$i]['class'];
                           $strComment = ": ".str_replace($substring, "<b>".$substring."</b>", $classes[$i]['comment']);                           
                           $classes[$i]['strPrintValue'] = $strPrintValue;
                           $classes[$i]['strComment'] = $strComment;
                           $classes[$i]['Comment'] = $classes[$i]['comment'];

                   
                           array_push($selected_classes,$classes[$i]);
                        }
                    }
                }
            }
        }
        
        $data['id'] = $id;
        $data['classes'] = $selected_classes;
        $this->load->view('workspace/suggest',$data);
        
        //echo "END FUNCTION suggest() Workspaces.php<br />";
    }
    
    function search_class(){
        //echo "FUNCTION search_class() Workspaces.php<br />";
        
        $selected_string = $this->input->post('selected_class');

        $selected_workspace = $this->input->post('workspace');

        $this->load->model('workspaces_model');
        $store_Mysql = $this->workspaces_model->connect_workspace($selected_workspace);
        
        $q = 'SELECT ?class WHERE {
                { 
                    ?class ?y <http://www.w3.org/2002/07/owl#Class> .
                }
                UNION
                { 
                    ?class ?y <http://www.w3.org/2000/01/rdf-schema#Class> .
                }
                FILTER regex(?class,"'.$selected_string.'", "i") .
            }';
        $classes = $store_Mysql->query($q,'rows');
        
        if($classes != null){
           echo $classes[0]['class'];
        }
        
        //echo "END FUNCTION searcg_class() Workspaces.php<br />";
    }
    
    //ajax
    function search_box(){
        //echo "FUNCTION search_box() Workspaces.php<br />";
        
        $data['id'] = $this->input->post('id');
        
        $this->load->view('workspace/search',$data);
        //echo "END FUNCTION search_box() Workspaces.php<br />";
    }
    
    //ajax function that return json to fill listbox with class of selected workspace.
    public function get_classes($workspace){
        //echo "FUNCTION get_classes(workspace) Workspace.php<br />";
        
        $this->load->model('workspaces_model');
        $classes_pre = $this->workspaces_model->get_all_classes($workspace);
        
        $return_classes = array();
        $this->load->model('prefixes_model');
        
        $i= 0;
        foreach ($classes_pre as $class) {
            $prefix_uri = $this->prefixes_model->get_prefix_and_uri($class['class']);
            $classes[$i]['class'] = ucfirst($prefix_uri['class']);
            $classes[$i]['class_uri'] = $class['class'];
            $i++;
        }
        
        $classes = $this->workspaces_model->sortmulti($classes, 'class', 'asc', false, true);
        
        foreach($classes as $class){
            $return_classes[$class['class_uri']] = $class['class'];
        }
        
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($return_classes));
        
        //echo "END FUNCTION get_classes(workspace) Workspace.php<br />";
    }
    
    
}