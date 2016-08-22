<?php
/**
 * Query Builder
 */

/**
 * Query builder Class
 * @package WebLauncher\Objects
 */
class QueryBuilder
{
	/**
	 * @var string $query Current query
	 */
	private $query='';
	/**
	 * @var string $table Table used
	 */
	private $table='';
	/**
	 * @var /DbManager $db DB Manager object
	 */
	private $db=null;
	/**
	 * @var string $query_type Query type 
	 */
	private $query_type='';
	/**
	 * @var bool $calculate Calculate lines flag
	 */
	private $calculate=false;
	/**
	 * @var int $no_values No values returned
	 */
	private $no_values=0;
	/**
	 * @var array $args Aguments to be pushed
	 */
	private $args=array();

	/**
	 * Constructor
	 * @param string $table
	 * @param string $query
	 */
	function __construct($table,$query='')
	{
		if(is_a($table,'Base'))
		{
			$this->table=&$table->table;
			$this->db=&$table->db;
		}
		else
		{
			$this->table=$table;
			global $dal;
			$this->db=&$dal->db;
		}
		$this->query=$query;
	}
	
	/**
	 * Append to current query
	 * @param string $sql_text
	 * @param string $separator
	 */
	function append($sql_text,$separator=' ')
	{
		$this->query.=($sql_text.$separator);
	}
	
	/**
	 * Add having to query
	 */
	function having($condition){
		if($condition)
			$this->append('having '.$condition);
		return $this;
	}
	
	/**
	 * Start update query
	 * @param array $fields
	 * @param bool $escape
	 */
	function update($fields=array(),$escape=true)
	{
		$this->query_type='query';
		$this->append('update');
		$this->append('`'.$this->table.'` set ');

		if($no_fields=count($fields))
		{
			$count=0;
			foreach($fields as $k=>$f)
			{
				if($escape) $this->append('`','');
				$this->append($k,'');
				if($escape) $this->append('`','');
				$this->append('=','');
				if($escape){
					$this->args[]=$f;
					$this->append('?');
				}
				else
					$this->append($f);
				if($count<$no_fields-1)
					$this->append(',');
				$count++;
			}
		}
		return $this;
	}
	
	/**
	 * Start insert query
	 * @param array $fields
	 */
	function insert($fields=array())
	{
		$this->query_type='insert';
		$this->append('insert into');
		$this->append('`'.$this->table.'`');

		if($no_fields=count($fields))
		{
			$this->append('(');
			$count=0;
			foreach($fields as $k=>$f)
			{
				if($count<$no_fields-1)
					$this->append('`'.$f.'`',',');
				else
					$this->append('`'.$f.'`');
				$count++;
			}
			$this->append(') values');
		}
		return $this;
	}
	
	/**
	 * Add values to query
	 * @param array $values
	 * @param bool $escape
	 */
	function values($values,$escape=true)
	{
		if($no_fields=count($values))
		{
			if($this->no_values)
			{
				$this->append('',',');
				$this->query_type='query';
			}
			$this->append('(');
			$count=0;
			foreach($values as $k=>$f)
			{
				if($escape){
					$this->args[]=$f;
					$this->append('?');
				}
				else
					$this->append($f);
				if($count<$no_fields-1)
					$this->append(',');
				$count++;
			}
			$this->append(')');
			$this->no_values++;
		}
		return $this;
	}
	
	/**
	 * Start delete query
	 */
	function delete()
	{
		$this->query_type='query';
		$this->append('delete from');
		$this->append('`'.$this->table.'`');

		return $this;
	}
	
	/**
	 * Start select query
	 * @param array $fields
	 */
	function select($fields=array())
	{
		$this->query_type='select';
		$this->append('select');
		if($this->calculate)
			$this->append('SQL_CALC_FOUND_ROWS');
		if($no_fields=count($fields))
		{
			$count=0;
			foreach($fields as $k=>$f)
			{
				if($count<$no_fields-1)
					$this->field($f,$k);
				else
					$this->field($f,$k,' ');
				$count++;
			}
		}
		else
		{
			$this->append('`'.$this->table.'`.*');
		}
		$this->append('from `'.$this->table.'`');
		return $this;
	}
	
