<?php


class MaponLog extends CI_Model
{
    var $ci = NULL;

    function __construct()
    {
        parent::__construct();
        $this->ci = &get_instance();

    }

    function add( $action, $logMessage )
    {

        $actualPath = $this->getActualLogPath();

        if ( $actualPath != null ) {
            $this->_db_insert_log( $actualPath, $logMessage, $action );
        }

    }
    
    function get ( $maxResults = 10 ) {

        $start_path = $this->getActualLogPath();

        if ( $start_path != null ) {
            return $this->_db_get_log( $maxResults, $start_path );
        }

        return $this->_db_get_log( $maxResults );
    }


    function getActualLogPath() {

        $tab = $this->ci->maponrouting->getTab();

        if ( $tab === "datasource" ) {

            $datasource_id = $this->ci->maponrouting->getDatasourceId();
            $mappingspace_id = $this->ci->maponrouting->getMappingSpaceId();
            $mapped_class_id = $this->ci->maponrouting->getMappedClassId();

            $location = 'datasource';

            if ( $datasource_id != null ) $location .= '/' . $datasource_id;

            if ( $mappingspace_id != null ) $location .= '/mappingspace/' . $mappingspace_id;

            if ( $mapped_class_id != null ) $location .= '/' . $mapped_class_id;

            return $location;
        }

        return null;

    }


    function _db_insert_log( $location, $logMessage, $action ) {
        $user_name = $this->ci->session->userdata('username');

        if ( $user_name == null ) $user_name = "User";

        $this->ci->team->db->insert('log',
            array(
                'location' => $location,
                'user_name' => $user_name,
                'log_message' => $logMessage,
                'action' => $action
            )
        );
    }

    function _db_get_log( $maxResults, $location_start = null ) {

        $this->ci->team->db->select( '*' );
        $this->ci->team->db->from( 'log' );
        if ( $location_start != null ) {
            $this->ci->team->db->like( 'location', $location_start, 'after'); // produces 'something%' not '%something%'
        }
        //$this->ci->team->db->where( 'location', $location );
        $this->ci->team->db->order_by( 'date', 'desc' );
        $this->ci->team->db->limit( $maxResults );

        return $this->ci->team->db->get()->result();
    }
    
}