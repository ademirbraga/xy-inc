<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class EntitySeeder extends Seeder {

	private $table = 'modelo';

	public function run() {
		/*$this->db->truncate($this->table);

		$data = [
			'id_modelo' => 1,
			'nome_modelo' => 'teste_prd',
            'ativo' => true,
            'descricao' => 'desc teste_prd'
		];
		$this->db->insert($this->table, $data);

        $data = [
            'id_modelo' => 2,
            'nome_modelo' => 'teste_cli',
            'ativo' => true,
            'descricao' => 'desc teste_cli'
        ];
		$this->db->insert($this->table, $data);

        $data = [
            'id_modelo' => 3,
            'nome_modelo' => 'teste_ord',
            'ativo' => true,
            'descricao' => 'desc teste_ord'
        ];
		$this->db->insert($this->table, $data);
		*/
	}

}
