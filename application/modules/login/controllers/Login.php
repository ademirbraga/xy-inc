<?php
(defined ( 'BASEPATH' )) or exit ( 'No direct script access allowed' );

class Login extends MX_Controller {
	public $response;
	
	function __construct() {
		parent::__construct ();
		$this->load->model('Login_model', 'login');
		$this->response = new stdClass;
		$this->response->url_base = site_url("login");
	}
	
	public function logar() {
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('email', 'E-mail', 'required');
		$this->form_validation->set_rules('senha', 'Senha', 'required');
		
		if ($this->form_validation->run() == false) {
			$this->load->view('index.phtml');
			
		} else {
			$username = $this->input->post('email');
			$password = $this->input->post('senha');
			
			$sessionLogin = $this->login->logar($username, $password);
			
			if (!empty($sessionLogin)) {
				$this->session->set_userdata('logged_in', $sessionLogin);
				redirect(site_url());
			}  else {
				$this->load->view('index.phtml', ['error' => 'Usuário/Senha incorretos.']);
			}
		}
	}
	
	public function logout() {
		$this->session->sess_destroy();
		redirect(site_url('login'));
	}
	
	public function forgotpassword() {
		$this->load->view('forgotpassword.phtml');
	}
}