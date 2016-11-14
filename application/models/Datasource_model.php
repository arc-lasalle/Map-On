<?php
class Datasource_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
	}
	

	
	function getDatasources()
	{
		$query = $this->team->db->get("datasource");
		
		return($query->result());	
	}
	
	function getDatasourcesAndOntologies()
	{
		$query = $this->team->db->query("SELECT d.id, d.name, d.type, d.sqlfile, d.stringconnection, d.xmlfile, d.basicuri, d.date, d.user_id, d.ontology_id, o.name as ontologyName FROM datasource as d, ontology as o WHERE o.id=d.ontology_id ORDER BY d.date ASC");
	
		return($query->result());	
	}
	
	
	function getDatasource($id)
	{
		$this->team->db->where('id', $id);

		$query = $this->team->db->get("datasource");
		
		return($query->result());	
	}
	
	function getTablesColumns($datasource_id)
	{
		 $query = $this->team->db->query("SELECT t.id as tableid, t.name as tablename, c.id as columnid, c.name as columnname FROM sourcetable as t, sourcecolumn as c WHERE t.id=c.sourcetable_id AND datasource_id=".$datasource_id);
	
		return($query->result());	
	}
	
	function getTables($datasource_id)
	{
		$this->team->db->where('datasource_id', $datasource_id);

		$query = $this->team->db->get("sourcetable");
		
		return($query->result());	
	}
		
	function getColumns($sourcetable_id)
	{
		$this->team->db->where('sourcetable_id', $sourcetable_id);

		$query = $this->team->db->get("sourcecolumn");
		
		return($query->result());	
	}
		
	function getTable($table_id)
	{
		$this->team->db->where('id', $table_id);

		$query = $this->team->db->get("sourcetable");
		
		return($query->result());	
	}
	
	function getColumn($column_id)
	{
		$this->team->db->where('id', $column_id);

		$query = $this->team->db->get("sourcecolumn");
		
		return($query->result());	
	}
	
	function getTableByName($tablename, $datasource_id)
	{
		$this->team->db->where('LOWER(name)', strtolower($tablename));
		$this->team->db->where('datasource_id', $datasource_id);

		$query = $this->team->db->get("sourcetable");
		
		return($query->result());	
	}
	
	function getColumnByName($columnname, $sourcetable_id)
	{
		$this->team->db->where('LOWER(name)', strtolower($columnname));

		$this->team->db->where('sourcetable_id', $sourcetable_id);

		$query = $this->team->db->get("sourcecolumn");
		
		return($query->result());	
	}
	
		
	function getPrimaryKeyColumn($sourcetable_id)
	{
		$this->team->db->where('primarykey', '1');

		$this->team->db->where('sourcetable_id', $sourcetable_id);

		$query = $this->team->db->get("sourcecolumn");
		
		return($query->result());	
	}
			
	function getForeignKeycolumns($sourcetable_id)
	{
		$this->team->db->where('foreignkey !=', '');
		$this->team->db->where('foreigntable !=', '');
		$this->team->db->where('sourcetable_id', $sourcetable_id);

		$query = $this->team->db->get("sourcecolumn");
		
		return($query->result());	
	}
				
	function getColumnByForeignKey($sourcetable_id, $foreignkey, $foreigntable)
	{
		$this->team->db->where('LOWER(foreignkey)', strtolower($foreignkey));
		$this->team->db->where('LOWER(foreigntable)', strtolower($foreigntable));
		$this->team->db->where('sourcetable_id', $sourcetable_id);

		$query = $this->team->db->get("sourcecolumn");
		
		return($query->result());	
	}
	
	function getColumnsByForeignKey($foreignkey, $foreigntable, $datasource_id)
	{
	/*
		$this->team->db->where('LOWER(foreignkey)', strtolower($foreignkey));
		$this->team->db->where('LOWER(foreigntable)', strtolower($foreigntable));
		$this->team->db->where('datasource_id', $datasource_id);
		$this->team->db->where('sourcetable_id', $sourcetable_id);
		
		$query = $this->team->db->get("sourcecolumn");
		$query = $this->team->db->get("sourcetable");*/
		
		$query = "SELECT distinct st.id as tableid, sc.name AS columnname, st.name AS tablename FROM sourcecolumn as sc, sourcetable as st WHERE st.id=sc.sourcetable_id AND st.datasource_id=".$datasource_id. " AND LOWER(foreignkey) = '" .strtolower($foreignkey). "' AND LOWER(foreigntable) = '" .strtolower($foreigntable)."'";
		
		$query = $this->team->db->query($query);
				

		
		return($query->result());	
	}
	function getColumnsByForeignTable($tablename, $foreigntable, $datasource_id)
	{
	/*
		$this->team->db->where('LOWER(foreignkey)', strtolower($foreignkey));
		$this->team->db->where('LOWER(foreigntable)', strtolower($foreigntable));
		$this->team->db->where('datasource_id', $datasource_id);
		$this->team->db->where('sourcetable_id', $sourcetable_id);
		
		$query = $this->team->db->get("sourcecolumn");
		$query = $this->team->db->get("sourcetable");*/
		
		$query = "SELECT distinct st.id as tableid, sc.foreignkey AS foreignkey, sc.name AS columnname, st.name AS tablename FROM sourcecolumn as sc, sourcetable as st WHERE st.id=sc.sourcetable_id AND st.datasource_id=".$datasource_id. " AND LOWER(st.name) ='".strtolower($tablename)."' AND LOWER(foreigntable) = '" .strtolower($foreigntable)."'";
		
		$query = $this->team->db->query($query);
				

		
		return($query->result());	
	}
	
	function getOntology($datasource_id)
	{
		$query = $this->team->db->query("SELECT d.ontology_id FROM datasource as d, ontology as o WHERE o.id=d.ontology_id AND d.id=".$datasource_id);
		$ret = $query->result();
		return($ret[0]->ontology_id);
	}
		
	function getBasicUri($datasource_id)
	{
		$query = $this->team->db->query("SELECT basicuri FROM datasource WHERE id=".$datasource_id);
		$ret = $query->result();
		return($ret[0]->basicuri);
	}
	
	function add($name, $sqlfile, $stringconnection, $xmlfile, $basicURI, $user_id, $ontology_id, $db_type = "mysql" )
	{
		$this->team->db->insert('datasource', array('name' => $name,'sqlfile' => $sqlfile,'stringconnection' => $stringconnection,'xmlfile' => $xmlfile,'basicuri' => $basicURI, 'date' => date("Y-m-d"),'user_id' => $user_id, 'ontology_id' => $ontology_id, 'type' => $db_type));
		return($this->team->db->insert_id());
	}
