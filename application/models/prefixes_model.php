<?php
class Prefixes_model extends CI_Model {
    
    function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }
    
    function get_prefixes($num, $offset) {
        $this->db->order_by("prefix", "ASC");
        $query = $this->db->get('prefixes', $num, $offset);	
        return $query->result();
    }
        
	function get_prefix($uri) {
        $this->db->where('uri', $uri);
        $query = $this->db->get('prefixes');	
        return $query;
    }
	
    function add($uri,$prefix,$color){
        $data = array(
            'uri' => $uri ,
            'prefix' => $prefix,
            'color' => $color
        );

        $this->db->insert('prefixes', $data); 
    }
    
    function modify($id,$uri,$prefix,$color){
        $data = array(
            'uri' => $uri,
            'prefix' => $prefix,
            'color' => $color
        );

        $this->db->where('id', $id);
        $this->db->update('prefixes', $data); 
    }
        
    function modifyPrefix($uri,$prefix){
        $data = array(
            'prefix' => $prefix,
        );

        $this->db->where('uri', $uri);
        $this->db->update('prefixes', $data); 
    }
	
    function delete($id){
        $this->db->where('id', $id);
        $this->db->delete('prefixes'); 
    }
    
    public function add_auto_prefix($uri){
        $this->db->select_max('id');
        $query_pre = $this->db->get('prefixes');
        $query = $query_pre->result();
        
        $prefix_pre = $query[0]->id + 1;
        
        $prefix = 'prefix-'.$prefix_pre;
        
        $data = array(
            'uri' => $uri,
            'prefix' => $prefix
        );

        $this->db->insert('prefixes', $data); 
        
        return $prefix;
    }
    
    public function get_uri($qname){
		if($qname == "")
            return "";
		
		$qnameexp = explode(':', $qname);		
		
		$this->db->select('uri');
        $this->db->where('prefix',$qnameexp[0]);
        $prefixes_query_pre = $this->db->get('prefixes');
        $prefixes_query = $prefixes_query_pre->result();
		
		if($prefixes_query == array())
            return "";
		else 
            return $prefixes_query[0]->uri.$qnameexp[1];
    }
    
    
    // get prefix and uri, (if the prefix isn't found, add generic prefix)
    public function get_prefix_and_uri($uri){
        $data = array();
        
		if($uri == '') {
            $data['uri'] = "";
            $data['prefix'] = "";
            $data['class'] = ""; 
        }
        
        //Is temporal class? False = it isn't temporal class
        else if( ($uri[0] != '_') && ($uri[1] != ':') && ($uri[2] != 'b')){
            $pos = strpos($uri, '#');

            //uri finish with #
            if ($pos !== false) {
                
                $class_uri = explode('#', $uri);
                
                $this->db->select('uri, prefix');
                $this->db->where('uri',$class_uri[0].'#');
                $prefixes_query_pre = $this->db->get('prefixes');
                $prefixes_query = $prefixes_query_pre->result();
                 
                //prefixed don't found in database
                if($prefixes_query == array()){
                    $this->load->model('prefixes_model');
                    //add prefix with random name (ex: prefix-xxx)
                    $new_generic_prefix = $this->prefixes_model->add_auto_prefix($class_uri[0].'#');
                    
                    $data['uri'] = $class_uri[0].'#';
                    $data['prefix'] = $new_generic_prefix; 
                    $data['class'] = $class_uri[1]; 
                }else{
                    $data['uri'] = $class_uri[0].'#';
                    $data['prefix'] = $prefixes_query[0]->prefix; 
                    $data['class'] = $class_uri[1]; 
                }

            //uri finish with /    
            }else{
                
                //explode sin coger el resto
                $class_uri_pre = explode('/', $uri);
                
                $class_uri = implode('/', array_slice($class_uri_pre, 0, count($class_uri_pre)-1)).'/';
                
                $this->db->select('prefix');
                $this->db->where('uri',$class_uri);
                $prefixes_query_pre = $this->db->get('prefixes');
                $prefixes_query = $prefixes_query_pre->result();

                //prefixed don't found in database
                if($prefixes_query == array()){
                    $this->load->model('prefixes_model');
                    //add prefix with random name (ex: prefix-xxx)
                    $new_generic_prefix = $this->prefixes_model->add_auto_prefix($class_uri);
                
                    $data['uri'] = $class_uri;
                    $data['prefix'] = $new_generic_prefix;
                    $data['class'] = $class_uri_pre[count($class_uri_pre)-1];
                }else{
                    $data['uri'] = $class_uri;
                    $data['prefix'] = $prefixes_query[0]->prefix; 
                    $data['class'] = $class_uri_pre[count($class_uri_pre)-1];
                }
            }
            
            return $data;
        }
           
    }
  
}