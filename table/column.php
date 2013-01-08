<?php namespace Squi;

/**
 * Controls the cells of a specific column
 */
class Table_Column extends HTML_Element {

	public $table;

	public $name;

	public $value;

	public $attr_callback;

	public $heading;

	public function __construct(Table $table = null, $callback = null)
	{
		$this->table = $table;

		if (is_callable($callback))
		{
			call_user_func($callback, $this, $table);
		}

		// Create the header object
		$this->heading = new HTML_Element;
	}

	public static function make(Table $table = null, $callback = null)
	{
		return new static($table, $callback);
	}

	/**
	 * Get or set the column name
	 */
	public function name($name = null)
	{
		if (is_null($name))
		{
			return $this->name;
		}

		$this->name = $name;

		return $this;
	}

	/**
	 * Set the value
	 */
	public function value($value)
	{
		$this->value = $value;

		return $this;
	}

	/**
	 * Return the cell value for a given row
	 */
	public function get_value($rowdata)
	{
		if (is_a($rowdata, 'Squi\\Table_Rowdata'))
		{
			return $rowdata->value($this);
		}

		if (is_callable($this->value))
		{
			return call_user_func($this->value, $rowdata);
		}

		// Literal string value ('=The Value')
		if (is_string($this->value) && substr($this->value, 0, 1) == '=')
		{
			return $this->value;
		}

		// Regular objects (DB::query() results)
		if (is_object($rowdata) && property_exists($rowdata, $this->value))
		{
			return $rowdata->{$this->value};
		}

		// Eloquent objects
		if (is_a($rowdata, 'Eloquent') && $value = $rowdata->value($this->value))
		{
			return $value;
		}

		// Arrays
		if (array_key_exists($this->value, $rowdata))
		{
			return $rowdata[$this->value];
		}
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

	/**
	 * Accepts an array configuration and sets
	 * the appropriate properties
	 */
	public function parse_config($config)
	{
		extract($config);

		isset($name) && $this->value($name);
		isset($value) && $this->value($value);
		isset($attr) && $this->attributes($attr);
	}

}