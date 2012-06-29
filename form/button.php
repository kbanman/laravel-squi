<?php namespace Squi;

class Form_Button extends HTML_Element {

	// Default attributes
	public $attr = array(
		'type' => 'submit',
	);

	protected static $accessible_attr = array(
		'type', 'value',
	);
	
	public function __construct($value = null)
	{
		$value && $this->value($value);
	}

	public static function make($value = null)
	{
		return new static($value);
	}

	public function value($value)
	{
		$this->value = $value;

		return $this;
	}

	public function type($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Configure the button from an assoc array
	 */
	public function parse_config($config)
	{
		if (isset($config['value']))
		{
			$this->value = $config['value'];
			unset($config['value']);
		}

		foreach ($config as $attr => $val)
		{
			$this->attr($attr, $val);
		}

		return $this;
	}

	public function html()
	{
		return '<input'.$this->attributes().' />';
	}

	public function __toString()
	{
		return $this->html();
	}
}