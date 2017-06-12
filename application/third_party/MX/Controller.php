<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/** load the CI class for Modular Extensions **/
require dirname(__FILE__).'/Base.php';

/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link	http://codeigniter.com
 *
 * Description:
 * This library replaces the CodeIgniter Controller class
 * and adds features allowing use of modules and the HMVC design pattern.
 *
 * Install this file as application/third_party/MX/Controller.php
 *
 * @copyright	Copyright (c) 2015 Wiredesignz
 * @version 	5.5
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 **/
class MX_Controller 
{
	public $autoload = array();
	public $response;
	
	public function __construct() 
	{
		$class = str_replace(CI::$APP->config->item('controller_suffix'), '', get_class($this));
		log_message('debug', $class." MX_Controller Initialized");
		Modules::$registry[strtolower($class)] = $this;	
		
		/* copy a loader instance and initialize */
		$this->load = clone load_class('Loader');
		$this->load->initialize($this);	
		
		/* autoload module items */
		$this->load->_autoloader($this->autoload);
		
		$controller = $this->router->fetch_module();
		$action     = $this->router->fetch_method();
		
		$controllerValido = (!empty($controller) && $controller != "error")? $controller : false;
		
		if ((!isset($this->session->userdata['logged_in']) && $action != 'logar')) {
			//redirect(site_url('login/logar'));
		}
		
		if (!in_array($controller, ["error", "welcome", "lista", "login"])) {
			//$this->verificarPermissoes();
		}
	}
	
	public function __get($class) {
		return CI::$APP->$class;
	}
	
	public function verificarPermissoes() {
		$session   = $this->session->userdata('logged_in');
		$controller = $this->router->fetch_module();
		$action     = $this->router->fetch_method();
		
		$this->load->model('usuario/Usuario_model', 'usuario');
		
		$joins = [
			["table" => "perfil_permissao pp", "clause" => "pp.id_perfil    = usuario.id_perfil"],
			["table" => "perfil per", 		   "clause" => "per.id_perfil   = usuario.id_perfil"],
			["table" => "permissao pm", 	   "clause" => "pm.id_permissao = pp.id_permissao", "fields" => ["codigo", "nome_permissao"]],
		];
		
		$this->usuario->setRows(0);
		
		$permissoes = $this->usuario->select("*", [
			"usuario.id_usuario" => $session['id_usuario'],
			"usuario.ativo" => 1
		], $joins);
		
		$controllers = array_column($permissoes, "codigo");

        $usuario = $this->session->userdata('logged_in');
		
		if ($controller && !empty($controllers) && !in_array($controller, $controllers)) {
			redirect(site_url('error/permissao'));
		}else {
			$listaModulos = array_combine(
				array_column($permissoes, "codigo"),
				array_column($permissoes, "nome_permissao")
			);
			$this->session->set_userdata('controller', $listaModulos[$controller]);
			
			$listaActions = [
				"index" => "Ver Todos",
				"registro" => "Registro"
			];
			
			$this->session->set_userdata('action', $listaActions[$action]);
		}
	}
	
	public function create_thumb($imagem, $largura, $altura) {
		$config['image_library']  = 'GD';
		$config['source_image']   = str_replace("-", "/", $imagem);
		$config['maintain_ratio'] = TRUE;
		$config['width']          = $largura;
		$config['quality']        = "100%";
		$config['height']         = $altura;
		$config['create_thumb']   = TRUE;
		$this->load->library('image_lib', $config);
		$retorno['error'] = false;
	
		if (!$this->image_lib->resize()) {
			$retorno['error'] = $this->image_lib->display_errors();
		}
		return $retorno;
	}
}