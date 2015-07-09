<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lookup extends MY_Controller {

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
		if ($this->session->userdata('username') == null) {
			$this->load->view('welcome_message', array("error"=>"Vous n'êtes pas authentifié."));
			return;
		}

		$this->load->view('form', array("username" => $this->session->userdata('username')));
	}

	public function show($nickname, $show = "results") {
		if (!isset($nickname)) {
			redirect(site_url(array("lookup", "index")));
			return;
		}
		
		if ($this->session->userdata('username') == null) {
			$this->load->view('welcome_message', array("error"=>"Vous n'êtes pas authentifié."));
			return;
		}

		if ($nickname == null or $nickname == "") {
			$this->load->view('form', array("username" => $this->session->userdata('username'), "error"=>"Aucun pseudo entré."));
			return;
		}
        
        $data = $this->NameToUUID($nickname);

        if ($data == null or $data == "") {
        	$this->load->view('noresult', array("username" => $this->session->userdata('username'), "pseudo" => $nickname));
			return;
        }


		$this->load->library("redis");       

		$back = array();
        if ($data != false)
        	$back["history"] = $this->getHistory($data);
        else
        	$back["history"] = null;
        $data = $this->FormatUUID($data);
        $back["uuid"] = $data;
        
		$remove = $this->input->post("remove");
        if ($remove != null && !empty($remove)) {
        	$remove = stripslashes($remove);
        	$score = $this->redis->zscore("case:".$data, $remove);
        	if ($score != null) {
        		$this->redis->zadd("removedcase:".$data, $score, $remove." -> ".$this->session->userdata('username')." | ".time());
        		$this->redis->zrem("case:".$data, $remove);
        	}
        }

        $addtofile = $this->input->post("addtofile");
        if ($addtofile != null && !empty($addtofile)) {
        	$json = array();
        	$json["addedBy"] = $this->session->userdata('username');
        	$json["type"] = "Remarque";
        	$json["motif"] = $addtofile;
        	$json["timestamp"] = time() * 1000;
        	$this->redis->zadd("case:".$data, $json["timestamp"], json_encode($json));

        	redirect(site_url(array("lookup", "show", urlencode($nickname))));

        	return;
        }

		//$data = file_get_contents("http://connorlinfoot.com/uuid/api/?user=".$nickname."&get=uuid");
		//$data = preg_replace("/([0-9a-f]{8})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{12})/", "$1-$2-$3-$4-$5", $data);
		
		$back["pseudo"] = $nickname;
		$back["username"] = $this->session->userdata('username');

		// Coins :
		$back["coins"] = $this->redis->get("coins:".$data);
		$back["stars"] = $this->redis->get("stars:".$data);

		// Stats :
		$back["stats"] = array();
		foreach ($this->redis->keys("gamestats:*") as $key) {
			$k = explode(":", $key);
			$back["stats"][$k[1]][$k[2]] = $this->redis->zscore($key, $data);

		}
		

		$back["points"] = $this->redis->get("userpoints.".$data);
		$back["flags"] = $this->redis->lrange("flags:".$data, 0, -1);
		$back["sanctions"] = $this->redis->lrange("sanctions:".$data, 0, -1);
		$back["case"] = $this->redis->command("ZREVRANGEBYSCORE case:".$data." +inf 0");

		// Sanctions actuelles //
		

		// Login time

		$logtime = $this->redis->hget("logintime", $data);
		$back["logtime"] = ($logtime != null) ? $this->formatTime(floor($logtime/1000)) : "Aucune donnée";

		$lastlogin = $this->redis->hget("lastlogin", $data);
		$back["lastlogin"] = ($lastlogin != null) ? date('l j F Y H:i:s', $lastlogin/1000) : "Aucune donnée";

		$back["shops"] = array();

		foreach ($this->redis->keys("shops:*:".$data.":owned") as $key) {
			$k = explode(":", $key);
			$l = $this->redis->get($key);
			$back["shops"][$k[1]][$k[2]] = ($l == null) ? array() : explode(":", $l);
		}

		$friends = $this->redis->lrange("friends:$data", 0, -1);
		$back["friends"] = ($friends == null) ? 0 : count($friends);

		$anticheatscore = $this->redis->hget("anticheat:banscores", $data);
		$back["acscore"] = ($anticheatscore == null) ? 0 : $anticheatscore;

		$cheats = $this->redis->smembers("anticheat:log:".$data);
		$back["accheats"] = ($cheats == null) ? array() : $cheats;

		$this->load->view($show, $back);
	}

	public function results()
	{
		$nickname = $this->input->post("pseudo");

		if ($nickname == null or $nickname == "") {
			$this->load->view('form', array("username" => $this->session->userdata('username'), "error"=>"Aucun pseudo entré."));
			return;
		}

		if (!preg_match("#^[a-zA-Z0-9_-]{3,20}$#", $nickname)) {
			$this->load->view('form', array("username" => $this->session->userdata('username'), "error"=>"Le pseudo entré est invalide."));
			return;
		}


		redirect(site_url(array("lookup", "show", urlencode($nickname))));
	}

	public function remove($uuid) {
		echo("UUID : ".$uuid);
	}

	function formatTime($time) {
		$days = floor($time / (3600*24));
        $remainder = $time - $days * (3600*24);
        $hours = floor($remainder / 3600);
        $remainder = $remainder - ($hours * 3600);
        $mins = floor($remainder / 60);
        $secs = $remainder - ($mins * 60);

        $str = "";
        if ($days > 0)
        	$str .= $days." jours ";

        if ($hours > 0)
        	$str .= $hours." heures ";

        if ($mins > 0)
        	$str .= $mins." min ";

        if ($mins > 0)
        	$str .= $secs." sec";

        return $str;
	}
	//returns username corresponding to the UUID or false if the UUID does not exist
	function UUIDToName($uuid)
	{	    
	    $result = @file_get_contents("https://sessionserver.mojang.com/session/minecraft/profile/$uuid", false, stream_context_create(array(
	        "http" => array(
	            "timeout" => 5
	        )
	    )));
	    
	    if ($result)
	    {
	        return $result;
	    }
	    else
	        return false;    
	}

	//returns uuid corresponding to the username, or false if user does not exist or when the mojang api is down
	//option to return capital-corrected name
	function NameToUUID($name)
	{
	    $result = @file_get_contents("https://api.mojang.com/users/profiles/minecraft/$name");
	        
	    if ($result) {
	        $decoded = json_decode($result, true);
	        return $decoded["id"];
	    }
	    else
	        return false;
	}

	function getHistory($uuid) {
		$result = @file_get_contents("https://api.mojang.com/user/profiles/$uuid/names");
	        
	    if ($result) {
	        $decoded = json_decode($result, true);
	        return $decoded;
	    }
	    else
	        return false;
	}

	//format the uuid like minecraft does (will not intefere with UUIDToName
	function FormatUUID($uuid) 
	{
	    $uid = "";
	    $uid .= substr($uuid, 0, 8) . "-";
	    $uid .= substr($uuid, 8, 4) . "-";
	    $uid .= substr($uuid, 12, 4) . "-";
	    $uid .= substr($uuid, 16, 4) . "-";
	    $uid .= substr($uuid, 20);
	    return $uid;
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */