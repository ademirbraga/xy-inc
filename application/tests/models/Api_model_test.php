<?php

class Api_model_test extends TestCase {

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        $CI =& get_instance();
        $CI->load->library('Seeder');
        $CI->seeder->call('ApiSeeder');
    }

    public function setUp() {
        $this->obj = $this->newModel('api/Api_model');
    }

    public function getDados() {


        $expected = [
            1 => 'Book',
            2 => 'CD',
            3 => 'DVD',
        ];
        $list = $this->obj->getDados("product", 5);
        foreach ($list as $category) {
            $this->assertEquals($expected[$category->id_product], $category->nome);
        }
    }

}