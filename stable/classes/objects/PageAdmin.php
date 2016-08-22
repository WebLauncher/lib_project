<?php
/**
 * Controller class for back-end
 */
/**
 * Controller Class for back-end usage
 * @ignore
 * @package WebLauncher\Objects
 */
class Page extends _Page
{
	/**
	 * @var \Base $model Model for the current page to use
	 */
	var $model=null;
	
	/**
	 * Set model for current cotroller to use
	 * @param \Base $model
	 */
	public function set_model($model){
		if(is_a($model, 'Base'))
			$this->model=$model;
		elseif(is_string($model))
			$this->model=&$this->models->{$model};
	}
	
	/**
	 * Get gridview table for model display
	 * @param bool $data Load data or just header
	 */
	public function get_model_table($data=0){
		if($this->model)
			return $this->model->get_admin_table('table_'.get_class($this).'_'.get_class($this->model), $data);
		return '';
	}
}
?>