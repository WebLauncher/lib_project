<?php
/**
 * Model Class
 */

/**
 * Model Class
 * @uses ArrayAccess
 * @property-read int $total_rows Total rows currently returned from database
 * @property-read DbManager $db Current manager used by model
 * @package WebLauncher\Objects
 */
class _Base implements ArrayAccess {
	/**
	 * @var string $table DB table for model
	 */
	public $table = '';
	/**
	 * @var string $id_field ID field of table
	 */
	public $id_field = 'id';
	/**
	 * @var string $order_field Order by field
	 */
	public $order_field = 'order';
	/**
	 * @var string $active_field Active row field
	 */
	public $active_field = 'is_active';
	/**
	 * @var string $process_row_func Process row function
	 */
	public $process_row_func = 'process_row';
	/**
	 * @var array $uses Other models used locally
	 */
	public $uses = '';
	/**
	 * @var \ModelsManager $models Reference to models
	 */
	public $models = null;
	/**
	 * @var array $extends Extensions of the current model
	 */
	public $extends = array();
	/**
	 * @var object $Extensions Instanciated extensions of the current model
	 */
	public $Extensions = null;
	/**
	 * @var bool $process If it should use process row
	 */
	public $process = true;
	/**
	 * @var array $joins Model joins
	 */
	public $joins = array();

	/**
	 * @var int $_total_rows Total rows returned
	 */
	protected $_total_rows = -1;
	/**
	 * @var string $_last_inserted_id Last inserted id
	 */
	protected $_last_inserted_id = '';
	/**
	 * @var array $_extend Extend model
	 */
	protected $_extend = array();

	/**
	 * COnstructor
	 * @return
	 */
	function __construct() {
		$this -> init_extesions();
	}

	/**
	 * Init extensions
	 */
	function init_extesions() {
		if (count($this -> extends)) {
			if (!$this -> Extensions)
				$this -> Extensions = new BaseExtenderList($this);
			$this -> extend($this -> extends);
		}
	}

	/**
	 * Extend model with more functionality of other classes
	 * @param object $class
	 */
	function extend($class) {
		$this -> Extensions -> add($class);
	}

