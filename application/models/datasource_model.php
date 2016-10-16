<?php
class Datasource_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
	}
	

	
	function getDatasources()
	{
		$query = $this->db->get("datasource");
		
		return($query->result());	
	}
	
	function getDatasourcesAndOntologies()
	{
		$query = $this->db->query("SELECT d.id, d.name, d.sqlfile, d.stringconnection, d.xmlfile, d.basicuri, d.date, d.user_id, d.ontology_id, o.name as ontologyName FROM datasource as d, ontology as o WHERE o.id=d.ontology_id ORDER BY d.date ASC");
	
		return($query->result());	
	}
	
	
	function getDatasource($id)
	{
		$this->db->where('id', $id);

		$query = $this->db->get("datasource");
		
		return($query->result());	
	}
	
	function getTablesColumns($datasource_id)
	{
		 $query = $this->db->query("SELECT t.id as tableid, t.name as tablename, c.id as columnid, c.name as columnname FROM sourcetable as t, sourcecolumn as c WHERE t.id=c.sourcetable_id AND datasource_id=".$datasource_id);
	
		return($query->result());	
	}
	
	function getTables($datasource_id)
	{
		$this->db->where('datasource_id', $datasource_id);

		$query = $this->db->get("sourcetable");
		
		return($query->result());	
	}
		
	function getColumns($sourcetable_id)
	{
		$this->db->where('sourcetable_id', $sourcetable_id);

		$query = $this->db->get("sourcecolumn");
		
		return($query->result());	
	}
		
	function getTable($table_id)
	{
		$this->db->where('id', $table_id);

		$query = $this->db->get("sourcetable");
		
		return($query->result());	
	}
	
	function getColumn($column_id)
	{
		$this->db->where('id', $column_id);

		$query = $this->db->get("sourcecolumn");
		
		return($query->result());	
	}
	
	function getTableByName($tablename, $datasource_id)
	{
		$this->db->where('LOWER(name)', strtolower($tablename));
		$this->db->where('datasource_id', $datasource_id);

		$query = $this->db->get("sourcetable");
		
		return($query->result());	
	}
	
	function getColumnByName($columnname, $sourcetable_id)
	{
		$this->db->where('name', $columnname);

		$this->db->where('sourcetable_id', $sourcetable_id);

		$query = $this->db->get("sourcecolumn");
		
		return($query->result());	
	}
	
		
	function getPrimaryKeyColumn($sourcetable_id)
	{
		$this->db->where('primarykey', '1');

		$this->db->where('sourcetable_id', $sourcetable_id);

		$query = $this->db->get("sourcecolumn");
		
		return($query->result());	
	}
			
	function getForeignKeycolumns($sourcetable_id)
	{
		$this->db->where('foreignkey !=', '');
		$this->db->where('foreigntable !=', '');
		$this->db->where('sourcetable_id', $sourcetable_id);

		$query = $this->db->get("sourcecolumn");
		
		return($query->result());	
	}
				
	function getColumnByForeignKey($sourcetable_id, $foreignkey, $foreigntable)
	{
		$this->db->where('LOWER(foreignkey)', strtolower($foreignkey));
		$this->db->where('LOWER(foreigntable)', strtolower($foreigntable));
		$this->db->where('sourcetable_id', $sourcetable_id);

		$query = $this->db->get("sourcecolumn");
		
		return($query->result());	
	}
	
	function getColumnsByForeignKey($foreignkey, $foreigntable, $datasource_id)
	{
	/*
		$this->db->where('LOWER(foreignkey)', strtolower($foreignkey));
		$this->db->where('LOWER(foreigntable)', strtolower($foreigntable));
		$this->db->where('datasource_id', $datasource_id);
		$this->db->where('sourcetable_id', $sourcetable_id);
		
		$query = $this->db->get("sourcecolumn");
		$query = $this->db->get("sourcetable");*/
		
		$query = "SELECT distinct st.id as tableid, sc.name AS columnname, st.name AS tablename FROM sourcecolumn as sc, sourcetable as st WHERE st.id=sc.sourcetable_id AND st.datasource_id=".$datasource_id. " AND LOWER(foreignkey) = '" .strtolower($foreignkey). "' AND LOWER(foreigntable) = '" .strtolower($foreigntable)."'";
		
		$query = $this->db->query($query);
				

		
		return($query->result());	
	}
	function getColumnsByForeignTable($tablename, $foreigntable, $datasource_id)
	{
	/*
		$this->db->where('LOWER(foreignkey)', strtolower($foreignkey));
		$this->db->where('LOWER(foreigntable)', strtolower($foreigntable));
		$this->db->where('datasource_id', $datasource_id);
		$this->db->where('sourcetable_id', $sourcetable_id);
		
		$query = $this->db->get("sourcecolumn");
		$query = $this->db->get("sourcetable");*/
		
		$query = "SELECT distinct st.id as tableid, sc.foreignkey AS foreignkey, sc.name AS columnname, st.name AS tablename FROM sourcecolumn as sc, sourcetable as st WHERE st.id=sc.sourcetable_id AND st.datasource_id=".$datasource_id. " AND LOWER(st.name) ='".strtolower($tablename)."' AND LOWER(foreigntable) = '" .strtolower($foreigntable)."'";
		
		$query = $this->db->query($query);
				

		
		return($query->result());	
	}
	
	function getOntology($datasource_id)
	{
		$query = $this->db->query("SELECT d.ontology_id FROM datasource as d, ontology as o WHERE o.id=d.ontology_id AND d.id=".$datasource_id);
		$ret = $query->result();
		return($ret[0]->ontology_id);
	}
		
	function getBasicUri($datasource_id)
	{
		$query = $this->db->query("SELECT basicuri FROM datasource WHERE id=".$datasource_id);
		$ret = $query->result();
		return($ret[0]->basicuri);
	}
	
	function add($name, $sqlfile, $stringconnection, $xmlfile, $basicURI, $user_id, $ontology_id)
	{
		$this->db->insert('datasource', array('name' => $name,'sqlfile' => $sqlfile,'stringconnection' => $stringconnection,'xmlfile' => $xmlfile,'basicuri' => $basicURI, 'date' => date("Y-m-d"),'user_id' => $user_id, 'ontology_id' => $ontology_id));
		return($this->db->insert_id());
	}
/*
	function update($id, $name, $sqlfile, $stringconnection, $xmlfile, $basicURI, $ontology_id)
	{
		$this->db->where('id', $id);
		$this->db->update('datasource', array('name' => $name, 'sqlfile' => $sqlfile,'stringconnection' => $stringconnection,'xmlfile' => $xmlfile,'basicuri' => $basicURI, 'date' => date("Y-m-d"), 'ontology_id' => $ontology_id));
	}*/
	
	function update($id, $name, $basicURI, $ontology_id)
	{
		$this->db->where('id', $id);
		$this->db->update('datasource', array('name' => $name, 'basicuri' => $basicURI, 'date' => date("Y-m-d"), 'ontology_id' => $ontology_id));
	}
	
	function updatePosition($datasource_id, $name, $layoutX, $layoutY)
	{
		$this->db->where('name', $name);
		$this->db->where('datasource_id', $datasource_id);
		$this->db->update('sourcetable', array('layoutX' => $layoutX, 'layoutY' => $layoutY));
	}

	function delete($id)
	{
		$this->db->where("id", $id);
		$this->db->delete("datasource");
	}
	
	
	
	function addTable($datasource_id, $name, $description = "")
	{
		$data = array(
			'name' => $name,
			'description' => $description,
			'datasource_id' => $datasource_id,
		);

	   $this->db->insert('sourcetable', $data);
	   
	   $ret = $this->db->insert_id();
	   
	   return ($ret);
	}

	function addColumn($sourcetable_id, $name, $type, $description = "", $primarykey=false,$foreignkey="", $foreigntable="")
	{
		$data = array(
			'name' => $name,
			'type' => $type,
			'description' => $description,
			'primarykey' => $primarykey,
			'foreignkey' => $foreignkey,
			'foreigntable' => $foreigntable,
			'sourcetable_id' => $sourcetable_id,
		);

	   $this->db->insert('sourcecolumn', $data);
	   
	   $ret = $this->db->insert_id();
	   
	   return ($ret);
	}

	function updateColumnPrimaryKey($columnName, $primarykey, $sourcetable_id) 
	{
		$data = array(
				'primarykey' => $primarykey);
		
		$this->db->where("name",$columnName);
		$this->db->where("sourcetable_id",$sourcetable_id);
		
		$this->db->update('sourcecolumn', $data);
	}
	
	
	function updateColumnForeignKey($columnName, $foreignkey, $foreigntable, $sourcetable_id) 
	{
		$data = array(
				'foreignkey' => $foreignkey,
				'foreigntable' => $foreigntable,
				);
		
		$this->db->where("name",$columnName);
		$this->db->where("sourcetable_id",$sourcetable_id);

		$this->db->update('sourcecolumn', $data);
	}

	function setDbgraphLayout( $datasource_id, $tableid, $insert, $layoutX, $layoutY )
	{
		// Delete if exist.
		$this->db->where("datasource_id", $datasource_id);
		$this->db->where("tableid", $tableid);
		$this->db->delete("datasource_layout");

		if ( !$insert ) return;

		$this->db->insert('datasource_layout', array('tableid' => $tableid, 'layoutX' => $layoutX, 'layoutY' => $layoutY, 'datasource_id' => $datasource_id));

	}

	function getDatasourceLayout( $datasource_id )
	{
		$this->db->where('datasource_id', $datasource_id);

		$query = $this->db->get("datasource_layout");

		return($query->result());
	}

	///////////////////////////////////////
}
	

	
	
?>