<?php
	class Log_model extends CI_Model 
	{
		function __construct()
		{
			parent::__construct();
		}
		
		function write($action, $type='edit', $table = '', $table_id=0)
		{
			if ($this->ion_auth->logged_in()) {
				$user = $this->ion_auth->user()->row();

				$this->db->insert('log', array('user_id' => $user->id, 'action' => $action, 'type' => $type, 'date' => date("Y-m-d H:i:s", time()), 'table_name' => $table, 'table_id' => $table_id));
			}
		}
		
		function get($table = '', $table_id=0, $limit=7)
		{
			$wheretable_id = "";
			
			if($table_id != 0)
				$wheretable_id = " AND table_id = $table_id ";
			
			$query = $this->db->query("SELECT u.id, u.first_name, u.last_name, l.date, l.action, l.type, l.table_name, l.table_id FROM users u, log l WHERE l.table_name = '$table' $wheretable_id AND l.user_id = u.id order by l.id desc LIMIT ".$limit);
			
			return($query->result());			
		}
		
		function getAll()
		{
			$query = $this->db->query("SELECT u.id, u.first_name, u.last_name, l.date, l.action, l.table_name, l.table_id FROM users u, log l WHERE l.user_id = u.id order by l.id desc");
			
			return($query->result());			
		}
		
		function getAll_pagination($num, $offset) {
		
			if($offset == "") 
				$query = $this->db->query("SELECT u.id, u.first_name, u.last_name, l.date, l.action, l.table_name, l.table_id FROM users u, log l WHERE l.user_id = u.id order by l.id desc LIMIT $num");
			else 
				$query = $this->db->query("SELECT u.id, u.first_name, u.last_name, l.date, l.action, l.table_name, l.table_id FROM users u, log l WHERE l.user_id = u.id order by l.id desc LIMIT $num OFFSET $offset");
		
			return $query->result();
		}

	}
	
?>