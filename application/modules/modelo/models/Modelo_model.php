<?php

class Modelo_model extends MY_Model {
    public $_table = 'modelo';
    public $primary_key = 'id';


    public function createModelo($dados) {
        $modeloId = $this->saveOrUpdate($dados);

        $this->load->dbforge();

        $fields = $this->setInputs($dados["nome"], $dados["inputs"]);

        $this->salvarInputs($modeloId, $dados["inputs"]);

        try {
            $this->dbforge->create_table($dados["nome"], true);

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    private function salvarInputs($modeloId, $inputs) {
        $this->setTable("modelo_input");

        $this->db->trans_start();

        try {
            foreach ($inputs as $input) {
                $input["modelo_id"] = $modeloId;
                $this->saveOrUpdate($input);
            }

            $this->db->trans_commit();

        } catch (Exception $exception) {
            $this->db->trans_rollback();
            throw $exception;
        }
    }

    private function setInputs($modelo, $inputs) {
        $fields = [];

        $pk = "id_" . strtolower($modelo);

        $fields[$pk] = [
            "type" => "INT",
            'auto_increment' => true,
            'unsigned' => true,
        ];
        $this->dbforge->add_key($pk, true);

        foreach ($inputs as $input) {
            $infoInput = [
                "type" => $this->getType($input["type"]),
                'null' => $input["null"],
                'unique' => isset($input['unique']) ? $input['unique'] : false,
                'default' => $this->getDefaultValue($input["type"], $input["null"]),
            ];

            $fields[$input['nome_input']] = $infoInput;
        }

        $this->dbforge->add_field($fields);

        return $fields;
    }

    private function getDefaultValue($type, $nullable) {

        if ($type == "DECIMAL") {
            return $nullable ? null : 0.00;
        }
        return null;

    }

    private function getType($type) {
        switch ($type) {
            case "DECIMAL": return "DECIMAL(10, 2)";
            default: return $type;
        }
    }
}