<?php
class Mappingspace_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
	}
	
	
	function getMappingspaces($datasource_id)
	{
		$this->db->where('datasource_id', $datasource_id);

		$query = $this->db->get("mappingspace");
		
		return($query->result());	
	}

	function getMappingspace($id)
	{
		$this->db->where('id', $id);

		$query = $this->db->get("mappingspace");
		
		return($query->result());	
	}
	
	function add($name, $user_id, $datasource_id)
	{
		$this->db->insert('mappingspace', array('name' => $name,'date' => date("Y-m-d"),'user_id' => $user_id,'datasource_id' => $datasource_id));
		$ret = $this->db->insert_id();
		
		return ($ret);
	}

	function update($id, $name)
	{
		$this->db->where('id', $id);
		$this->db->update('mappingspace', array('name' => $name,'date' => date("Y-m-d")));
	}

	function delete($id)
	{
		//first we delete the layout
		$this->db->where("mappingspace_id", $id);
		$this->db->delete("mappingspace_layout");
		
		$this->db->where("id", $id);
		$this->db->delete("mappingspace");
	}
			
	function updatePosition($mappingspace_id, $nodeid, $layoutX, $layoutY)
	{
		$this->db->from('mappingspace_layout');
    
	// Check if a record exists for this SKU
		$this->db->where('nodeid',$nodeid);
		$this->db->where('mappingspace_id',$mappingspace_id);
		if ($this->db->count_all_results() == 0) {
			//insert
			$this->db->insert('mappingspace_layout', array('nodeid' => $nodeid, 'layoutX' => $layoutX, 'layoutY' => $layoutY, 'mappingspace_id' => $mappingspace_id));

		} else {
			$this->db->where('nodeid', $nodeid);
			$this->db->where('mappingspace_id', $mappingspace_id);
			$this->db->update('mappingspace_layout', array('layoutX' => $layoutX, 'layoutY' => $layoutY));
		}
	}
	
	function getLayout($mappingspace_id) 
	{
		$this->db->where('mappingspace_id', $mappingspace_id);

		$query = $this->db->get("mappingspace_layout");
		
		return($query->result());	
	}
	///////////////////////////////////////
}
	

	
	
?>