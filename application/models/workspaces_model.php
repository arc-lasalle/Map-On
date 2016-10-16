<?php
class Workspaces_model extends CI_Model {
    
    function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }
    
    function add($name,$endpoint){
        //echo "FUNCTION add(name,endpoint) Workspaces_model.php<br />";
        
        // create row in workspace table.
        $data = array(
            'name' => strtolower($name),
            'endpoint' => $endpoint,
        );
        $this->db->insert('workspaces', $data);
        
        include_once("public/arc2/ARC2.php");

        // Config mysql
        $config_Mysql = array(
            // db 
            'db_host' => $this->db->hostname, // default: localhost
            'db_name' => $this->db->database,
            'db_user' => $this->db->username,
            'db_pwd' => $this->db->password,
            // store 
            'store_name' => $name,
        );

        $store_Mysql = ARC2::getStore($config_Mysql);
        
        /* create MySQL tables 
         *
         * _g2t
         * _id2val
         * _o2val
         * _s2val
         * _setting
         * _triple
         */
        $store_Mysql->setUp(); 
  
        //echo "END FUNCTION add(name,endpoint) Workspaces_model.php<br />";
    }
    
    function modify($id,$name,$endpoint){
        //echo "FUNCTION modify(id,name,endpoint) Workspaces_model.php<br />";
        
        $data = array(
            'name' => strtolower($name),
            'endpoint' => $endpoint,
        );
        
        $this->db->where('id', $id);
        $this->db->update('workspaces', $data);
        
        //echo "END FUNCTION modify(id,name,endpoint) Workspaces_model.php<br />";
    }
    
    function find_data_workspace($name){
        //echo "FUNCTION find_data_workspace(name) Workspaces_model.php<br />";
        
        $this->db->where('name', $name);
        $workspaces = $this->db->get('workspaces');
        return $workspaces->result();
        
        //echo "END FUNCTION find_data_workspace(name) Workspaces_model.php<br />";
    }
    
    // return name of first workspace
    function first_workspace(){
        //echo "FUNCTION first_workspace() Workspaces_model.php<br />";
        
        $this->db->select('name');
        $this->db->select_min('id');
        $workspaces_pre = $this->db->get('workspaces');
        $workspace = $workspaces_pre->result();
        if(isset($workspace[0])){
            return $workspace[0]->name;
        }else{
            return false;
        }
        
        //echo "END FUNCTION first_workspace() Workspaces_model.php<br />";
    }
    
    function connect_workspace($name){
        //echo "FUNCTION connect_workspace(name) Workspaces_model.php<br />";
        
        include_once("public/arc2/ARC2.php");

        // Config mysql
        $config_Mysql = array(
            // db 
            'db_host' => $this->db->hostname, // default: localhost
            'db_name' => $this->db->database,
            'db_user' => $this->db->username,
            'db_pwd' => $this->db->password,
            // store 
            'store_name' => $name,
        );

        // instantiation mysql
        $store_Mysql = ARC2::getStore($config_Mysql);
        
        //echo "END FUNCTION connect_workspace(name) Workspaces_model.php<br />";
        
        return $store_Mysql;
    }
    
    public function get_color($prefixed ){
        //echo "FUNCTION get_color(prefixed) Workspaces_model.php<br />";
        
        $color = '';

        if(!$this->session->userdata('nextcolors')){
            $this->session->set_userdata('colors', array());
            $this->session->set_userdata('nextcolors', 0);
        }

        $colors = $this->session->userdata('colors');
        $nextcolors = $this->session->userdata('nextcolors');

        if( isset($colors[$prefixed]) ){
            $color = ', $color: "'.$colors[$prefixed].'"';
        }else{
            
            $this->db->select('color');
            $this->db->where('prefix',$prefixed);
            $color_quey = $this->db->get('prefixes');
            $new_color = $color_quey->result();
            
            if($new_color != null){
            //if($new_color[0]->color != ""){
                $new_color_for = $new_color[0]->color;
                
                $colors[$prefixed] = $new_color_for;
                $color = ', $color: "'.$colors[$prefixed].'"';
                $this->session->set_userdata('colors',$colors);
                
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

                $colors[$prefixed] = $new_color_for;
                $color = ', $color: "'.$colors[$prefixed].'"';
                $this->session->set_userdata('colors', $colors);
            }
        }
        
        //echo "END FUNCTION get_color(prefixed) Workspaces_model.php<br />";
        
        return $color;
    }
    
    public function get_workspace_id($workspace_name){
        //echo "FUNCTION get_workspace_id(workspace_name) Workspaces_model.php<br />";
        
        $this->db->select('id');
        $this->db->where('name', $workspace_name);
        $workspace_pre = $this->db->get('workspaces');
        
        $workspace = $workspace_pre->result();
        
        $workspace_id = 0;
        if($workspace!=null){
            $workspace_id = $workspace[0]->id;
        }
        
        //echo "END FUNCTION get_workspace_id(workspace_model) Workspaces_model.php<br />";
        
        return $workspace_id;
    }
    
    function sortmulti ($array, $index, $order, $natsort=FALSE, $case_sensitive=FALSE) {
        //echo "FUNCTION sortmulti(array,index,order,natsort,case_sensitive) Workspaces_model.php<br />";
        
        if(is_array($array) && count($array)>0) {
            foreach(array_keys($array) as $key)
            $temp[$key]=$array[$key][$index];
            if(!$natsort) {
                if ($order=='asc')
                    asort($temp);
                else   
                    arsort($temp);
            }
            else
            {
                if ($case_sensitive===true)
                    natsort($temp);
                else
                    natcasesort($temp);
            if($order!='asc')
                $temp=array_reverse($temp,TRUE);
            }
            foreach(array_keys($temp) as $key)
                if (is_numeric($key))
                    $sorted[]=$array[$key];
                else   
                    $sorted[$key]=$array[$key];
            return $sorted;
        }
        
        //echo "END FUNCTION sortmulti(array,index,order,natsort,case_sensitive) Workspaces_model.php<br />";
        
        return $sorted;
    }
    
    
    /* 
     * 
     * !!!!!!!!!!!!!!!!!!! SPARQL FUNCTIONS !!!!!!!!!!!!!!!!!!!!
     *  
     */
    
    
    // get classes than aren't subclassof anyother class. (root class)
    // used in => idented list // treemap
    function get_first_level_class($workspace){
        //echo "FUNCTION get_first_level_class(workspace) workspaces_model.php<br />";
        
        $store_Mysql = $this->connect_workspace($workspace);
        
        // class than aren't subclassof anyother class. 
        $query_subclassof = '
            SELECT DISTINCT ?cl
            WHERE {
                {
                    ?cl ?type <http://www.w3.org/2002/07/owl#Class> .
                }
                UNION
                {
                    ?cl ?type <http://www.w3.org/2000/01/rdf-schema#Class> .
                }
                OPTIONAL {
                    ?cl <http://www.w3.org/2000/01/rdf-schema#subClassOf> ?sc .
                }
                FILTER ( !bound(?sc) ) .
                FILTER isIRI(?cl) .
                FILTER (?cl != <http://www.w3.org/2002/07/owl#Thing> ).
            }
            ORDER BY ?cl
        ';
        
        $subclassof_pre = $store_Mysql->query($query_subclassof,'rows');
        
        $array_subclassof = array();
        foreach($subclassof_pre as $row){
            array_push($array_subclassof, $row['cl']);
        }
        
        // class than are equivalentClass class.
        $query_equivalentClass = '
            SELECT DISTINCT ?cl
            WHERE {
                {
                    ?cl ?type <http://www.w3.org/2002/07/owl#Class> .
                }
                UNION
                {
                    ?cl ?type <http://www.w3.org/2000/01/rdf-schema#Class> .
                }
                OPTIONAL 
                {
                    ?cl <http://www.w3.org/2002/07/owl#equivalentClass> ?sc .
                }
                FILTER ( bound(?sc) ) .
                FILTER isIRI(?cl) .
            }
            ORDER BY ?cl
        ';
        
        $equivalentClass_pre = $store_Mysql->query($query_equivalentClass,'rows');
        
        $array_equivalentClass = array();
        foreach($equivalentClass_pre as $row){
            array_push($array_equivalentClass, $row['cl']);
        }
        
        // delete row in subclass array that are a equivalentClass of another class.
        $root_classes = array_diff($array_subclassof, $array_equivalentClass);
        
        $this->load->model('prefixes_model');
        
        $i = 0;
        foreach($root_classes as $class){
            $prefix_first_level = $this->prefixes_model->get_prefix_and_uri($class);
            
            $root_classes_final[$i]['uri'] = $class;
            $root_classes_final[$i]['prefix'] = $prefix_first_level['prefix'];
            $root_classes_final[$i]['class'] = ucfirst($prefix_first_level['class']);
            $i++;
        }
        
        $sort_classes = $this->sortmulti($root_classes_final, 'class', 'asc');
        
        //echo "END FUNCTION get_first_level_class(workspace) workspaces_model.php<br />";
        
        return $sort_classes;
    }
    
    // used in => idented list // treemap
    function get_next_level_class($workspace, $id){
        //echo "FUNCTION get_next_level_class(workspace,id) workspaces_model.php<br />";

        $store_Mysql = $this->connect_workspace($workspace);
        
        $query = '
            SELECT *
            WHERE {
                {
                    ?uri <http://www.w3.org/2002/07/owl#equivalentClass> <'.$id.'> .
                }
                UNION
                {
                    ?uri <http://www.w3.org/2000/01/rdf-schema#subClassOf> <'.$id.'> .
                }
                FILTER isIRI(?uri) .
            }
            ORDER BY ?uri
            '; 
        
        $child_classes = $store_Mysql->query($query,'rows');
        
        $sort_classes = array();
        
        if( $child_classes != array() ){
            $this->load->model('prefixes_model');

            for($i=0;$i<count($child_classes);$i++){
                $class_info = $this->prefixes_model->get_prefix_and_uri($child_classes[$i]['uri']);
                $child_classes[$i]['class'] = $class_info['class'];
            }
        
            $sort_classes = $this->sortmulti($child_classes, 'class', 'asc');
        }
        
        //echo "END FUNCTION get_next_level_class(workspace,id) Workspaces_model.php<br />";
        
        return $sort_classes;
    }
    
    // used in => workspaces // treemap
    public function get_all_classes($workspace_name){
        //echo "FUNCTION get_all_classes(workspace_name) Workspaces_model.php<br />";
        
        $store_Mysql = $this->connect_workspace($workspace_name);
        
        $q = 'SELECT DISTINCT ?class WHERE {
                { 
                    ?class ?y <http://www.w3.org/2002/07/owl#Class> .
                }
                UNION
                { 
                    ?class ?y <http://www.w3.org/2000/01/rdf-schema#Class> .
                }
                FILTER isIRI(?class) .
            }ORDER BY ?class';
        
        $classes = $store_Mysql->query($q, 'rows');
        
        //echo "END FUNCTION get_all_classes(workspace_name) Workspaces_model.php<br />";
        
        return $classes;
    }
    
    // used in => treemap
    public function get_class_subclassofs($workspace_name, $class_uri){
        //echo "FUNCTION get_class_subclassofs(workspace_name,class_uri) Workspaces_model.php<br />";
        
        $store_Mysql = $this->connect_workspace($workspace_name);
        
        $subClassOf_domain = 'SELECT DISTINCT ?class_uri ?relation_uri WHERE {
            {
                <'.$class_uri.'> ?relation_uri ?class_uri .
                FILTER ( ?relation_uri = <http://www.w3.org/2000/01/rdf-schema#subClassOf> || ?relation_uri = <http://www.w3.org/2002/07/owl#equivalentClass> ) .
            }
            FILTER isIRI(?class_uri) .
        }
        ORDER BY ?relation_uri ?class_uri
        ';

        $subClassOf_domain_relations = $store_Mysql->query($subClassOf_domain,'rows');

        $subClassOf_range = 'SELECT DISTINCT ?class_uri ?relation_uri WHERE{
            {
                ?class_uri ?relation_uri <'.$class_uri.'> .
                FILTER ( ?relation_uri = <http://www.w3.org/2000/01/rdf-schema#subClassOf> || ?relation_uri = <http://www.w3.org/2002/07/owl#equivalentClass> ) .
            }
            FILTER isIRI(?class_uri) .
        }
        ORDER BY ?relation_uri ?class_uri
        ';

        $subClassOf_range_relations = $store_Mysql->query($subClassOf_range, 'rows');
        
        //revisar !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $subclassofs = array_merge((array)$subClassOf_domain_relations, (array)$subClassOf_range_relations);
        
        //echo "END FUNCTION get_class_subclassofs(workspace_name,class_uri) Workspaces_model.php<br />";
        
        return $subclassofs;
    }
    
    // used in => treemap
    public function get_class_aggregations($workspace_name, $class_uri){
        //echo "FUNCTION get_class_aggregations(workspace_name,class_uri) Workspaces_model.php<br />";
        
        $store_Mysql = $this->connect_workspace($workspace_name);
        
        $aggregation_query = 'SELECT DISTINCT ?class_uri ?relation_uri WHERE {
            {
                ?relation_uri <http://www.w3.org/2000/01/rdf-schema#range> <'.$class_uri.'> .
                ?relation_uri <http://www.w3.org/2000/01/rdf-schema#domain> ?class_uri .
                ?relation_uri <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?type .
            }
            UNION
            {
                ?relation_uri <http://www.w3.org/2000/01/rdf-schema#domain> <'.$class_uri.'>.
                ?relation_uri <http://www.w3.org/2000/01/rdf-schema#range> ?class_uri .
                ?relation_uri <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?type .
            }
            FILTER isIRI(?class_uri) .
        }';
        
        $aggregations = $store_Mysql->query($aggregation_query, 'rows');
        
        //echo "END FUNCTION get_class_aggregations(workspace_name,class_uri) Workspaces_model.php<br />";
        
        return $aggregations;
    }
    
    // use in rgraph create_var_graph
    public function rgraph_level_aggegations($selected_workspace, $selected_class_uri, $classes_filter_query){
        
        $store_Mysql = $this->connect_workspace($selected_workspace);
        
        $domain = 'SELECT DISTINCT ?class ?relation ?type WHERE {
            {
                ?relation <http://www.w3.org/2000/01/rdf-schema#domain> <'.$selected_class_uri.'>.
                ?relation <http://www.w3.org/2000/01/rdf-schema#range> ?class .
            }
            UNION
            {
                ?relation <http://www.w3.org/2000/01/rdf-schema#range> <'.$selected_class_uri.'> .
                ?relation <http://www.w3.org/2000/01/rdf-schema#domain> ?class .
            }
            ?relation <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?type .
            FILTER isIRI( ?class ) .
            '.$classes_filter_query.'
            FILTER ( ?type = <http://www.w3.org/2002/07/owl#ObjectProperty> || ?type = <http://www.w3.org/1999/02/22-rdf-syntax-ns#Property> ) .
        }'; 

        return $store_Mysql->query($domain, 'rows');
    }
    
    // use in rgraph create_var_graph
    public function rgraph_level_subclassof($selected_workspace, $selected_class_uri, $classes_filter_query){
        
        $store_Mysql = $this->connect_workspace($selected_workspace);
        
        $subClassOf_domain_right = 'SELECT DISTINCT ?class ?relation WHERE {
            ?class ?relation <'.$selected_class_uri.'> .
            FILTER ( ?relation = <http://www.w3.org/2000/01/rdf-schema#subClassOf> || ?relation = <http://www.w3.org/2002/07/owl#equivalentClass> ) .
            FILTER isIRI( ?class ) .
            '.$classes_filter_query.'
        }';

        $subClassOf_domain_relations_right = $store_Mysql->query($subClassOf_domain_right,'rows');
        for($i=0; $i<count($subClassOf_domain_relations_right); $i++){
            $subClassOf_domain_relations_right[$i]['direction'] = "right";
        }

        $subClassOf_domain_left = 'SELECT DISTINCT ?class ?relation WHERE {
            <'.$selected_class_uri.'> ?relation ?class .
            FILTER ( ?relation = <http://www.w3.org/2000/01/rdf-schema#subClassOf> || ?relation = <http://www.w3.org/2002/07/owl#equivalentClass> ) .
            FILTER isIRI( ?class ) .
            '.$classes_filter_query.'
        }';

        $subClassOf_domain_relations_left = $store_Mysql->query($subClassOf_domain_left,'rows');

        for($i=0; $i<count($subClassOf_domain_relations_left); $i++){
            $subClassOf_domain_relations_left[$i]['direction'] = "left";
        }

        return array_merge((array)$subClassOf_domain_relations_left,(array)$subClassOf_domain_relations_right);

    }
    
}
