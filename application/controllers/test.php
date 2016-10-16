<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Test extends CI_Controller {

	function __construct()
	{
		parent::__construct();	

	}
		
	public function index2()
	{
		echo "This script has been used to fill with random values the North Harbour repository. <br>";
		echo "Particularly, the supply technologies. <br>";
		echo "test:...<br>";
		
		$config['hostname'] = "localhost";
		$config['username'] = 'root';
		$config['password'] = '';
		$config['database'] = "northharbourrepository";
		$config['dbdriver'] = "mysql";
		$config['dbprefix'] = "";
		$config['pconnect'] = TRUE;
		$config['db_debug'] = TRUE;
		$config['cache_on'] = FALSE;
		$config['cachedir'] = "";
		$config['char_set'] = "utf8";
		$config['dbcollat'] = "utf8_general_ci";

		$DB1 = $this->load->database($config, TRUE);
		
		$ret = $DB1->query('SELECT * FROM supplytechnologies');
		//echo $DB1->conn_id.'<br/>'.
		//var_dump ($DB1);	
		var_dump ($ret);	
		
		$DB1->query('DELETE FROM emissionfactors4technologies WHERE 1');

		foreach($ret->result() as $row) {
			//var_dump ($row);	
			
			for($year = 2012; $year <= 2035; $year++) {	
				$emissionfactor = rand (90, 400);
				$DB1->insert('emissionfactors4technologies', array('technology_id' => $row->ID,'emissionfactor' => $emissionfactor,'year' => $year));
			}
			
		}
		
		
		
		echo "end:...<br>";
	}
		
	
	
		
}

