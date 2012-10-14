<?php namespace Squi;

class Form_Field extends HTML_Element {

	// Form instance this field belongs to
	public $form;

	// Reference to the Form_Label instance
	public $label;

	// In case of multi-field, this is an array of Form_Fields
	public $fields;

	// Array of value => label options for <select/> and radio fields
	public $options;

	// Value callback to use when data is attached to the form
	public $value_callback;

	// Selected value for select fields
	public $selected;

	// Attributes accessible as chainable methods
	public static $chainable_attr = array(
		'label', 'name', 'attr', 'attributes', 'type', 'value', 'options',
	);

	// Attributes
	public $attr = array('type' => 'text');

	// Attributes accessible as properties (HTML_Element)
	protected static $accessible_attr = array(
		'name',
		'type',
		'value',
	);

	public function __construct($name = null, $callback = null)
	{
		is_null($name) || $this->name = $name;

		if (is_callable($callback))
		{
			call_user_func($callback, $this);
		}
	}

	public static function make($name = null, $callback = null)
	{
		return new static($name, $callback);
	}
	
	/**
	 * Set the Label for this field
	 * Can either be a string or a Form_Label instance
	 */
	public function label($label)
	{
		if (is_a($label, 'Squi\\Form_Label'))
		{
			$this->label = $label;
		}
		else
		{
			$this->label = Form_Label::make($label);
		}

		// Set the back reference
		$this->label->field = $this;

		return $this;
	}

	/**
	 * Get or set the field value
	 */
	public function value($value = null)
	{
		if (is_callable($value))
		{
			$this->value_callback = $value;

			return $this;
		}
		elseif (is_null($value) && is_callable($this->value_callback) && isset($this->form->values))
		{
			return $this->value(call_user_func($this->value_callback, $this->form->values));
		}

		// Special case for selects
		if ($this->type == 'select')
		{
			$this->selected = $value;

			return $this;
		}
		
		return $this->attr('value', $value);
	}

	/**
	 * Get the first error for this field
	 * $format is used for Laravel\Messages errors
	 * @return string|null
	 */
	public function error($format = null)
	{
		return $this->form->error($this);
	}

	/**
	 * Set the value given an object or array
	 * that might not even exist
	 */
	public function extract_value($values)
	{
		// Multi-field
		if ( ! empty($this->fields))
		{
			foreach ($this->fields as $field)
			{
				$field->extract_value($values);
			}

			return $this;
		}

		if (is_object($values) && $values->{$this->name()})
		{
			return $this->value($values->{$this->name()});
		}
		elseif (is_array($values) && isset($values[$this->name()]))
		{
			return $this->value($values[$this->name()]);
		}
	}

	/**
	 * Set the field parameters from an assoc array
	 */
	public function parse_config($config)
	{
		extract($config);

		$parameters = array(
			'name', 'type', 'label', 'value', 'options', 'attr',
		);

		foreach ($parameters as $param)
		{
			if ( ! isset($$param))
			{
				continue;
			}

			if (method_exists($this, $param))
			{

				$this->$param($$param);
			}
			else
			{
				$this->{$param} = $$param;
			}
		}

		return $this;
	}

	/**
	 * Mark the field as required
	 * Adds a 'required' class to the label and field,
	 * and 'required' HTML5 attribute to the field
	 */
	public function required()
	{
		$this->add_class('required');
		isset($this->label) && $this->label->add_class('required');

		$this->attr('required', 'required');

		return $this;
	}

	/**
	 * Returns boolean true if the field is set as required
	 */
	public function is_required()
	{
		return (bool) $this->attr('required');
	}

	/**
	 * Mark the field as hidden
	 * Note: labels will not be rendered for hidden fields
	 */
	public function hidden()
	{
		return $this->attr('type', 'hidden');
	}

	/**
	 * Returns true if this is a hidden field
	 */
	public function is_hidden()
	{
		return $this->attr('type') == 'hidden';
	}

	/**
	 * Get the field as HTML string
	 */
	public function html()
	{
		// Multi-field
		if ( ! empty($this->fields))
		{
			return implode("\n", array_map(function($field) { return $field->html(); }, $this->fields));
		}

		if ($this->type == 'select')
		{
			return \Form::select($this->name, $this->options, $this->selected, $this->attributes_array());
		}
		elseif ($this->type == 'checkbox')
		{
			return \Form::checkbox($this->name, $this->value, $this->checked, $this->attributes_array());
		}
		elseif ($this->type == 'password')
		{
			return \Form::password($this->name, $this->attributes_array());
		}
		elseif ($this->type == 'radio')
		{
			$fields = array();

			foreach ($this->options as $value => $label)
			{
				$id = $this->name.'_'.$value;
				$selected = ( ! is_null($this->value)) && ($value == $this->value);
				$fields[] = sprintf('<label for="%s" class="radio"%s>%s %s</label>',
					$id,
					$selected ? ' selected="selected"' : '',
					\Form::radio($this->name, $value, $selected, array('id' => $id)),
					$label);
			}

			return implode("\n", $fields);
		}
		elseif ($this->type == 'file')
		{
			return \Form::file($this->name, $this->attributes_array());
		}

		return call_user_func('\\Form::'.$this->type, $this->name, $this->value, $this->attributes_array());
	}

	public function __toString()
	{
		try
		{
			return $this->html();
		}
		catch (\Exception $e)
		{
			die($e->getMessage().' on line '.$e->getLine().' of '.$e->getFile());
		}
	}

	public function __call($method, $args)
	{
		if (property_exists($this, $method))
		{
			if (isset($args[0]))
			{
				$this->$method = $args[0];
			}
			else
			{
				return $this->$method;
			}
		}
		elseif (in_array($method, static::$chainable_attr))
		{
			if (isset($args[0]))
			{
				$this->attr($method, $args[0]);
			}
			else
			{
				return $this->attr($method);
			}
		}

		return $this;
	}
	
}