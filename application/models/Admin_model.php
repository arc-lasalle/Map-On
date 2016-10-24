<?php
class Admin_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function getTeams( $user_id = null )
    {
        $this->db->select('*');
        $this->db->from('teams');

        if ( $user_id != null ) {
            $this->db->join( 'users_teams', 'teams.id = users_teams.team_id' );
            $this->db->where( 'users_teams.user_id', $user_id );
        }

        $query = $this->db->get();

        return $query->result();
    }

    function getTeam( $team_id )
    {
        $this->db->where('id', $team_id);

        $query = $this->db->get('teams');

        return $query->row();
    }

    function addTeam( $team_name, $team_db_name, $team_db_dir )
    {
        $this->db->insert('teams', array(
            'name' => $team_name,
            'database_name' => $team_db_name,
            'directory_name' => $team_db_dir)
        );

        return $this->db->insert_id();
    }

    function deleteTeam ( $team_id ) {

        $sql = "DELETE FROM users_teams WHERE team_id=". $team_id;
        $this->db->query($sql);

        $sql = "DELETE FROM teams WHERE id=". $team_id;
        $this->db->query($sql);

    }

    function editTeam( $team_id, $team_name, $team_db_name, $team_db_dir )
    {
        $this->db->where( 'id', $team_id );

        return $this->db->update( 'teams', array(
            'name' => $team_name,
            'database_name' => $team_db_name,
            'directory_name' => $team_db_dir)
        );
    }

    function setUserTeams( $user_id, $user_teams ) {

        $sql = "DELETE FROM users_teams WHERE user_id=". $user_id;

        $this->db->query($sql);

        if ( is_array($user_teams) ) {
            foreach ( $user_teams as $team_id ) {
                $this->db->insert('users_teams', array('user_id' => $user_id, 'team_id' => $team_id ));
            }
        }

    }

    function setUserGroups( $user_id, $user_groups ) {

        $sql = "DELETE FROM users_groups WHERE user_id=". $user_id;

        $this->db->query($sql);

        foreach ( $user_groups as $group_id ) {
            $this->db->insert('users_groups', array('user_id' => $user_id, 'group_id' => $group_id ));
        }

    }

    function createDatabase( $database_name, $sql_file ) {

        // Create database

        $dbhost = $this->db->hostname;
        $dbuser = $this->db->username;
        $dbpass = $this->db->password;
        $conn = mysqli_connect($dbhost, $dbuser, $dbpass);

        if(! $conn ) {
            die('Could not connect: ' . mysqli_error($conn) . '<br>');
        }

        echo 'Connected successfully <br>';

        $sql_create = "CREATE DATABASE " . $database_name;
        if (mysqli_query($conn, $sql_create) === TRUE) {
            echo "Database created successfully <br>";
        } else {
            echo "Error creating database: " . mysqli_error($conn) . '<br>';
        }

        mysqli_close($conn);

        $sql_file = getcwd() . "/" . $sql_file;

        //$command = "/usr/bin/mysql --user=$dbuser --password=$dbpass $database_name < $sql_file";
        $command = "mysql --user=$dbuser --password=$dbpass $database_name < $sql_file";

        set_time_limit ( 120 );
        shell_exec($command);
        set_time_limit ( 30 );

        echo "Database imported successfully <br>";

    }

}