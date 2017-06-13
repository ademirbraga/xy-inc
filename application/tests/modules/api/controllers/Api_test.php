<?php
(defined ( 'BASEPATH' )) or exit ( 'No direct script access allowed' );

/**
 * Class Api
 *
 * Classe utilizada para manipulação de dados dos modelos criados
 */
class Api_test extends TestCase {

    private $config;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        $CI =& get_instance();

        self::createModelo();

        $CI->load->library('Seeder');
        $CI->seeder->call('ApiSeeder');
    }

    public static function createModelo() {
        $obj = (new TestCase())->newModel('modelo/Modelo_model');

        $obj->dropModelo('teste_modelo');

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
                ]
            ]
        ];
        $obj->createModelo($expected);
    }

    public function test_generic_get() {
        $this->config =& load_class('Config', 'core');
        $this->config->set_item('rest_auth', false);
        $this->config->set_item('auth_library_class', '');
        $this->config->set_item('auth_library_function', '');


        $this->assertEquals($this->config->item('rest_auth'), false);
        $this->assertEquals($this->config->item('auth_library_function'), '');
        $this->assertEquals($this->config->item('auth_library_function'), '');

        try {
            $output = $this->request('GET', 'api/teste_modelo/1');
        } catch (CIPHPUnitTestExitException $e) {
            $output = ob_get_clean();
        }
        echo'AAAAAA::::::';print_r($output);
          $this->assertEquals('[{"id_teste_modelo":1,"nome":"modelo_seed_api","valor": "100.00"}]', $output);
         $this->assertResponseCode(200);
    }


}