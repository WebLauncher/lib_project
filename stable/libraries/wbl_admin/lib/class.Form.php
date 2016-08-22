<?php

	class Wbl_Form_FieldTypes
	{
		/**
		 *  Show just value text with no edit.
		 *  @var string
		 */
		const NONE = 'none';
		/**
		 * Show text field.
		 *  @var string
		 */
		const TEXT = 'text';
		/**
		 * Show ajax binded field.
		 *  @var string
		 */
		const AJAX = 'ajax';
		/**
		 *  Show checkbox field.
		 *  @var string
		 */
		const CHECKBOX = 'checkbox';
		/**
		 * Show checkboxes list.
		 *  @var string
		 */
		const CHECKBOXLIST = 'checkboxlist';
		/**
		 * Show file browse field.
		 *  @var string
		 */
		const FILE = 'file';
		/**
		 * Show hidden field.
		 *  @var string
		 */
		const HIDDEN = 'hidden';
		/**
		 * Show html editing field (CKEditor).
		 *  @var string
		 */
		const HTMLEDITOR = 'htmleditor';
		/**
		 * Show image.
		 *  @var string
		 */
		const IMAGE = 'image';
		/**
		 * Show url to a different page.
		 *  @var string
		 */
		const LINK = 'link';
		/**
		 * Show list picker field.
		 *  @var string
		 */
		const LISTPICKER = 'listpicker';
		/**
		 * Show password field.
		 *  @var string
		 */
		const PASSWORD = 'password';
		/**
		 * Show radio list for images selecting.
		 *  @var string
		 */
		const RADIOIMAGES = 'radioimages';
		/**
		 * Show radio list.
		 *  @var string
		 */
		const RADIOLIST = 'radiolist';
		/**
		 * Show select box field.
		 *  @var string
		 */
		const SELECT = 'select';
		/**
		 * Show slider field.
		 *  @var string
		 */
		const SLIDER = 'slider';
		/**
		 * Show textarea field.
		 *  @var string
		 */
		const TEXTAREA = 'textarea';
		/**
		 * Show calendar text field
		 * @var string
		 */
		const CALENDAR = 'calendar';
		/**
		 * Show autocomplete text field
		 * @var string
		 */
		const AUTOCOMPLETE = 'autocomplete';
		/**
		 * Show progress bar text field
		 * @var string
		 */
		const PROGRESSBAR = 'progressbar';
	}

	class Wbl_Form_Field
	{
		public $label = '';
		public $type = Wbl_Form_FieldTypes::NONE;
		public $attributes = array();

		function __construct($label, $type, $attributes)
		{
			$attributes['type'] = $type ? $type : Wbl_Form_FieldTypes::NONE;
			$this -> label = $label;
			$this -> attributes = $attributes;
		}

		public function generate()
		{
			$arr=$this->attributes;
			$arr['label']=$this->label;			
			return $arr;
		}

	}

	class Wbl_Form_Zone
	{
		public $name = '';
		public $description = '';
		public $content = array();
		public $current_location = null;

		function __construct($name, $description = '')
		{
			$this -> name = $name;
			$this -> description = $description;
		}

		function generate()
		{
			$zones = array();
			$zones['name'] = $this -> name;
			$zones['description'] = $this -> description;

			foreach($this->content as $k => $v)
			{
				$cls = get_class($v);
				switch($cls)
				{
					case 'Wbl_Form_Layout_Row' :
						$zones['content'][$k]['type'] = 'row';
						break;
					case 'Wbl_Form_Field' :
						$zones['content'][$k]['type'] = 'field';
						break;					
				}
				$zones['content'][$k]['value']=$v -> generate();
			}
			return $zones;
		}

		function addContent($content)
		{
			if(!is_a($content,'Wbl_Form_Field'))
				$this -> current_location = $content;
			$this -> content[] = $content;
		}

		function get_current_location()
		{
			if(is_null($this -> current_location))
				return $this;
			else
				return $this -> current_location -> get_current_location();
		}

		function back_current_location()
		{
			if(is_null($this -> current_location -> current_location))
			{
				$this -> current_location = null;
				return $this;
			}
			else
				return $this -> current_location -> back_current_location();
		}

	}

	class Wbl_Form_Layout_Col implements ArrayAccess
	{
		public $width = '100%';
		public $zones = array();
		public $current_location = null;

		function __construct()
		{

		}

		function addZone($name, $description = '')
		{
			$this -> current_location = new Wbl_Form_Zone($name, $description);
			$this -> zones[] = $this -> current_location;
		}

		function addField($field)
		{
			$this -> zones[] = $field;
		}

		function offsetExists($zone)
		{
			return isset($this -> zones[$zone]);
		}

		function offsetGet($zone)
		{
			if(!isset($this -> zones[$zone]))
				$this -> zones[$zone] = new Wbl_Form_Layout_Zone();
			return $this -> zones[$zone];
		}

		function offsetSet($row, $value)
		{
			if(is_a($value, 'Wbl_Form_Zone') || is_a($value, 'Wbl_Form_Layout_Row') || is_a($value, 'Wbl_Form_Field'))
			{
				if($row)
					$this -> zones[$zone] = $value;
				else
				{
					if(!is_a($value, 'Wbl_Form_Field'))
						$this -> current_location = $value;
					$this -> zones[] = $value;
				}
			}
		}

		function offsetUnset($zone)
		{
			unset($this -> zones[$zone]);
		}

		function generate()
		{
			$zones = array();
			$zones['width'] = $this -> width;
			foreach($this->zones as $k => $v)
			{
				$cls = get_class($v);
				switch($cls)
				{
					case 'Wbl_Form_Layout_Row' :
						$zones['content'][$k]['type']='row'; 						
						break;
					case 'Wbl_Form_Zone' :
						$zones['content'][$k]['type']='zone';
						break;
					case 'Wbl_Form_Field' :
						$zones['content'][$k]['type']='field';
						break;
				}
				$zones['content'][$k]['value']=$v -> generate();
			}
			return $zones;
		}

		function get_current_location()
		{
			if(is_null($this -> current_location))
			{
				return $this;
			}
			else
				return $this -> current_location -> get_current_location();
		}

		function back_current_location()
		{
			if(is_null($this -> current_location -> current_location))
			{
				$this -> current_location = null;
				return $this;
			}
			else
				return $this -> current_location -> back_current_location();
		}

	}

	class Wbl_Form_Layout_Row implements ArrayAccess
	{
		public $cols = array();
		public $current_location = null;

		function __construct()
		{
			$this -> current_location = new Wbl_Form_Layout_Col();
			$this -> cols[] = $this -> current_location;
		}

		function offsetExists($col)
		{
			return isset($this -> cols[$row]);
		}

		function offsetGet($col)
		{
			if(!isset($this -> cols[$col]))
				$this -> cols[$col] = new Wbl_Form_Layout_Col();
			return $this -> cols[$col];
		}

		function offsetSet($col, $value)
		{

			if(is_a($value, 'Wbl_Form_Layout_Col'))
			{
				if($col)
					$this -> cols[$col] = $value;
				else
				{
					$this -> current_location = $value;
					$this -> cols[] = $value;
				}
				$this -> _fix_width();
			}
		}

		function offsetUnset($row)
		{
			unset($this -> rows[$row]);
		}

		function _fix_width()
		{
			$width = floor(100 / count($this -> cols));
			foreach($this->cols as $k => $v)
			{
				$this -> cols[$k] -> width = $width . '%';
			}
		}

		function generate()
		{
			$cols = array();
			foreach($this->cols as $k => $v)
			{
				$cols['cols'][$k] = $v -> generate();
			}
			return $cols;
		}

		function get_current_location()
		{
			if(is_null($this -> current_location))
				return $this;
			else
				return $this -> current_location -> get_current_location();
		}

		function back_current_location()
		{
			if(is_null($this -> current_location -> current_location))
			{
				$this -> current_location = null;
				return $this;
			}
			else
				return $this -> current_location -> back_current_location();
		}

	}

	/**
	 * Form Layout Class
	 */
	class Wbl_Form_Layout implements ArrayAccess
	{
		public $rows = array();
		public $current_location = null;

		function __construct()
		{
			$this -> current_location = new Wbl_Form_Layout_Row();
			$this -> rows[] = $this -> current_location;
		}

		function offsetExists($row)
		{
			return isset($this -> rows[$row]);
		}

		function offsetGet($row)
		{
			if(!isset($this -> rows[$row]))
				$this -> rows[$row] = new Wbl_Form_Layout_Row();
			return $this -> rows[$row];
		}

		function offsetSet($row, $value)
		{
			if(is_a($value, 'Wbl_Form_Layout_Row'))
			{
				if($row)
					$this -> rows[$row] = $value;
				else
				{
					$this -> current_location = $value;
					$this -> rows[] = $value;
				}
			}
		}

		function offsetUnset($row)
		{
			unset($this -> rows[$row]);
		}

		function generate()
		{
			$rows = array();
			foreach($this->rows as $k => $v)
			{
				$rows['rows'][$k] = $v -> generate();
			}
			return $rows;
		}

		function get_current_location()
		{
			if(is_null($this -> current_location))
				return null;
			else
				return $this -> current_location -> get_current_location();
		}

		function back_current_location()
		{
			if(is_null($this -> current_location) || is_null($this -> current_location -> current_location))
			{
				$this -> current_location = null;
				return null;
			}
			else
				return $this -> current_location -> back_current_location();
		}

		function startRow()
		{
			$this -> endRow();
			$obj = $this -> get_current_location();
			if(is_null($obj))
			{
				$this -> current_location = new Wbl_Form_Layout_Row();
				$this[] = $this -> current_location;
			}
			elseif(is_a($obj, 'Wbl_Form_Zone'))
			{
				$row = new Wbl_Form_Layout_Row();
				$obj -> addContent($row);
			}
			elseif(is_a($obj, 'Wbl_Form_Layout_Col'))
			{
				$row = new Wbl_Form_Layout_Row();
				$obj[] = $row;
			}
		}

		function endRow()
		{
			$obj = $this -> get_current_location();
			while(!is_a($obj, 'Wbl_Form_Zone') && !is_a($obj, 'Wbl_Form_Layout_Col') && !is_null($obj))
			{
				$obj = $this -> back_current_location();
			}
		}

		function startCol()
		{
			$this -> endCol();
			$obj = $this -> get_current_location();
			if(is_a($obj, 'Wbl_Form_Layout_Row'))
			{
				$col = new Wbl_Form_Layout_Col();
				$obj[] = $col;
			}
		}

		function endCol()
		{
			$obj = $this -> get_current_location();
			while(!is_a($obj, 'Wbl_Form_Layout_Row'))
			{
				$obj = $this -> back_current_location();
			}
		}

		function startZone($name, $description = '')
		{
			$this -> endZone();
			$obj = $this -> get_current_location();
			if(is_a($obj, 'Wbl_Form_Layout_Col'))
			{
				$zone = new Wbl_Form_Zone($name, $description);
				$obj[] = $zone;
			}
		}

		function endZone()
		{
			$obj = $this -> get_current_location();
			while(!is_a($obj, 'Wbl_Form_Layout_Col'))
			{
				$obj = $this -> back_current_location();
			}
		}

		function addField($label, $type, $attributes)
		{

			$obj = $this -> get_current_location();
			if(is_a($obj, 'Wbl_Form_Zone'))
			{
				$field = new Wbl_Form_Field($label, $type, $attributes);
				$obj -> addContent($field);
			}
			elseif(is_a($obj, 'Wbl_Form_Layout_Col'))
			{
				$field = new Wbl_Form_Field($label, $type, $attributes);
				$obj[] = $field;
			}

		}

	}

	/**
	 * Form Class
	 */
	class Wbl_Form implements ArrayAccess
	{
		var $id = '';
		var $name = '';
		var $action = '';
		var $method = 'post';
		var $width = '';

		// flags
		var $use_translations = true;
		var $show_btn_reset = false;
		var $show_btn_submit_return = false;

		// texts
		var $text_btn_submit = 'Submit';
		var $text_btn_submit_return = 'Submit then show list';
		var $text_btn_cancel = 'Cancel';
		var $text_btn_reset = 'Reset';

		// links
		var $link_btn_cancel = '';

		// zones
		var $zones = array();
		var $layout = '';

		// field
		var $current_field = '';

		// form
		var $form = array();

		var $fields_names = array();

		/**
		 * Constructor
		 * @return
		 */
		function __construct($name = '', $id = '', $action = '')
		{
			$rand = base64_encode(microtime());
			if($name)
			{
				$this -> name = $name;
				$this -> id = $name;
			}
			if($id)
				$this -> id = $id;
			$this -> action = $action;

			if(!$this -> name)
				$this -> name = 'form_name_' . $rand;
			if(!$this -> id)
				$this -> id = 'form_id_' . $rand;
			$this -> layout = new Wbl_Form_Layout();
		}

		function offsetExists($row)
		{
			return isset($this -> layout[$row]);
		}

		function offsetGet($row)
		{
			return $this -> layout[$row];
		}

		function offsetSet($row, $value)
		{
			$this -> layout[$row] = $value;
		}

		function offsetUnset($row)
		{
			unset($this -> layout[$row]);
		}

		/**
		 * Start new form zone.
		 * @param $name
		 * @param $description
		 */
		public function startZone($name, $description = '')
		{
			$this -> layout -> startZone($name, $description);
		}

		public function endZone()
		{
			$this -> layout -> endZone();
		}

		public function startRow()
		{
			$this -> layout -> startRow();
		}

		public function endRow()
		{
			$this -> layout -> endRow();
		}

		public function startCol()
		{
			$this -> layout -> startCol();
		}

		public function endCol()
		{
			$this -> layout -> endCol();
		}

		/**
		 * Start field adding generally.
		 * @param $name
		 * @param $type
		 */
		public function startField($label = '', $type = '')
		{
			$rand = base64_encode(microtime());
			$this -> current_field = ($label ? $label : 'no_label_' . $rand);
			$this -> _setAttr('type', ($type ? $type : Wbl_Form_FieldTypes::none));
		}

		function generate()
		{
			$this -> form['name'] = $this -> name;
			$this -> form['id'] = $this -> id;
			$this -> form['type'] = 'new';
			$this -> form['action'] = $this -> action;
			$this -> form['method'] = $this -> method;
			$this -> form['width'] = $this -> width;
			$this -> form['use_translations'] = $this -> use_translations;
			$this -> form['show_btn_reset'] = $this -> show_btn_reset;
			$this -> form['show_btn_submit_return'] = $this -> show_btn_submit_return;
			$this -> form['btn_submit_text'] = $this -> text_btn_submit;
			$this -> form['btn_submit_return_text'] = $this -> text_btn_submit_return;
			$this -> form['btn_reset_text'] = $this -> text_btn_reset;
			$this -> form['btn_cancel_text'] = $this -> text_btn_cancel;
			$this -> form['btn_cancel_link'] = $this -> link_btn_cancel;
			$this -> form['layout'] = $this -> layout -> generate();
			return $this -> form;
		}

		protected function _addField($label, $type, $attributes)
		{
			if((isset_or($attributes['name']) && !in_array($attributes['name'], $this -> fields_names)) || !isset_or($attributes['name']))
			{
				if(isset_or($attributes['name']))
					$this -> fields_names[] = $attributes['name'];
				$this -> layout -> addField($label, $type, $attributes);
			}
			else
			{
				trigger_error('Field name "' . $attributes['name'] . '" already used in the form "' . $this -> name . '" in the form generator!');
			}
		}

		public function addField($label, $type, $attributes)
		{
			$this->_addField($label, $type, $attributes);
		}

	}

	class Wbl_Form_Generator extends Wbl_Form
	{
		/**
		 * Start form zone.
		 * @param $name
		 * @param $description
		 */
		function startZone($name = '', $description = '')
		{
			parent::startZone($name, $description);
			return $this;
		}

		function endZone()
		{
			parent::endZone();
			return $this;
		}

		function startCol()
		{
			parent::startCol();
			return $this;
		}

		function endCol()
		{
			parent::endCol();
			return $this;
		}

		function startRow()
		{
			parent::startRow();
			return $this;
		}

		function endRow()
		{
			parent::endRow();
			return $this;
		}

		/**
		 * Adds a field with no editing just text display.
		 * @param $label
		 * @param $description
		 * @param $value
		 */
		function addNone($label = '', $description = '', $value = '', $script = '')
		{
			$attributes = array(
				'value' => $value,
				'description' => $description
			);
			$this -> _addField($label, Wbl_Form_FieldTypes::NONE, $attributes);
			return $this;
		}

		/**
		 * Adds a select field with the prefered options
		 * @param $label
		 * @param $name
		 * @param $value
		 * @param $default
		 * @param $default_show
		 * @param $default_text
		 * @param $options
		 * @param $option_field_value
		 * @param $option_field_text
		 * @param $description
		 * @param $validators
		 */
		function addSelect($label = '', $name = '', $value = '', $default = '', $default_show = false, $default_text = '', $options = array(), $option_field_value = '', $option_field_text = '', $description = '', $validators = array(), $script = '')
		{
			$attributes = array(
				'name' => $name,
				'value' => $value,
				'default' => $default,
				'description' => $description,
				'default_show' => $default_show,
				'default_text' => $default_text,
				'options' => $options,
				'option_field_value' => $option_field_value,
				'option_field_text' => $option_field_text,
				'validate' => $validators
			);
			$this -> _addField($label, Wbl_Form_FieldTypes::SELECT, $attributes);
			return $this;
		}

		/**
		 * Add hidden field.
		 * @param $label
		 * @param $name
		 * @param $value
		 * @param $validators
		 */
		function addHidden($label = '', $name = '', $value = '', $validators = array(), $script = '')
		{
			$attributes = array(
				'name' => $name,
				'value' => $value,
				'validate' => $validators
			);
			$this -> _addField($label, Wbl_Form_FieldTypes::HIDDEN, $attributes);
			return $this;
		}

		/**
		 * Add calendar field.
		 * @param unknown_type $label
		 * @param unknown_type $name
		 * @param unknown_type $value
		 * @param unknown_type $description
		 * @param unknown_type $validators
		 */
		public function addCalendar($label = '', $name = '', $value = '', $description = '', $validators = array(), $script = '')
		{
			$attributes = array(
				'name' => $name,
				'value' => $value,
				'description' => $description,
				'validate' => $validators
			);
			$this -> _addField($label, Wbl_Form_FieldTypes::CALENDAR, $attributes);
			return $this;
		}

		/**
		 * Add checkbox field.
		 * @param $label
		 * @param $name
		 * @param $value
		 * @param $checked
		 * @param $description
		 * @param $validators
		 */
		public function addCheckbox($label = '', $name = '', $value = '', $checked = false, $description = '', $validators = array(), $script = '')
		{
			$attributes = array(
				'name' => $name,
				'value' => $value,
				'description' => $description,
				'checked' => $checked,
				'validate' => $validators
			);
			$this -> _addField($label, Wbl_Form_FieldTypes::CHECKBOX, $attributes);
			return $this;
		}

		/**
		 * Add file browse field.
		 * @param $label
		 * @param $name
		 * @param $value
		 * @param $description
		 * @param $validators
		 */
		function addFile($label = '', $name = '', $value = '', $description = '', $validators = array(), $script = '')
		{
			$attributes = array(
				'name' => $name,
				'value' => $value,
				'description' => $description,
				'validate' => $validators
			);
			$this -> _addField($label, Wbl_Form_FieldTypes::FILE, $attributes);
			return $this;
		}

		/**
		 * Add html editor field (CKEditor)
		 * @param $label
		 * @param $name
		 * @param $value
		 * @param $description
		 * @param $validators
		 */
		function addHtmleditor($label = '', $name = '', $value = '', $description = '', $validators = array(), $script = '')
		{
			$attributes = array(
				'name' => $name,
				'value' => $value,
				'description' => $description,
				'validate' => $validators
			);
			$this -> _addField($label, Wbl_Form_FieldTypes::HTMLEDITOR, $attributes);
			return $this;
		}

		/**
		 * Add textarea field.
		 * @param $label
		 * @param $name
		 * @param $value
		 * @param $cols
		 * @param $rows
		 * @param $description
		 * @param $validators
		 */
		function addTextarea($label = '', $name = '', $value = '', $cols = '', $rows = '', $description = '', $validators = array(), $script = '')
		{
			$attributes = array(
				'name' => $name,
				'value' => $value,
				'description' => $description,
				'cols' => $cols,
				'rows' => $rows,
				'validate' => $validators
			);
			$this -> _addField($label, Wbl_Form_FieldTypes::TEXTAREA, $attributes);
			return $this;
		}

		/**
		 * Add image field.
		 * @param $label
		 * @param $title
		 * @param $url
		 * @param $description
		 */
		function addImage($label = '', $title = '', $url = '', $description = '', $script = '')
		{
			$attributes = array(
				'value' => $title,
				'description' => $description,
				'url' => $url
			);
			$this -> _addField($label, Wbl_Form_FieldTypes::IMAGE, $attributes);
			return $this;
		}

		/**
		 * Add link with label in form.
		 * @param $label
		 * @param $text
		 * @param $href
		 * @param $target
		 * @param $description
		 */
		function addLink($label = '', $text = '', $href = '', $target = '', $description = '', $script = '')
		{
			$attributes = array(
				'value' => $text,
				'description' => $description,
				'href' => $href,
				'target' => $target
			);
			$this -> _addField($label, Wbl_Form_FieldTypes::LINK, $attributes);
			return $this;
		}

		/**
		 * Add listpicker field.
		 * @param $label
		 * @param $name
		 * @param $values
		 * @param $values_field_id
		 * @param $options
		 * @param $options_id
		 * @param $options_value
		 * @param $description
		 * @param $validators
		 */
		function addListpicker($label = '', $name = '', $values = array(), $values_field_id = '', $options = array(), $options_id = '', $options_value = '', $description = '', $validators = array(), $script = '')
		{
			$attributes = array(
				'name' => $name,
				'value' => $values,
				'description' => $description,
				'value_id' => $values_field_id,
				'options' => $options,
				'options_id' => $options_id,
				'options_value' => $options_value,
				'validate' => $validators
			);
			$this -> _addField($label, Wbl_Form_FieldTypes::LISTPICKER, $attributes);
			return $this;
		}

		/**
		 * Add password field
		 * @param $label
		 * @param $name
		 * @param $value
		 * @param $description
		 * @param $validators
		 */
		function addPassword($label = '', $name = '', $value = '', $description = '', $validators = array(), $script = '')
		{
			$attributes = array(
				'name' => $name,
				'value' => $value,
				'description' => $description,
				'validate' => $validators
			);
			$this -> _addField($label, Wbl_Form_FieldTypes::PASSWORD, $attributes);
			return $this;
		}

		/**
		 * Add radio list with images as labels
		 * @param $label
		 * @param $name
		 * @param $value
		 * @param $options
		 * @param $options_value
		 * @param $options_image_id
		 * @param $options_title
		 * @param $description
		 * @param $validators
		 */
		function addRadioimages($label = '', $name = '', $value = '', $options = array(), $options_value = '', $options_image_id = '', $options_title = '', $description = '', $validators = array(), $script = '')
		{
			$attributes = array(
				'name' => $name,
				'value' => $value,
				'options' => $options,
				'field_value' => $options_value,
				'field_image' => $options_image_id,
				'field_title' => $options_title,
				'description' => $description,
				'validate' => $validators
			);
			$this -> _addField($label, Wbl_Form_FieldTypes::RADIOIMAGES, $attributes);
			return $this;
		}

		/**
		 * Add radio list field.
		 * @param $label
		 * @param $name
		 * @param $value
		 * @param $options
		 * @param $options_value
		 * @param $options_text
		 * @param $description
		 * @param $validators
		 */
		function addRadiolist($label = '', $name = '', $value = '', $options = '', $options_value = '', $options_text = '', $description = '', $validators = array(), $script = '')
		{
			$attributes = array(
				'name' => $name,
				'value' => $value,
				'options' => $options,
				'field_value' => $options_value,
				'field_text' => $options_text,
				'description' => $description,
				'validate' => $validators
			);
			$this -> _addField($label, Wbl_Form_FieldTypes::RADIOLIST, $attributes);
			return $this;
		}

		/**
		 * Add slider field.
		 * @param $label
		 * @param $name
		 * @param $value
		 * @param $min
		 * @param $max
		 * @param $step
		 * @param $description
		 * @param $validators
		 */
		function addSlider($label = '', $name = '', $value = '', $min = 0, $max = 100, $step = 1, $description = '', $validators = array(), $script = '')
		{
			$attributes = array(
				'name' => $name,
				'value' => $value,
				'min' => $min,
				'max' => $max,
				'step' => $step,
				'description' => $description,
				'validate' => $validators
			);
			$this -> _addField($label, Wbl_Form_FieldTypes::SLIDER, $attributes);
			return $this;
		}

		/**
		 * Add text field.
		 * @param $label
		 * @param $name
		 * @param $value
		 * @param $description
		 * @param $validators
		 */
		function addText($label = '', $name = '', $value = '', $description = '', $validators = array(), $script = '')
		{
			$attributes = array(
				'name' => $name ? $name : $label,
				'value' => $value,
				'description' => $description,
				'validate' => $validators
			);
			$this -> _addField($label, Wbl_Form_FieldTypes::TEXT, $attributes);
			return $this;
		}

		/**
		 * Return the form array for template.
		 */
		function getForm()
		{
			global $page;
			$page -> template -> assign('form', $this -> generate());
			return $page -> template -> fetch($page -> objects['templates']['form_new']);
		}

	}
?>