<?php
(defined ( 'BASEPATH' )) or exit ( 'No direct script access allowed' );

class Modelo extends MX_Controller {

    function __construct() {
        parent::__construct ();
        $this->load->model('Modelo_model', 'modelo');

    }

    public function ajax() {
        $json_data = array(
            "draw" => 0,
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => []
        );

        echo json_encode($json_data);
    }

    public function index() {
        $this->load->view('index.phtml');
    }

    public function registro() {
        $this->load->view('modelo.phtml');
    }


    public function getModelos() {
        $post = $this->input->post(null);

        $json_data = array(
            "draw" => 0,
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => []
        );

        $json_data['data'] = $this->modelo->getModelos();

        echo json_encode($json_data);
        exit();
    }

    public function carregarInputs() {
        $post = $this->input->post(null);

        $json_data = array(
            "draw" => 1,
            "recordsTotal" => 1,
            "recordsFiltered" => 1,
            "data" => []
        );

        $this->modelo->setTable('modelo_input');
        $json_data['data'] = $this->modelo->get_by([]);

        echo json_encode($json_data);
        exit();
    }

    public function salvar() {
        $post = $this->input->post(null);

        $this->db->trans_start();

        try {

            if($this->db->table_exists($post["nome_modelo"])) {
                throw new Exception("O modelo '{$post["nome_modelo"]}' já existe.");
            }
            $this->modelo->createModelo($post);
            $this->db->trans_commit();

        } catch (Exception $exception) {
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                throw $exception;
            }
        }
        echo json_encode($this->response);
       // exit();
    }
}
