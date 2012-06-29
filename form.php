<?php namespace Squi;

class Form extends HTML_Element {

	// Bundled layouts
	public static $layouts = array(
		'basic',
		'table',
		'bootstrap',
	);

	// The View used to render the form
	public $layout = 'table';

	// Array of Form_Field objects
	public $fields;

	// The object or array from which to get field values
	public $values;

	// Form Buttons
	// Can be just the string value, array descriptor, or HTML_Element instance
	// Default buttons 'Cancel' and 'Submit' are added
	// unless populated or set to false.
	public $buttons = array();

	// Arbitrary content to prepend or append to the form
	public $before, $after;

	// Optional <legend/> contents
	public $legend;

	// Default attributes
	public $attr = array(
		'method' => 'post',
	);

	// Laravel\Messages or array containing errors
	public $errors;

	// Boolean flag for whether to render fields in a <fieldset />
	public $fieldset;

	public function __construct($fields = null)
	{
		is_array($fields) && $this->fields($fields);

		is_callable($fields) && call_user_func($fields, $this);
	}

	public static function make($fields = null)
	{
		return new static($fields);
	}

	/**
	 * Add a field using one of many syntaxes
	 * Returns reference to the field
	 */
	public function field($label, $callback = null)
	{
		if (is_a($callback, 'Squi\\Form_Field'))
		{
			$field = $callback;
		}
		else
		{
			$field = Form_Field::make();
		}
		$field->form = $this;

		// field('field_name')
		if (is_null($callback) && is_string($label))
		{
			$field->name = $label;

			// Automagic label by capitalization
			$field->label(ucwords(str_replace('_', ' ', $label)));
		}

		// field(function($field){})
		elseif (is_null($callback) && is_callable($label))
		{
			call_user_func($label, $field, $this);
		}

		// field(array('name' => 'field_name'))
		elseif (is_null($callback) && is_array($label))
		{
			if (isset($label[0]) && isset($label[1]))
			{
				$field = $label[1];
				$field->label($label[0]);
			}
			else
			{
				$field->parse_config($label);
			}
		}

		// field('Label', array('name' => 'field_name'))
		// field('Label', array(Form_Field, Form_Field))
		elseif (is_string($label) && is_array($callback))
		{
			$field->label($label);

			// The array could either be an array of Table_Fields
			// or an assoc array defining a field
			if (is_a(reset($callback), 'Squi\\Form_Field'))
			{
				$field->fields = $callback;
			}
			else
			{
				$field->parse_config($callback);
			}
		}

		// field(Form_Label)
		elseif (is_a($label, 'Squi\\Form_Label'))
		{
			$field->label($label);
		}
		
		// field('Label', 'field_name')
		elseif (is_string($label) && is_string($callback))
		{
			$field->label($label);
			$field->name = $callback;
		}

		$this->fields[] = $field;

		return $field;
	}

	/**
	 * Add fields using array syntax
	 */
	public function fields($fields)
	{
		foreach ($fields as $key => $value)
		{
			$args = is_int($key) ? array($value) : array($key, $value);
			call_user_func_array(array($this, 'field'), $args);
		}

		return $this;
	}

	/**
	 * Specify the markup layout to use
	 * This can be a View object or one of basic, table or bootstrap
	 */
	public function layout($layout)
	{
		if (is_a($layout, 'View'))
		{
			$this->layout = $layout;
		}
		elseif (in_array($layout, static::$layouts))
		{
			$this->layout = \View::make('squi::form.'.$layout);
		}
		else
		{
			$this->layout = \View::make($layout);
		}

		return $this;
	}

	/**
	 * Set the object or array from which to get field values
	 * @return Form instance
	 */
	public function values($values)
	{
		$this->values = $values;

		return $this;
	}

	/**
	 * Set the form buttons
	 * @return Form instance
	 */
	public function buttons($buttons)
	{
		$this->buttons = $buttons;

		return $this;
	}

