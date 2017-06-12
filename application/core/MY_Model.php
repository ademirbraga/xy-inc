<?php
/**
 * A base model with a series of CRUD functions (powered by CI's query builder),
 * validation-in-model support, event callbacks and more.
 *
 * @link http://github.com/jamierumbelow/codeigniter-base-model
 * @copyright Copyright (c) 2012, Jamie Rumbelow <http://jamierumbelow.net>
 */
class MY_Model extends CI_Model {
	
	/*
	 * --------------------------------------------------------------
	 * VARIABLES
	 * ------------------------------------------------------------
	 */
	
	/**
	 * This model's default database table.
	 * Automatically
	 * guessed by pluralising the model name.
	 */
	protected $_table;
	
	/**
	 * The database connection object.
	 * Will be set to the default
	 * connection. This allows individual models to use different DBs
	 * without overwriting CI's global $this->db connection.
	 */
	public $_database;
	
	/**
	 * This model's default primary key or unique identifier.
	 * Used by the get(), update() and delete() functions.
	 */
	public $primary_key = 'id';
	
	/**
	 * Support for soft deletes and this model's 'deleted' key
	 */
	protected $soft_delete = FALSE;
	protected $soft_delete_key = 'deleted';
	protected $_temporary_with_deleted = FALSE;
	protected $_temporary_only_deleted = FALSE;
	
	/**
	 * The various callbacks available to the model.
	 * Each are
	 * simple lists of method names (methods will be run on $this).
	 */
	protected $before_create = [];
	protected $after_create = [];
	protected $before_update = [];
	protected $after_update = [];
	protected $before_get = [];
	protected $after_get = [];
	protected $before_delete = [];
	protected $after_delete = [];
	protected $callback_parameters = [];
	
	/**
	 * Protected, non-modifiable attributes
	 */
	protected $protected_attributes = [];
	
	/**
	 * Relationship arrays.
	 * Use flat strings for defaults or string
	 * => array to customise the class name and primary key
	 */
	protected $belongs_to = [];
	protected $has_many = [];
	protected $_with = [];
	
	/**
	 * An array of validation rules.
	 * This needs to be the same format
	 * as validation rules passed to the Form_validation library.
	 */
	protected $validate = [];
	
	/**
	 * Optionally skip the validation.
	 * Used in conjunction with
	 * skip_validation() to skip data validation for any future calls.
	 */
	protected $skip_validation = FALSE;
	
	/**
	 * By default we return our results as objects.
	 * If we need to override
	 * this, we can, or, we could use the `as_array()` and `as_object()` scopes.
	 */
	protected $return_type = 'object';
	protected $_temporary_return_type = NULL;
	
	
	
	protected $rows;
	protected $page;
	protected $sord;
	protected $sidx;
	
	/*
	 * --------------------------------------------------------------
	 * GENERIC METHODS
	 * ------------------------------------------------------------
	 */
	
	/**
	 * Initialise the model, tie into the CodeIgniter superobject and
	 * try our best to guess the table name.
	 */
	public function __construct() {
		parent::__construct ();
		
		$this->load->helper ( 'inflector' );
		
		$this->_fetch_table ();
		
		$this->_database = $this->db;
		
		array_unshift ( $this->before_create, 'protect_attributes' );
		array_unshift ( $this->before_update, 'protect_attributes' );
		
		$this->_temporary_return_type = $this->return_type;
		
		$this->setPaginate();
	}
	
	public function setRows($rows) {
		$this->rows = $rows;
	}
	
	/*
	 * --------------------------------------------------------------
	 * CRUD INTERFACE
	 * ------------------------------------------------------------
	 */
	
	public function setTable($table) {
		$this->_table = $table;
	}

    public function getPostGresPK($modelo) {
        $query = "
        SELECT a.attname, format_type(a.atttypid, a.atttypmod) AS data_type
        FROM   pg_index i
          JOIN   pg_attribute a ON a.attrelid = i.indrelid
                           AND a.attnum = ANY(i.indkey)
        WHERE  i.indrelid = '{$modelo}'::regclass
          AND    i.indisprimary;";

        $query = $this->db->query($query);
        $pk = $query->result_array();
        return $pk[0]["attname"];
    }
	
