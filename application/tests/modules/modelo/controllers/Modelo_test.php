<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Modelo_test extends TestCase {

    public function setUp() {
        $this->obj = $this->newModel('modelo/Modelo_model');
        $this->obj->dropModelo('xxx');
    }

    public function test_salvar() {
        $post = [
            "nome_modelo" => "xxx",
            "descricao" => "teste",
            "ativo" => true,
            "fields" => [
                ["nome" => "nome_produto", "type" => "VARCHAR", "tamanho" => 100, "required" => true, "unique" => false, "ativo" => true],
                ["nome" => "valor", "type" => "DECIMAL", "tamanho" => 0, "required" => true, "unique" => false, "ativo" => true],
                ["nome" => "codigo", "type" => "VARCHAR", "tamanho" => 10, "required" => true, "unique" => true, "ativo" => true],
            ]
        ];

        try {
            $output = $this->ajaxRequest('POST', 'modelo/salvar', $post);
        } catch (Exception $e) {
            return;
        }
        print_r($output);
        $this->fail('No exception has been raised');
    }

    public function test_getModelos() {
        $output = $this->ajaxRequest('GET', 'modelo/ajax');
        $expected = '{"draw":0,"recordsTotal":0,"recordsFiltered":0,"data":[]}';

        echo 'saida:';print_r($output);
        echo 'esperado:';print_r($expected);
        $this->assertEquals($expected, $output);
    }


    public function xtest_salvarDuplicado() {

        $post = [
            "nome_modelo" => "xxx",
            "descricao" => "teste",
            "ativo" => true,
            "fields" => [
                ["nome" => "nome_produto", "type" => "VARCHAR", "tamanho" => 100, "required" => true, "unique" => false, "ativo" => true],
                ["nome" => "valor", "type" => "DECIMAL", "tamanho" => 0, "required" => true, "unique" => false, "ativo" => true],
                ["nome" => "codigo", "type" => "VARCHAR", "tamanho" => 10, "required" => true, "unique" => true, "ativo" => true],
            ]
        ];

        try {
            $output = $this->request('POST', 'modelo/salvar', $post);
        } catch (Exception $e) {
            return;
        }
        print_r($output);
        $this->fail('No exception has been raised');
    }

    protected function tearDown() {
        /*$modelo  = $this->obj->get_by(["nome_modelo" => 'xxx']);
       echo'============================='; print_r($modelo);

        if (!empty($modelo)) {
            $this->obj->setTable("modelo_input");
            $this->obj->delete_by(["id_modelo" => $modelo[0]->id_modelo]);

            $this->obj->setTable("modelo");
            $this->obj->delete_by(["id_modelo" => $modelo[0]->id_modelo]);

            $this->obj->dropModelo('xxx');
        }
        */

    }
}