	/**
	 * Get magic method
	 * @param string $name
	 */
	function __get($name) {
		if ($name == 'total_rows') {
			if ($this -> _total_rows < 0)
				$this -> _total_rows = $this -> db -> countTotalRows();
			return $this -> _total_rows;
		}
		if ($name == 'db') {
			global $dal;
			return $dal -> db;
		}
		if ($this -> uses && is_array($this -> uses) && in_array($name, $this -> uses)) {
			return $this -> models -> $name;
		} else {
			if (isset($this -> db -> tables[$name]))
				return $this -> db -> tables[$name];
			elseif (strpos($name, 'tbl_') == 0)
				return substr($name, 4, strlen($name) - 4);
		}
		$trace = debug_backtrace();
		trigger_error('Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE);
		return null;
	}

	/**
	 * Call magic method
	 * @param string $name
	 * @param array $arguments
	 */
	function __call($name, $arguments) {
		// process old methods
		$results = array();
		preg_match_all('/[A-Z][^A-Z]*/', $name, $results);
		if (isset($results[0])) {
			$method = implode('_', array_map('strtolower', $results[0]));
			if (method_exists($this, $method))
				return call_user_func_array(array($this, $method), $arguments);
		}
		if (is_object($this -> Extensions))
			if ($this -> Extensions -> method_exists($name))
				return call_user_func_array(array($this -> Extensions, $name), $arguments);
		trigger_error('Model for ' . $this -> table . ' does not have method "' . $name . '" defined!');
	}

	/**
	 * Deletes row from database table mantaining order field
	 * @param object $id
	 * @param object $id_field [optional]
	 * @param object $order_field [optional]
	 * @return
	 */
	public function delete($id) {
		if ($this -> table && $id) {
			if (is_array($id)) {
				foreach ($id as $sid)
					$this -> delete($sid);
			} else {
				$this -> _before_delete($id);
				$this -> delete_cond('`' . $this -> id_field . '`="' . $id . '"');
				$this -> _after_delete($id);
			}
		}
	}

	/**
	 * Before delete method
	 * @param object $id
	 */
	private function _before_delete(&$id) {
		$obj = $this -> get($id);
		// maintain order
		if (isset($obj[$this -> order_field]) && $obj[$this -> order_field] >= 0)
			$this -> builder('update ' . $this -> table . ' set `' . $this -> order_field . '`=`' . $this -> order_field . '`-1 where `order`>' . $obj[$this -> order_field]) -> execute();
		$this -> before_delete($id);
	}

    /**
     * Before delete public method
     * @param string $id
     */
    public function before_delete(&$id)
    {
    }

	/**
	 * After delete method
	 * @param object $id
	 */
	private function _after_delete($id) {
		$this -> after_delete($id);
	}

	/**
	 * After delete public method
	 * @param string $id
	 */
	public function after_delete($id = '') {
	}

	/**
	 * Delete from table on found condition
	 * @return
	 * @param object $condition
	 */
	function delete_cond($condition) {
		if ($this -> table && $condition)
			$this -> builder() -> delete() -> where($this -> __process_cond($condition)) -> execute();
	}

	/**
	 * Return the row from the current table at the given id
	 * @return array|null Data found in the model database table looking by $this->id_field equal to $id
	 * @param object $id
	 * @example: In model use: $this->get(1); or $this->get('hash');
	 */
	public function get($id) {
		if (is_array($id))
			return $this -> get_all('', '', '', '', '`' . $this -> id_field . '` in (' . implode(',', $id) . ')');
		if ($this -> table && $id)
			return $this -> get_cond('`' . $this -> id_field . '`=' . sat($id));
		return '';
	}

	/**
	 * Returns the first row found by condition
	 * @return array|null Data found in the model database table looking by condition $condition
	 * @param string|array $condition
	 * @example In model use: $this->get_cond('field="value"')
	 */
	public function get_cond($condition) {
		$arr = $this -> builder() -> select() -> join($this -> joins) -> where($this -> __process_cond($condition)) -> first(); ;
		if (count($arr))
			return $this -> _process_row($arr);
		return '';
	}

	/**
	 * Returns the rows from the current table limited and sorted using the parameters
	 * @return array|null
	 * @param int $skip [optional]
	 * @param int $nr_rows [optional]
	 * @param string|array $order_by [optional]
	 * @param string|array $order_dir [optional]
	 * @param string|array $cond [optional]
	 * @param bool $calc_rows [optional]
	 * @param array $search_fields [optional]
	 * @param string $keyword [optional]
	 * @param array $group_by [optional]
	 * @param string $having [optional]
	 * @example In model use: $this->get_all(0,10,'field','desc','field1="value"',true,array('field1','field2',...),'keyword')
	 */
	public function get_all($skip = '', $nr_rows = '', $order_by = '', $order_dir = '', $cond = '', $calc_rows = false, $search_fields = '', $keyword = '', $group_by = '', $having = '') {
		return $this -> get_colls('*', $skip, $nr_rows, $order_by, $order_dir, $cond, $calc_rows, $search_fields, $keyword, $group_by, $having);
	}

	/**
	 * Returns the rows and required columns from the current table limited and
	 * sorted using the parameters
	 * @return
	 * @param array $colls [optional]
	 * @param int $skip [optional]
	 * @param int $nr_rows [optional]
	 * @param string|array $order_by [optional]
	 * @param string|array $order_dir [optional]
	 * @param string|array $cond [optional]
	 * @param bool $calc_rows [optional]
	 * @param array $search_fields [optional]
	 * @param string $keyword [optional]
	 * @param array $group_by [optional]
	 * @param string $having [optional]
	 * @example In model use: $this->get_cools(array('field1','field1'),0,10,'field','desc','field1="value"',true,array('field1','field2',...),'keyword')
	 */
	public function get_colls($colls = array('*'), $skip = '', $nr_rows = '', $order_by = '', $order_dir = '', $cond = '', $calc_rows = false, $search_fields = '', $keyword = '', $group_by = '', $having = '') {
		$cond_text = '';
		$order_text = '';
		$skip_text = '';
		$builder = $this -> builder();
		if ($calc_rows)
			$builder -> calculate();
		if (!is_array($colls))
			$colls = explode(',', $colls);
		$builder -> select($colls) -> join($this -> joins);

		$cond_s = '';
		if ($search_fields && $keyword)
			$cond_s = $this -> _searchLikeCond($search_fields, $keyword);

		if ($cond != '')
			$cond_text = '(' . $cond . ')' . ($cond_s ? ' and (' . $cond_s . ')' : '');
		else
			$cond_text = ($cond_s ? '(' . $cond_s . ')' : '');

		$builder -> where($cond_text);

		if ($group_by)
			$builder -> group($group_by);

		if ($having)
			$builder -> having($having);

		if ($order_by != '')
			if (is_array($order_by))
				$builder -> order($order_by, $order_dir ? (is_array($order_dir) ? $order_dir : array($order_dir)) : array());
			else
				$builder -> order(array($order_by), array($order_dir));
		$arr = array();
		if ($skip >= 0 && $nr_rows > 0)
			$builder -> limit($skip, $nr_rows);
		if ($this -> table) {
			$arr = $builder -> execute();
		}
		if ($calc_rows)
			$this -> _total_rows = $this -> db -> countTotalRows();
		return $this -> _process_array($arr);
	}

	/**
	 * Processes returned array
	 * @return
	 * @param object $arr
	 */
	private function _process_array($arr) {
		return $this -> process ? array_map(array($this, '_process_row'), $arr) : $arr;
	}

	/**
	 * Process row private
	 * @param array $row
	 */
	private function _process_row($row) {
		if ($this -> process) {
			if (is_callable($this -> process_row_func))
				return call_user_func($this -> process_row_func, $row);
			else
				return call_user_func(array($this, $this -> process_row_func), $row);
		}
		return $row;
	}

	/**
	 * Count all found rows in table of model by condition
	 * @return int|0
	 * @param string|array $cond [optional]
	 * @example In model use: $this->count_all('field1="value"');
	 */
	public function count_all($cond = '') {
		if ($this -> table) {
			$count = $this -> builder() -> select(array('count(*)')) -> join($this -> joins) -> where($this -> __process_cond($cond)) -> value();
			return $count ? $count : 0;
		}
		return 0;
	}

	/**
	 * Decreases or increase `order` field of the row indentified by id
	 * @return
	 * @param string|int $id - id of the object to increase or descrease order
	 * @param int $order - [-1,1] for decrease and increase
	 * @param object $field
	 */
	public function set_order($id, $order, $field = '') {
		if ($field)
			$this -> order_field = $field;
		if ($id && $obj = $this -> get($id)) {
			$this -> builder('update ' . $this -> table . ' set `' . $this -> order_field . '`=`' . $this -> order_field . '`-(' . $order . ') where `' . $this -> order_field . '`=' . ($obj[$this -> order_field] + $order)) -> execute('query');
			$this -> builder('update ' . $this -> table . ' set `' . $this -> order_field . '`=`' . $this -> order_field . '`+(' . $order . ') where `' . $this -> id_field . '`=' . $id) -> execute('query');
			return 1;
		}
		return 0;
	}

	/**
	 * Sets the value of the field 'is_active' to the value given
	 * @return
	 * @param object $id
	 * @param object $value
	 * @param object $field
	 */
	public function set_active($id, $value, $field = '') {
		if ($field)
			$this -> active_field = $field;
		$this -> update_field($id, $this -> active_field, $value);
	}

	/**
	 * Inserts into the current table of model according to the parameter array
	 * @return int|string Inserted id
	 * @param array $params ex. array('name'=>'John','function'=>'operator');
	 * @example In model use:
	 * $this->insert(array(
	 * 		'field1'=>'value1'
	 * 		'field2'=>'value2'
	 * 		...
	 * ))
	 */
	public function insert($params) {
		if (is_array($params)) {
			$this -> _before_insert($params);
			$this -> _last_inserted_id = $this -> builder() -> insert(array_keys($params)) -> values($params) -> execute();
			$this -> _after_insert($params);
			return $this -> last_id();
		}
		return '';
	}

    /**
     * Before insert callback private method
     * @param array $params
     */
    private function _before_insert(&$params)
    {
        $this->before_insert($params);
    }

    /**
     * Before insert callback public method
     * @param array $params
     */
    public function before_insert(&$params)
    {
    }

	/**
	 * After insert callback private method
	 * @param object $params
	 */
	private function _after_insert($params) {
		$this -> after_insert($params);
	}

	/**
	 * After insert callback public method
	 * @param object $params
	 */
	public function after_insert($params = '') {
	}

	/**
	 * Inserts into the current table according to the parameter array multiple rows
	 * @return
	 * @param object $fields
	 * @param array $params
	 */
	public function insert_multiple($fields, $params) {
		if (is_array($params)) {
			$builder = $this -> builder();
			$builder -> insert($fields);

			foreach ($params as $k => $v)
				$builder -> values($v);
			$builder -> execute();
			return $this -> db -> last_id();
		}
		return '';
	}

	/**
	 * Update in current table of model according to the parameter array
	 * @return
	 * @param array $params ex. array('name'=>'John','function'=>'operator');
	 * @param string|array $cond '`id`=1
	 * @example In model use:
	 * $this->update(array(
	 * 		'field1'=>'value1'
	 * 		'field2'=>'value2'
	 * 		...
	 * ),'field1="value1"');
	 */
	function update($params, $cond = '') {
		if (!$cond) {
			if (isset_or($params[$this -> id_field]))
				$cond = '`' . $this -> id_field . '`="' . $params[$this -> id_field] . '"';
			else {
				trigger_error('Wrong call of function ' . get_class($this) . '->update()', E_USER_NOTICE);
				return null;
			}
		}
		if (is_array($params) && $cond) {
			$this -> _before_update($params, $cond);
			$this -> builder() -> update($params) -> where($this -> __process_cond($cond)) -> execute();
			$this -> _after_update($params, $cond);
		}
	}

    /**
     * Before update callback private method
     * @param array $params
     * @param object $cond
     */
    private function _before_update(&$params, &$cond)
    {
        $this->before_update($params, $cond);
    }

    /**
     * Before update callback public method
     * @param array $params
     * @param object $cond
     */
    public function before_update(&$params, &$cond)
    {
    }

	/**
	 * After update callback private method
	 * @param array $params
	 * @param object $cond
	 */
	private function _after_update($params, $cond) {
		$this -> after_update($params, $cond);
	}

	/**
	 * After update callback public method
	 * @param array $params
	 * @param object $cond
	 */
	public function after_update($params, $cond) {
	}

	/**
	 * Updates the field from the table at the given id
	 * @return
	 * @param object $id
	 * @param object $field
	 * @param object $value
	 * @example In model use: $this->update_field(1,'fiel1','value1');
	 */
	public function update_field($id, $field, $value) {
		if ($id && $field) {
			if (is_array($id))
				$cond = '`' . $this -> id_field . '` in (' . implode(',', $id) . ')';
			else
				$cond = '`' . $this -> id_field . '`="' . $id . '"';
			$this -> update_field_cond($field, $value, $cond);
		}
	}

	/**
	 * Updates the field from the table at the given cond
	 * @return
	 *
	 * @param object $field
	 * @param object $value
	 * @param object $cond
	 * @example In model use: $this->update_field_cond('fiel1','value1','field1="value1"');
	 */
	public function update_field_cond($field, $value, $cond) {
		if ($field)
			$this -> builder() -> update(array($field => $value)) -> where($this -> __process_cond($cond)) -> execute();
	}

	/**
	 * Checks if it exist in the current table a row with the field equal to a value
	 * @return [true/false]
	 * @param string|array $field
	 * @param string|int $value
	 * @example In model use: $this->exists('field1','value1');
	 * @example Look in multiple fields $this->exists(array('field1'=>'value1','field2'=>'value2'));
	 */
	function exists($field, $value = null) {
		if (is_null($value))
			return $this -> exists_cond('`' . $this -> id_field . '`="' . $field . '"');
		if ($field && $value)
			if (is_array($field) && is_array($value) && count($field) == count($value))
				return $this -> exists_cond(array_combine($field, $value));
			else
				return $this -> exists_cond('`' . $field . '`="' . $value . '"');
		return false;
	}

	/**
	 * Processes array to cond
	 * @param object $cond
	 */
	private function __process_cond($cond) {
		if (is_array($cond))
			$cond = implode(' AND ', array_map(function($v, $k) {
				return '`' . $k . '`=' . sat($v);
			}, $cond, array_keys($cond)));
		return $cond;
	}

	/**
	 * Checks if there exista a row with the given condition in this table
	 * @return [true/false]
	 * @param string|array $cond
	 * @example In model use: $this->exists_cond('field1="value1"');
	 */
	function exists_cond($cond) {
		if ($cond)
			if ($this -> builder() -> select() -> where($this -> __process_cond($cond)) -> first())
				return true;
		return false;
	}

	/**
	 * get a value from a selected field
	 * @return int|string the value of the field from the record $id
	 * @param int|string $id
	 * @param string $field
	 * @example In model use: $this->get_field(1,'field1');
	 */
	function get_field($id, $field) {
		if ($id)
			return $this -> get_field_cond($field, '`' . $this -> id_field . '`="' . $id . '"');
		return '';
	}

	/**
	 * Gets field from the first found row from table on condition
	 * @return field value
	 * @param string $field
	 * @param string|array $cond
	 * @example In model use: $this->get_field_cond('field1','field2="value2"');
	 */
	function get_field_cond($field, $cond) {
		if ($cond && $field)
			return $this -> builder() -> select(array('`' . $field . '`')) -> join($this -> joins) -> where($this -> __process_cond($cond)) -> limit(0, 1) -> value();
		return '';
	}

	/**
	 * Generates search condition using Like
	 * @return condition string
	 * @param object $fields
	 * @param object $kwd
	 */
	function _searchLikeCond($fields, $kwd) {
		$cond_s = '1=0 ';

		$kwd = stripslashes($kwd);
		if ((strpos($kwd, '"') == 0 && strrpos($kwd, '"') == strlen($kwd) - 1) || (strpos($kwd, '\'') == 0 && strrpos($kwd, '\'') == strlen($kwd) - 1)) {
			$kwd = substr($kwd, 1, strlen($kwd) - 2);
			foreach ($fields as $f)
				$cond_s .= 'or lower(`' . $f . '`) like lower("%' . $kwd . '%") ';
		} else {
			foreach ($fields as $v) {
				$cond_s .= 'or lower(`' . $v . '`) like lower("%' . $kwd . '%") ';
			}
			$kwds = explode(' ', $kwd);
			if (count($kwds) > 1)
				foreach ($kwds as $k)
					foreach ($fields as $f)
						$cond_s .= 'or lower(`' . $f . '`) like lower("%' . $k . '%") ';
		}
		return $cond_s;
	}

	/**
	 * Process row returned
	 * @param array $row
	 */
	function process_row($row) {
		return $row;
	}

	/**
	 * Process array of rows
	 * @param array $arr
	 */
	function process_array($arr) {
		return $this -> _process_array($arr);
	}

	/**
	 * Process object
	 * @param object $obj
	 */
	function process_obj($obj) {
		return $obj;
	}

	/**
	 * ArrayAccess exists
	 * @param string $id
	 */
	function offsetExists($id) {
		return $this -> exists($id);
	}

	/**
	 * ArrayAccess get
	 * @param string $id
	 */
	function offsetGet($id) {
		$obj = new DbRowObject($this -> get($id), $this);
		return $this -> process_obj($obj);
	}

	/**
	 * ArrayAccess set
	 * @param string $id
	 * @param array $value
	 */
	function offsetSet($id, $value) {
		if (is_array($value)) {
			if ($id)
				$this -> update($value, '`' . $this -> id_field . '`="' . $id . '"');
			else
				$this -> _last_inserted_id = $this -> insert($value);
		}
	}

	/**
	 * ArrayAccess unset
	 * @param string $id
	 */
	function offsetUnset($id) {
		$this -> delete($id);
	}

	/**
	 * Last inserted id
	 * @return int|string Last inserted id
	 */
	function last_id() {
		return $this -> _last_inserted_id;
	}

	/**
	 * Return a query builder for this model
	 * @param string $query
	 * @return QueryBuilder
	 */
	function builder($query = '') {
		return new QueryBuilder($this, $query);
	}

	/**
	 * Executes a query on this models db connection
	 * @param string $query  query string to execute
	 * @param array $args  arguments to insert in prepared query
	 * @example In model use: $this->query('select * from table1 where field1=?',array('value1'));
	 */
	function query($query, $args = array()) {
		return $this -> db -> query($query, $args);
	}

	/**
	 * To string method
	 */
	function __toString() {
		return $this -> table;
	}

	/**
	 * Clone method
	 */
	function __clone() {

	}

	/**
	 * Create a temporary join with another table
	 * @return Base clone of the current model with added join
	 * @param string $table table to make the join to
	 * @param string|array $condition [optional] condition to use for join
	 * @param string $type [optional] type of join to use [LEFT, INNER, RIGHT, OUTER] default: ''
	 * @example In model use: $this->join('table2','table1.field1=table2.field2','LEFT')->get(1)
	 */
	function join($table, $condition = '', $type = '') {
		$obj = clone $this;
		$obj -> joins[] = array('table' => $table, 'on' => $condition, 'type' => $type);
		return $obj;
	}

}
?>