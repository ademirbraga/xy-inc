<?php
(defined ( 'BASEPATH' )) or exit ( 'No direct script access allowed' );

class Login extends MX_Controller {
    public $response;
    
    function __construct() {
        parent::__construct ();
        $this->load->model('Login_model', 'login');

        $this->response = new stdClass;
        $this->response->url_base = site_url("login");

        $this->load->add_package_path(APPPATH.'third_party/ion_auth/');
        $this->load->library('ion_auth');
        $this->load->remove_package_path(APPPATH.'third_party/ion_auth/');
    }
    
    public function logar() {

        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            //$this->render('user/login_view');
            $this->load->view('index.phtml', ['error' => 'Usuário/Senha incorretos.']);
        } else {
            $remember = (bool) $this->input->post('remember');
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            if ($this->ion_auth->login($username, $password, $remember)) {
                redirect('dashboard');
            } else {
                $_SESSION['auth_message'] = $this->ion_auth->errors();
                $this->session->mark_as_flash('auth_message');
                redirect('user/login');
            }
        }

/*



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
*/
    }
    
    public function logout() {
        $this->session->sess_destroy();
        redirect(site_url('login'));
    }
    
    public function forgotpassword() {
        $this->load->view('forgotpassword.phtml');
    }
}
