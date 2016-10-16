<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Searchpath extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		
	}
		
	public function index()
	{
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		}
       
	   	$vars["classA"] = "http://www.ontologyportal.org/SUMO.owl#Building";
		$vars["classB"] = "http://www.semanco-project.eu/2012/5/SEMANCO.owl#Age";
		
		$vars["Summary"] = "";
		$vars["Response"] = "Response...";
		$vars["Exception"] = "";
		
		$this->load->view('header');
		$this->load->view('searchpath_post', $vars);
		$this->load->view('footer');
	}
	
	public function searchpath_post() 
	{
		$classA = $this->input->post('input_nameA');
		$classB = $this->input->post('input_nameB');
		
		$vars["classA"] = $classA;
		$vars["classB"] = $classB;
		
		$vars["Summary"] = "Path from <strong>".$classA."</strong> to <strong>".$classB."</strong>...<br><br>";
		$vars["Response"] = "";
		$vars["Exception"] = "";
		
		try{
            $option = array( 'login' => "semana",
                                'password' => "semana");  

            //El webservice d'amazon tampoc funciona en el servidor.
            //$sClient = new SoapClient('http://soap.amazon.com/schemas2/AmazonWebServices.wsdl');
            $sClient = new SoapClient('http://semanco01.hs-albsig.de/SemAna/ShortestPathService.wsdl',$option);

            $params = array($classA, $classB);
            $response = $sClient->ShortestPath($params);

            echo "Correct<br />";
            echo "<pre>";
            $vars["Response"] = $response;
            echo "</pre>";
            
		}catch(SoapFault $e){
				echo "Exception<br />";
				echo "<pre>";
				
				$vars["Exception"] = $e;
				echo "</pre>";
		}
	
		$this->load->view('header');
		$this->load->view('searchpath_post', $vars);
	
		$this->load->view('footer');
	}
}
