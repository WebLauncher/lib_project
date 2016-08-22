<?php
	/**
	 * Model extender base class
	 */
	/**
	 * Model Extender base class
	 * @package WebLauncher\Objects
	 */
	class BaseExtender {
		/**
		 * @var \Base $model Model that is attached to
		 */
		protected $_model=null;
		/**
		 * @var bool $accept_all_methods Accept all methods
		 */
		public $accept_all_methods=false;
		/**
		 * Constructor
		 * @param \Base $model
		 */
		function __construct($model){
			$this->_model=$model;
		}
		/**
		 * Init method
		 */
		function init(){
			
		}
	}
?>