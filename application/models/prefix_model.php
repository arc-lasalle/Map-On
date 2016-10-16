<?php
class Prefix_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
	}
	
	function getPrefixes($ontology_id) {
		$this->db->where('ontology_id', $ontology_id);
		$query = $this->db->get("prefix");
	
		return($query->result());	
	}
	
	function getPrefix($id)
	{
		$this->db->where('id', $id);

		$query = $this->db->get("prefix");
		
		return($query->result());	
	}
	
	function getByIRI($iri, $ontology_id)
	{
		$this->db->where('ontology_id', $ontology_id);
		$this->db->where('iri', $iri);

		$query = $this->db->get("prefix");
		if ($query->num_rows() > 0)
		{
			$row = $query->row(); 
			return $row->prefix;
		}
		
		return "";	
	}
	
	function getByPrefix($prefix, $ontology_id)
	{
		$this->db->where('ontology_id', $ontology_id);
		$this->db->where('prefix', $prefix);

		$query = $this->db->get("prefix");
		
		if ($query->num_rows() > 0)
		{
			$row = $query->row(); 
			return $row->iri;
		}
		
		return "";	
	}
	
	function getQName($URI, $ontology_id)
	{
		$this->db->where('ontology_id', $ontology_id);
		$results = $this->db->get("prefix")->result();
		
		foreach($results as $row) {
			if(strpos($URI, $row->iri) === 0) {
				return str_replace($row->iri, $row->prefix.":", $URI);
			}
		}
		
		return $URI;
	}
	
	function getURI($qname, $ontology_id)
	{
		$arr = explode(':', $qname);
		
		if(count($arr) == 2) {
			$iri = $this->getByPrefix($arr[0], $ontology_id);
								
			return str_replace($arr[0].":", $iri, $qname);
		}
		
		return $qname;
	}
	
	function add($prefix, $iri, $ontology_id)
	{
		$this->db->insert('prefix', array('prefix' => $prefix,'iri' => $iri,'ontology_id' => $ontology_id));
		
		return($this->db->insert_id());
	}

	function update($id, $prefix, $iri, $ontology_id)
	{
		$this->db->where('id', $id);
		$this->db->update('prefix', array('prefix' => $prefix,'iri' => $iri,'ontology_id' => $ontology_id));
	}

	function delete($id)
	{
		$this->db->where("id", $id);
		$this->db->delete("prefix");
	}
	
}
?>