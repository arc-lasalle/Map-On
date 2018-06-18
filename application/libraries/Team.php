<?php

class Team {

    var $ci = NULL;
    var $db = NULL;

    public function __construct() {

        $this->ci = &get_instance();
        $this->ci->load->library('ion_auth');
        $this->ci->load->library('session');
        $this->ci->load->model("Admin_model", "admin");
        $this->ci->load->model("Ion_auth_model", "auth");

        if ( !$this->ci->ion_auth->logged_in() ) return;

        $this->setTeam();
        $this->setDb();

    }

    public function setTeam() {

        if ( isset($_POST["set_team"]) ) {
            // Set team by ajax.

            $team_id = intval( $_POST["set_team"] );
            
            $team_db = $this->ci->admin->getTeam( $team_id );

            $this->ci->session->set_userdata('team_database', $team_db );

            exit(0);

        }

        $team_selected = $this->ci->session->userdata('team_database');

        if ( $team_selected == NULL ) {
            // Establecemos el team por defecto

            $teams = $this->getTeams();

            if ( count($teams) <= 0 ) {
                $this->connected( false );
                return;
            }

            $this->ci->session->set_userdata('team_database', $teams[0] );
        }

    }

    public function setDb( ) {

        if ( !$this->connected() ) return;

        if ( $this->db != NULL ) $this->db->close();

        // Obtenemos los valores del archivo ce configuracion
        $dsn = [
			'dbdriver' => $this->ci->db->dbdriver,
			'username' => $this->ci->db->username,
			'password' => $this->ci->db->password,
			'hostname' => $this->ci->db->hostname,
			'database' => $this->ci->session->userdata('team_database')->database_name
		];
		
		$this->db = $this->ci->load->database( $dsn, TRUE );

        if ( $this->db->conn_id == false ) {
            //$this->ci->session->unset_userdata('team_database');
            $this->connected( false );
        }

    }

    public function getTeams () {
        $user_id = $this->ci->ion_auth->user()->row()->id;

        //if ( $this->ci->ion_auth->is_admin() ) {
        //    return $this->ci->admin->getTeams();
        //}

        return $this->ci->admin->getTeams( $user_id );

    }

    public function connected( $connected = null ) {
        if ( $connected === false ) $this->ci->session->set_userdata('team_database', null );
        return ($this->ci->session->userdata('team_database') != null);
    }

    public function dir() {
        if ( $this->connected() ) {
            return $this->ci->session->userdata('team_database')->directory_name;
        } else {
            return "default";
        }
    }

    public function getid() {
        if ( $this->connected() ) {
            return $this->ci->session->userdata('team_database')->id;
        } else {
            return -1;
        }
    }

}