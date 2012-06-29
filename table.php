<?php namespace Squi;

class Table extends HTML_Element {

	// Array of Table_Column objects
	public $columns = array();

	// Row data
	public $rows = array();

	// Layout to use (View instance)
	public $layout = 'horizontal';

	// Shell row on which common row attributes
	// and default values are stored (instance of Table_Row)
	public $row;

	// Header row (instance of Table_Row)
	public $header;

	// Instance of Table_Rowdata to contain column names
	public $header_data;

	// Message to show when there are no rows
	public $empty_message = 'No records to show';

	// Built-in layouts
	public static $layouts = array(
		'horizontal',
		'vertical',
		'bootstrap',
	);

	public function __construct($callback = null, $rows = null)
	{
		// Create the shell rows
		$this->row = new Table_Row;
		$this->header = new Table_Row;

		if (is_callable($callback))
		{
			call_user_func($callback, $this);
		}

		$rows && $this->rows($rows);
	}

	/**
	 * Create a new table using Schema-style callback
	 */
	public static function make($callback = null, $rows = null)
	{
		return new static($callback, $rows);
	}

	/**
	 * Specify the model from which to take column definitions
	 */
	public static function of($model, $method = 'configure_table')
	{
		$table = new static;

		is_object($model) and $model = get_class($model);

		call_user_func($model.'::'.$method, $table);

		return $table;
	}

	/**
	 * Specify the markup layout to use
	 * This can be an arbitrary View or one of horizontal, vertical, or bootstrap
	 */
	public function layout($layout)
	{
		if (is_a($layout, 'View'))
		{
			$this->layout = $layout;
		}
		elseif (in_array($layout, static::$layouts))
		{
			$this->layout = \View::make('squi::table.'.$layout);
		}
		else
		{
			$this->layout = \View::make($layout);
		}

		return $this;
	}
	
	/**
	 * Define a column using Schema-style callback
	 */
	public function column($name, $callback = null)
	{
		if (is_null($callback))
		{
			$col = Table_Column::make($this, $name);
		}
		else
		{
			$col = Table_Column::make($this)
				->name($name)
				->value($callback);
		}
		
		$this->columns[] = $col;

		return $this;
	}

	/**
	 * Add columns using array-style declaration
	 */
	public function columns($columns)
	{
		foreach ($columns as $key => $val)
		{
			$column = new Table_Column($this);

			// If there is only a value
			if (is_int($key))
			{
				// Shortcut that converts a single column descriptor
				// into a label (captitalizing and converting underscores)
				// and assuming that string is the row property/key from
				// which to grab the column value.
				if (is_string($val))
				{
					$column->name = ucwords(str_replace('_', ' ', $val));
					$column->value = $val;
				}
				// Parameters are specified as an array
				else
				{
					$column->parse_config($val);
				}
			}
			// The key is the column name,
			// the value is the row value callback or property name
			else
			{
				$column->name = $key;

				if (is_array($val))
				{
					$column->parse_config($val);
				}
				else
				{
					$column->value = $val;
				}
			}

			$this->columns[] = $column;
		}

		return $this;
	}

	/**
	 * Add a row to the table
	 */
	public function row($row)
	{
		$this->rows[] = $row;

		return $this;
	}

	/**
	 * Add an array of rows to the table
	 */
	public function rows($rows)
	{
		$this->rows += $rows;

		return $this;
	}

	/**
	 * Add a non-standard row to the table
	 * (Useful for footers, button rows, etc.)
	 */
	public function row_raw($values)
	{
		$this->rows[] = new Table_Row($values);
	}

	/**
	 * Render the table
	 */
	public function render()
	{
		// Create the pseudo-data row to contain the column names
		$this->header_data = new Table_Rowdata;
		foreach ($this->columns as $col)
		{
			$this->header_data->set_value($col, $col->name);
		}

		// Create the View
		if ( ! is_a($this->layout, 'View'))
		{
			$this->layout($this->layout);
		}

		// Add reference to the table
		$this->layout->with('table', $this);

		// Add columns
		$this->layout->with('columns', $this->columns);

		// Add rows
		$this->layout->with('rows', $this->rows);

		// Header row
		$this->layout->with('header', $this->header);

		// Render that mofo
		return $this->layout->render();
	}

	/**
	 * Render the table to form a string value
	 */
	public function __toString()
	{
		return $this->render();
	}
}

