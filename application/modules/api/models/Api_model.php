<?php

class Api_model extends MY_Model {
    public $_table = 'modelo';
    public $primary_key = 'id';

    public function getDados($modelo, $id = null) {
        $this->setTable($modelo);

        if ($id) {
            $pk = $this->getPostGresPK($modelo);
            return $this->get_by([$pk => $id]);
        }
        return $this->get_all();
    }

    public function salvarRegistro($modelo, $dados) {
        $this->setTable($modelo);

        $this->db->trans_start();

        try {
            $id = $this->saveOrUpdate($dados);
            $this->db->trans_commit();
            return $id;

        } catch (CiError $exception) {
            $this->db->trans_rollback();
            throw $exception;
        }
    }

    public function deletarRegistro($modelo, $id) {
        $this->setTable($modelo);

        $this->db->trans_start();

        try {
            $pk = $this->getPostGresPK($modelo);

            $result = $this->delete_by([$pk => $id]);

            $this->db->trans_commit();

            return $result;

        } catch (CiError $exception) {
            $this->db->trans_rollback();
            throw $exception;
        }
    }
}