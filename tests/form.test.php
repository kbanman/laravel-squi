<?php

class TestForm extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		Bundle::start('squi');
		require_once Bundle::path('squi').'tests/client.php';
	}

	/**
	 * Test rendering of simple forms
	 *
	 * @return void
	 */
	public function testSimpleForm()
	{
		// Simple form with table layout
		$fields = array(
			'name',
			'email',
			'password:password',
			'password2:password' => 'Confirm Password',
			'user_source:select' => array(
				'label' => 'How did you hear about us?',
				'options' => array(
					'friend' => 'From a friend',
					'google' => 'Google search',
					'other' => 'Other'
				),
			),
		);

		$form = Squi\Form::make($fields)->with('action', 'users/new');

		$expected = $this->markup('simple_table');
		$this->assertSame($expected, $form->render());

		// Simple form with twitter bootstrap layout
		$form = Squi\Form::make($fields)
			->with('action', 'users/new')
			->with('layout', 'bootstrap');

		$expected = $this->markup('simple_bootstrap');
		$this->assertSame($expected, $form->render());

		// Simple form with basic layout
		$form = Squi\Form::make($fields)
			->with('action', 'users/new')
			->with('layout', 'basic');

		$expected = $this->markup('simple_basic');
		$this->assertSame($expected, $form->render());
	}

	/**
	 * Test rendering of attributized forms
	 *
	 * @return void
	 */
	public function testAttributeForm()
	{
		$fields = array(
			'name',
			'email' => array(
				'required',
				'value' => 'example@example.com',
			),
			'password:password',
			'password2:password' => 'Confirm Password',
			'user_source' => array(
				'type' => 'select',
				'label' => 'How did you hear about us?',
				'label_attr' => array('style' => 'font-weight:bold;'),
				'options' => array(
					'friend' => 'From a friend',
					'google' => 'Google search',
					'other' => 'Other'
				),
			),
			'buttons' => array(
				'Sign Up' => array('class' => 'btn btn-primary'),
			),
		);
		$form_attr = array('data-test' => 'result!');
		$field_attr = array('data-testing' => 'results!');

		$form = Squi\Form::make($fields)
			->with('form_attr', $form_attr)
			->with('field_attr', $field_attr)
			->with('action', 'users/new')
			->with('method', 'get');

		$expected = $this->markup('attr_table');
		$this->assertSame($expected, $form->render());


		// Attributized form with twitter bootstrap layout
		$form = Squi\Form::make($fields)
			->with('form_attr', $form_attr)
			->with('field_attr', $field_attr)
			->with('action', 'users/new')
			->with('method', 'get')
			->with('layout', 'bootstrap');

		$expected = $this->markup('attr_bootstrap');
		$this->assertSame($expected, $form->render());


		// Attributized form with twitter bootstrap layout
		$form = Squi\Form::make($fields)
			->with('form_attr', $form_attr)
			->with('field_attr', $field_attr)
			->with('action', 'users/new')
			->with('method', 'get')
			->with('layout', 'basic');

		$expected = $this->markup('attr_basic');
		$this->assertSame($expected, $form->render());
	}

	/**
	 * Test shorthand methods
	 *
	 * @return void
	 */
	public function testShorthandForm()
	{
		$fields = array(
			'name',
			'email',
			'password:password',
			'password2:password' => 'Confirm Password',
			'user_source:select' => array(
				'label' => 'How did you hear about us?',
				'options' => array(
					'friend' => 'From a friend',
					'google' => 'Google search',
					'other' => 'Other'
				),
			),
		);

		$instance = Client::find(542);

		$form = Squi\Form::make($fields, $instance);
		$expected = $this->markup('shorthand_table');
		$this->assertSame($expected, $form->render());

		$form = Squi\Form::basic($fields, $instance);
		$expected = $this->markup('shorthand_basic');
		$this->assertSame($expected, $form->render());

		$form = Squi\Form::table($fields, $instance);
		$expected = $this->markup('shorthand_table');
		$this->assertSame($expected, $form->render());

		$form = Squi\Form::bootstrap($fields, $instance);
		$expected = $this->markup('shorthand_bootstrap');
		$this->assertSame($expected, $form->render());

		$form = Squi\Form::of('Client', $instance, 'custom_field_method');
		$expected = $this->markup('shorthand_custom');
		$this->assertSame($expected, $form->render());

		$form = Squi\Form::of($instance);
		$expected = $this->markup('shorthand_model');
		$this->assertSame($expected, $form->render());
	}

	/**
	 * Test multi-field madness
	 *
	 * @return void
	 */
	public function testMultifieldForm()
	{
		$fields = array(
			'firstname, lastname' => 'Full Name',
			array(
				'label' => 'Full Name',
				'fields' => array(
					'first_name',
					'last_name' => array(
						'field_attr' => array('disabled'),
					),
				),
			),
			'password:password, confirm:text' => 'Password',
		);

		// Form with a multi-field
		$form = Squi\Form::make($fields);
		$expected = $this->markup('multifield');
		$this->assertSame($expected, $form->render());
	}


	protected function markup($path)
	{
		return file_get_contents(Bundle::path('squi').'tests/markup/form/'.$path.'.html');
	}

}