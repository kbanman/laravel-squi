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

	// Footer row (instance of Table_Row)
	public $footer;

	// Footer data (instance of Table_Rowdata)
	public $footer_data;

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
		elseif (is_array($callback))
		{
			$this->columns($callback);
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
		$col = Table_Column::make($this);

		// column(Table_Column)
		if (is_a($name, 'Squi\\Table_Column'))
		{
			$col = $name;
			$col->table = $this;
		}
		// column(function($col){ })
		elseif (is_callable($name) && is_null($callback))
		{
			call_user_func($name, $col, $this);
		}
		// column('prop_name')
		elseif (is_string($name) && is_null($callback))
		{
			// Shortcut that converts a single column descriptor
			// into a label (captitalizing and converting underscores)
			// and assuming that string is the row property/key from
			// which to grab the column value.
			$col->name(ucwords(str_replace('_', ' ', $name)));
		}
		// column('Label', 'prop_name')
		elseif (is_string($name) && is_string($callback))
		{
			$col->name($name)->value($callback);
		}
		// column('Label', function($row){ })
		elseif (is_string($name) && is_callable($callback))
		{
			$col->name($name)->value($callback);
		}

		return $this->columns[] = $col;
	}

	/**
	 * Add columns using array-style declaration
	 */
	public function columns($columns)
	{
		foreach ($columns as $key => $val)
		{
			$args = is_int($key) ? array($val) : array($key, $val);
			call_user_func_array(array($this, 'column'), $args);
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
		$this->rows[] = new Table_Rowdata($values);

		return $this;
	}

	/**
	 * Set attributes for the rows
	 * Can be either an assoc array,
	 * a closure (function($row){}) returning an assoc array,
	 * or an attribute and value string
	 */
	public function row_attr($attr, $value = null)
	{
		$this->row->attr($attr, $value);

		return $this;
	}

	/**
	 * Set the message to display when no rows exist
	 */
	public function empty_message($message)
	{
		$this->empty_message = $message;

		return $this;
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

