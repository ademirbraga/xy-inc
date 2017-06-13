<?php

class Modelo_model_test extends TestCase {

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        $CI =& get_instance();
        $CI->load->library('Seeder');
        $CI->seeder->call('EntitySeeder');
    }

    public function setUp() {
        $this->obj = $this->newModel('modelo/Modelo_model');
        $this->obj->dropModelo('teste_modelo');
    }

    public function test_createModelo() {
        $expected = [
            "nome_modelo"=> "teste_modelo",
            "fields"=> [
                [
                    "nome"=> "nome",
                    "type"=> "string",
                    "tamanho"=> "100",
                    "required"=> true,
                    "unico"=> true
                ],
                [
                    "nome"=> "valor",
                    "type"=> "decimal",
                    "tamanho"=> "20",
                    "required"=> false,
                    "unico"=> false
                ],
                [
                    "nome"=> "valor2",
                    "type"=> "decimal",
                    "tamanho"=> "20",
                    "required"=> false,
                    "unico"=> false
                ],
                [
                    "nome"=> "codigo",
                    "type"=> "int",
                    "tamanho"=> "20",
                    "required"=> false,
                    "unico"=> false
                ]
            ]
        ];
        $idModelo = $this->obj->createModelo($expected);
        $modelo  = $this->obj->getModelos(["modelo.id_modelo" => $idModelo]);
        $modelo = $modelo[0];

        $this->assertEquals($expected["nome_modelo"], $modelo->nome_modelo);

        foreach ($expected["fields"] as $key => $field) {
            foreach ($field as $k => $f) {
                $this->assertEquals($f, $modelo->fields[$key]->$k);
            }
        }
    }

    public function test_createModeloFalhaCriarModeloComErros() {
        try {
            $expected = [
                "nome_modelo"=> "teste_modelo",
                "fields"=> [
                    [
                        "nome"=> "nome",
                        "type"=> "TIPO_NAO_DEFINIDO",
                        "tamanho"=> "100",
                        "required"=> true,
                        "unico"=> true
                    ],
                    [
                        "nome"=> "telefone",
                        "type"=> "string",
                        "tamanho"=> "20",
                        "required"=> false,
                        "unico"=> false
                    ]
                ]
            ];
            $this->obj->createModelo($expected);
        }
        catch(Exception $e) {
            return;
        }

        $this->fail('No exception has been raised');
    }

    public function test_createModeloFalhaCriarModelo() {
        try {
            $expected = [
                "xxx"=> "teste_modelo",
                "fields"=> [
                    [
                        "nome"=> "nome",
                        "type"=> "string",
                        "tamanho"=> "100",
                        "required"=> true,
                        "unico"=> true
                    ],
                    [
                        "nome"=> "telefone",
                        "type"=> "string",
                        "tamanho"=> "20",
                        "required"=> false,
                        "unico"=> false
                    ]
                ]
            ];
            $this->obj->createModelo($expected);
        }
        catch(Exception $e) {
            return;
        }

        $this->fail('No exception has been raised');
    }

    public function test_createModeloFalhaSalvarRegistroModelo() {
        try {
            $this->obj->createModelo([]);
        }
        catch(Exception $e) {
            return;
        }

        $this->fail('No exception has been raised');
    }

    public function test_createModeloFalhaSalvarRegistroModeloInputs() {
        $expected = [
            "nome_modelo"=> "teste_modelo",
            "fields"=> [
                [
                    "erro"=> "erro",
                    "type"=> "string",
                    "tamanho"=> "100",
                    "required"=> true,
                    "unico"=> true
                ]
            ]
        ];
        try {
            $this->obj->createModelo($expected);
        }
        catch(Exception $e) {
            return;
        }

        $this->fail('No exception has been raised');
    }

    public function test_dropModelo() {
        $drop = $this->obj->dropModelo("cccc");
        $this->assertNull($drop);
    }

    public function test_existeModelo() {
        $existe = $this->obj->existeModelo("cccc");
        $this->assertEquals($existe, false);
    }

    protected function tearDown() {
        $modelo  = $this->obj->getModelos(["modelo.nome_modelo" => 'teste_modelo']);
        if (!empty($modelo)) {
            $this->obj->setTable("modelo_input");
            $this->obj->delete_by(["id_modelo" => $modelo[0]->id_modelo]);

            $this->obj->setTable("modelo");
            $this->obj->delete_by(["id_modelo" => $modelo[0]->id_modelo]);

            $this->obj->dropModelo('teste_modelo');
        }
    }
}