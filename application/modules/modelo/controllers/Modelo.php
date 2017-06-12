<?php
(defined ( 'BASEPATH' )) or exit ( 'No direct script access allowed' );

class Modelo extends MX_Controller {

    function __construct() {
        parent::__construct ();
        $this->load->model('Modelo_model', 'modelo');

    }

    public function index() {

        $this->load->view('modelo.phtml');
    }

    public function salvar() {
        $post = $this->input->post(null);

        $post = [
            "nome" => "product",
            "descricao" => "teste",
            "ativo" => true,
            "data_cadastro" => "2017-01-01",
            "inputs" => [
                ["nome_input" => "nome_produto", "type" => "VARCHAR", "tamanho" => 100, "required" => true, "null" => false],
                ["nome_input" => "valor", "type" => "DECIMAL", "tamanho" => 0, "required" => true, "null" => false],
                ["nome_input" => "codigo", "type" => "VARCHAR", "tamanho" => 10, "required" => true, "null" => false, "unique" => true],
            ]
        ];

        $this->db->trans_start();

        try {
            $this->modelo->createModelo($post);

            $this->db->trans_commit();

            echo 'ok';

        } catch (Exception $exception) {
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                throw $exception;
            }
        }

        echo json_encode($this->response);
        exit();
    }
}