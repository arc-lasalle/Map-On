<?php
class Mappedobjectproperty_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
	}
		
	function getMappedobjectproperties($mappedclassdomain_id)
	{
		$query = $this->team->db->query("SELECT op.id AS id, op.objectproperty AS objectproperty, op.uri AS uri, c.class AS target, c.id as targetId FROM mappedobjectproperty AS op, mappedclass AS c WHERE mappedclassdomain_id = ".$mappedclassdomain_id." AND c.id = op.mappedclassrange_id");
		
		return($query->result());	
	}

	function getMappedobjectproperty($id)
	{
		$this->team->db->where('id', $id);

		$query = $this->team->db->get("mappedobjectproperty");
		
		return($query->result());	
	}

	function getMappedobjectpropertiesbyRange($mappedclassrange_id)
	{
		$query = $this->team->db->query("SELECT op.id AS id, op.objectproperty AS objectproperty, op.uri AS uri, c.class AS target, c.id as domainId FROM mappedobjectproperty AS op, mappedclass AS c WHERE mappedclassrange_id = ".$mappedclassrange_id." AND c.id = op.mappedclassdomain_id");
		
		return($query->result());	
	}
	
	function add($objectproperty, $uri, $mappedclassdomain_id, $mappedclassrange_id)
	{
		$this->team->db->insert('mappedobjectproperty', array('objectproperty' => $objectproperty,'uri' => $uri,'mappedclassdomain_id' => $mappedclassdomain_id,'mappedclassrange_id' => $mappedclassrange_id));
		$ret = $this->team->db->insert_id();
		
		return $ret;
	}

	function update($id, $objectproperty, $uri, $mappedclassdomain_id, $mappedclassrange_id)
	{
		$this->team->db->where('id', $id);
		$this->team->db->update('mappedobjectproperty', array('objectproperty' => $objectproperty, 'uri' => $uri, 'mappedclassdomain_id' => $mappedclassdomain_id,'mappedclassrange_id' => $mappedclassrange_id));
	}

	function delete($id)
	{
		$this->team->db->where("id", $id);
		$this->team->db->delete("mappedobjectproperty");
	}
			
	///////////////////////////////////////
}

?>