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
}