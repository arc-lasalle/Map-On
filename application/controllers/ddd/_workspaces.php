<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Workspaces extends CI_Controller {
    
    public function read_sparql_endpoint(){
        
        $name = strtolower($this->input->post('name'));
        $endpoint = $this->input->post('endpoint');
        $graph = $this->input->post('graph');
        
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
            }';

        $properties_rows = $store->query($properties, 'rows');
        if($properties_rows != array()){
            echo "properties !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br />";
            echo '<pre style="text-align:left">';
            var_dump($properties_rows);
            echo '</pre>';
            
            $store_Mysql->insert($properties_rows, 'rows');
            unset($properties_rows); //save memory to read big ontologies.
        }
        
        // Data Properties
        $dataproperties = 'SELECT DISTINCT ?s ?p ?o '.$from.' WHERE {
            { ?s ?p ?o . 
                FILTER ( ?o = <http://www.w3.org/2002/07/owl#DatatypeProperty>) .
            }
                UNION
            { ?s ?p ?o . 
                FILTER ( ?o = <http://www.w3.org/2000/01/rdf-schema#Datatype> ) .
            }                
        }';
        
        $dataproperties_rows = $store->query($dataproperties,'rows');
        
        if($dataproperties_rows != array()){
            echo "dataproperties !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br />";
            echo '<pre style="text-align:left">';
            var_dump($dataproperties_rows);
            echo '</pre>';

            $store_Mysql->insert($dataproperties_rows,'rows');
            unset($dataproperties_rows);
        }
        // Annotation Properties
        $annotation = 'SELECT distinct ?s ?p ?o '.$from.' WHERE {
            ?s ?p ?o .
            FILTER ( ?o = <http://www.w3.org/2002/07/owl#AnnotationProperty>) .
        }';
        
        $annotation_rows = $store->query($annotation,'rows');
        
        if($annotation_rows != array()){
            echo "annotation !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br />";
            echo '<pre style="text-align:left">';
            var_dump($annotation_rows);
            echo '</pre>';

            $store_Mysql->insert($annotation_rows,'rows');
            unset($annotation_rows);
        }
        
        // Annotation Properties datas
        $annotation_data = 'SELECT DISTINCT ?s ?p ?o '.$from.' WHERE {
            ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#AnnotationProperty> .
            ?s ?p ?o.
        }';
        
        $annotation_data_rows = $store->query($annotation_data,'rows');
        
        if($annotation_data_rows != array()){
            echo "annotation_data !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br />";
            echo '<pre style="text-align:left">';
            var_dump($annotation_data_rows);
            echo '</pre>';

            $store_Mysql->insert($annotation_data_rows,'rows');
            unset($annotation_data_rows);       
        }
        
        //specific annotations
        $annotation_label = 'SELECT DISTINCT ?s ?p ?o'.$from.' WHERE {
            ?s ?p ?o .
            FILTER ( ?p = <http://www.w3.org/2000/01/rdf-schema#label>) .
        }';
        
        $annotation_label_rows = $store->query($annotation_label); 
        if($annotation_label_rows != array()){
            echo "annotation_labels !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br />";
            echo '<pre style="text-align:left">';
            var_dump($annotation_label_rows);
            echo '</pre>';
            
            $store_Mysql->insert($annotation_label_rows,'rows');
            unset($annotation_label_rows);
        }
        
        $annotation_isDefinedBy = 'SELECT DISTINCT ?s ?p ?o'.$from.' WHERE {
            ?s ?p ?o .
            FILTER ( ?p = <http://www.w3.org/2000/01/rdf-schema#isDefinedBy>) .
        }';
        $annotation_isDefinedBy_rows = $store->query($annotation_isDefinedBy);
        if($annotation_isDefinedBy_rows != array()){
            echo "annotation_isdefinedby!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br />";
            echo '<pre style="text-align:left">';
            var_dump($annotation_isDefinedBy_rows);
            echo '</pre>';
            
            $store_Mysql->insert($annotation_isDefinedBy_rows,'rows');
            unset($annotation_isDefinedBy_rows);
        }
        
        $annotation_seealso='SELECT DISTINCT ?s ?p ?o '.$from.' WHERE {
            ?s ?p ?o .
            FILTER ( ?p = <http://www.w3.org/2000/01/rdf-schema#seeAlso>) .
        }';
        $annotation_seealso_rows = $store->query($annotation_seealso); 
        if($annotation_seealso_rows != array()){
            echo "annotation_seeAlso!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br />";
            echo '<pre style="text-align:left">';
            var_dump($annotation_seealso_rows);
            echo '</pre>';
            
            $store_Mysql->insert($annotation_seealso_rows,'rows');
            unset($annotation_seealso_rows);
        }
        
        $annotation_comment='SELECT DISTINCT ?s ?p ?o '.$from.' WHERE {
            ?s ?p ?o .
            FILTER ( ?p = <http://www.w3.org/2000/01/rdf-schema#comment>) .
        }';
        $annotation_comment_rows= $store->query($annotation_comment);
        if($annotation_comment_rows != array()){
            echo "annotation_comment!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br />";
            echo '<pre style="text-align:left">';
            var_dump($annotation_comment_rows);
            echo '</pre>';
            
            $store_Mysql->insert($annotation_comment_rows,'rows');
            unset($annotation_comment_rows);
        }

            
        // CLASSES
        $class = 'SELECT DISTINCT ?s ?p ?o '.$from.' WHERE {
                {   ?s ?p ?o .
                    FILTER ( ?o = <http://www.w3.org/2002/07/owl#Class>) .
                }
                    UNION
                {   ?s ?p ?o .
                    FILTER ( ?o = <http://www.w3.org/2000/01/rdf-schema#Class>) .
                }    
            }';

        $class_rows = $store->query($class, 'rows');
        
        if($class_rows != array()){
            echo "class !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br />";
            echo '<pre style="text-align:left">';
            var_dump($class_rows);
            echo '</pre>';

            $store_Mysql->insert($class_rows, 'rows');
            unset($class_rows);
        }

        //RELACIONS DOMAIN
        $domain = 'SELECT DISTINCT ?s ?p ?o '.$from.' WHERE { 
                ?s ?p ?o .
                FILTER ( ?p = <http://www.w3.org/2000/01/rdf-schema#domain>).
            }';

        $domain_rows = $store->query($domain, 'rows');
        
        if($domain_rows != array()){
            echo "domain !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br />";
            echo '<pre style="text-align:left">';
            var_dump($domain_rows);
            echo '</pre>';

            $store_Mysql->insert($domain_rows, 'rows');
            unset($domain_rows);
        }
        
        //RELACIONS RANGE
        $range = 'SELECT DISTINCT ?s ?p ?o '.$from.' WHERE { 
                ?s ?p ?o .
                FILTER (?p = <http://www.w3.org/2000/01/rdf-schema#range>) .
            }';
        $range_rows = $store->query($range, 'rows');
        
        if($range_rows != array()){
            echo '<pre style="text-align:left">';
            var_dump($range_rows);
            echo '</pre>';

            $store_Mysql->insert($range_rows, 'rows');
            unset($range_rows);
        }

        //RELACIONS SUBCLASSOF
        $subclassof = 'SELECT DISTINCT ?s ?p ?o '.$from.' WHERE {
                ?s ?p ?o .
                FILTER (?p = <http://www.w3.org/2000/01/rdf-schema#subClassOf>) .
            }';
        $subclassof_rows = $store->query($subclassof, 'rows');
    
        if($subclassof_rows){
            echo "subclassof !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br />";
            echo '<pre style="text-align:left">';
            var_dump($subclassof_rows);
            echo '</pre>';

            $store_Mysql->insert($subclassof_rows, 'rows');
            unset($subclassof_rows);
        }
        
        //redirect('interfaces/index','refresh');
    }
    
    public function read_owl_file(){
        
        // upload file
        $target_path = "./upload/";
        $target_path = $target_path . basename( $_FILES['owl_file_input']['name']); 
        
        if ((($_FILES["owl_file_input"]["type"] == "application/octet-stream") || ($_FILES["owl_file_input"]["type"] == "application/rdf+xml")) && ($_FILES["owl_file_input"]["size"] < 6000000)){
        
            if(move_uploaded_file($_FILES['owl_file_input']['tmp_name'], $target_path)) {
                echo "The file ".  basename( $_FILES['owl_file_input']['name'])." has been uploaded";
                
                //read owl/rdf file
                include_once("public/arc2/ARC2.php");
                
                $parser = ARC2::getRDFParser();
                $parser->parse(base_url('upload/'.$_FILES['owl_file_input']['name']));
                $triples = $parser->getTriples();
                
                $name = strtolower($this->input->post('input_name'));
                
                //config of mysql and add triples to it.
                $this->load->model('workspaces_model');
                $store_Mysql = $this->workspaces_model->connect_workspace($name);
                $store_Mysql->insert($triples, 'rows');
                
                echo "<pre style='text-align: left'>";
                var_dump($triples);
                echo "</pre>";
                
                $workspaces = $this->workspaces_model->find_data_workspace($name);        
        
                if($workspaces == null){
                    $this->workspaces_model->add($name, $_FILES['owl_file_input']['name']); 
                }else{
                    $this->workspaces_model->modify($workspaces[0]->id, $name, $_FILES['owl_file_input']['name']); 
                }
                
                //redirect('interfaces/index','refresh');
                
            } else{
                echo "There was an error uploading the file, please try again!";
            }
        }else{
            echo "Type or size file was invalid.";
        }
    }
    
    function view_all_data_workspace($name){
        $this->load->model('workspaces_model');
        $store_Mysql = $this->workspaces_model->connect_workspace($name);

        $all = 'SELECT * WHERE { ?s ?p ?o}';

        $all_result = $store_Mysql->query($all, 'rows');
        echo "<pre>";
        var_dump($all_result);
        echo "</pre>";
    }
    
    function annotations(){
        echo '<div style="text-align:left">';
        echo "Name: <b>".$this->input->post('node_name')."</b><br />";
        
        $this->load->model('workspaces_model');
        $store_Mysql = $this->workspaces_model->connect_workspace($this->input->post('workspace'));
        
        
        //get all annotations properties of a class
        $annotation_properties = 'SELECT DISTINCT ?class ?s ?literal WHERE{
            {
                ?class ?s ?literal .
                FILTER ( ?class = <'.$this->input->post('node_id').'> ) .
                FILTER ( ?s = <http://www.w3.org/2000/01/rdf-schema#comment>) .
            }
            UNION
            {
                ?class ?s ?literal .
                FILTER ( ?class = <'.$this->input->post('node_id').'> ) .
                FILTER ( ?s = <http://www.w3.org/2000/01/rdf-schema#seeAlso>) .
            }
            UNION
            {
                ?class ?s ?literal .
                FILTER ( ?class = <'.$this->input->post('node_id').'> ) .
                FILTER ( ?s = <http://www.w3.org/2000/01/rdf-schema#isDefinedBy>) .
            }
            UNION
            {
                ?class ?s ?literal .
                FILTER ( ?class = <'.$this->input->post('node_id').'> ) .
                FILTER ( ?s = <http://www.w3.org/2000/01/rdf-schema#label>) .
            }
            UNION
            {
                ?class ?s ?literal .
                ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#AnnotationProperty> .
                FILTER (?class = <'.$this->input->post('node_id').'>) .
            }
            
        }';
        
        $annotation_properties_rows = $store_Mysql->query($annotation_properties, 'rows');
        
        //load all the prefixes
        $this->db->select('uri, prefix');
        $prefixes_query = $this->db->get('prefixes');
        
        $uris = array();
        $prefixes = array();
        $i=0;
        
        foreach($prefixes_query->result() as $prefixe_query){
            $uris[$i] = $prefixe_query->uri;
            $prefixes[$i] = $prefixe_query->prefix.':';
            $i++;
        }
        
        foreach($annotation_properties_rows as $row){
            echo str_replace($uris, $prefixes,$row['s']).': ';
            echo '<b>'.$row['literal'].'</b><br />';
        }
        
        echo '</div>';
    }
}

/*

SELECT DISTINCT ?s ?p ?o WHERE {
    { ?s ?p ?o . 
        filter ( ?o = <http://www.w3.org/2002/07/owl#DatatypeProperty>) .
    }
        UNION
    { ?s ?p ?o . 
        filter ( ?o = <http://www.w3.org/2000/01/rdf-schema#Datatype> ) .
    }                
}


// Annotation Properties datas
'SELECT DISTINCT ?class ?s ?literal '.$from.' WHERE {
    ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#AnnotationProperty> .
    ?class ?s ?literal
}';

*/