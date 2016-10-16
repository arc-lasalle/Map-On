<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rgraphs extends CI_Controller {
    
    public function rgraph(){
        //echo "FUNCTION rgraph() rgraphs.php<br />";
        
        
        $this->load->helper('form');
        
        $workspace = $this->input->post('workspace');
        $selected_class_uri = $this->input->post('selected_class');
        
        $id = $this->input->post('id');
        if($id == null){
            $id = '1';
        }
        
        //init subclassOf
        $subclassof = $this->input->post('subclassof');
        if($subclassof == null){
            $subclassof = true;
        }else{
            if($subclassof == false){
                $subclassof = false;
            }else{
                $subclassof = true;
            }
        }
        
        $aggregation = $this->input->post('aggregation');
        if($aggregation == null){
            $aggregation = true;
        }else{
            if($aggregation == "false"){
                $aggregation = false;
            }else{
                $aggregation = true;
            }
        }
        
        //init relation with icon?
        $relation_icon = $this->input->post('relation_icon');
        if($relation_icon == null){
            $relation_icon = false;
        }else{
            if($relation_icon == "false"){
                $relation_icon = false;
            }else{
                $relation_icon = true;
            }
        }
        
        // normal_relation                          ""
        // subclasOf_relation (or equivalentClass)  "#dd99dd"
        // datatype                                 "#808000"
        // both_relation                            "#800080"
        
        $subclass_relation_color = $this->input->post("subclass_relation_color");
        if($subclass_relation_color == null){
            $subclass_relation_color = "#dd99dd";
        }
        
        $both_relation_color = $this->input->post("both_relation_color");
        if($both_relation_color == null){
            $both_relation_color = "#800080";
        }
        
        $classes_filter = $this->input->post("classes_filter");
        if($classes_filter == null){
            $classes_filter = array();
        }
        
        $data = $this->create_var_graph($id, $selected_class_uri, $workspace, $subclassof, $aggregation,
                $subclass_relation_color, $both_relation_color, $relation_icon, $classes_filter);
        
        $data['id'] = $id;
        $data['workspace'] = $workspace;
        

        if($subclassof){
            $data['subclassof'] = "true";
        }else{
            $data['subclassof'] = "false";
        }

        if($aggregation){
            $data['aggregation'] = "true";
        }else{
            $data['aggregation'] = "false";
        }
        
        $color = $this->input->post('color');
        if($relation_icon == null){
            $data['color'] = "false";
        }else{
            $data['color'] = $color;
        }
        
        if($relation_icon){
            $data['relation_icon'] = "true";
        }else{
            $data['relation_icon'] = "false";
        }
        
        //data about uri
        $data['selected_class'] = $selected_class_uri;
        $this->load->model('prefixes_model');
        $data['info_selected_class'] = $this->prefixes_model->get_prefix_and_uri($selected_class_uri); 
        
        $data['node_color'] = $this->input->post('node_color');
        if($data['node_color'] == null){
            $data['node_color'] = '#7878ff';
        }
        
        $data['default_relation_color'] = $this->input->post("default_relation_color");
        if($data['default_relation_color'] == null){
            $data['default_relation_color'] = "#FF3838";
        }
        
        $data['circle_color'] = $this->input->post('circle_color');
        if($data['circle_color'] == null){
            $data['circle_color'] = '#000000';
        }
        
        $data['subclass_relation_color'] = $subclass_relation_color;
        $data['both_relation_color'] = $both_relation_color;
        
        $data['search_div_id'] = $this->input->post('search_div_id');
        $data['annot_class_div_id'] = $this->input->post('annot_class_div_id');
        $data['annot_onto_div_id'] = $this->input->post('annot_onto_div_id');
        $data['annot_relat_div_id'] = $this->input->post('annot_relat_div_id');
        $data['path'] = $this->input->post('path');        
        
        //init control_panel
        $control_panel = $this->input->post('control_panel');
        if($control_panel == null){
            $data['control_panel'] = "false";
        }else{
            $data['control_panel'] = $control_panel;
        }

        $data['onfocus'] = $this->input->post('onfocus');
        $data['onmouseover'] = $this->input->post('onmouseover');
        $data['onclick'] = $this->input->post('onclick');
        
        $width = $this->input->post('width');
        
        if( $width == null ){
            $width = "550px";
        }
        $data['width'] = $width;
        
        
        $height = $this->input->post('height');
        if( $height == null ){
            $height = "550px";
        }
        $data['height'] = $height;
        
            
        $zoom = $this->input->post('zoom');
        if( $zoom == null ){
            $zoom = "1";
        }
        $data['zoom'] = $zoom;
        
        $data['classes_filter'] = json_encode($classes_filter);
        
        //list of classes showed in rgraph.
        $rgraph_classes_div_id = $this->input->post('rgraph_classes_div_id');
        if($rgraph_classes_div_id != null){
            $temp = '<div class=\"rgraph_classes_list\">';
            
            if($data['unerasable_relations'] != 'null'){
                $this->load->model('workspaces_model');
                $classes = $this->workspaces_model->sortmulti($data['unerasable_relations'], 'level', 'asc', false, true);
                
                foreach($classes as $class){
                    $fontsize = '100';
                    switch($class['level']){
                        case 2:
                            $fontsize = '120';
                            $fontweight = 'Bold';
                            $opacity = "opacity:0.95; filter:alpha(opacity=95);";
                            break;
                        case 3:
                            $fontsize = '110';
                            $fontweight = 'Bold';
                            $opacity = "opacity:0.50; filter:alpha(opacity=50);";
                            break;
                        case 4:
                            $fontsize = '100';
                            $fontweight = 'Bold';
                            $opacity = "opacity:0.15; filter:alpha(opacity=15);";
                            break;
                        default:
                            $fontsize = '100';
                            $fontweight = 'Bold';
                            $opacity = "opacity:0.15; filter:alpha(opacity=15);";
                            break;
                    }
                    
                    $info = $this->prefixes_model->get_prefix_and_uri(str_replace($id."_", "", $class['id']));
                    $temp .= '<div style=\"cursor: pointer; cursor: hand; font-weight:'.$fontweight.'; font-size:'.$fontsize.'%;'.$opacity.' \" onclick=\"link_rgraph_'.$id.'(\''.$info['uri'].$info['class'].'\')\" >'.$info['class'].'</div>';
                }
            }
            
            $temp .= '</div>';
            $data['rgraph_classes_content'] = $temp;
        }
        $data['rgraph_classes_div_id'] = $rgraph_classes_div_id;
        
        /*
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
        */
        
        $this->load->view('rgraph/init_rgraph');
        $this->load->view('rgraph/rgraph',$data);
        
        //echo "END FUNCTION rgraph() rgraphs.php<br />";
    }

    /*
     returns:
        $data['graph'] =>  variable javascript que printara el rgraph sense color
        $data['graph_color'] => variable javascript que printara el rgraph amb color
        $data['unerasable_relations'] => variable que filtrara les relacions que no es poden borrar
        $data['relation_to_print'] => variable que servira per fer les relacions que apareixeran i desaparaixeran en el mousover 
     */    
    private function create_var_graph($id,$selected_class_uri,$selected_workspace,$subclassof, $aggregation,
            $subclass_relation_color, $both_relation_color, $relation_content_boolean, $classes_filter){
        
        //echo "FUNCTION create_var_graph(id,selected_class_uri,selected_workspace,subclassof,$aggregation,subclass_relation_color,both_relation_color,relation_content_boolean) rgraphs.php<br />";
        
        $classes_filter_query = 'FILTER ( ?class != <'.$selected_class_uri.'> ) .
            ';
        foreach($classes_filter as $class){
            $classes_filter_query .= 'FILTER ( ?class != <'.$class.'> ) .
                ';
        }
        
        $this->load->model('prefixes_model');
        $prefix_first_level = $this->prefixes_model->get_prefix_and_uri($selected_class_uri);
        
        $this->load->model('workspaces_model');
        $color = $this->workspaces_model->get_color($prefix_first_level['prefix']);
        
        $graph = '[ 
        { 
            id: "'.$id.'_'.$selected_class_uri.'",
            
            name: "'.$id.'_'.$selected_class_uri.'", 
            data: {"$dim": 5.0, real_id: "'.$selected_class_uri.'",uri: "'.$prefix_first_level['uri'].'", prefix: "'.$prefix_first_level['prefix'].'", class: "'.$prefix_first_level['class'].'" } 
        }';
           
        
        $graph_color = '[ 
        { 
            id: "'.$id.'_'.$selected_class_uri.'",
            
            name: "'.$id.'_'.$selected_class_uri.'", 
            data: {"$dim": 5.0, real_id: "'.$selected_class_uri.'",uri: "'.$prefix_first_level['uri'].'", prefix: "'.$prefix_first_level['prefix'].'", class: "'.$prefix_first_level['class'].'"'.$color.'} 
        }';
        
        $unerasable_relations = array();
        
        // es comprova que totes les relacions siguin tot faless (si son falses no cal fer res més)
        if( $aggregation == false && $subclassof == false ){
           
            $data['graph'] = $graph.']';
            $data['graph_color'] = $graph_color.']';
            $data['unerasable_relations'] = array();
            $data['relation_to_print'] = array();
            return $data;
        }
        
        $domain_relations = null;
        // buscar les relacions d'agregacio
        if($aggregation){
            $domain_relations = $this->workspaces_model->rgraph_level_aggegations($selected_workspace, $selected_class_uri,$classes_filter_query);
        }
        
        $subClassOf_domain_relations = null;
        // buscar les relacions subclassof
        if($subclassof){
            $subClassOf_domain_relations = $this->workspaces_model->rgraph_level_subclassof($selected_workspace, $selected_class_uri, $classes_filter_query);
        }

        if($domain_relations != null && $subClassOf_domain_relations != null ){
            $second_level_nodes = $this->set_color_weight_adjacencies(array_merge((array)$domain_relations, (array)$subClassOf_domain_relations),
                    $subclass_relation_color, $both_relation_color);
        }else if($subClassOf_domain_relations != null){
            $second_level_nodes = $this->set_color_weight_adjacencies((array)$subClassOf_domain_relations,
                    $subclass_relation_color, $both_relation_color);
        }else{
            $second_level_nodes = $this->set_color_weight_adjacencies((array)$domain_relations,
                    $subclass_relation_color, $both_relation_color); 
        }
        
        // foreach all related nodes (2 level) to the focused, get his related nodes (3 level)
        foreach($second_level_nodes as $second_level){
            if($second_level['class type']=='uri' && $second_level['class'] != $selected_class_uri ){
                
                $data_second_level = $this->prefixes_model->get_prefix_and_uri($second_level['class']);
                
                $color = $this->workspaces_model->get_color($data_second_level['prefix']);
                
                $relation_content = "";
                if($relation_content_boolean){
                    $relation_content = '<img src=\''.base_url().'public/icons/connector_empty.gif\' onmouseout=\'onmouseout_relation(\"'.$id.'-'.$data_second_level['class'].'\")\' onmouseover=\'onmouseover_relation(\"'.$id.'-'.$data_second_level['class'].'\")\'  >';
                }
                
                //in subclassof relations the direction of the arrow
                $direction = "";
                if(isset($second_level['direction'])){
                    if($second_level['direction'] == "left"){
                        $direction = '"$type":"arrow", "$direction": ["'.$id.'_'.$second_level['class'].'", "'.$id.'_'.$selected_class_uri.'"], "$dim":10,';
                    }else{
                        $direction = '"$type":"arrow", "$direction": ["'.$id.'_'.$selected_class_uri.'", "'.$id.'_'.$second_level['class'].'"], "$dim":10,';
                    }
                }
                
                //print node level 2
                $graph .= '
                ,{
                    id: "'.$id.'_'.$second_level['class'].'",
                    name: "'.$id.'_'.$second_level['class'].'",
                    data: { "$dim": 5.0, real_id: "'.$second_level['class'].'", prefix: "'.$data_second_level['prefix'].'", class: "'.$data_second_level['class'].'" },
                    adjacencies: [ 
                        { "nodeTo": "'.$id.'_'.$selected_class_uri.'", "data": { '.$direction.' '.$second_level['color'].' "$lineWidth": '.$second_level['weight'].', "relations": "'.$relation_content.'" } }
                    ]
                }';
                   
                $graph_color .= '
                ,{
                    id: "'.$id.'_'.$second_level['class'].'",
                    name: "'.$id.'_'.$second_level['class'].'",
                    data: { "$dim": 5.0, real_id: "'.$second_level['class'].'", prefix: "'.$data_second_level['prefix'].'", class: "'.$data_second_level['class'].'"'.$color.' },
                    adjacencies: [ 
                        { "nodeTo": "'.$id.'_'.$selected_class_uri.'",  "data": { '.$direction.' '.$second_level['color'].' "$lineWidth": '.$second_level['weight'].', "relations": "'.$relation_content.'" } }
                    ]
                }'; 
                                
                $new_relation = array(
                    'id' => $id.'_'.$second_level['class'],
                    'nodeTo' => $id.'_'.$selected_class_uri,
                    'level' => 2
                );
                
                array_push($unerasable_relations,$new_relation);
            }
        }
        
        $nodes_already_printed  = $second_level_nodes;
        $relation_to_print = array();

        // foreach all related nodes (3 level) to the 2 level nodes.
        foreach($second_level_nodes as $second_level){
            
            if($second_level['class type']=='uri' && $second_level['class'] != $selected_class_uri ){
                
                $third_level_nodes_pre = array();
                // buscar les relacions d'agregacio
                if($aggregation){ 
                    $third_level_nodes_pre = $this->workspaces_model->rgraph_level_aggegations($selected_workspace, $second_level['class'], $classes_filter_query);
                }
                
                $sparql_subClassOf_nodes = array();
                // buscar les relacions subclassof
                if($subclassof){
                    $sparql_subClassOf_nodes = $this->workspaces_model->rgraph_level_subclassof($selected_workspace, $second_level['class'], $classes_filter_query);
                }
                    
                if($third_level_nodes_pre != null && $sparql_subClassOf_nodes != null ){
                    $third_level_nodes = $this->set_color_weight_adjacencies(array_merge((array)$third_level_nodes_pre, (array)$sparql_subClassOf_nodes),
                            $subclass_relation_color, $both_relation_color);
                }else if($sparql_subClassOf_nodes != null){
                    $third_level_nodes = $this->set_color_weight_adjacencies((array)$sparql_subClassOf_nodes,
                            $subclass_relation_color, $both_relation_color);
                }else{
                    $third_level_nodes = $this->set_color_weight_adjacencies((array)$third_level_nodes_pre,
                            $subclass_relation_color, $both_relation_color); 
                }
                
                //Serach if the node is already printed
                foreach($third_level_nodes as $third_level){
           
                    if($third_level['class type']=='uri'){
                                                
                        $find = false;

                        foreach($nodes_already_printed as $node_printed ){
                            if($node_printed['class']==$third_level['class']){
                                $find = true;
                            }
                        }

                        if($find == false){
                            array_push($nodes_already_printed,$third_level);
                            
                            $data_third_level = $this->prefixes_model->get_prefix_and_uri($third_level['class']);
                
                            $color = $this->workspaces_model->get_color($data_third_level['prefix']);
                            
                            //in subclassof relations the direction of the arrow
                            $direction = "";
                            if(isset($third_level['direction'])){
                                if($third_level['direction'] == "left"){
                                    $direction = '"$type":"arrow", "$direction": ["'.$id.'_'.$third_level['class'].'", "'.$id.'_'.$second_level['class'].'"], "$dim":10,';
                                }else{
                                    $direction = '"$type":"arrow", "$direction": ["'.$id.'_'.$second_level['class'].'", "'.$id.'_'.$third_level['class'].'"], "$dim":10,';
                                }
                            }
                            
                            //print node level 3
                            $graph .= '
                            ,{
                                id: "'.$id.'_'.$third_level['class'].'",
                                name: "'.$id.'_'.$third_level['class'].'",
                                data: { "$dim": 5.0, real_id: "'.$third_level['class'].'", prefix: "'.$data_third_level['prefix'].'", class: "'.$data_third_level['class'].'" },
                                adjacencies: [ 
                                    { "nodeTo": "'.$id.'_'.$second_level['class'].'", "data": { '.$direction.' '.$third_level['color'].' "$lineWidth": '.$third_level['weight'].', "relations": "" } }
                                ]   
                            }';
                                                       
                            $graph_color .= '
                            ,{
                                id: "'.$id.'_'.$third_level['class'].'",
                                name: "'.$id.'_'.$third_level['class'].'",
                                data: { "$dim": 5.0, real_id: "'.$third_level['class'].'", prefix: "'.$data_third_level['prefix'].'", class: "'.$data_third_level['class'].'"'.$color.' },
                                adjacencies: [ 
                                    { "nodeTo": "'.$id.'_'.$second_level['class'].'", "data": { '.$direction.' '.$third_level['color'].' "$lineWidth": '.$third_level['weight'].', "relations": "" } }
                                ]   
                            }';
                                                        
                            $new_relation = array(
                                'id' => $id.'_'.$third_level['class'],
                                'nodeTo' => $id.'_'.$second_level['class'],
                                'level' => 3
                            );

                            array_push($unerasable_relations,$new_relation);
                            
                            $fourth_level_nodes_pre = array();
                            // buscar les relacions d'agregacio
                            if($aggregation){
                                $fourth_level_nodes_pre = $this->workspaces_model->rgraph_level_aggegations($selected_workspace, $third_level['class'], $classes_filter_query);
                            }
                            
                            $sparql_subClassOf_nodes = array();
                            // buscar les relacions subclassof
                            if($subclassof){
                                $sparql_subClassOf_nodes = $this->workspaces_model->rgraph_level_subclassof($selected_workspace, $third_level['class'], $classes_filter_query);
                            }
                            
                            if($fourth_level_nodes_pre != null && $sparql_subClassOf_nodes != null ){
                                $fourth_level_nodes = $this->set_color_weight_adjacencies(array_merge((array)$fourth_level_nodes_pre, (array)$sparql_subClassOf_nodes),
                                        $subclass_relation_color, $both_relation_color);
                            }else if($sparql_subClassOf_nodes != null){
                                $fourth_level_nodes = $this->set_color_weight_adjacencies((array)$sparql_subClassOf_nodes,
                                        $subclass_relation_color, $both_relation_color);
                            }else{
                                $fourth_level_nodes = $this->set_color_weight_adjacencies((array)$fourth_level_nodes_pre,
                                        $subclass_relation_color, $both_relation_color); 
                            }

                            //Serach if the node is already printed
                            foreach($fourth_level_nodes as $fourth_level){
                                
                                if($fourth_level['class type']=='uri'){
                                    
                                    $find = false;

                                    foreach($nodes_already_printed as $node_printed ){
                                        if($node_printed['class']==$fourth_level['class']){
                                            $find = true;
                                        }
                                    }

                                    if($find == false){
                                        array_push($nodes_already_printed,$fourth_level);

                                        $data_fourth_level =  $this->prefixes_model->get_prefix_and_uri($fourth_level['class']);
                
                                        $color = $this->workspaces_model->get_color($data_fourth_level['prefix']);
                                        
                                        //in subclassof relations the direction of the arrow
                                        $direction = "";
                                        if(isset($fourth_level['direction'])){
                                            if($fourth_level['direction'] == "left"){
                                                $direction = '"$type":"arrow", "$direction": ["'.$id.'_'.$fourth_level['class'].'", "'.$id.'_'.$third_level['class'].'"], "$dim":10,';
                                            }else{
                                                $direction = '"$type":"arrow", "$direction": ["'.$id.'_'.$third_level['class'].'", "'.$id.'_'.$fourth_level['class'].'"], "$dim":10,';
                                            }
                                        }
                                        
                                        //print node level 4
                                        $graph .= '
                                        ,{
                                            id: "'.$id.'_'.$fourth_level['class'].'",
                                            name: "'.$id.'_'.$fourth_level['class'].'",
                                            data: { "$dim": 5.0, real_id: "'.$fourth_level['class'].'", prefix: "'.$data_fourth_level['prefix'].'", class: "'.$data_fourth_level['class'].'" },
                                            adjacencies: [ 
                                                { "nodeTo": "'.$id.'_'.$third_level['class'].'", "data": { '.$direction.' '.$fourth_level['color'].' "$lineWidth": '.$fourth_level['weight'].', "relations": "" } }
                                            ]   
                                        }';
                                                                                
                                        $graph_color .= '
                                        ,{
                                            id: "'.$id.'_'.$fourth_level['class'].'",
                                            name: "'.$id.'_'.$fourth_level['class'].'",
                                            data: { "$dim": 5.0, real_id: "'.$fourth_level['class'].'", prefix: "'.$data_fourth_level['prefix'].'", class: "'.$data_fourth_level['class'].'"'.$color.' },
                                            adjacencies: [ 
                                                { "nodeTo": "'.$id.'_'.$third_level['class'].'", "data": { '.$direction.' '.$fourth_level['color'].' "$lineWidth": '.$fourth_level['weight'].', "relations": "" } }
                                            ]   
                                        }';
                                        
                                       //array de les relacions que no es printaran a priori
                                       $new_relation = array(
                                            'id' => $id.'_'.$fourth_level['class'],
                                            'nodeTo' => $id.'_'.$third_level['class'],
                                            'level' => 4
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
        
        //echo "END FUNCTION create_var_graph(id,selected_class_uri,selected_workspace,subclassof,subclass_relation_color, both_relation_color, relation_content_boolean) rgraphs.php<br />";
        
        return $vars;
    }
    
    private function set_color_weight_adjacencies($rows, $subclass_relation_color, $both_relation_color){
        
        //echo "FUNCTION set_color_weight_adjacencies(rows, subclass_relation_color, both_relation_color) rgraphs.php<br />";
        
        $new_array = array();
        
        foreach ($rows as $row){
            $i=0;
            $add = false;
            while($i<count($new_array) && !$add){
                if($row['class'] == $new_array[$i]['class']){
                    
                    /*
                    Default colors:
                    normal_relation                          ""
                    subclasOf_relation (or equivalentClass)  "#dd99dd"
                    both_relation                            "#808000"
                    */
                    if($row['relation'] == 'http://www.w3.org/2000/01/rdf-schema#subClassOf' || 
                       $row['relation'] == 'http://www.w3.org/2002/07/owl#equivalentClass' ){
                        
                        if($new_array[$i]['color'] == ""){
                            $new_array[$i]['color'] = '"$color":"'.$both_relation_color.'",';
                        }
                    }else{
                        if($new_array[$i]['color'] == '"$color":"'.$subclass_relation_color.'",'){
                            $new_array[$i]['color'] = '"$color":"'.$both_relation_color.'",';
                        }
                    }
                    
                    if($new_array[$i]['weight'] < 6){
                        $new_array[$i]['weight'] = $new_array[$i]['weight'] + 1;
                    }else{
                        $new_array[$i]['weight'] = 6;
                    }
                    
                    $add=true;
                }
                $i++;
            }
            if(!$add){
                $new_row = array();
                $new_row['class'] = $row['class'];
                $new_row['class type'] = $row['class type'];
                if(isset($row['direction'])){
                    $new_row['direction'] = $row['direction'];
                }
                $new_row['weight'] = 1;
                if($row['relation'] == 'http://www.w3.org/2000/01/rdf-schema#subClassOf' ||
                   $row['relation'] == 'http://www.w3.org/2002/07/owl#equivalentClass' ){
                    $new_row['color'] = '"$color":"'.$subclass_relation_color.'",';                    
                }else{
                    $new_row['color'] = '';
                }
                array_push($new_array,$new_row);
            }
        }
        
        //echo "END FUNCTION set_color_weight_adjacencies(rows, subclass_relation_color, both_relation_color) rgraphs.php<br />";
        
        return $new_array;
    }
    
    // end private functions.
    
}