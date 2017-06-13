<?php
(defined ( 'BASEPATH' )) or exit ( 'No direct script access allowed' );

require(APPPATH.'libraries/REST_Controller.php');

class Api extends REST_Controller {

    public function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->model('Api_model', 'api');
        $this->load->model('modelo/Modelo_model', 'modelo');
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
        $post   = $this->post(null);

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

    public function generic_delete() {
        $modelo = $this->uri->segment(2);
        $id     = $this->uri->segment(3);

        try {
            if (empty($modelo)) {
                throw new Exception("O nome do modelo deve ser informado.");
            } elseif (empty($id)) {
                throw new Exception("O identificador do modelo deve ser informado.");
            } elseif (!$this->modelo->existeModelo($modelo)) {
                throw new Exception("O modelo '$modelo' informado não existe.");
            }

            try {

                $result = $this->api->deletarRegistro($modelo, $id);

                $this->response([
                    'status' => 'success',
                    'message' => ($result) ? "O registro '" . $id . "' foi apagado com sucesso." : 'O registro ' . $id. ' não pode ser apagado',
                    'dados'  => []
                ], REST_Controller::HTTP_OK);

            } catch (Exception $exception) {
                throw new Exception("Não foi possível apagar o registro '$id' informado.");
            }

        } catch (Exception $exception) {
            $this->response([
                'status' => 'failed',
                'message' => 'Não foi possivel apagar o registro. ' . $exception->getMessage(),
                'dados'  => []
            ], REST_Controller::HTTP_OK);
        }
    }

    public function generic_put() {
        $modelo = $this->uri->segment(2);
        $id     = $this->uri->segment(3);
        $post   = $this->post(null);

        try {

            if (empty($modelo)) {
                throw new Exception("O nome do modelo deve ser informado.");
            } elseif (empty($id)) {
                throw new Exception("O identificador do modelo deve ser informado.");
            } elseif (!$this->modelo->existeModelo($modelo)) {
                throw new Exception("O modelo '$modelo' informado não existe.");
            } elseif (empty($post)) {
                throw new Exception("É necessário infomar os dados a serem atualizados.");
            }

            $pk        = $this->api->getPostGresPK($modelo);
            $post[$pk] = $id;
            $id        = $this->api->salvarRegistro($modelo, $post);

            if ($id) {
                $post = $this->api->getDados($modelo, $id);

                $this->response([
                    'status' => 'success',
                    'dados' => $post,
                    'message' => 'Registro atualizado com sucesso.'
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