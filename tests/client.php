<?php
/**
 * Fake model declaration for testing
 *
 * @package Squi
 * @author Kelly Banman (kelly.banman@gmail.com)
 **/
class Client {

	public $name, $firstname, $lastname, $birth_date, $lead_source, $newsletter;

	public static $sample_records = array(
		array(
			'id' => 413,
			'firstname' => 'Jonathon',
			'lastname' => 'Brooks',
			'birth_date' => '1982-04-20',
			'lead_source' => 'google',
			'newsletter' => 'no',
		),
		array(
			'id' => 123,
			'firstname' => 'Ashleigh',
			'lastname' => 'Crayford',
			'birth_date' => '1988-11-04',
			'lead_source' => 'other',
			'newsletter' => 'yes',
		),
		array(
			'id' => 542,
			'firstname' => 'Cindy',
			'lastname' => 'Montgomery',
			'birth_date' => '1971-10-18',
			'lead_source' => 'friend',
			'newsletter' => 'yes',
		),
		array(
			'id' => 2142,
			'firstname' => 'Bradley',
			'lastname' => 'Bruce',
			'birth_date' => '1984-07-21',
			'lead_source' => 'google',
			'newsletter' => 'no',
		),
	);

	public function __construct($record)
	{
		foreach ($record as $field => $value)
		{
			$this->$field = $value;
		}
		$this->name = $record['firstname'].' '.$record['lastname'];
	}

	public static function all()
	{
		$records = array();

		foreach (static::$sample_records as $record)
		{
			$records[] = new static($record);
		}

		return $records;
	}

	public static function find($id)
	{
		foreach (static::$sample_records as $record)
		{
			if ($record['id'] == $id)
			{
				return new static($record);
			}
		}
		return false;
	}

	public static function form_fields($form = null)
	{
		return array(
			'firstname, lastname:text' => 'Full Name',
			'birthdate' => array(
				'label' => 'birthdate',
				'attr' => array('class' => 'datepicker'),
			),
			'lead_source:select' => array(
				'label' => 'How did you hear about us?',
				'options' => array(
					'friend' => 'From a friend',
					'google' => 'Google search',
					'other' => 'Other'
				),
			),
		);
	}

	public static function custom_field_method($form = null)
	{
		return array(
			'firstname' => 'Full Name',
			'lead_source:select' => array(
				'label' => 'How did you hear about us?',
				'options' => array(
					'friend' => 'From a friend',
					'google' => 'Google search',
					'other' => 'Other'
				),
			),
		);
	}

	
	public static function table_columns($table = null)
	{
		return array(
			'name',
			'Birthdate' => function($row)
			{
				return date('d-M Y', strtotime($row->birth_date));
			},
			'lead_source' => array(
				'heading' => 'Lead Source',
				'value' => function($row)
				{
					return Client::select_value('lead_source', $row->lead_source);
				},
			),
		);
	}

	public static function summary_columns()
	{
		return array(
			'firstname' => 'First Name',
			'lastname'  => 'Last Name',
		);
	}

	/**
	 * This method doesn't account for multi-field shenanigans
	 */
	public static function select_value($field_name, $value)
	{
		$fields = static::form_fields();
		$field = isset($fields[$field_name]) ? $fields[$field_name] : $fields[$field_name.':select'];
		return isset($field['options'][$value]) ? $field['options'][$value] : null;
	}
}