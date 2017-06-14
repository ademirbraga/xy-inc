<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Entity_test extends TestCase {

    private $config;

    public function xtest_index_get() {
        $this->config =& load_class('Config', 'core');
        $this->config->set_item('rest_auth', false);
        $this->config->set_item('auth_library_class', '');
        $this->config->set_item('auth_library_function', '');

        $this->request('GET', '/entity');
        $this->assertResponseCode(REST_Controller::HTTP_OK);
    }

    public function test_index_get() {
        $output = $this->ajaxRequest('GET', 'entity');
        $expected = '{"draw":0,"recordsTotal":0,"recordsFiltered":0,"data":[]}';

        echo 'saida:';print_r($output);
        echo 'esperado:';print_r($expected);
        $this->assertEquals($expected, $output);
    }

}