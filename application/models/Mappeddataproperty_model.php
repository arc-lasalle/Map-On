<?php
class Mappeddataproperty_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
	}
	
	
	function getMappeddataproperties($mappedclass_id)
	{
		$this->team->db->where('mappedclass_id', $mappedclass_id);

		$query = $this->team->db->get("mappeddataproperty");
		
		return($query->result());	
	}

	function getMappeddataproperty($id)
	{
		$this->team->db->where('id', $id);

		$query = $this->team->db->get("mappeddataproperty");
		
		return($query->result());	
	}
	
	function add($dataproperty, $value, $type, $mappedclass_id )
	{
		$this->team->db->insert('mappeddataproperty', array('dataproperty' => $dataproperty,'value' => $value,'type' => $type,'mappedclass_id' => $mappedclass_id));
		$ret = $this->team->db->insert_id();

		return $ret;
	}

	function update($id, $dataproperty, $value, $type)
	{
		$this->team->db->where('id', $id);
		$this->team->db->update('mappeddataproperty', array('dataproperty' => $dataproperty, 'value' => $value, 'type' => $type));
	}

	function delete($id)
	{
		$this->team->db->where("id", $id);
		$this->team->db->delete("mappeddataproperty");
	}
			
	///////////////////////////////////////
}
	

	
	
?>