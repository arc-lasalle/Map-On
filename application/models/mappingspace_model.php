<?php
class Mappingspace_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}


	function getMappingspaces($datasource_id)
	{
		$this->team->db->where('datasource_id', $datasource_id);

		$query = $this->team->db->get("mappingspace");

		return ($query->result());
	}

	function getMappingspace($id)
	{
		$this->team->db->where('id', $id);

		$query = $this->team->db->get("mappingspace");

		return ($query->result());
	}

	function add($name, $user_id, $datasource_id)
	{
		$this->team->db->insert('mappingspace', array('name' => $name, 'date' => date("Y-m-d"), 'user_id' => $user_id, 'datasource_id' => $datasource_id));
		$ret = $this->team->db->insert_id();

		return ($ret);
	}

	function update($id, $name)
	{
		$this->team->db->where('id', $id);
		$this->team->db->update('mappingspace', array('name' => $name, 'date' => date("Y-m-d")));
	}

	function delete($id)
	{
		//first we delete the layout
		$this->team->db->where("mappingspace_id", $id);
		$this->team->db->delete("mappingspace_layout");

		$this->team->db->where("id", $id);
		$this->team->db->delete("mappingspace");
	}

	function updatePosition($mappingspace_id, $nodeid, $layoutX, $layoutY)
	{
		$this->team->db->from('mappingspace_layout');

		// Check if a record exists for this SKU
		$this->team->db->where('nodeid', $nodeid);
		$this->team->db->where('mappingspace_id', $mappingspace_id);
		if ($this->team->db->count_all_results() == 0) {
			//insert
			$this->team->db->insert('mappingspace_layout', array('nodeid' => $nodeid, 'layoutX' => $layoutX, 'layoutY' => $layoutY, 'mappingspace_id' => $mappingspace_id));

		} else {
			$this->team->db->where('nodeid', $nodeid);
			$this->team->db->where('mappingspace_id', $mappingspace_id);
			$this->team->db->update('mappingspace_layout', array('layoutX' => $layoutX, 'layoutY' => $layoutY));
		}
	}

	function deletePosition( $mappingspace_id, $nodeid ) {
		$this->team->db->where("mappingspace_id", $mappingspace_id);
		$this->team->db->where("nodeid", $nodeid);
		
		$this->team->db->delete("mappingspace_layout");
	}
	
	function getLayout($mappingspace_id, $as_key_array = false ) 
	{
		$this->team->db->where('mappingspace_id', $mappingspace_id);

		$query = $this->team->db->get("mappingspace_layout");
		
		$layout = $query->result();

		if ( $as_key_array ) {
			$ret = array();
			foreach( $layout as $row ) $ret[strtolower($row->nodeid)] = $row;
			return $ret;
		} else {
			return $layout;
		}
		
	}
	///////////////////////////////////////
}
	

	
	
?>