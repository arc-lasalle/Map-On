<?php
class Mappedclass_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
	}
	
	
	function getMappedclasses($mappingspace_id)
	{
		$this->db->where('mappingspace_id', $mappingspace_id);

		$query = $this->db->get("mappedclass");
		
		return($query->result());	
	}

	function getMappedclass($id)
	{
		$this->db->where('id', $id);

		$query = $this->db->get("mappedclass");
		
		return($query->result());	
	}
	
	function getMappedclassByClass($class, $mappingspace_id)
	{
		$this->db->where('class', $class);
		$this->db->where('mappingspace_id', $mappingspace_id);

		$query = $this->db->get("mappedclass");
		
		return($query->result());	
	}
	
	function getMappedclassByURI($uri, $mappingspace_id)
	{
		$this->db->where('uri', $uri);
		$this->db->where('mappingspace_id', $mappingspace_id);

		$query = $this->db->get("mappedclass");
		
		return($query->result());	
	}
	
	function add($class, $sql, $uri, $user_id, $mappingspace_id, $mappedtablecolumn = "")
	{
		$this->db->insert('mappedclass', array('class' => $class,'sql' => $sql,'uri' => $uri, 'mappedtablecolumn' => $mappedtablecolumn, 'date' => date("Y-m-d"),'user_id' => $user_id,'mappingspace_id' => $mappingspace_id));
		
		return($ret = $this->db->insert_id());
	}

	function update($id, $class, $sql, $uri, $mappedtablecolumn = "")
	{
		$this->db->where('id', $id);
		$this->db->update('mappedclass', array('class' => $class,'sql' => $sql,'uri' => $uri, 'mappedtablecolumn' => $mappedtablecolumn, 'date' => date("Y-m-d")));
	}

	function updateSQL( $mappedclass_id, $sql ) {
		$this->db->where('id', $mappedclass_id);
		$this->db->update('mappedclass', array('sql' => $sql) );
	}

	function delete($id)
	{
		//first we delete the layout
		$this->db->where("mappedclass_id", $id);
		$this->db->delete("mappedclass_layout");
		
		$this->db->where("id", $id);
		$this->db->delete("mappedclass");
	}
	
	////////////////////////////
	
			
	function updatePosition($mappedclass_id, $nodeid, $layoutX, $layoutY)
	{
		$this->db->from('mappedclass_layout');
    	
    	echo "updatePosition: " + $mappedclass_id + " v " + $nodeid;
	// Check if a record exists for this SKU
		$this->db->where('nodeid',$nodeid);
		$this->db->where('mappedclass_id',$mappedclass_id);
		if ($this->db->count_all_results() == 0) {
			//insert
			$this->db->insert('mappedclass_layout', array('nodeid' => $nodeid, 'layoutX' => $layoutX, 'layoutY' => $layoutY, 'mappedclass_id' => $mappedclass_id));

		} else {
			$this->db->where('nodeid', $nodeid);
			$this->db->where('mappedclass_id', $mappedclass_id);
			$this->db->update('mappedclass_layout', array('layoutX' => $layoutX, 'layoutY' => $layoutY));
		}
	}
	
	function getLayout($mappedclass_id) 
	{
		$this->db->where('mappedclass_id', $mappedclass_id);

		$query = $this->db->get("mappedclass_layout");
		
		return($query->result());	
	}		
	///////////////////////////////////////
	
	function insertTableson($mappedclass_id, $tableid)
	{
		$this->db->from('mappedclass_tableson');
    	
    	echo "updatePosition: " + $mappedclass_id + " v " + $tableid;
	// Check if a record exists for this SKU
		$this->db->where('tableid',$tableid);
		$this->db->where('mappedclass_id',$mappedclass_id);
		if ($this->db->count_all_results() == 0) {
			//insert
			$this->db->insert('mappedclass_tableson', array('tableid' => $tableid, 'mappedclass_id' => $mappedclass_id));
		}
	}
	
	function deleteTableson($mappedclass_id, $tableid)
	{
		$this->db->from('mappedclass_tableson');
    	
    	echo "updatePosition: " + $mappedclass_id + " v " + $tableid;
	// Check if a record exists for this SKU
		$this->db->where('tableid',$tableid);
		$this->db->where('mappedclass_id',$mappedclass_id);
		$this->db->delete('mappedclass_tableson');
	}
	
	function getTableson($mappedclass_id) 
	{
		$this->db->where('mappedclass_id', $mappedclass_id);

		$query = $this->db->get("mappedclass_tableson");
		
		return($query->result());	
	}	
}
	

	
	
?>