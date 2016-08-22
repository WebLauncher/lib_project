<?php 
	/**
	 * Base Tree Extender
	 */
	/**
	 * Model Extender with Tree functionality
	 * @ignore
	 * @package WebLauncher\Objects
	 */
	class BaseTree extends BaseExtender{
		/**
		 * @var bool $accept_all_methods Accept all called methods
		 */
		public $accept_all_methods=false;
		/**
		 * @var string $parent_id_field Parent id field name
		 */
		public $parent_id_field='parent_id';
		/**
		 * @var string $children_arr_key Key for children store
		 */
		public $children_arr_key='kids';
		
		/**
		 * Before delete handler
		 * @param string $id
		 */
		function before_delete($id){
			$children=$this->get_children($id);
			foreach($children as $v)
				$this->_model->delete($v[$this->_model->id_field]);
		}
		
		/**
		 * Get children for a node
		 * @param string $id
		 * @return array
		 */
		function get_children($id){
			return $this->_model->get_all('','','','','`'.$this->parent_id_field.'`='.sat($id));
		}
		
		/**
		 * Get path to the given $id
		 * @param string $id
		 * @return array
		 */
		function get_path($id){
			if($id!=0)
			{
				$row=$this->_model->get($id);
				$arr=array($id=>$id);
				return array_merge($arr,$this->get_path($row[$this->parent_id_field]));
			}
			else
				return array(0);
		}
		
		/**
		 * Get tree from current $id
		 * @param string $id
		 * @return array
		 */
		function get_tree($id=0)
		{
			$kids=$this->_model->get_all('','','','','`'.$this->parent_id_field.'`='.$id);
			$arr=array();
			if(count($kids))
				foreach($kids as $v)
				{
					$v[$this->children_arr_key]=$this->get_tree($v[$this->_model->id_field]);
					$arr[]=$v;				
				}
			return $arr;
		}
	}
?>