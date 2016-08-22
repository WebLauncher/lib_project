<?php
/** 
 * Scripts Manager
 */

/**
 * Javascript JQuery Scripts Generator/Manager Class
 * @package WebLauncher\Managers
 */
class ScriptManager
{
	/**
	 * @var string $content generated content
	 */
	var $content='';
	/**
	 * @var string $after_load after load  content
	 */
	var $after_load='';
	/**
	 * @var array $forms forms used
	 */
	var $forms=array();
	
	/**
	 * Constructor
	 */
	function __construct()
	{
	}
	
	/**
	 * Get generated script
	 */
	function get_script()
	{
		$this->content='jQuery(document).ready(function() {
			'.$this->after_load.$this->process_validators().'
		});';

		return $this->content;
	}
	
	/**
	 * Get validators script
	 */
	function process_validators()
	{
		$content='';
		foreach($this->forms as $form)
		{
			$content.=$form->get_script();
		}
		return $content;
	}
	
	/**
	 * Add javascript validator
	 * @param string $form_id
	 * @param string $name
	 * @param string $rule
	 * @param string $message
	 */
	function add_validator($form_id,$name,$rule,$message)
	{
		if(!isset($this->forms[$form_id]))
		{
			$this->forms[$form_id]=new JsFormValidator($form_id);
		}
		$this->forms[$form_id]->add_validator($name,$rule,$message);
		
		$this->save();
	}

	/**
	 * Save script in session
	 */
	function save()
	{
		global $page;
		$page->session['script']=$this->get_script();
	}
}

?>