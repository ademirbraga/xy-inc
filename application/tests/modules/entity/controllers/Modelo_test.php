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

    private $config;

    public function test_index_get() {
        $this->config =& load_class('Config', 'core');
        $this->config->set_item('rest_auth', false);
        $this->config->set_item('auth_library_class', '');
        $this->config->set_item('auth_library_function', '');

        $this->request('GET', '/entity');
        $this->assertResponseCode(REST_Controller::HTTP_OK);
    }

    public function test_index() {
        $output = $this->request('GET', 'modelo/index');
        echo'AAAAAAAAAAAAAAAAAAAAAAA';
        print_r($output);
        $this->assertContains('<title>Page Title</title>', $output);
    }


    public function test_index_ajax_call() {
        $output = $this->ajaxRequest('GET', 'modelo/getModelos');
        $expected = '{"name":"John Smith","age":33}';
        $this->assertEquals($expected, $output);
    }

}
