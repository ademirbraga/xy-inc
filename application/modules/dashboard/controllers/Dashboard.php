<?php
(defined ( 'BASEPATH' )) or exit ( 'No direct script access allowed' );

class Dashboard extends MX_Controller {
	
	function __construct() {
        parent::__construct ();
	}

	public function index() {
		$this->load->view('dashboard.phtml', ['MEDIA_PATH'=>MEDIA_PATH]);
	}
}