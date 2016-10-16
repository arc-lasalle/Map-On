<?php
class Vowl_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function getVowlFile( $ontology_id ) {
        $ret["ok"] = false;
        $ret["status"] = "Vowl not loaded.";
        $ret["vowl"] = "";

		
		$ont = $this->ontology->getOntology($ontology_id);


        /*
		$owl_file_path = "/upload/owl/".$ontology_id."_".$ont->name.".owl";

        // Get .owl file name from db
        $this->db->select( "file" );
        $this->db->from( "ontology_modules" );
        $this->db->where( "ontology_id", $ontology_id );
        $result = $this->db->get()->result();
		
        if ( !isset($result[0]->file) ) return $ret;
        $owl_file_path = $result[0]->file;

        $owl_file_path_parts = pathinfo( $owl_file_path );
		// Get corresponding vowl .json file path
		$json_file_path = "/upload/vowl/" . $owl_file_path_parts['filename'] . ".json";
        */

        $base_path = "/upload/ontologies/" . $ontology_id."_".$ont->name . "/";
        $owl_file_path = $base_path . $ont->name.".owl";
        $json_file_path = $base_path . $ont->name.".json";

        // Create the json vowl file if not exist.
        $result = $this->createVowl( $owl_file_path, $json_file_path, false );
		
        if ( !$result["ok"] ) {
            $ret["status"] = $result["error"];
						
            return $ret;
        }


        // Read the vowl file.
        $ret["vowl"] = $this->readVowl( $json_file_path );
        if ( $ret["vowl"] == "" ) {
            $ret["status"] = "Error loading the json vowl file.";
            return $ret;
        }

        $ret["ok"] = true;
        $ret["status"] = "Vowl loaded successfully.";

        return $ret;
    }


    function createVowl ( $orig_owl_path, $dest_vowl_path, $force ) {

        $actual_dir = getcwd();
        $orig_owl_path = $actual_dir . $orig_owl_path;
        $dest_vowl_path = $actual_dir . $dest_vowl_path;

        if ( file_exists($dest_vowl_path) && !$force ) {
            $ret["ok"] = true;
            $ret["error"] = "The file exists.";
            return $ret;
        }

        //$startParameter = '-file ' . $_FILES['ontology']['tmp_name'];
        $startParameter = '-file ' . $orig_owl_path;

        $command = 'java -jar ' . $actual_dir . '/owl2vowl.jar -echo ' . $startParameter;
		
		//var_dump($command);
        $process = proc_open($command, array(1 => array("pipe", "w"), 2 => array("pipe", "w")), $pipes, dirname(__FILE__));

        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $error_output = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $return_var = proc_close($process);

        if ($return_var === 0) {
            $fp = fopen($dest_vowl_path, 'w');
            fwrite($fp, $output);
        }

        $ret["ok"] = ($return_var === 0);
        $ret["error"] = $error_output;
        return $ret;

    }

    function readVowl ( $vowl_path ) {

        $vowl_path = getcwd() . $vowl_path;

        $handle = fopen( $vowl_path, "r" );

        if ( !$handle ) return "";

        $vowl_content = fread( $handle, filesize($vowl_path) );

        fclose($handle);

        return $vowl_content;
    }

}

?>