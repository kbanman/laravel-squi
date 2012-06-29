<?php

class TestTable extends PHPUnit_Framework_TestCase {


	public static function setUpBeforeClass()
	{
		Bundle::start('squi');
		require_once Bundle::path('squi').'tests/client.php';
	}

	/**
	 * Test rendering of simple tables
	 *
	 * @return void
	 */
	public function testSimpleTables()
	{
		$clients = Client::all();

		// Simple Table
		$table = Squi\Table::make(array(
			'name',
			'birth_date' => 'Birthdate',
		), $clients);

		$expected = $this->markup('simple_horizontal');
		$this->assertSame($expected, $table->render());


		// Simple Table
		$table = Squi\Table::make(array(
			'name',
			'birth_date' => 'Birthdate',
		), $clients)->with('layout', 'vertical');

		$expected = $this->markup('simple_vertical');
		$this->assertSame($expected, $table->render());
	}

	public function testAttrTables()
	{
		$clients = Client::all();

		$columns = array(
			'name' => array(
				'heading' => 'Client Name',
				'value' => function($row)
				{
					return $row->firstname.' '.$row->lastname;
				},
				'cell_attr' => function($row)
				{
					return array('style' => 'font-weight:bold;');
				},
			),
			'Birthdate' => function($row)
			{
				return date('d-M Y', strtotime($row->birth_date));
			},
		);

		// Attribute Horizontal
		$table = Squi\Table::make($columns)
			->with('row_attr', function($row)
			{
				return array('data-uri' => 'clients/'.$row->id);
			})
			->with('class', 'table table-striped')
			->with('rows', $clients);

		$expected = $this->markup('attribute_horizontal');
		$this->assertSame($expected, $table->render());

		// Attribute Vertical
		$table = Squi\Table::make($columns)
			->with('row_attr', function($row)
			{
				return array('data-uri' => 'clients/'.$row->id);
			})
			->with('class', 'table')
			->with('rows', $clients)
			->with('layout', 'vertical');
		$table->add_class('table-striped');

		$expected = $this->markup('attribute_vertical');
		$this->assertSame($expected, $table->render());
	}

	/**
	 * Test Class-based config
	 *
	 * @return void
	 */
	public function testClassConfig()
	{
		$clients = Client::all();

		$table = Squi\Table::of('Client', $clients);

		$expected = $this->markup('classconfig_horizontal');
		$this->assertSame($expected, $table->render());
	}

	/**
	 * Test shorthand
	 *
	 * @return void
	 */
	public function testShorthand()
	{
		$clients = Client::all();

		$columns = array(
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

		$expected_horizontal = $this->markup('classconfig_horizontal');
		$expected_vertical = $this->markup('classconfig_vertical');

		$table = Squi\Table::make($columns, $clients, 'vertical');
		$this->assertSame($expected_vertical, $table->render());

		$table = Squi\Table::horizontal($columns, $clients);
		$this->assertSame($expected_horizontal, $table->render());

		$table = Squi\Table::vertical($columns, $clients);
		$this->assertSame($expected_vertical, $table->render());

		$table = Squi\Table::of('Client', $clients, 'summary_columns');
		$expected = $this->markup('classconfig_summary');
		$this->assertSame($expected, $table->render());

		$table = Squi\Table::of($clients);
		$this->assertSame($expected_horizontal, $table->render());

		$table = Squi\Table::of('Client', $clients);
		$this->assertSame($expected_horizontal, $table->render());
	}

	protected function markup($path)
	{
		return file_get_contents(Bundle::path('squi').'tests/markup/table/'.$path.'.html');
	}

}