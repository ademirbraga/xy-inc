<?php
(defined ( 'BASEPATH' )) or exit ( 'No direct script access allowed' );

require(APPPATH.'libraries/REST_Controller.php');

class Api extends REST_Controller {

    public function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->model('Api_model', 'api');
    }

    public function generic_get() {
        $modelo = $this->uri->segment(2);
        $id     = $this->uri->segment(3);

        if (empty($modelo)) {
            $this->response([
                'status' => 'failed',
                'message' => 'Serviço não encontrado.'
            ], REST_Controller::HTTP_BAD_REQUEST);

        } else {

            $dados = $this->api->getDados($modelo, $id);

            $this->response([
                'status' => 'success',
                'dados'  => $dados
            ], REST_Controller::HTTP_OK);
        }
    }

    public function generic_post() {
        $modelo = $this->uri->segment(2);
        $post   = $this->input->post(null);

        try {
            $id     = $this->api->salvarRegistro($modelo, $post);

            if ($id) {
                $post["id"] = $id;
                $this->response([
                    'status' => 'success',
                    'dados' => $post,
                    'message' => 'Registro salvo com sucesso.'
                ], REST_Controller::HTTP_OK);

            } else {
                $this->response($post, REST_Controller::HTTP_BAD_REQUEST);
            }

        } catch (Exception $exception) {
            $this->response([
                'status' => 'failed',
                'dados' => $post,
                'message' => "Não foi possível salvar o registro. " . $exception->getMessage()
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}