	/**
	 * Set errors
	 * @return Form instance
	 */
	public function errors($errors)
	{
		$this->errors = $errors;

		return $this;
	}

	/**
	 * Get first error for a field
	 * @return string|null
	 */
	public function error($field, $format = null)
	{
		if (is_a($field, 'Squi\\Form_Field'))
		{
			$field = $field->name;
		}

		if ( ! isset($this->errors))
		{
			return null;
		}

		if (is_a($this->errors, 'Laravel\\Messages') && $this->errors->has($field))
		{
			return $this->errors->first($field, $format);
		}
		elseif (is_array($this->errors) && isset($this->errors[$field]))
		{
			return $this->errors[$field];
		}

		return null;
	}

	/**
	 * Find a field by its name
	 */
	public function find($name)
	{
		foreach ($this->fields as $field)
		{
			if ($field->name == $name) return $field;
			if ($field->fields)
			{
				foreach ($field->fields as $field)
				{
					if ($field->name == $name) return $field;
				}
			}
		}

		return null;
	}

	/**
	 * Create a form using the configuration provided by a model
	 */
	public static function of($model, $method = 'configure_form')
	{
		$form = new static;

		if (is_object($model))
		{
			$this->values = $model;
			$model = get_class($model);
		}

		call_user_func($model.'::'.$method, $form);

		return $form;
	}

	/**
	 * Render the form as HTML
	 */
	public function render()
	{
		// Create the view
		$this->layout($this->layout);

		// Separate hidden and displayed fields
		$fields = array();
		$hidden = array();

		// Are we using a twitter bootstrap layout?
		$bootstrap = strrpos($this->layout->view, 'bootstrap') !== false;

		foreach ($this->fields as $field)
		{
			// Add value from the model instance
			$field->extract_value($this->values);

			// Add bootstrap classes if appropriate
			if ($bootstrap && $field->label)
			{
				$field->label->add_class('control-label');
			}

			// Is it hidden?
			// @todo: this prevents hidden fields from being
			// usable inside multi-field, but I think that's okay
			if ($field->is_hidden())
			{
				$hidden[] = $field;
			}
			else
			{
				if ( ! isset($field->label))
				{
					$field->label(ucwords(str_replace('_', ' ', $field->name)));

					// Retroactively apply required class to label
					if ($field->is_required())
					{
						$field->label->add_class('required');
					}
				}

				$fields[] = $field;
			}
		}

		// Make the buttons
		$buttons = array();

		if ( ! is_array($this->buttons))
		{
			$this->buttons = array();
		}
		elseif ( ! count($this->buttons))
		{
			$this->buttons = array(
				Form_Button::make('Cancel')->attr('type', 'button')->attr('class', 'btn'),
				Form_Button::make('Submit')->attr('type', 'submit')->attr('class', 'btn btn-primary'),
			);
		}

		foreach ($this->buttons as $key => $val)
		{
			if (is_int($key) && is_array($val))
			{
				$buttons[] = Form_Button::make()->parse_config($val);
			}
			elseif (is_int($key) && is_a($val, 'Squi\\Form_Button'))
			{
				$buttons[] = $val;
			}
			elseif (is_int($key))
			{
				$buttons[] = Form_Button::make($val)->attr('class', 'btn');
			}
			elseif (is_string($key) && is_array($val))
			{
				$buttons[] = Form_Button::make($key)->parse_config($val);
			}
			elseif (is_string($key) && is_string($val))
			{
				$buttons[] = Form_Button::make($key)->attr('name', $val)->attr('class', 'btn');
			}
		}
		
		return $this->layout
			->with('form', $this)
			->with('fields', $fields)
			->with('hidden', $hidden)
			->with('buttons', $buttons)
			->render();
	}

	/**
	 * Render the form as a string
	 */
	public function __toString()
	{
		try
		{
			return $this->render();
		}
		catch (\Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function __call($method, $args)
	{
		return call_user_func_array(array($this, 'field'), $args)->type($method);
	}

}