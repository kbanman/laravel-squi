<?php namespace Squi;

/**
 * Base class to add some common functionality
 * for classes that represent HTML Elements.
 *
 * Incomplete - has no concept of the tag it represents
 *
 * Mostly valuable for its handling of the class attribute.
 * A gotcha: retrieving the class by any means except for the
 * attributes() method will return it as an array of classes.
 *
 * This would be fantastic as a trait in PHP 5.4 (ex. Attributable)
 */
class HTML_Element {

	public $attr = array(
		'class' => array(),
	);

	protected $no_value_attr = array(
		'selected',
		'checked',
		'required',
	);

	protected static $accessible_attr = array();
	
	/**
	 * Get or set the value of a single attribute (jQuery style)
	 */
	public function attr($attr, $value = null)
	{
		if (is_array($attr))
		{
			return $this->attributes($attr);
		}
		elseif (is_null($value))
		{
			return isset($this->attr[$attr]) ? $this->attr[$attr] : null;
		}

		if ($attr == 'class')
		{
			return $this->add_class($value);
		}

		$this->attr[$attr] = $value;

		return $this;
	}

	/**
	 * Get or set an array of attributes
	 * The returned attributes are in HTML string format
	 */
	public function attributes($attributes = null)
	{
		if (is_array($attributes))
		{
			$this->attr += $attributes;

			return $this;
		}

		return \HTML::attributes($this->attributes_array());
	}

	/**
	 * Get the attributes as an array
	 */
	public function attributes_array()
	{
		$attr = $this->attr;

		// Convert the classes to string
		isset($attr['class']) && $attr['class'] = implode(' ', $attr['class']);

		if (empty($attr['class'])) unset($attr['class']);

		return $attr;
	}

	/**
	 * Add a class or array of classes
	 */
	public function add_class($class)
	{
		// Accept either string or array format
		is_array($class) || $class = explode(' ', $class);

		// Make sure attribute exists
		isset($this->attr['class']) || $this->attr['class'] = array();

		// Add new class(es) to attributes
		$this->attr['class'] = array_merge($this->attr['class'], $class);
		return $this;
	}

	/**
	 * Remove a class or an array of classes
	 */
	public function remove_class($class = null)
	{
		// Remove an array of classes
		if (is_array($class))
		{
			array_walk($class, array($this, 'remove_class'));
			return $this;
		}

		$index = array_search($class, $this->attr['class']);

		if ($index !== false)
		{
			unset($this->attr['class'][$index]);
		}

		if (is_null($class) || empty($this->attr['class']))
		{
			unset($this->attr['class']);
		}

		return $this;
	}

	/**
	 * Helpers to translate accessible attributes
	 * to their actual attribute values
	 */
	public function __get($prop)
	{
		if (in_array($prop, static::$accessible_attr))
		{
			return $this->attr($prop);
		}
	}

	public function __set($prop, $value)
	{
		if (in_array($prop, static::$accessible_attr))
		{
			return $this->attr($prop, $value);
		}
	}

}