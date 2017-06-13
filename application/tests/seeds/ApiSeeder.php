<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class ApiSeeder extends Seeder {

	public function run() {
		$data = [
		    'id_teste_modelo' => 1,
			'nome' => "modelo_seed_api",
            'valor' => 100.00
		];
		$this->db->insert('teste_modelo', $data);
	}

}
