<?php
class Mappeddataproperty_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
	}
	
	
	function getMappeddataproperties($mappedclass_id)
	{
		$this->db->where('mappedclass_id', $mappedclass_id);

		$query = $this->db->get("mappeddataproperty");
		
		return($query->result());	
	}

	function getMappeddataproperty($id)
	{
		$this->db->where('id', $id);

		$query = $this->db->get("mappeddataproperty");
		
		return($query->result());	
	}
	
	function add($dataproperty, $value, $type, $mappedclass_id )
	{
		$this->db->insert('mappeddataproperty', array('dataproperty' => $dataproperty,'value' => $value,'type' => $type,'mappedclass_id' => $mappedclass_id));
		$ret = $this->db->insert_id();

		return $ret;
	}

	function update($id, $dataproperty, $value, $type)
	{
		$this->db->where('id', $id);
		$this->db->update('mappeddataproperty', array('dataproperty' => $dataproperty, 'value' => $value, 'type' => $type));
	}

	function delete($id)
	{
		$this->db->where("id", $id);
		$this->db->delete("mappeddataproperty");
	}
			
	///////////////////////////////////////
}
	

	
	
?>