	/**
	 * Calculate total rows
	 */
	function calculate()
	{
		$this->calculate=true;
	}
	
	/**
	 * Add field to query
	 * @param string $field
	 * @param string $as
	 * @param string $separator
	 */
	function field($field,$as=0,$separator=',')
	{
		if($as && !is_numeric($as))
			$this->append($field.' as '.$as,$separator);
		else
			$this->append($field,$separator);
		return $this;
	}
	
	/**
	 * Add join to query
	 * @param string|array $table
	 * @param string $condition
	 * @param string $join_type
	 */
	function join($table,$condition='',$join_type='')
	{
		if(is_array($table)){
			if(count($table))
				foreach($table as $v)
					$this->_append_join($v['table'],$v['on'],$v['type']);	
		}
		else{
			$this->_append_join($table,$condition,$join_type);
		}
		return $this;
	}
	
	/**
	 * Private append join
	 * @param string $table
	 * @param string $condition
	 * @param string $join_type
	 */
	private function _append_join($table,$condition='',$join_type=''){
		$this->append($join_type.' join');
		$this->append('`'.$table.'`');
		if($condition)
			$this->append('on '.$condition);
	}
	
	/**
	 * Add group to query
	 * @param array $fields
	 */
	function group($fields)
	{
		if($no_fields=count($fields))
		{
			$this->append('group by');
			foreach($fields as $k=>$v)
			{
				if($k<$no_fields-1)
					$this->append($v,',');
				else
					$this->append($v);
			}
		}
		return $this;
	}
	
	/**
	 * Add order to query
	 * @param array $fields
	 * @param array $directions
	 */
	function order($fields,$directions)
	{
		if($no_fields=count($fields))
		{
			$this->append('order by');
			foreach($fields as $k=>$v)
			{
				if($k<$no_fields-1)
					$this->append('`'.$v.'`'.(isset($directions[$k])?' '.$directions[$k]:''),',');
				else
					$this->append('`'.$v.'`'.(isset($directions[$k])?' '.$directions[$k]:''));
			}
		}
		return $this;
	}
	
	/**
	 * Add where to query
	 * @param string $condition
	 */
	function where($condition)
	{
		if($condition)
			$this->append('where '.$condition);
		return $this;
	}
	
	/**
	 * Add limit to query
	 * @param int $start
	 * @param int $no_rows
	 */
	function limit($start=0,$no_rows='')
	{
		$this->append('limit '.$start.','.$no_rows);
		return $this;
	}
	
	/**
	 * Execute current query
	 * @param string $query_type
	 */
	function execute($query_type='')
	{
		if($query_type)$this->query_type=$query_type;
		if($this->query_type)
		{
			$func='execute_'.$this->query_type;
			$return=$this->$func();
			$this->reset();
			return $return;
		}
	}
	
	/**
	 * Reset current query
	 */
	function reset()
	{
		$this->args=array();
		$this->query='';
		$this->query_type='';
		$this->no_values=0;
	}
	
	/**
	 * Get first row from query response
	 * @return array
	 */
	function first()
	{
		return $this->db->getRow($this->query,$this->args);
	}
	
	/**
	 * Get first value from the query response
	 * @return object
	 */
	function value()
	{
		return $this->execute_one();
	}
	
	/**
	 * Execute query for value return
	 */
	private function execute_one()
	{
		return $this->db->getOne($this->query,$this->args);
	}
	
	/**
	 * Execute query for all rows returned by query
	 */
	private function execute_select()
	{
		return $this->db->getAll($this->query,$this->args);
	}
	
	/**
	 * Execute simple query with no response
	 */
	private function execute_query()
	{
		$this->db->query($this->query,$this->args);
	}
	
	/**
	 * Execute insert query
	 */
	private function execute_insert()
	{
		$this->db->query($this->query,$this->args);
		return $this->db->last_id();
	}
	
	/**
	 * Start new query
	 */
	function new_query()
	{
		$this->append(';');
		$this->query_type='query';
		return $this;
	}
	
	/**
	 * __toString Magic return this current query
	 */
	function __toString(){
		return $this->query;
	}
}

?>