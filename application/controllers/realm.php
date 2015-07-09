<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Realm extends MY_Controller {

	public function login() {
		$login = $this->input->get('username');
		$password = $this->input->get('password');

		if ($login == null || $password == null) {
			$this->load->view('welcome_message', array("error"=>"Pseudo ou mot de passe manquant."));
			return;
		}

		$this->load->library("redis");
		$db_pwd = $this->redis->get("modpassword:".$login);
		if ($db_pwd == $password) {
			$this->redis->set("modologin:".$login.":".time(), $_SERVER['REMOTE_ADDR']. " (login)");
			$this->session->set_userdata('username', $login);
			redirect('lookup/index', 'refresh');
		}
	}

	public function logout() {
		if ($this->session->userdata('username') == null) {
			$this->load->view('welcome_message', array("error"=>"Vous n'êtes pas authentifié."));
			return;
		}
		$login = $this->session->userdata('username');
		$this->session->sess_destroy();
		$this->load->library("redis");
		$this->redis->del("modpassword:".$login);
		$this->load->view('welcome_message', array("success"=>"Vous êtes bien déconnecté. Votre mot de passe unique a été supprimé."));
	}

}