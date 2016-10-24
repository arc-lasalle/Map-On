<?php


class MaponRouting
{
    var $ci = NULL;
    
    public function __construct() {
        $this->ci = &get_instance();

        /*
        $previous_tab_id = $this->ci->session->userdata('tab_id');
        $actual_tab_id = empty($_COOKIE['tab_id']) ? null : $_COOKIE['tab_id'];

        if ( $previous_tab_id === null ) {
            $this->ci->session->set_userdata('tab_id', $actual_tab_id);
        } else if ( $actual_tab_id === null ) {
            // Nothing, internal redirect.
        } else if ( $actual_tab_id != $previous_tab_id ) {
            $this->ci->session->sess_destroy();
            $controller = $this->ci->router->fetch_class();
            $function = $this->ci->router->fetch_method();
            if ( !($controller === 'auth' && $function == 'login') ) {
                redirect('/auth/login');
            }
        }
        */
        
    }

    // Tab data sources
    public function set( $datasource_id, $mappingspace_id = null, $mapped_class_id = null  ) {

        $routing = [];
        $routing['tab'] = "datasource";
        $routing['base_url'] = base_url();
        $routing['datasource_id'] = $datasource_id;
        if ( $mappingspace_id != null ) $routing['mappingspace_id'] = $mappingspace_id;
        if ( $mapped_class_id != null ) $routing['mapped_class_id'] = $mapped_class_id;

        $this->ci->session->set_userdata('mapon_routing', $routing );
        
    }

    // Tab ontologies
    public function setO( $ontology_id = null ) {
        $routing = [];
        $routing['tab'] = "ontology";
        if ( $ontology_id != null ) $routing['ontology_id'] = $ontology_id;

        $this->ci->session->set_userdata('mapon_routing', $routing );
    }

    
    public function get( $attr = null ) {
        $routing = $this->ci->session->userdata('mapon_routing');
        
        if ( $attr == null ) return $routing;
        
        if ( isset($routing[$attr]) ) return $routing[$attr];
        
        return null;
    }
    
    public function clean() {
        $this->ci->session->unset_userdata('mapon_routing');
    }

    public function getTab() {
        return $this->get('tab');
    }
    
    public function getDatasourceId() {
        return $this->get('datasource_id');
    }

    public function getMappingSpaceId() {
        return $this->get('mappingspace_id');
    }

    public function getMappedClassId() {
        return $this->get('mapped_class_id');
    }

    public function showMessage( $success, $message_header, $message_description = "" ) {
        $data = [$success, $message_header ];
        if ( !empty($message_description) ) $data[] = $message_description;
        $this->ci->session->set_flashdata('error_message', $data );
    }
    
}