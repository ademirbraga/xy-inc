<?php
(defined ( 'BASEPATH' )) or exit ( 'No direct script access allowed' );

class Dashboard extends MX_Controller {

    function __construct() {
        parent::__construct ();

        $this->load->add_package_path(APPPATH.'third_party/ion_auth/');
        $this->load->library('ion_auth');
        $this->load->remove_package_path(APPPATH.'third_party/ion_auth/');

        if (!$this->ion_auth->logged_in()) {
            redirect('/login');
        }
    }

    public function index() {
        $this->load->view('dashboard.phtml', ['MEDIA_PATH'=>MEDIA_PATH]);
    }
}
