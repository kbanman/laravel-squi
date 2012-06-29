<?php namespace Squi;

class Form_Label extends HTML_Element {
	
	// Reference to the field this label belongs to
	public $field;

	// The string value for the label
	public $label;

	public function __construct($label = null, $callback = null)
	{
		$this->label($label);

		if (is_callable($callback))
		{
			call_user_func($callback, $label);
		}
	}

	public static function make($label = null, $callback = null)
	{
		return new static($label, $callback);
	}

	/**
	 * Get or set the label value
	 */
	public function label($label = null)
	{
		if (is_null($label))
		{
			return $this->label;
		}

		$this->label = $label;

		return $this;
	}

	/**
	 * Set the related field
	 */
	public function field(Form_Field $field)
	{
		$this->field = $field;

		return $this;
	}

	/**
	 * Get the related field name
	 */
	public function field_name()
	{
		if ( ! empty($this->field->fields))
		{
			return reset($this->field->fields)->name;
		}

		return $this->field->name;
	}

	/**
	 * Get the label as HTML string
	 */
	public function html()
	{
		return \Form::label($this->field_name(), $this->label, $this->attributes_array());
	}

	public function __toString()
	{
		return $this->html();
	}
}