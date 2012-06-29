## Squi Bundle for Laravel

Currently Squi consists of two UI helpers: `Table` and `Form`. 
Squi is installable via the Artisan CLI:

	php artisan bundle:install squi

After installation, you can either auto-load the bundle in bundles.php:

	return array(
		'squi' => array('auto' => true),
	);

Or start the bundle on demand:

	Bundle::start('squi');


### Table

Creating a simple table:

	// Grab some objects to play with
	$things = Client::all();

	$table = Squi\Table::make()
		->with('columns', array(
			'name',
			'birth_date' => 'Birthdate',
		))
		->with('rows', $things);

This will create a table with the headings `Name` (capitalized from "name") and `Birthdate`. Each row will contain the values for the keys/properties `name` and `birth_date`, respectively. Simple, right?

But often we want to do fancier, one-off things with our tables. No problem:

	$table = Squi\Table::make()
		->with('columns', array(
			'name' => array(
				'heading' => 'Client Name',
				'value' => function($row)
				{
					return $row->firstname.' '.$row->lastname;
				},
				'cell_attr' => function($row)
				{
					return array('style' => 'font-weight: bold;');
				},
			),
			'birth_date' => array(
				'heading' => 'Birthdate',
				'value' => function($row)
				{
					return date('d-M Y', $row->birth_date);
				},
			),
		))
		->with('row_attr', function($row)
		{
			return array('data-uri' => 'clients/'.$row->id);
		})
		->with('class', 'table-striped');

Clone the repo and hit the URI `/squi/docs` for full documentation.

### Form

Laravel has a fantastic set of form element helpers, but Squi takes it a step further to allow you to generate an entire laid-out form in just a few lines. Here's an example of a user registration form:

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

Like the Table library, Squi\Forms are very much open to fine customizations. Clone the repo and hit the URI `/squi/docs` for full documentation and plenty of examples.

### Fork Me!

Squi is very young, and I'm open to any suggestions and pull requests.
