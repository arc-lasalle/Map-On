<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mappings extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		$this->load->model("Datasources_model", "datasources");
		$this->load->model("Mappings_model", "mappings");
		$this->load->model("Table_datatypes_model", "datatypes");
		$this->load->model("Energymodel_model", "energymodel");
		$this->load->model("Workspaces_model", "workspaces_model");
		$this->load->model("Prefixes_model", "prefixes_model");
		$this->load->model("Messages_model", "messages");		
		$this->load->model("Log_model", "log");
		$this->load->model("Table_mappings_constrained", "constrains");
	}
		
	public function index()
	{
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}
		
		$this->load->view('header');
		$this->load->view('footer');
	}

	function view($id = 0)
	{
	}
	
	function delete($datasourceid, $id)
	{
		$ds = $this->mappings->getTable($id)->result();
		
		$prefix_uri = $this->prefixes_model->get_prefix_and_uri($ds[0]->name);
		
		$this->log->write("delete a table on <a href='index.php/datasources/view/".$datasourceid."'>".$prefix_uri["prefix"].":".$prefix_uri["class"]."</a>", "mappings", $id);
		
		$this->mappings->delete($id);
		
		redirect('/datasources/view/'.$datasourceid, 'refresh');
	}
	
	function edit($datasourceid = 0, $idmapping = 0)
	{
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}
		
		$vars = $this->mappings->getTable($idmapping)->row_array();
		
		//$vars["ds"] = $this->datasources->getDataSource($datasourceid)->row_array();
		$ds = $this->datasources->getDataSource($datasourceid)->row_array();
		
		$store_Mysql = $this->workspaces_model->connect_workspace("EnergyModel");		
		
		$vars["datasourceid"] = $datasourceid;
		$vars["classuris"] = $this->energymodel->getClasses ($store_Mysql);
		$vars["emdatatypes"] = $this->energymodel->getDatatypes ($store_Mysql);
		$vars["table_datatypes"] = $this->datatypes->getDatatypes ($idmapping);
		$vars['log'] = $this->log->get('mappings', $idmapping);
		$vars['msg'] = $this->messages->get("mappings", $idmapping);
		$vars["xsdtypes"] = $this->energymodel->getXSDDatatypes ($store_Mysql);
		$vars["unitmeasureclasses"] = $this->energymodel->getUnitmeasureclasses ($store_Mysql);
		$vars['constrains'] = $this->constrains->getConstrains($idmapping);
		$vars['constrainclassnames'] = $this->energymodel->getSubClasses ($store_Mysql, $vars['classname']);
		
		
		if($vars["databasecolumn"] != "" ) {
			$dbcolumn = explode (".", $vars["databasecolumn"]);
			$vars["databasecolumns"] = $this->datasources->getDatabasecolumns ($datasourceid, $dbcolumn[0]);
		} else {
			$vars["databasecolumns"] = array();
		}

		//if is the first call save workspace
        // $selected_workspace = $this->input->post('selected_workspace');
        
        //call function to create graph
        // $selected_class = $this->input->post('selected_class');
        
		if(empty ($vars['classname']))
			$vars['focused_class'] = "http://www.owl-ontologies.com/SUMO155.owl#Entity";
		else
			$vars['focused_class'] = $vars['classname'];
		
		
        $vars['color'] = false;
        $vars['subclassof'] = true;
        $vars['workspace'] = "EnergyModel";
		
		
		$this->load->view('header');
		
		$this->load->view('datasource_header', $ds);
		$this->load->view('mappings_edit', $vars);
		$this->load->view('footer');
	}
	
	function edit_post()
	{
		////////////////////////////////////////////////////
		// get datasource descriptive fields
		$datasourceid = $this->input->post('datasource_id');
		$id = $this->input->post('mapping_id');
		$name = $this->input->post('mapping_name');
		$selected = $this->input->post('select_selected');
		$option = $this->input->post('option');
		
		if($option == "option1") {
			$qname = $this->input->post('input_classname');
			$classname = $this->prefixes_model->get_uri($qname);
			$this->mappings->update($id, $classname, $selected == "Yes" ? 1 : 0, 1);
		} else  {
			$qname = $this->input->post('input_classuri');
			$classuri = $this->prefixes_model->get_uri($qname);
			$this->mappings->update($id, $classuri, $selected == "Yes" ? 1 : 0, 0);
		}
		
		$this->log->write("has edited <a href='index.php/mappings/edit/".$datasourceid."/".$id."'>".$name."</a> mapping", "mappings", $id);

		redirect(base_url().'index.php/mappings/edit/'.$datasourceid."/".$id);  
	}
	
	function addsuperclass($datasourceid = 0, $idmapping = 0, $name= '')
	{
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}
		
		$vars["datasourceid"] = $datasourceid;
		$vars["id"] = $idmapping;
		$vars["name"] = $name;
		
		$store_Mysql = $this->workspaces_model->connect_workspace("EnergyModel");		
		
		$vars["classuris"] = $this->energymodel->getClasses ($store_Mysql);
	
		$this->load->view('header');
		$this->load->view('mappings_addsuperclass', $vars);
		$this->load->view('footer');
	}
	
	function addsuperclass_post()
	{
		////////////////////////////////////////////////////
		// get datasource descriptive fields
		$datasourceid = $this->input->post('datasource_id');
		$id = $this->input->post('mapping_id');
		$name = $this->input->post('mapping_name');
		$qname = $this->input->post('input_superclassuri');
		
		$classuri = $this->prefixes_model->get_uri($qname);
		$this->mappings->addsuperclass($id, $classuri);
		$this->datasources->updateDate($datasourceid);
		
		$this->log->write("has added ".$qname." supper class to <a href='index.php/mappings/edit/".$datasourceid."/".$id."'>".$name."</a> mapping", "mappings", $id);

		$this->edit($datasourceid, $id);
	}

	function addconstrain_post()
	{
		////////////////////////////////////////////////////
		// get datasource descriptive fields
		$datasourceid = $this->input->post('datasource_id');
		$id = $this->input->post('mapping_id');
		$name = $this->input->post('mapping_name');
		$constrain = $this->input->post('input_constrain');
		$constrainclassname = $this->input->post('input_constrainclassname');
		
		$classuri = $this->prefixes_model->get_uri($constrainclassname);
		$this->constrains->add($constrain, $classuri, $id);
		
		
		
		$this->edit($datasourceid, $id);
	}
	
	
		
	function comment_post()
	{
		$id = $this->input->post('mapping_id');
		$datasourceid = $this->input->post('datasource_id');
		$name = $this->input->post('mapping_name');
		$message = $this->input->post('input_message');
		
		$this->messages->write($message, "mappings", $id);
		$this->log->write("on <a href='index.php/mappings/edit/".$datasourceid."/".$id."'>".$name."</a>: <span style='font-style:italic'>".substr($message, 0, 30)."...</span>", "mappings", $id);
		
		$this->edit($datasourceid, $id);
	}
	
	function delete_message($datasourceid, $id=0, $messageid = 0)
	{
		$this->messages->delete($messageid);
		
		$ds = $this->mappings->getTable($id)->result();
		
		$prefix_uri = $this->prefixes_model->get_prefix_and_uri($ds[0]->name);
		
		$this->log->write("delete a message on <a href='index.php/mappings/edit/".$datasourceid."/".$id."'>".$prefix_uri["prefix"].":".$prefix_uri["class"]."</a>", "mappings", $id);

		
		redirect('/mappings/edit/'.$datasourceid."/".$id, 'refresh');
		//$this->edit($datasourceid, $id);
		
	}
	
	function delete_superclass($relationid, $datasourceid, $id)
	{
		$ds = $this->mappings->getTable($id)->result();
		$sc = $this->mappings->getSuperClass($relationid)->result();
		
		$prefix_uri = $this->prefixes_model->get_prefix_and_uri($ds[0]->name);
		$prefix_uri2 = $this->prefixes_model->get_prefix_and_uri($sc[0]->emclassuri);
	
		$this->mappings->deleteSuperClass($relationid);
		
		$this->log->write("delete ".$prefix_uri2["prefix"].":".$prefix_uri2["class"]." supperclass on <a href='index.php/mappings/edit/".$datasourceid."/".$id."'>".$prefix_uri["prefix"].":".$prefix_uri["class"]."</a>", "mappings", $id);

		$this->edit($datasourceid, $id);
	}
		
	function delete_constrain($constrainid, $datasourceid, $id)
	{
//		$ds = $this->mappings->getTable($id)->result();
		
		
//		$prefix_uri = $this->prefixes_model->get_prefix_and_uri($ds[0]->name);
//		$prefix_uri2 = $this->prefixes_model->get_prefix_and_uri($sc[0]->emclassuri);
	
		$this->constrains->delete($constrainid);
		
		//$this->log->write("delete ".$prefix_uri2["prefix"].":".$prefix_uri2["class"]." supperclass on <a href='index.php/mappings/edit/".$datasourceid."/".$id."'>".$prefix_uri["prefix"].":".$prefix_uri["class"]."</a>", "mappings", $id);

		$this->edit($datasourceid, $id);
	}
	
	function modifydatatype()
	{
		////////////////////////////////////////////////////
		// get datasource descriptive fields
		$datasourceid = $this->input->post('datasource_id');
		$id = $this->input->post('mapping_id');
		$name = $this->input->post('mapping_name');
		$xsdtype = $this->input->post('input_xsddatatype');
		
		$tableid = $this->input->post('modify_table_id');
				
		$qname = $this->input->post('hidden_search_inputtext_EMDatatypes');
		
		//If qname is false it means that the user entered a new vale.
		if($qname == "") {
			$qname = $this->energymodel->getGlobalOntologyPrefix().":".$this->input->post('input_datatypename');
		}
		
		$classuri = $this->prefixes_model->get_uri($qname);
		
		$this->datatypes->update($tableid, $classuri, $xsdtype);
		$this->datasources->updateDate($datasourceid);
		
		$this->log->write("has modified data type ".$qname." of <a href='index.php/mappings/edit/".$datasourceid."/".$id."'>".$name."</a> mapping", "mappings", $id);

		$this->edit($datasourceid, $id);
	}
	
	function modifymappingoptions()
	{
		////////////////////////////////////////////////////
		// get datasource descriptive fields
		$datasourceid = $this->input->post('datasource_id');
		$id = $this->input->post('mapping_id');
		$name = $this->input->post('mapping_name');

		$databasecolumn = $this->input->post('input_databasecolumn');
		$inferred = $this->input->post('input_inferred');
		$unitmeasureclass = $this->input->post('input_unitmeasureclass');

		$this->mappings->updateDatabasecolumn($id, $databasecolumn);
		$this->mappings->updateInferredSuperclasses($id, $inferred == "Yes" ? 1 : 0);
		$this->mappings->updateUnitemeasureclass($id, $unitmeasureclass);
		
		
		$this->datasources->updateDate($datasourceid);
		
		$this->log->write("has update the mapping options of the <a href='index.php/mappings/edit/".$datasourceid."/".$id."'>".$name."</a> mapping", "mappings", $id);

		$this->edit($datasourceid, $id);
	}
	
	function suggestemdatatype()
	{
        $substring = $this->input->post('string');
		
		//load all class of the first workspace
        $this->load->model('workspaces_model');
        $store_Mysql = $this->workspaces_model->connect_workspace("EnergyModel"); 

        //Get all classes
        $q = 'SELECT DISTINCT ?datatype ?comment WHERE {
                 ?datatype <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#DatatypeProperty>. 
				 OPTIONAL { ?datatype  <http://www.w3.org/2000/01/rdf-schema#comment>  ?comment. }
            }';
        $classes = $store_Mysql->query($q, 'rows');
        
        $this->load->model('prefixes_model');

        //put prefixes 
        for($i=0; $i < count($classes); $i++){
            $prefix_uri = $this->prefixes_model->get_prefix_and_uri($classes[$i]['datatype']);
            
            if($prefix_uri != null){
                
				$pos1 = stripos($prefix_uri['class'], $substring);
				
				
				if($pos1 !== false) {
				   $strPrintValue = str_replace($substring, "<strong>".$substring."</strong>", $prefix_uri['class']);
				   $strComment = isset ($classes[$i]['comment']) ? ": ".$classes[$i]['comment']: "" ;
                   echo '<span style="font-size:11px; color: #4F6228; cursor: pointer; cursor: hand;" onclick="add_search_box_EMDatatype(\''.$prefix_uri['class'].'\',\''.$prefix_uri['prefix'].":".$prefix_uri['class'].'\')">'.$strPrintValue.'</span>'.$strComment.'<br /><br />';
                } else {
					if(isset ($classes[$i]['comment']) ){
						$pos2 = stripos($classes[$i]['comment'], $substring);
						
						if($pos2 !== false) {
						   $strPrintValue = $prefix_uri['class'];
						   $strComment = ": ".str_replace($substring, "<strong>".$substring."</strong>", $classes[$i]['comment']);
						   echo '<span style="font-size:11px; color: #4F6228; cursor: pointer; cursor: hand;" onclick="add_search_box_EMDatatype(\''.$prefix_uri['class'].'\',\''.$prefix_uri['prefix'].":".$prefix_uri['class'].'\')">'.$strPrintValue.'</span>'.$strComment.'<br /><br />';
						}
					}
				}
            }
        }
	}
	
}
