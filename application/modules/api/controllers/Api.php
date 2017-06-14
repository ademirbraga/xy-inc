<?php
(defined ( 'BASEPATH' )) or exit ( 'No direct script access allowed' );

require(APPPATH.'libraries/REST_Controller.php');

/**
 * Class Api
 *
 * Classe utilizada para manipulação de dados dos modelos criados
 */
class Api extends REST_Controller {

    public function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->model('Api_model', 'api');
        $this->load->model('modelo/Modelo_model', 'modelo');

        $modelo = $this->uri->segment(2);
        $this->validateRequestModel($modelo);
    }

    public function generic_get() {
        $modelo = $this->uri->segment(2);
        $id     = $this->uri->segment(3);

        try {
            if (!$this->modelo->existeModelo($modelo) || !$this->modelo->existeTabela($modelo)) {
                throw new Exception("O modelo '{$modelo}' informado não existe.", REST_Controller::HTTP_OK);
            }

            $dados = $this->api->getDados($modelo, $id);

            $this->response([
                'status' => 'success',
                'message' => (count($dados)) . ' registro(s) encontrado(s)',
                'dados' => $dados
            ], REST_Controller::HTTP_OK);

        } catch (Exception $exception) {
            $this->badRequestModelo($exception);
        }
    }

    public function generic_post() {
        $modelo = $this->uri->segment(2);
        $post   = $this->post();

        try {
            $id = $this->api->salvarRegistro($modelo, $post[0]);

            $dados = $this->api->getDados($modelo, $id);

            $this->response([
                'status' => 'success',
                'dados' => $dados,
                'message' => 'Registro salvo com sucesso.'
            ], REST_Controller::HTTP_OK);

        } catch (Exception $exception) {
            $this->badRequestModelo($exception);
        }
    }

    public function generic_delete() {
        $modelo = $this->uri->segment(2);
        $id     = $this->uri->segment(3);

        try {
            if (empty($modelo)) {
                throw new Exception("O nome do modelo deve ser informado.");
            } elseif (empty($id)) {
                throw new Exception("O identificador do modelo '{$modelo}' deve ser informado.");
            } elseif (!$this->modelo->existeModelo($modelo)) {
                throw new Exception("O modelo '$modelo' informado não existe.");
            }

            $dados = $this->api->getDados($modelo, $id);

            if (empty($dados)) {
                throw new Exception("O registro '$id' do modelo '{$modelo}' informado não existe.");
            }

            try {

                $result = $this->api->deletarRegistro($modelo, $id);

                $this->response([
                    'status' => 'success',
                    'message' => ($result) ? "O registro '" . $id . "' foi apagado com sucesso." : 'O registro ' . $id. ' não pode ser apagado',
                    'dados'  => $dados
                ], REST_Controller::HTTP_OK);

            } catch (Exception $exception) {
                throw new Exception("Não foi possível apagar o registro '$id' informado.");
            }

        } catch (Exception $exception) {
            $this->badRequestModelo($exception);
        }
    }

    public function generic_put() {
        $modelo = $this->uri->segment(2);
        $id     = $this->uri->segment(3);
        $put    = $this->put();

        try {

            if (empty($id)) {
                throw new Exception("O identificador do modelo '{$modelo}' deve ser informado.", REST_Controller::HTTP_OK);
            } elseif (empty($put)) {
                throw new Exception("É necessário infomar os dados do modelo '{$modelo}' a serem atualizados.", REST_Controller::HTTP_OK);
            }

            $put   = $put[0];
            $pk    = $this->api->getPostGresPK($modelo);
            $dados = $this->api->getDados($modelo, $id);

            if (empty($dados)) {
                throw new Exception("O registro '$id' do modelo '{$modelo}' informado não existe.", REST_Controller::HTTP_OK);
            }

            $put[$pk]  = $id;

            try {
                $this->api->salvarRegistro($modelo, $put);

                $this->response([
                    'status' => 'success',
                    'dados' => $put,
                    'message' => 'Registro atualizado com sucesso.'
                ], REST_Controller::HTTP_OK);

            } catch (Exception $exception) {
                throw $exception;
            }

        } catch (Exception $exception) {
            $this->badRequestModelo($exception);
        }

    }
}