	public function setPK() {
		//$fields = $this->db->field_data($this->_table);
        $this->primary_key = $this->getPostGresPK($this->_table);

		/*foreach ($fields as $field) {
			if ($field->primary_key) {
				$this->primary_key = $field->name;
			}
		}*/
	}
	
	
	/**
	 * Fetch a single record based on the primary key.
	 * Returns an object.
	 */
	public function get($primary_value) {
		$this->setPK();
		return $this->get_by ( $this->primary_key, $primary_value );
	}
	
	/*public function query($params = NULL) {
	
		$this->_prep_params($params);
	
		$this->_prep_joins($params);
	
		return $this->db->get($this->table_name);
	
	}*/
	
	
	/**
	 * Fetch a single record based on an arbitrary WHERE call.
	 * Can be
	 * any valid value to $this->_database->where().
	 */
	public function get_by() {
		$where = func_get_args ();
		
		if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE) {
			$this->_database->where ( $this->soft_delete_key, ( bool ) $this->_temporary_only_deleted );
		}
		
		$this->_set_where ( $where );
		
		$this->trigger ( 'before_get' );
		
		$row = $this->_database->get ( $this->_table )->{$this->_return_type ()} ();
		$this->_temporary_return_type = $this->return_type;
		
		$row = $this->trigger ( 'after_get', $row );
		