/*
	function update($id, $name, $sqlfile, $stringconnection, $xmlfile, $basicURI, $ontology_id)
	{
		$this->team->db->where('id', $id);
		$this->team->db->update('datasource', array('name' => $name, 'sqlfile' => $sqlfile,'stringconnection' => $stringconnection,'xmlfile' => $xmlfile,'basicuri' => $basicURI, 'date' => date("Y-m-d"), 'ontology_id' => $ontology_id));
	}*/
	
	function update($id, $name, $basicURI, $ontology_id)
	{
		$this->team->db->where('id', $id);
		$this->team->db->update('datasource', array('name' => $name, 'basicuri' => $basicURI, 'date' => date("Y-m-d"), 'ontology_id' => $ontology_id));
	}

	function setDbType($datasource_id, $database_type)
	{
		$this->team->db->where( 'id', $datasource_id );
		$this->team->db->update( 'datasource', array('type' => $database_type) );
	}
	
	function updatePosition($datasource_id, $name, $layoutX, $layoutY)
	{
		$this->team->db->where('name', $name);
		$this->team->db->where('datasource_id', $datasource_id);
		$this->team->db->update('sourcetable', array('layoutX' => $layoutX, 'layoutY' => $layoutY));
	}

	function delete($id)
	{
		$this->team->db->where("id", $id);
		$this->team->db->delete("datasource");
	}
	
	
	
	function addTable($datasource_id, $name, $description = "")
	{
		$data = array(
			'name' => $name,
			'description' => $description,
			'datasource_id' => $datasource_id,
		);

	   $this->team->db->insert('sourcetable', $data);
	   
	   $ret = $this->team->db->insert_id();
	   
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

	   $this->team->db->insert('sourcecolumn', $data);
	   
	   $ret = $this->team->db->insert_id();
	   
	   return ($ret);
	}

	function updateColumnPrimaryKey($columnName, $primarykey, $sourcetable_id) 
	{
		$data = array(
				'primarykey' => $primarykey);
		
		$this->team->db->where("name",$columnName);
		$this->team->db->where("sourcetable_id",$sourcetable_id);
		
		$this->team->db->update('sourcecolumn', $data);
	}
	
	
	function updateColumnForeignKey($columnName, $foreignkey, $foreigntable, $sourcetable_id) 
	{
		$data = array(
				'foreignkey' => $foreignkey,
				'foreigntable' => $foreigntable,
				);
		
		$this->team->db->where("name",$columnName);
		$this->team->db->where("sourcetable_id",$sourcetable_id);

		$this->team->db->update('sourcecolumn', $data);
	}

	function setDatasourceLayout( $datasource_id, $tableid, $insert, $layoutX, $layoutY )
	{
		// Delete if exist.
		$this->team->db->where("datasource_id", $datasource_id);
		$this->team->db->where("tableid", $tableid);
		$this->team->db->delete("datasource_layout");

		if ( !$insert ) return;

		$this->team->db->insert('datasource_layout', array('tableid' => $tableid, 'layoutX' => $layoutX, 'layoutY' => $layoutY, 'datasource_id' => $datasource_id));

	}

	function getDatasourceLayout( $datasource_id )
	{
		$this->team->db->where('datasource_id', $datasource_id);

		$tables = $this->team->db->get("datasource_layout")->result();

        foreach( $tables as $i => $table ) {

            $dbtable = $this->getTableByName( $table->tableid, $datasource_id );
            if ( empty($dbtable[0]) ) continue;

            $table->columns = $this->getColumns( $dbtable[0]->id );
            $table->name = $table->tableid;
        }

		return($tables);
	}

	
	function getTableTree( $datasource_id, $layout = [] ) {
		$tables = $this->getTables( $datasource_id );

		foreach( $tables as $i => $table ) {
			$table_name = strtolower( $table->name );

			if ( isset($layout[ $table_name ]) ) {
				$table->layoutX = $layout[ $table_name ]->layoutX;
				$table->layoutY = $layout[ $table_name ]->layoutY;
			} else {
				$table->layoutX = 0; //Por defecto es la del datasource, no del mappingspace
				$table->layoutY = 0;
			}

			$table->columns = $this->getColumns( $table->id );

			foreach( $table->columns as $k => $col ) {
				$col_full_name = strtolower($table->name."_".$col->name);

				if ( isset($layout[ $col_full_name ]) ) {
					$col->layoutX = $layout[ $col_full_name ]->layoutX;
					$col->layoutY = $layout[ $col_full_name ]->layoutY;
				}
			}
		}
		
		return $tables;
	}

	function getColumnType( $table_name, $column_name, $datasource_id) {
		$types_int = ["int", "number"];
		$types_decimal = ["decimal"];
		$types_string = ["varchar", "varchar2"];
		
		$row = $this->datasource->getTableByName($table_name, $datasource_id);
		if ( count($row) <= 0 ) return "";
		
		$col = $this->datasource->getColumnByName($column_name, $row[0]->id);
		if ( count($col) <= 0 ) return "";

		$db_type = $col[0]->type;
		
		if ( in_array( $db_type, $types_int ) ) {
			return "xsd:integer";
		} else if ( in_array( $db_type, $types_decimal ) ) {
			return "xsd:decimal";
		} else if ( in_array( $db_type, $types_string ) ) {
			return "xsd:string";
		} else {
			return "xsd:string";
		}

		
	}



}
	

	
	
?>