<?php

class Modelo_model extends MY_Model {
    public $_table = 'modelo';
    public $primary_key = 'id';
    private $modelo;

    public function createModelo($dados) {
        $dados["ativo"] = true;
        $this->db->trans_start();

        try {
            $modeloId = $this->saveOrUpdate($dados);

            $this->modelo = $dados["nome_modelo"];

            $this->salvarInputs($modeloId, $dados["fields"]);

            try {
                $this->load->dbforge();

                $this->setInputs($dados["nome_modelo"], $dados["fields"]);

                $this->dbforge->create_table($dados["nome_modelo"], false);

                $this->db->trans_commit();

                return $modeloId;

            } catch (Exception $exception) {
                throw new CiError("Ocorreu um erro ao tentar criar o modelo '" . $this->modelo . "'" . $exception->getMessage());
            }

        } catch (Exception $exception) {
            $this->db->trans_rollback();
            throw $exception;
        }
    }

    public function dropModelo($modelo) {
        $this->load->dbforge();
        $this->dbforge->drop_table($modelo, true);
    }

    private function salvarInputs($modeloId, $inputs) {
        $this->setTable("modelo_input");

        $this->db->trans_start();

        try {
            foreach ($inputs as $input) {
                $input["id_modelo"] = $modeloId;
                $this->saveOrUpdate($input);
            }

            $this->db->trans_commit();

        } catch (Exception $exception) {
            $this->db->trans_rollback();
            throw new Exception("Ocorreu um erro ao tentar salvar um dos campos do modelo '" . $this->modelo."'.");
        }
    }

    private function setInputs($modelo, $inputs) {
        $fields = [];

        $pk = "id_" . strtolower($modelo);

        $fields[$pk] = [
            "type" => "SERIAL PRIMARY KEY",
            'auto_increment' => true,
            'unsigned' => true,
        ];
        $this->dbforge->add_key($pk, true);

        foreach ($inputs as $input) {
            $infoInput = [
                "type" => $this->getType($input["type"], $input["tamanho"]),
                //'null' => isset($input["null"]) ? $input["null"] : true,
                'unique' => isset($input['unico']) ? $input['unico'] : false,
            ];

            $fields[$input['nome']] = $infoInput;
        }

        $this->dbforge->add_field($fields);

        return $fields;
    }

    private function getType($type, $size) {
        switch (strtoupper($type)) {
            case "DECIMAL": return "DECIMAL(10, 2)";
            case "STRING": return "VARCHAR($size)";
            default: return $type;
        }
    }

    public function getModelos($where = []) {
        $this->setTable("modelo");
        $modelos = $this->get_many_by($where);

        $inputs  = $this->getModeloInput($where);

        foreach ($modelos as &$modelo) {
            foreach ($inputs as $input) {
                if ($input->id_modelo == $modelo->id_modelo) {
                    $modelo->fields[] = $input;
                }
            }
        }
        return $modelos;
    }

    private function getModeloInput($where) {
        $this->setTable("modelo_input");
        $this->db->select("modelo_input.*");
        $this->db->join("modelo", "modelo.id_modelo = modelo_input.id_modelo");
        return $this->get_many_by($where);
    }

    public function existeModelo($modelo) {
        $this->setTable("modelo");
        return !empty($this->get_by(["nome_modelo" => $modelo]));
    }

    public function existeTabela($tabela) {
        return $this->db->table_exists($tabela);
    }
}
