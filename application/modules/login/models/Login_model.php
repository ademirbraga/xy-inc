<?php

class Login_model extends MY_Model { 
	public $_table = 'usuario';
	
	public function logar($username, $password) {
		
		if (empty($password) || empty($username)) {
			return false;
		}
		
		$join = [
			["table" => "empresa emp", "clause" => "emp.id_empresa = usuario.id_empresa", "fields" => ["nome_empresa", "logo"]],
            ["table" => "unidade_negocio uneg", "clause" => "uneg.id_unidade_negocio = usuario.id_unidade_negocio", "fields" => ["nome_unidade", "cidade", "uf"]],
			["table" => "cargo c", "clause" => "c.id_cargo = usuario.id_cargo", "fields" => ["nome_cargo"]],
			["table" => "perfil p", "clause" => "p.id_perfil = usuario.id_perfil", "fields" => ["nome_perfil"]],
			["table" => "setor s", "clause" => "s.id_setor = usuario.id_setor", "fields" => ["nome_setor"]],
			["table" => "usuario sup", "clause" => "sup.id_usuario = usuario.id_superior", "fields" => ["nome_usuario as nome_superior"], "type" => "left"],
		];
		
		return $this->select("*", [
			"usuario.email"  => $username,
			"usuario.senha" => sha1($password.$username)
		], $join, [], true);
	}
}