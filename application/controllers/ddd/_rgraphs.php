<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rgraphs extends CI_Controller {
    
    /* En construccio
    public function test_template(){
        
        //init subclassOf
        $subclassOf = $this->input->post('subclassOf');
        if($subclassOf != null){
            $this->session->set_userdata('subclassOf', $this->input->post('subclassOf'));
        }else{
            $subclassOf = $this->session->userdata('subclassOf');
            if($subclassOf == null){
                $this->session->set_userdata('subclassOf', 'active');
                $subclassOf = 'active';
            }
        }
        
        //init color 
        $color = $this->session->userdata('graph_color');
        if($color == null){
            $color = 'graph';
        }
        
                        

        //call function to create graph
        $selected_workspace_right = $this->input->post('selected_workspace_right');
        $selected_class_left = $this->input->post('selected_class_left');
        $left_data = $this->create_var_graph('left', $selected_class_left, $selected_workspace_left);
        
        $data['id'] = 'test';
        $data['focused_class'] = $selected_class_left;
        $data['graph'] = $left_data['graph'];
        $data['graph_color'] = $left_data['graph_color'];
        $data['unerasable_relations'] = $left_data['unerasable_relations']; 
        $data['relation_to_print'] = $left_data['relation_to_print'];
        $data['workspace'] = $selected_workspace_left;
        
        //data needed
        //
        //['subclassOf']
        //['color']
        
        $this->load->view('rgraph/rgraph',$data_rgraph);
    }
     */
    
    public function visualization(){
        $this->load->helper('form');
        
        //init subclassOf
        $subclassOf = $this->input->post('subclassOf');
        if($subclassOf != null){
            $this->session->set_userdata('subclassOf', $this->input->post('subclassOf'));
        }else{
            $subclassOf = $this->session->userdata('subclassOf');
            if($subclassOf == null){
                $this->session->set_userdata('subclassOf', 'active');
                $subclassOf = 'active';
            }
        }
        
        //init color 
        $color = $this->session->userdata('graph_color');
        if($color == null){
            $color = 'graph';
        }
        
        //if is the first call save workspace
        $selected_workspace = $this->input->post('selected_workspace');
        if($selected_workspace != NULL){
            $current_workspace = array();
            $current_workspace[0] = $selected_workspace; 
            $this->session->set_userdata('current_workspace', $current_workspace);
        }else{
            $selected_workspaces = $this->session->userdata('current_workspace');
            $selected_workspace = $selected_workspaces[0];
        }
        
        $id = 1;
        
        //call function to create graph
        $selected_class = $this->input->post('selected_class');
        $data = $this->create_var_graph($id,$selected_class,$selected_workspace);
        
        $data['focused_class'] = $selected_class;
        $data['color'] = $color;
        $data['subclassOf'] = $subclassOf;
        $data['workspace'] = $selected_workspace;
        
        $this->load->view('rgraph/visualization',$data);
    }
    
    public function visualization2(){
        $this->load->helper('form');
        
        //init subclassOf
        $subclassOf = $this->input->post('subclassOf');
        if($subclassOf != null){
            $this->session->set_userdata('subclassOf', $this->input->post('subclassOf'));
        }else{
            $subclassOf = $this->session->userdata('subclassOf');
            if($subclassOf == null){
                $this->session->set_userdata('subclassOf', 'active');
                $subclassOf = 'active';
            }
        }
        
        //init color 
        $color = $this->session->userdata('graph_color');
        if($color == null){
            $color = 'graph';
        }
        
        //if is the first call save workspace
        $selected_workspace_left = $this->input->post('selected_workspace_left');
        $selected_workspace_right = $this->input->post('selected_workspace_right');
        if($selected_workspace_left != NULL && $selected_workspace_right != NULL ){
            $current_workspace = array();
            $current_workspace[0] = $selected_workspace_left;
            $current_workspace[1] = $selected_workspace_right;
            $this->session->set_userdata('current_workspace', $current_workspace);
        }else{
            $selected_workspaces = $this->session->userdata('current_workspace');
            $selected_workspace_left = $selected_workspaces[0];
            $selected_workspace_right = $selected_workspaces[1];
        }

        //call function to create graph
        $selected_class_left = $this->input->post('selected_class_left');
        $left_data = $this->create_var_graph('left', $selected_class_left, $selected_workspace_left);
        
        //call function to create graph
        $selected_class_right = $this->input->post('selected_class_right');
        $right_data = $this->create_var_graph('right', $selected_class_right, $selected_workspace_right);
        
        //left rgraph data
        $data['focused_class_left'] = $selected_class_left;
        $data['graph_left'] = $left_data['graph'];
        $data['graph_color_left'] = $left_data['graph_color'];
        $data['unerasable_relations_left'] = $left_data['unerasable_relations']; 
        $data['relation_to_print_left'] = $left_data['relation_to_print'];
        $data['workspace_left'] = $selected_workspace_left;
        
        //right rgraph data
        $data['focused_class_right'] = $selected_class_right;
        $data['graph_right'] = $right_data['graph'];
        $data['graph_color_right'] = $right_data['graph_color'];
        $data['unerasable_relations_right'] = $right_data['unerasable_relations']; 
        $data['relation_to_print_right'] = $right_data['relation_to_print'];
        $data['workspace_right'] = $selected_workspace_right;
        
        $data['color'] = $color;
        $data['subclassOf'] = $subclassOf;
        
        $this->load->view('rgraph/visualization2',$data);
    }
    

    /*
    private function visualization_context_data($selected_class){
        
        $this->load->model('workspaces_model');
        $store_Mysql = $this->workspaces_model->connect_workspace($this->session->userdata('current_workspace'));
        
        $domain = 'SELECT DISTINCT ?class ?relation WHERE {
            ?relation <http://www.w3.org/2000/01/rdf-schema#domain> <'.$selected_class.'>.
            ?relation <http://www.w3.org/2000/01/rdf-schema#range> ?class .
        }'; 
        
        $range = 'SELECT DISTINCT ?class ?relation WHERE {
            ?relation <http://www.w3.org/2000/01/rdf-schema#range> <'.$selected_class.'> .
            ?relation <http://www.w3.org/2000/01/rdf-schema#domain> ?class .
        }';
        
        $domain_relations = $store_Mysql->query($domain, 'rows');
        $range_relations = $store_Mysql->query($range, 'rows');
        
        $subclassOf = $this->session->userdata('subclassOf');
        
        $subClassOf_domain_relations = array();
        $subClassOf_range_relations = array();
            
        if($subclassOf == 'active'){
            
            $subClassOf_domain = 'SELECT DISTINCT ?class ?relation WHERE {
                <'.$selected_class.'> ?relation ?class .
                FILTER ( ?relation = <http://www.w3.org/2000/01/rdf-schema#subClassOf>) .
            }';

            $subClassOf_range = 'SELECT DISTINCT ?class ?relation WHERE{
                ?class ?relation <'.$selected_class.'> .
                FILTER ( ?relation = <http://www.w3.org/2000/01/rdf-schema#subClassOf>) .
            }';

            $subClassOf_domain_relations = $store_Mysql->query($subClassOf_domain,'rows');
            $subClassOf_range_relations = $store_Mysql->query($subClassOf_range, 'rows');

            $second_level_nodes = array_merge($domain_relations, $subClassOf_domain_relations, $range_relations, $subClassOf_range_relations);
        }else{
            $second_level_nodes = array_merge($domain_relations, $range_relations);
        }
            
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
        
        //put the prefix in second level nodes
        for($i=0; $i < count($domain_relations); $i++){
            $domain_relations[$i]['class'] = str_replace($uris, $prefixes, $domain_relations[$i]['class']);
            $domain_relations[$i]['relation'] = str_replace($uris, $prefixes, $domain_relations[$i]['relation']);
        }
        
        for($i=0; $i < count($range_relations); $i++){
            $range_relations[$i]['class'] = str_replace($uris, $prefixes, $range_relations[$i]['class']);
            $range_relations[$i]['relation'] = str_replace($uris, $prefixes, $range_relations[$i]['relation']);
        }
        
        if($subclassOf == 'active'){
        
            for($i=0; $i < count($subClassOf_domain_relations); $i++){
                $subClassOf_domain_relations[$i]['class'] = str_replace($uris, $prefixes, $subClassOf_domain_relations[$i]['class']);
                $subClassOf_domain_relations[$i]['relation'] = str_replace($uris, $prefixes, $subClassOf_domain_relations[$i]['relation']);            
            }

            for($i=0; $i < count($subClassOf_range_relations); $i++){
                $subClassOf_range_relations[$i]['class'] = str_replace($uris, $prefixes, $subClassOf_range_relations[$i]['class']);
                $subClassOf_range_relations[$i]['relation'] = str_replace($uris, $prefixes, $subClassOf_range_relations[$i]['relation']);                        
            }
            
        }
                
        //relation types between focused node and level 2 nodes 
        $data['domain_relations'] = $domain_relations;
        $data['subClassOf_domain_relations'] = $subClassOf_domain_relations;
        $data['range_relations'] = $range_relations;
        $data['subClassOf_range_relations'] = $subClassOf_range_relations;
        
        return $data;
    }
     */
    
    
    /*
     returns:
        $data['graph'] =>  variable javascript que printara el rgraph sense color
        $data['graph_color'] => variable javascript que printara el rgraph amb color
        $data['unerasable_relations'] => variable que filtrara les relacions que no es poden borrar
        $data['relation_to_print'] => variable que servira per fer les relacions que apareixeran i desaparaixeran en el mousover 
    */    
    private function create_var_graph($id,$selected_class,$selected_workspace){
        
        $this->load->model('workspaces_model');
        $store_Mysql = $this->workspaces_model->connect_workspace($selected_workspace);
        
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
        
        $prefix_first_level = str_replace($uris, $prefixes,$selected_class);
        
        $color = '';
        if($selected_class != $prefix_first_level){
            $color = $this->get_color($prefix_first_level);
        }
        
        $graph = '[ 
        { 
            id: "'.$id.'_'.$selected_class.'",
            
            name: "'.str_replace($uris, $prefixes,$selected_class).'", 
            data: {"$dim": 5.0, real_id: "'.$selected_class.'"} 
        }';
        
        $graph_color = '[ 
        { 
            id: "'.$id.'_'.$selected_class.'",
            
            name: "'.str_replace($uris, $prefixes,$selected_class).'", 
            data: {"$dim": 5.0, real_id: "'.$selected_class.'"'.$color.'} 
        }';
        
        $unerasable_relations = array();
        
        $domain = 'SELECT DISTINCT ?class WHERE {
            {
                ?relation <http://www.w3.org/2000/01/rdf-schema#domain> <'.$selected_class.'>.
                ?relation <http://www.w3.org/2000/01/rdf-schema#range> ?class .
            }
            UNION
            {
                ?relation <http://www.w3.org/2000/01/rdf-schema#range> <'.$selected_class.'> .
                ?relation <http://www.w3.org/2000/01/rdf-schema#domain> ?class .
            }
        }'; 
        
        $domain_relations = $store_Mysql->query($domain, 'rows');
        
        $subclassOf = $this->session->userdata('subclassOf');
        if($subclassOf == 'active'){
            
            $subClassOf_domain = 'SELECT DISTINCT ?class WHERE {
                {
                    <'.$selected_class.'> ?relation ?class .
                    FILTER ( ?relation = <http://www.w3.org/2000/01/rdf-schema#subClassOf>) .
                }
                UNION
                {
                    ?class ?relation <'.$selected_class.'> .
                    FILTER ( ?relation = <http://www.w3.org/2000/01/rdf-schema#subClassOf>) .
                }
            }';

            $subClassOf_domain_relations = $store_Mysql->query($subClassOf_domain,'rows');

            $second_level_nodes = array_merge((array)$domain_relations, (array)$subClassOf_domain_relations);
        }else{
            $second_level_nodes = (array)$domain_relations;
        }        
        
        // foreach all related nodes (2 level) to the focused, get his related nodes (3 level)
        foreach($second_level_nodes as $second_level){
            if($second_level['class type']=='uri' && $second_level['class'] != $selected_class ){
                
                //write the prefix into the node.
                $second_level['class_prefixed'] = str_replace($uris, $prefixes, $second_level['class']);
                
                $color = '';
                if($second_level['class'] != $second_level['class_prefixed']){
                    $color = $this->get_color($second_level['class_prefixed']);
                }

                //print node level 2
                $graph .= '
                ,{
                    id: "'.$id.'_'.$second_level['class'].'",
                    
                    name: "'.$second_level['class_prefixed'].'",
                    data: { "$dim": 5.0, real_id: "'.$second_level['class'].'" },
                    adjacencies: [ 
                        { "nodeTo": "'.$id.'_'.$selected_class.'", "data": { "weight": 3, "relations": "<img src=\''.base_url().'public/icons/connector_empty.gif\' onclick=\'openPopup(\"show-relations\");\' onmouseout=\'document.body.style.cursor=\"default\"\' onmouseover=\'document.body.style.cursor=\"pointer\"\'  >" } }
                    ]   
                }'; 
                
                $graph_color .= '
                ,{
                    id: "'.$id.'_'.$second_level['class'].'",
                    real_id: "'.$second_level['class'].'",
                    name: "'.$second_level['class_prefixed'].'",
                    data: { "$dim": 5.0, real_id: "'.$second_level['class'].'"'.$color.' },
                    adjacencies: [ 
                        { "nodeTo": "'.$id.'_'.$selected_class.'", "data": { "weight": 3, "relations": "<img src=\''.base_url().'public/icons/connector_empty.gif\' onclick=\'openPopup(\"show-relations\");\' onmouseout=\'document.body.style.cursor=\"default\"\' onmouseover=\'document.body.style.cursor=\"pointer\"\'  >" } }
                    ]   
                }'; 
                
                $new_relation = array(
                    'id' => $id.'_'.$second_level['class'],
                    'nodeTo' => $id.'_'.$selected_class
                );
                
                array_push($unerasable_relations,$new_relation);
            }
        }
        
        $nodes_already_printed  = $second_level_nodes;
        $relation_to_print = array();


       
        // foreach all related nodes (3 level) to the 2 level nodes.
        foreach($second_level_nodes as $second_level){
            
            if($second_level['class type']=='uri' && $second_level['class'] != $selected_class ){
                
                /*
                 * Aquestes lineas de codi fa que les datatypepropietats no mostrin els seus links 
                 * 
                 * ?relation <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?type .
                 * 
                 * FILTER (?type != <http://www.w3.org/2002/07/owl#DatatypeProperty>) .
                 * FILTER (?type != <http://www.w3.org/2000/01/rdf-schema#Datatype>) .
                 * 
                 */
                
                $sparql_third_level = 'SELECT DISTINCT ?class WHERE {
                    {
                        ?relation <http://www.w3.org/2000/01/rdf-schema#domain> <'.$second_level['class'].'>.
                        ?relation <http://www.w3.org/2000/01/rdf-schema#range> ?class .
                        ?relation <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?type .
                        FILTER (?class != <'.$selected_class.'> ) .
                        FILTER (?type != <http://www.w3.org/2002/07/owl#DatatypeProperty>) .
                        FILTER (?type != <http://www.w3.org/2000/01/rdf-schema#Datatype>) .
                    }
                    UNION
                    {
                        ?relation <http://www.w3.org/2000/01/rdf-schema#range> <'.$second_level['class'].'> .
                        ?relation <http://www.w3.org/2000/01/rdf-schema#domain> ?class .
                        ?relation <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?type .
                        FILTER (?class != <'.$selected_class.'> ) .
                        FILTER (?type != <http://www.w3.org/2002/07/owl#DatatypeProperty>) .
                        FILTER (?type != <http://www.w3.org/2000/01/rdf-schema#Datatype>) .
                    }
                }'; 
                
                $third_level_nodes_pre = $store_Mysql->query($sparql_third_level,'rows');
                
                if($this->session->userdata('subclassOf') == 'active'){
                    
                    $sparql_subClassOf = 'SELECT DISTINCT ?class WHERE {
                        {
                            <'.$second_level['class'].'> ?relation ?class .
                            FILTER ( ?relation = <http://www.w3.org/2000/01/rdf-schema#subClassOf>) .
                            FILTER (?class != <'.$selected_class.'> ) .
                        }
                        UNION
                        {
                            ?class ?relation <'.$second_level['class'].'> .
                            FILTER ( ?relation = <http://www.w3.org/2000/01/rdf-schema#subClassOf>) .
                            FILTER (?class != <'.$selected_class.'> ) .
                        }
                    }';

                    $sparql_subClassOf_nodes = $store_Mysql->query($sparql_subClassOf,'rows');

                    $third_level_nodes = array_merge((array)$third_level_nodes_pre, (array)$sparql_subClassOf_nodes);
                }else{
                    $third_level_nodes = (array)$third_level_nodes_pre;
                }
                
                
                
                //Serach if the node is already printed
                foreach($third_level_nodes as $third_level){
           
                    if($third_level['class type']=='uri'){
                        
                        //put the prefix in the nodes
                        $third_level['class_prefixed'] = str_replace($uris, $prefixes, $third_level['class']);
                        
                        $find = false;

                        foreach($nodes_already_printed as $node_printed ){
                            if($node_printed['class']==$third_level['class']){
                                $find = true;
                            }
                        }

                        if($find == false){
                            array_push($nodes_already_printed,$third_level);
                            
                            $color = '';
                            if($third_level['class'] != $third_level['class_prefixed']){
                                $color = $this->get_color($third_level['class_prefixed']);
                            }
                            
                            //print node level 3
                            $graph .= '
                            ,{
                                id: "'.$id.'_'.$third_level['class'].'",
                                name: "'.$third_level['class_prefixed'].'",
                                data: { "$dim": 5.0, real_id: "'.$third_level['class'].'" },
                                adjacencies: [ 
                                    { "nodeTo": "'.$id.'_'.$second_level['class'].'", "data": { "weight": 3, "relations": "<img src=\''.base_url().'public/icons/connector_empty.gif\' onclick=\'openPopup(\"show-relations\");\' onmouseout=\'document.body.style.cursor=\"default\"\' onmouseover=\'document.body.style.cursor=\"pointer\"\' >" } }
                                ]   
                            }';
                            
                            $graph_color .= '
                            ,{
                                id: "'.$id.'_'.$third_level['class'].'",
                                name: "'.$third_level['class_prefixed'].'",
                                data: { "$dim": 5.0, real_id: "'.$third_level['class'].'"'.$color.' },
                                adjacencies: [ 
                                    { "nodeTo": "'.$id.'_'.$second_level['class'].'", "data": { "weight": 3, "relations": "<img src=\''.base_url().'public/icons/connector_empty.gif\' onclick=\'openPopup(\"show-relations\");\' onmouseout=\'document.body.style.cursor=\"default\"\' onmouseover=\'document.body.style.cursor=\"pointer\"\' >" } }
                                ]   
                            }';
                            
                            $new_relation = array(
                                'id' => $id.'_'.$third_level['class'],
                                'nodeTo' => $id.'_'.$second_level['class']
                            );

                            array_push($unerasable_relations,$new_relation);
                            
                            /*
                            * Aquestes lineas de codi fa que les datatypepropietats no mostrin els seus links 
                            * 
                            * ?relation <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?type .
                            * 
                            * FILTER (?type != <http://www.w3.org/2002/07/owl#DatatypeProperty>) .
                            * FILTER (?type != <http://www.w3.org/2000/01/rdf-schema#Datatype>) .
                            * 
                            */
                            
                            $sparql_fourth_level = 'SELECT DISTINCT ?class ?relation WHERE {
                                {
                                    ?relation <http://www.w3.org/2000/01/rdf-schema#domain> <'.$third_level['class'].'>.
                                    ?relation <http://www.w3.org/2000/01/rdf-schema#range> ?class .
                                    ?relation <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?type .
                                    FILTER (?class != <'.$selected_class.'> ) .
                                    FILTER (?type != <http://www.w3.org/2002/07/owl#DatatypeProperty>) .
                                    FILTER (?type != <http://www.w3.org/2000/01/rdf-schema#Datatype>) .
                                }
                                UNION
                                {
                                    ?relation <http://www.w3.org/2000/01/rdf-schema#range> <'.$third_level['class'].'> .
                                    ?relation <http://www.w3.org/2000/01/rdf-schema#domain> ?class .
                                    ?relation <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?type .
                                    FILTER (?class != <'.$selected_class.'> ) .
                                    FILTER (?type != <http://www.w3.org/2002/07/owl#DatatypeProperty> .
                                    FILTER (?type != <http://www.w3.org/2000/01/rdf-schema@Datatype> .
                                }
                            }'; 

                            $fourth_level_nodes_pre = $store_Mysql->query($sparql_fourth_level,'rows');
                            
                            if($this->session->userdata('subclassOf') == 'active'){
                                
                                $sparql_subClassOf = 'SELECT DISTINCT ?class WHERE {
                                    {
                                        <'.$third_level['class'].'> ?relation ?class .
                                        FILTER ( ?relation = <http://www.w3.org/2000/01/rdf-schema#subClassOf>) .
                                        FILTER (?class != <'.$selected_class.'> ) .
                                    }
                                    UNION
                                    {
                                        ?class ?relation <'.$third_level['class'].'> .
                                        FILTER ( ?relation = <http://www.w3.org/2000/01/rdf-schema#subClassOf>) .
                                        FILTER (?class != <'.$selected_class.'> ) .
                                    }
                                }';
                                
                                $sparql_subClassOf_nodes = $store_Mysql->query($sparql_subClassOf,'rows');
                            
                                $fourth_level_nodes = array_merge((array)$fourth_level_nodes_pre, (array)$sparql_subClassOf_nodes);
                            
                            }else{
                                $fourth_level_nodes = (array)$fourth_level_nodes_pre;
                            }

                            //Serach if the node is already printed
                            foreach($fourth_level_nodes as $fourth_level){
                                
                                if($fourth_level['class type']=='uri'){
                                    
                                    //put the prefix into the node.
                                    $fourth_level['class_prefixed'] = str_replace($uris, $prefixes, $fourth_level['class']);
                                    
                                    $find = false;

                                    foreach($nodes_already_printed as $node_printed ){
                                        if($node_printed['class']==$fourth_level['class']){
                                            $find = true;
                                        }
                                    }

                                    if($find == false){
                                        array_push($nodes_already_printed,$fourth_level);

                                        $color = '';
                                        if($fourth_level['class'] != $fourth_level['class_prefixed']){
                                            $color = $this->get_color($fourth_level['class_prefixed']);
                                        }

                                        //print node level 4
                                        $graph .= '
                                        ,{
                                            id: "'.$id.'_'.$fourth_level['class'].'",
                                            name: "'.$fourth_level['class_prefixed'].'",
                                            data: { "$dim": 5.0, real_id: "'.$fourth_level['class'].'" },
                                            adjacencies: [ 
                                                { "nodeTo": "'.$id.'_'.$third_level['class'].'", "data": { "weight": 3, "relations": "<img src=\''.base_url().'public/icons/connector_empty.gif\' onclick=\'openPopup(\"show-relations\");\' onmouseout=\'document.body.style.cursor=\"default\"\' onmouseover=\'document.body.style.cursor=\"pointer\"\' >" } }
                                            ]   
                                        }';
                                        
                                        $graph_color .= '
                                        ,{
                                            id: "'.$id.'_'.$fourth_level['class'].'",
                                            name: "'.$fourth_level['class_prefixed'].'",
                                            data: { "$dim": 5.0, real_id: "'.$fourth_level['class'].'"'.$color.' },
                                            adjacencies: [ 
                                                { "nodeTo": "'.$id.'_'.$third_level['class'].'", "data": { "weight": 3, "relations": "<img src=\''.base_url().'public/icons/connector_empty.gif\' onclick=\'openPopup(\"show-relations\");\' onmouseout=\'document.body.style.cursor=\"default\"\' onmouseover=\'document.body.style.cursor=\"pointer\"\' >" } }
                                            ]   
                                        }';
                                        
                                       //array de les relacions que no es printaran a priori
                                       $new_relation = array(
                                            'id' => $id.'_'.$fourth_level['class'],
                                            'nodeTo' => $id.'_'.$third_level['class']
                                        );

                                        array_push($unerasable_relations,$new_relation);

                                    }else{
                                        
                                        //array de les relacions que no es printaran a priori
                                        $new_relation = array(
                                            'id' => $id.'_'.$third_level['class'],
                                            'nodeTo' => $id.'_'.$fourth_level['class']
                                        );

                                        array_push($relation_to_print,$new_relation);
                                                                              
                                   }
                                }
                            }
                        }else{
                            
                            //array de les relacions que no es printaran a priori
                            $new_relation = array(
                                'id' => $id.'_'.$second_level['class'],
                                'nodeTo' => $id.'_'.$third_level['class']
                            );

                            array_push($relation_to_print,$new_relation);
                        }
                    }
                }
            }
        }
        
        $graph .= ']';
        $graph_color .= ']';
        
        $vars['graph'] = $graph;
        $vars['graph_color'] = $graph_color;
        $vars['unerasable_relations'] = $unerasable_relations;
        $vars['relation_to_print'] = $relation_to_print; 
        
        return $vars;
    }
    
    public function get_color($class_prefixed ){
        $color = '';
        
        $pieces = explode(":", $class_prefixed);

        if(!$this->session->userdata('nextcolors')){
            $this->session->set_userdata('colors', array());
            $this->session->set_userdata('nextcolors', 0);
        }

        $colors = $this->session->userdata('colors');
        $nextcolors = $this->session->userdata('nextcolors');

        if( isset($colors[$pieces[0]]) ){
            $color = ', $color: "'.$colors[$pieces[0]].'"';
        }else{
            $nextcolors = $nextcolors+1;
            if($nextcolors > 14){
                $nextcolors = 1;
            }
            $this->session->set_userdata('nextcolors', $nextcolors);
            
            $this->db->select('color');
            $this->db->where('id',$nextcolors);
            $color_query = $this->db->get('colors');
            $new_color = $color_query->result();

            $new_color_for = $new_color[0]->color ;
            
            $colors[$pieces[0]] = $new_color_for;
            $color = ', $color: "'.$colors[$pieces[0]].'"';
            $this->session->set_userdata('colors', $colors);
        }
        
        return $color;
    }
    // end private functions.
    
    //ajax  functions:
    public function get_classes($workspace){
        
        $this->load->model('workspaces_model');
        $store_Mysql = $this->workspaces_model->connect_workspace($workspace);
        
        $q = 'SELECT ?class WHERE {
                { ?class ?y <http://www.w3.org/2002/07/owl#Class>}
                    UNION
                { ?class ?y <http://www.w3.org/2000/01/rdf-schema#Class>} 
            }';
        
        $classes = $store_Mysql->query($q, 'rows');
        
        $return_classes = array();
        $this->load->model('prefixes_model');
        //put prefixes 
        foreach ($classes as $class) {
            $prefix_uri = $this->prefixes_model->get_prefix_and_uri($class['class']);
            $return_classes[$class['class']] = str_replace($prefix_uri['uri'], $prefix_uri['prefix'].':', $class['class']);
        }
        
        
        
        //load all the prefixes
        /*$this->db->select('uri, prefix');
        $prefixes_query = $this->db->get('prefixes');
        $uris = array();
        $prefixes = array();
        $i=0;       
        foreach($prefixes_query->result() as $prefixe_query){
            $uris[$i] = $prefixe_query->uri;
            $prefixes[$i] = $prefixe_query->prefix.':';
            $i++;
        }
        
        $return_classes = array();
        foreach ($classes as $class) {
            $return_classes[$class['class']] = str_replace($uris, $prefixes,$class['class'],$count);
            
            if($count==0){
                //Is temporal class? False = it isn't temporal class
                if( ($return_classes[$class['class']][0] != '_') && ($return_classes[$class['class']][1] != ':') && ($return_classes[$class['class']][2] != 'b')){
                    $pos = strpos($return_classes[$class['class']], '#');

                    //uri finish with #
                    if ($pos !== false) {
                        $class_uri = explode('#', $return_classes[$class['class']]);

                        $this->load->model('prefixes_model');
                        $new_generic_prefix = $this->prefixes_model->add_auto_prefix($class_uri[0].'#');

                        array_push($uris, $class_uri[0].'#');
                        array_push($prefixes, $new_generic_prefix.':' );

                        $return_classes[$class['class']] = str_replace($class_uri[0].'#', $new_generic_prefix.':', $return_classes[$class['class']]);

                    //uri finish with /    
                    }else{
                        $class_uri_pre = explode('/', $return_classes[$class['class']],-1);
                        $class_uri = implode('/', $class_uri_pre).'/';

                        $this->load->model('prefixes_model');
                        $new_generic_prefix = $this->prefixes_model->add_auto_prefix($class_uri);

                        array_push($uris, $class_uri );
                        array_push($prefixes, $new_generic_prefix.':' );

                        $return_classes[$class['class']] = str_replace($class_uri, $new_generic_prefix.':', $return_classes[$class['class']]);
                    } 
                }
            }
        }*/
        
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($return_classes));
    }
    
    public function color(){
        $this->session->set_userdata('graph_color', $this->input->post('color'));
    }
    //end ajax functions
}