		$this->_with = [];
		$this->log();
		return $row;
	}
	
	/**
	 * Fetch an array of records based on an array of primary values.
	 */
	public function get_many($values) {
		$this->_database->where_in ( $this->primary_key, $values );
		
		return $this->get_all ();
	}
	
	/**
	 * Fetch an array of records based on an arbitrary WHERE call.
	 */
	public function get_many_by() {
		$where = func_get_args ();
		
		$this->_set_where ( $where );
		
		return $this->get_all ();
	}
	
	/**
	 * Fetch all the records in the table.
	 * Can be used as a generic call
	 * to $this->_database->get() with scoped methods.
	 */
	public function get_all() {
		$this->trigger ( 'before_get' );
		
		if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE) {
			$this->_database->where ( $this->soft_delete_key, ( bool ) $this->_temporary_only_deleted );
		}
		
		$result = $this->_database->get ( $this->_table )->{$this->_return_type ( 1 )} ();
		$this->_temporary_return_type = $this->return_type;
		
		foreach ( $result as $key => &$row ) {
			$row = $this->trigger ( 'after_get', $row, ($key == count ( $result ) - 1) );
		}
		
		$this->_with = [];
		$this->log();
		return $result;
	}
	
	public function queryByFile($query, $bindings = []) {
		$response            = new stdClass();
		$response->page      = 1;
		$response->total     = 10;
		
		$response->registros = $this->db->query($query, $bindings)->result();
		
		if (!empty($post['page']) && $post['page'] > 1) {
			$response->page = $post['page'];
			$response->total = $post['rows'] + (count($response->registros));
		}
		
		$response->records   = $this->countRegistros();
	
		$this->log();
	
		return $response;
	}
	
	public function selectByExample($fields = "*", $where = [], $joins = [], $order = [], $groupBy = null) {
		$response            = new stdClass();
        $response->page      = 1;
        $response->total     = 10;

        $post = $this->input->post(null);

        $this->setWhere($where);
		$this->setGroupBy($groupBy);
		
		$response->registros = array_map(function ($reg) {
			return (object)$reg;
		}, $this->select($fields, $where, $joins, $order));

        if (!empty($post['page']) && $post['page'] > 1) {
            $response->page = $post['page'];
            $response->total = $post['rows'] + (count($response->registros));
        }

        $this->limparFiltro($where);
		$response->records   = $this->countRegistros();
		return $response;
	}


	private function countRegistros() {
        $patterns = [
            "/limit(\s)*[0-9]*(\s)*,(\s)*[0-9]*/i",
            "/limit(\s)*[0-9]*(\s)*/i"
        ];
        $query = preg_replace($patterns, [' ', ' '], $this->db->last_query());

        $query = "select count(0) as total from (".$query . ") as tmp";
        $result = $this->db->query($query)->result_array()[0]['total'];
        $this->log();
        return $result;
    }
	
	private function setWhere(&$where) {
		$this->setDefaultWhere($where);
		
		if (!empty($where)) {
			foreach ($where as $key => $dado) {
				if ((stristr($key, "data_") ||  stristr($key, "created_") || stristr($key, "canceled_")) && !empty($dado)) {
					$where[$key] = implode("-", array_reverse(explode("/", $dado)));
				}
			}
		}
		$post = $this->input->post(null);
		
		if (!empty($post["filters"])) {
			$filters = json_decode($post["filters"]);
			$tables  = (!empty($post["tables"])) ? $post["tables"] : [];
			
			foreach ($filters->rules as $k=> $f) {
				
				if (!empty($tables)) {
					foreach ($tables as $key => $table) {
						if (in_array($f->field, $table['campos'])) {
							$f->field = $table['alias'].".".(!empty($table['alias_fields'][$f->field]) ? $table['alias_fields'][$f->field]: $f->field);
						}		
					}
				}
				$ret = $this->getOperatorValue($f->field, $f->data, $f->op);
			}
		}
	}
	/**
	 * recupera o operador da consulta de acordo com o valor informado pelo jqgrid
	 * 
	 * @param string $field
	 * @param string $value
	 * @param string $op
	 */
	private function getOperatorValue($field, $value, $op = "eq") {
		
		if ((stristr($field, "data_") ||  stristr($field, "created_") || stristr($field, "canceled_")) && !empty($value)) {
			$value = implode("-", array_reverse(explode("/", $value)));
		}
		
		switch($op) {
			case 'eq': 
				$this->db->where($field, $value); 
			break;
			case 'ne': 
				$this->db->where($field . ' != ', $value); 
			break;
			case 'lt': 
				$this->db->where($field . ' < ', $value); 
			break;
			case 'le': 
				$this->db->where($field . ' <= ', $value); 
			break;
			case 'gt': 
				$this->db->where($field . ' > ', $value); 
			break;
			case 'ge': 
				$this->db->where($field . ' >= ', $value); 
			break;
			
			case 'cn': 
				$this->db->like($field, $value);     
			break;
			case 'nc': 
				$this->db->not_like($field, $value); 
			break;
			
			case 'ni': 
				$value = explode(",", $value); 
				$this->db->where_not_in($field , $value); 
			break;
			
			case 'in':
				$value = explode(",", $value);
				$this->db->where_in($field , $value);
				break;
			
			case 'ew': 
				$this->db->like($field, $value, 'before'); 
			break;
			case 'bw': 
				$this->db->like($field, $value, 'after'); 
			break;

			case 'bn': 
				$this->db->not_like($field, $value, 'after'); 
			break;
			case 'en': 
				$this->db->not_like($field, $value, 'before'); 
			break;
		}
		return $field .'-'. $op .'-'. $value;
	}
	
	private function setGroupBy($groupBy) {
		if (!empty($groupBy)) {
			$this->db->group_by($groupBy);
		}
	}
	
	/**
	 * Set paginação das grids
	 * 
	 * @param [] $paginate
	 */
	public function setPaginate() {
		$paginate = $this->input->post(null);
		if (!empty($paginate["rows"])) {
			$this->rows = $paginate["rows"];
		}
		if (!empty($paginate["page"])) {
			$this->page = $paginate["page"];
		}
		if (!empty($paginate["sord"])) {
			$this->sord = $paginate["sord"];
		}
	}
	
	public function select($fields = "*", $where = [], $joins = [], $order = [], $unique = false, $limit = false) {
		$fields = $this->_set_fields($fields, $joins);
		
		$this->db->select($fields);
		$this->db->from($this->_table);
		if (!empty($joins)) {
			$this->_set_join($joins);
		}
		
		$this->setDefaultWhere($where);
		
		$this->limparFiltro($where);
		
		$this->db->where($where);
		$this->db->order_by($order);
		
		if ($limit || $this->rows > 0) {
			$rows = $limit ? $limit : $this->rows;
			$page = $this->page > 0 ? (($this->page - 1) * $this->rows) : 0;
			$this->db->limit($rows, $page);
		}
		
		$query = $this->db->get();	
		$this->log();
		$result = $query->result_array();
		
		$result = $this->formatDate($result);
		return ($unique) ? $result[0] : $result;
	}
	
	public function query($query) {
		$query = $this->db->query($query);
		$this->log();
		return $query->result_array();
	}
	
	private function setDefaultWhere(&$where) {
		$profile = $this->session->userdata('logged_in');
		$fields   = $this->db->list_fields($this->_table);
		
		$excludeModulos = ["relatorio", "lista"];
		$modulo         = $this->uri->segment(1);

		if ($profile["id_tipo_usuario"] != USUARIO_SUPER
		&& !empty($profile['id_empresa']) 
		&& in_array("id_empresa", $fields) 
		&& !in_array($modulo, $excludeModulos)) {
			$where[$this->_table.".id_empresa"] = $profile['id_empresa'];
		}

	}
	
	
	private function limparFiltro(&$where) {
		$filtrosGrid = ['_search', 'nd', 'rows', 'page', 'sidx', 'sord'];
		
		foreach ($where as $key => $filtro) {
			if (is_array($filtro)) {
				$this->limparFiltro($filtro);
			} else {
				if (in_array($key, $filtrosGrid) || empty($filtro)) {
					unset($where[$key]);
				} elseif ($filtro == 'null') {
					$this->db->where($key.' IS NULL', null, false);
					unset($where[$key]);
				} elseif ($filtro == 'is not null') {
					$this->db->where($key.' IS NOT NULL', null, false);
					unset($where[$key]);
				}
			}
		}
	}
	
	private function formatDate($dados) {
		foreach ($dados as $ch => $lstDados) {
			foreach ($lstDados as $key => $dado) {
				if ((stristr($key, "data_") ||  in_array($key, ["created_at", "canceled_at"])) && !empty($dado)) {
					
					if ($dado == "0000-00-00 00:00:00" || $dado == "0000-00-00") {
						$dados[$ch][$key] = "";
					} else {
						$date = date_create($dado);
						$format = strlen($dado) == 10 ? "d/m/Y" : "d/m/Y H:i:s";
						$dados[$ch][$key] =  date_format($date, $format);
					}
				} else if ((stristr($key, "valor_")) && !empty($dado)) {
					$dados[$ch][$key] =  number_format($dado, 2, ",", ".");
				}
			}
		}
		return $dados;
	}
	
	protected function _set_fields($fields = "*", $joins = []) {
		$_fields = ($fields == "*") ? ($this->_table .'.'. $fields) : $fields;
		
		if (!empty($joins)) {
			foreach ($joins as $join) {
				if (!empty($join["fields"])) {
					
					foreach ($join["fields"] as &$field) {
						
						$alias = explode(" ", $join["table"]);
						$alias = end($alias);
						
						$field = $alias . "." . $field;
					}
					
					$_fields .= ", " . implode(", ", $join["fields"]);
				}
			}
		}
		return $_fields;
	}
	
	
	public function saveOrUpdate($data, $skip_validation = FALSE) {
		$this->setData($data);

		if (empty($data[$this->primary_key])) {
			return $this->insert($data, $skip_validation);
		} else {
			unset($data["created_at"]);
			return $this->update($data[$this->primary_key], $data);
		}

	}
	
	/**
	 * Insert a new row into the table.
	 * $data should be an associative array
	 * of data to be inserted. Returns newly created ID.
	 */
	public function insert($data, $skip_validation = FALSE) {
		if ($skip_validation === FALSE) {
			$data = $this->validate ( $data );
		}
		
		if ($data !== FALSE) {
			$data = $this->trigger ( 'before_create', $data );

			$this->_database->insert($this->_table, $data);

			$insert_id = $this->_database->insert_id ();
			
			$this->log();
			
			$this->trigger ( 'after_create', $insert_id );
			
			return $insert_id;
		} else {
			return FALSE;
		}
	}

	public function setFiltroUsuario($tabela = "usuario") {
        $usuario = $this->session->userdata('logged_in');

        //se nao tiver subordinados deve ver somente o proprio PDI
        if (!empty($usuario['id_superior'])) {
            $this->db->where($tabela.".id_usuario = " . $usuario['id_usuario']);
        } else {
            //quando tiver subordinados deve ver o proprio  e os dos subordinados
            $this->db->where("($tabela.id_superior = ".$usuario['id_usuario']." OR $tabela.id_usuario = ".$usuario['id_usuario'].")");
        }
    }
	
	/**
	 * Insert multiple rows into the table.
	 * Returns an array of multiple IDs.
	 */
	public function insert_many($data, $skip_validation = FALSE) {
		$ids = [];
		foreach ( $data as $key => $row ) {
			$ids [] = $this->insert ( $row, $skip_validation, ($key == count ( $data ) - 1) );
			$this->log();
		}
		return $ids;
	}
	
	/**
	 * Updated a record based on the primary value.
	 */
	public function update($primary_value, $data, $skip_validation = FALSE) {
		$this->setPK();
		
		$data = $this->trigger ( 'before_update', $data );
		
		if ($skip_validation === FALSE) {
			$data = $this->validate ( $data );
		}
		
		if ($data !== FALSE) {
			$result = $this->_database->where($this->primary_key, $primary_value)
									  ->set($data)
									  ->update($this->_table);
			
			$this->trigger('after_update', [$data, $result]);
			$this->log();
			return ($result ? $primary_value : $result);
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Update many records, based on an array of primary values.
	 */
	public function update_many($primary_values, $data, $skip_validation = FALSE) {
		$data = $this->trigger ( 'before_update', $data );
		
		if ($skip_validation === FALSE) {
			$data = $this->validate ( $data );
		}
		
		if ($data !== FALSE) {
			$result = $this->_database->where_in ( $this->primary_key, $primary_values )->set ( $data )->update ( $this->_table );
			$this->log();
			$this->trigger ( 'after_update', array (
					$data,
					$result 
			) );
			
			return $result;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Updated a record based on an arbitrary WHERE clause.
	 */
	public function update_by() {
		$args = func_get_args ();
		$data = array_pop ( $args );
		
		$data = $this->trigger ( 'before_update', $data );
		
		if ($this->validate ( $data ) !== FALSE) {
			$this->_set_where ( $args );
			$this->log();
			$result = $this->_database->set ( $data )->update ( $this->_table );
			$this->trigger ( 'after_update', array (
					$data,
					$result 
			) );
			
			return $result;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Update all records
	 */
	public function update_all($data) {
		$data = $this->trigger ( 'before_update', $data );
		$result = $this->_database->set ( $data )->update ( $this->_table );
		$this->trigger ( 'after_update', array (
				$data,
				$result 
		) );
		
		return $result;
	}
	
	/**
	 * Delete a row from the table by the primary value
	 */
	public function delete($id) {
		$this->trigger ( 'before_delete', $id );
		
		$this->_database->where ( $this->primary_key, $id );
		$this->log();
		if ($this->soft_delete) {
			$result = $this->_database->update ( $this->_table, array (
					$this->soft_delete_key => TRUE 
			) );
			$this->log();
		} else {
			$result = $this->_database->delete ( $this->_table );
			$this->log();
		}
		
		$this->trigger ( 'after_delete', $result );
		
		return $result;
	}
	
	/**
	 * Delete a row from the database table by an arbitrary WHERE clause
	 */
	public function delete_by() {
		$where = func_get_args ();
		
		$where = $this->trigger ( 'before_delete', $where );
		
		$this->_set_where ( $where );
		$this->log();
		if ($this->soft_delete) {
			$result = $this->_database->update ( $this->_table, array (
					$this->soft_delete_key => TRUE 
			) );
		} else {
			$result = $this->_database->delete ( $this->_table );
		}
		$this->log();
		$this->trigger ( 'after_delete', $result );
		
		return $result;
	}
	
	/**
	 * Delete many rows from the database table by multiple primary values
	 */
	public function delete_many($primary_values) {
		$primary_values = $this->trigger ( 'before_delete', $primary_values );
		
		$this->_database->where_in ( $this->primary_key, $primary_values );
		
		if ($this->soft_delete) {
			$result = $this->_database->update ( $this->_table, array (
					$this->soft_delete_key => TRUE 
			) );
		} else {
			$result = $this->_database->delete ( $this->_table );
		}
		
		$this->trigger ( 'after_delete', $result );
		$this->log();
		return $result;
	}
	
	/**
	 * Truncates the table
	 */
	public function truncate() {
		$result = $this->_database->truncate ( $this->_table );
		
		return $result;
	}
	
	private function setData(&$data) {
		$this->setAuditoria($data);
		$fields   = $this->db->list_fields($this->_table);
		$registro = [];
		
		foreach ($fields as $field) {
			if (array_key_exists($field, $data)  && !$this->isEmpty($data[$field])) {
				$registro[$field] = $this->setValor($field, $data[$field]);
			}
		}
		
		$data = $registro;
		$this->setPK();
	}
	
	private function setValor($field, $valor) {
		if ((stristr($field, "data_") ||  in_array($field, ["created_at", "canceled_at"])) && !empty($valor)) {
			$data = explode(" ", $valor);
			return (implode("-", array_reverse(explode("/", $data[0])))) . " " . (!empty($data[1]) ? $data[1] : date("H:i:s"));
			
		} else if (stristr($field, "valor_") && !empty($valor)) {
			return str_replace(",", ".", str_replace(".", "", $valor));//1.000,00 -> 1000.00
		}
		return $valor;
	}
	
	private function setAuditoria(&$data) {
		$session   = $this->session->userdata('logged_in');
		$data["id_usuario_alteracao"] = $session['id_usuario'];
		$data["data_alteracao"] = date('Y-m-d H:i:s');
		$data['created_at'] = date('Y-m-d H:i:s');
	}
	
	private function isEmpty($valor) {
		return ($valor === '' || $valor === null || ($valor != '0' && empty($valor)));
	}
	
	/*
	 * --------------------------------------------------------------
	 * RELATIONSHIPS
	 * ------------------------------------------------------------
	 */
	public function with($relationship) {
		$this->_with [] = $relationship;
		
		if (! in_array ( 'relate', $this->after_get )) {
			$this->after_get [] = 'relate';
		}
		
		return $this;
	}
	public function relate($row) {
		if (empty ( $row )) {
			return $row;
		}
		
		foreach ( $this->belongs_to as $key => $value ) {
			if (is_string ( $value )) {
				$relationship = $value;
				$options = array (
						'primary_key' => 'id_'.$value,
						'model' => $value . '_model' 
				);
			} else {
				$relationship = $key;
				$options = $value;
			}
			
			if (in_array ( $relationship, $this->_with )) {
				$this->load->model ( $options ['model'], $relationship . '_model' );
				
				if (is_object ( $row )) {
					$row->{$relationship} = $this->{$relationship . '_model'}->get ( $row->{$options ['primary_key']} );
				} else {
					$row [$relationship] = $this->{$relationship . '_model'}->get ( $row [$options ['primary_key']] );
				}
			}
		}
		
		foreach ( $this->has_many as $key => $value ) {
			if (is_string ( $value )) {
				$relationship = $value;
				$options = array (
						'primary_key' => singular ( $this->_table ) . '_id',
						'model' => singular ( $value ) . '_model' 
				);
			} else {
				$relationship = $key;
				$options = $value;
			}
			
			if (in_array ( $relationship, $this->_with )) {
				$this->load->model ( $options ['model'], $relationship . '_model' );
				
				if (is_object ( $row )) {
					$row->{$relationship} = $this->{$relationship . '_model'}->get_many_by ( $options ['primary_key'], $row->{$this->primary_key} );
				} else {
					$row [$relationship] = $this->{$relationship . '_model'}->get_many_by ( $options ['primary_key'], $row [$this->primary_key] );
				}
			}
		}
		
		return $row;
	}
	
	/*
	 * --------------------------------------------------------------
	 * UTILITY METHODS
	 * ------------------------------------------------------------
	 */
	
	/**
	 * Retrieve and generate a form_dropdown friendly array
	 */
	function dropdown() {
		$args = func_get_args ();
		
		if (count ( $args ) == 2) {
			list ( $key, $value ) = $args;
		} else {
			$key = $this->primary_key;
			$value = $args [0];
		}
		
		$this->trigger ( 'before_dropdown', array (
				$key,
				$value 
		) );
		
		if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE) {
			$this->_database->where ( $this->soft_delete_key, FALSE );
		}
		
		$result = $this->_database->select ( array (
				$key,
				$value 
		) )->get ( $this->_table )->result ();
		
		$options = [];
		
		foreach ( $result as $row ) {
			$options [$row->{$key}] = $row->{$value};
		}
		
		$options = $this->trigger ( 'after_dropdown', $options );
		
		return $options;
	}
	
	
	public function countByExample($where = [], $joins = []) {
		if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE) {
			$this->_database->where ( $this->soft_delete_key, ( bool ) $this->_temporary_only_deleted );
		}
	
		$this->_set_where($where);
		
		$this->_set_join($joins);
	
		$result = $this->_database->count_all_results($this->_table);
		
		$this->log();
		
		return $result;
	}
	
	/**
	 * Fetch a count of rows based on an arbitrary WHERE call.
	 */
	public function count_by() {
		if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE) {
			$this->_database->where ( $this->soft_delete_key, ( bool ) $this->_temporary_only_deleted );
		}
		$where = func_get_args ();
		
		$profile = $this->session->userdata('logged_in');
		$fields   = $this->db->list_fields($this->_table);
		if (!empty($profile['id_empresa']) && in_array("id_empresa", $fields)) {
			$where[0][$this->_table.".id_empresa"] = $profile['id_empresa'];
		}
// 		$this->limparFiltro($where);
		$this->_set_where ( $where );
		
		$result = $this->_database->count_all_results ( $this->_table );
		$this->log();
		return $result;
	}
	
	/**
	 * Fetch a total count of rows, disregarding any previous conditions
	 */
	public function count_all() {
		if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE) {
			$this->_database->where ( $this->soft_delete_key, ( bool ) $this->_temporary_only_deleted );
		}
		
		return $this->_database->count_all ( $this->_table );
	}
	
	/**
	 * Tell the class to skip the insert validation
	 */
	public function skip_validation() {
		$this->skip_validation = TRUE;
		return $this;
	}
	
	/**
	 * Get the skip validation status
	 */
	public function get_skip_validation() {
		return $this->skip_validation;
	}
	
	/**
	 * Return the next auto increment of the table.
	 * Only tested on MySQL.
	 */
	public function get_next_id() {
		return ( int ) $this->_database->select ( 'AUTO_INCREMENT' )->from ( 'information_schema.TABLES' )->where ( 'TABLE_NAME', $this->_table )->where ( 'TABLE_SCHEMA', $this->_database->database )->get ()->row ()->AUTO_INCREMENT;
	}
	
	/**
	 * Getter for the table name
	 */
	public function table() {
		return $this->_table;
	}
	
	/*
	 * --------------------------------------------------------------
	 * GLOBAL SCOPES
	 * ------------------------------------------------------------
	 */
	
	/**
	 * Return the next call as an array rather than an object
	 */
	public function as_array() {
		$this->_temporary_return_type = 'array';
		return $this;
	}
	
	/**
	 * Return the next call as an object rather than an array
	 */
	public function as_object() {
		$this->_temporary_return_type = 'object';
		return $this;
	}
	
	/**
	 * Don't care about soft deleted rows on the next call
	 */
	public function with_deleted() {
		$this->_temporary_with_deleted = TRUE;
		return $this;
	}
	
	/**
	 * Only get deleted rows on the next call
	 */
	public function only_deleted() {
		$this->_temporary_only_deleted = TRUE;
		return $this;
	}
	
	/*
	 * --------------------------------------------------------------
	 * OBSERVERS
	 * ------------------------------------------------------------
	 */
	
	/**
	 * MySQL DATETIME created_at and updated_at
	 */
	public function created_at($row) {
		if (is_object ( $row )) {
			$row->created_at = date ( 'Y-m-d H:i:s' );
		} else {
			$row ['created_at'] = date ( 'Y-m-d H:i:s' );
		}
		
		return $row;
	}
	public function updated_at($row) {
		if (is_object ( $row )) {
			$row->updated_at = date ( 'Y-m-d H:i:s' );
		} else {
			$row ['updated_at'] = date ( 'Y-m-d H:i:s' );
		}
		
		return $row;
	}
	
	/**
	 * Serialises data for you automatically, allowing you to pass
	 * through objects and let it handle the serialisation in the background
	 */
	public function serialize($row) {
		foreach ( $this->callback_parameters as $column ) {
			$row [$column] = serialize ( $row [$column] );
		}
		
		return $row;
	}
	public function unserialize($row) {
		foreach ( $this->callback_parameters as $column ) {
			if (is_array ( $row )) {
				$row [$column] = unserialize ( $row [$column] );
			} else {
				$row->$column = unserialize ( $row->$column );
			}
		}
		
		return $row;
	}
	
	/**
	 * Protect attributes by removing them from $row array
	 */
	public function protect_attributes($row) {
		foreach ( $this->protected_attributes as $attr ) {
			if (is_object ( $row )) {
				unset ( $row->$attr );
			} else {
				unset ( $row [$attr] );
			}
		}
		
		return $row;
	}
	
	/*
	 * --------------------------------------------------------------
	 * QUERY BUILDER DIRECT ACCESS METHODS
	 * ------------------------------------------------------------
	 */
	
	/**
	 * A wrapper to $this->_database->order_by()
	 */
	public function order_by($criteria, $order = 'ASC') {
		if (is_array ( $criteria )) {
			foreach ( $criteria as $key => $value ) {
				$this->_database->order_by ( $key, $value );
			}
		} else {
			$this->_database->order_by ( $criteria, $order );
		}
		return $this;
	}
	
	/**
	 * A wrapper to $this->_database->limit()
	 */
	public function limit($limit, $offset = 0) {
		$this->_database->limit ( $limit, $offset );
		return $this;
	}
	
	/*
	 * --------------------------------------------------------------
	 * INTERNAL METHODS
	 * ------------------------------------------------------------
	 */
	
	/**
	 * Trigger an event and call its observers.
	 * Pass through the event name
	 * (which looks for an instance variable $this->event_name), an array of
	 * parameters to pass through and an optional 'last in interation' boolean
	 */
	public function trigger($event, $data = FALSE, $last = TRUE) {
		if (isset ( $this->$event ) && is_array ( $this->$event )) {
			foreach ( $this->$event as $method ) {
				if (strpos ( $method, '(' )) {
					preg_match ( '/([a-zA-Z0-9\_\-]+)(\(([a-zA-Z0-9\_\-\., ]+)\))?/', $method, $matches );
					
					$method = $matches [1];
					$this->callback_parameters = explode ( ',', $matches [3] );
				}
				
				$data = call_user_func_array ( array (
						$this,
						$method 
				), array (
						$data,
						$last 
				) );
			}
		}
		
		return $data;
	}
	
	/**
	 * Run validation on the passed data
	 */
	public function validate($data) {
		if ($this->skip_validation) {
			return $data;
		}
		
		if (! empty ( $this->validate )) {
			foreach ( $data as $key => $val ) {
				$_POST [$key] = $val;
			}
			
			$this->load->library ( 'form_validation' );
			
			if (is_array ( $this->validate )) {
				$this->form_validation->set_rules ( $this->validate );
				
				if ($this->form_validation->run () === TRUE) {
					return $data;
				} else {
					return FALSE;
				}
			} else {
				if ($this->form_validation->run ( $this->validate ) === TRUE) {
					return $data;
				} else {
					return FALSE;
				}
			}
		} else {
			return $data;
		}
	}
	
	/**
	 * Guess the table name by pluralising the model name
	 */
	private function _fetch_table() {
		if ($this->_table == NULL) {
			$this->_table = plural ( preg_replace ( '/(_m|_model)?$/', '', strtolower ( get_class ( $this ) ) ) );
		}
	}
	
	/**
	 * Guess the primary key for current table
	 */
	private function _fetch_primary_key() {
		if ($this->primary_key == NULl) {
			$this->primary_key = $this->_database->query ( "SHOW KEYS FROM `" . $this->_table . "` WHERE Key_name = 'PRIMARY'" )->row ()->Column_name;
		}
	}
	
	
	protected function _set_join($joins) {
		if (!empty($joins)) {
			foreach ($joins as $join) {
				if (!empty($join["table"]) && !empty($join["clause"])) {
					$this->_database->join($join["table"], $join["clause"], (!empty($join["type"]) ? $join["type"] : "inner"));
				}
			}
		}
	}
	/**
	 * Set WHERE parameters, cleverly
	 */
	protected function _set_where($params) {
		$this->limparFiltro($params);
		
		if (count ( $params ) == 1 && is_array ( $params [0] )) {
			foreach ( $params [0] as $field => $filter ) {
				if (is_array ( $filter )) {
					$this->_database->where_in ( $field, $filter );
				} else {
					if (is_int ( $field )) {
						$this->_database->where ( $filter );
					} else {
						$this->_database->where ( $field, $filter );
					}
				}
			}
		} else if (count ( $params ) == 1) {
			$this->_database->where ( $params [0] );
		} else if (count ( $params ) == 2) {
			if (is_array ( $params [1] )) {
				$this->_database->where_in ( $params [0], $params [1] );
			} else {
				$this->_database->where ( $params [0], $params [1] );
			}
		} else if (count ( $params ) == 3) {
			$this->_database->where ( $params [0], $params [1], $params [2] );
		} else {
			if (is_array ( $params [1] )) {
				$this->_database->where_in ( $params [0], $params [1] );
			} else {
				$this->_database->where ( $params [0], $params [1] );
			}
		}
	}
	
	
	public function isUsuarioSuperior() {
		$profile = $this->session->userdata('logged_in');
		$usuario = $this->get_by(['id_usuario' => $profile['id_usuario']]);
		return empty($usuario["id_superior"]);
	}
	
	/**
	 * Return the method name for the current return type
	 */
	protected function _return_type($multi = FALSE) {
		$method = ($multi) ? 'result' : 'row';
		return $this->_temporary_return_type == 'array' ? $method . '_array' : $method;
	}
	
	public function log() {
		$str = str_repeat("*", 150);
		file_put_contents("/tmp/ccgc.log", $this->db->last_query().PHP_EOL.$str.PHP_EOL, FILE_APPEND);
	}
}