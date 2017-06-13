<?php
(defined ( 'BASEPATH' )) or exit ( 'No direct script access allowed' );

require(APPPATH.'libraries/REST_Controller.php');

class Entity extends REST_Controller {

    public function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->model('modelo/Modelo_model', 'modelo');
    }

    public function index_get() {
        $modelo  = $this->uri->segment(2);

        try {
            $modelos = $this->modelo->getModelos(["nome_modelo" => $modelo]);

            $this->response([
                'status' => 'success',
                "meesage" => (count($modelos) . " modelo(s) encontrado(s)."),
                'dados' => $modelos
            ], REST_Controller::HTTP_OK);

        } catch (Exception $exception) {
            $this->badRequestModelo($exception);
        }
    }

    public function index_post() {
        $post = $this->post(null);

        try {

            $this->validateEntity($post);

            $post = json_decode($post[0]);
            $modeloId = $this->modelo->createModelo($post);

            $this->response([
                'status' => 'success',
                "meesage" => "Modelo cadastrado com sucesso.",
                'dados' => $this->modelo->getModelos(["id_modelo" => $modeloId])
            ], REST_Controller::HTTP_OK);

        } catch (Exception $exception) {
            $this->badRequestModelo($exception);
        }
    }

    public function index_delete() {
        $post = $this->post(null);

        try {
            $this->modelo->deleteModelo();


        } catch (Exception $exception) {
            $this->badRequestModelo($exception);
        }
    }

    private function validateEntity($entity) {
        $entity = json_decode($entity[0]);

        if (empty($entity)) {
            throw new Exception("Malformed json string");
        } elseif (empty($entity->nome_modelo)) {
            throw new Exception("O nome da entity não foi informada.");
        } elseif (empty($entity->fields)) {
            throw new Exception("Os fields não foram informados.");
        }

        $modelo = $this->modelo->get_by(["nome_modelo" => $entity->nome_modelo]);

        if (!empty($modelo)) {
            throw new Exception("O modelo '" . $entity->nome_modelo . "' já existe.");
        }
    }
}