<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modlookup extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index() {
		if ($this->session->userdata('usernamexd') == null) {
			$this->load->view('welcome_message', array("error"=>"Vous n'êtes pas authentifié."));
			return;
		}

		$this->load->view('form', array("username" => $this->session->userdata('usernamexd')));
	}

	public function show($nickname) {
		if (!isset($nickname)) {
			redirect(site_url(array("lookup", "index")));
			return;
		}

		if ($this->session->userdata('usernamexd') == null) {
			$this->load->view('welcome_message', array("error"=>"Vous n'êtes pas authentifié."));
			return;
		}

		if ($nickname == null or $nickname == "") {
			$this->load->view('form', array("username" => $this->session->userdata('usernamexd'), "error"=>"Aucun pseudo entré."));
			return;
		}
        
		$this->load->library("redis");
		$this->redis->set("modologin:".$this->session->userdata('usernamexd').":".time(), $_SERVER['REMOTE_ADDR']. " (modloockup show fetch)");

		$i = 0;
		foreach ($this->redis->keys("case:*") as $key) {
			$dat = $this->redis->command("ZREVRANGEBYSCORE ".$key." +inf 0");
			$uuid = explode(":", $key)[1];
			foreach ($dat as $line) {
				$json = json_decode($line, true);
				if ($json["addedBy"] == $nickname) {
					$i++;
					echo $i." : "/*.$name. " "*/;
					echo "ID=".$uuid." | TYPE=".$json["type"]." | DURATION=".(!isset($json["duration"]) ? "DEF" : $json["duration"])." | TIMECODE=".date('l j F Y H:i:s', $json["timestamp"]/1000)." | MOTIF=".(!isset($json["motif"]) ? "<strong>AUCUN</strong>" : $json["motif"]);
					echo "<hr/>";
				}
			}
		}
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */