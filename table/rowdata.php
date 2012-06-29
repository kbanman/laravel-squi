<?php namespace Squi;

/**
 * Wrapper class to contain non-standard row data
 */
class Table_Rowdata extends HTML_Element {
	
	// Values and attributes for the row
	public $values = array();

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
	 * Set the value for a column
	 */
	public function set_value(Table_Column $column, $value)
	{
		$this->values[$this->column_index($column)] = $value;
	}

	/**
	 * Get the value for a given column
	 */
	public function value(Table_Column $column)
	{
		// Find out where the column stands in the table
		$index = $this->column_index($column);

		return isset($this->values[$index]) ? $this->values[$index] : null;
	}

	/**
	 * Add column values to the row
	 * Can be either an array of string cell contents
	 * or the string contents as keys and array of attributes
	 * as values, or a combination of the above.
	 */
	public function values($values)
	{
		foreach ($values as $key => $val)
		{
			$col = new HTML_Element;

			$attr = is_array($val) ? $val : array();

			if (is_int($key) && is_array($val))
			{
				$col->value = isset($val['value']) ? $val['value'] : '';
				isset($val['attr']) ? $val['value'] : '';
			}
		}
	}

	protected function column_index(Table_Column $column)
	{
		return array_search($column, $column->table->columns);
	}

}