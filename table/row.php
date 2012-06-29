<?php namespace Squi;

class Table_Row extends HTML_Element {

	// Callback function
	public $attr_callback;
	
	public function __construct($values = null)
	{
		is_array($values) && $this->values($values);
	}

	public static function make($values = null)
	{
		$row = new static($values);

		return $row;
	}

	/**
	 * Get the value for a given column and row
	 */
	public function value(Table_Column $column, $rowdata)
	{
		// Find out where the column stands in the table
		$index = array_search($column, $column->table->columns);

		return isset($this->values[$index]) ? $this->values[$index] : null;
	}

	/**
	 * Set or get the row attributes
	 * Differs from parent implementation by allowing the attributes
	 * to be returned by a callback of signature function($row){}
	 */
	public function attributes($attributes = null)
	{
		// Set dynamic attributes
		if (is_callable($attributes))
		{
			$this->attr_callback = $attributes;

			return $this;
		}

		// Normal functionality
		return parent::attributes($attributes);
	}

	/**
	 * Get the computed attributes for given row data
	 * Returns html attribute string
	 */
	public function get_attributes($rowdata)
	{
		// Static attributes
		$attr = $this->attributes_array();

		// Dynamic attributes?
		if (is_callable($this->attr_callback))
		{
			$attr = array_merge($attr, call_user_func($this->attr_callback, $rowdata));
		}

		return \HTML::attributes($attr);
	}
}