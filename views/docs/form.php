<?php
// Helper for reusing the unit-testing markup
$markup = function($path)
{
   return file_get_contents(Bundle::path('squi').'tests/markup/table/'.$path.'.html');
};
?>
<h1>Squi\Form</h1>
<p class="lead">Laravel has a fantastic set of form element helpers (and as of 3.1, the really cool macro feature), but Squi takes it a step further to allow you to generate an entire laid-out form in just a few lines.</p>
<div class="subnav">
   <ul class="nav nav-pills" id="pagenav">
      <li><a href="#simple">Simple Form</a></li>
      <li><a href="#attr">Attributes</a></li>
      <li><a href="#layouts">Layouts</a></li>
      <li><a href="#model">Model Integration</a></li>
      <li><a href="#alloptions">All Options</a></li>
   </ul>
</div>
<section id="simple">
<div class="row-fluid">
   <div class="span12">
      <h2>Simple form</h2>
      <p>Let's start with a form we're all familiar with: a user registration form. Here are some basics demonstrating text, password and dropdown fields.</p>
      <div class="output" id="simple-form"><?php $code = 
'$fields = array(
   "name",
   "email",
   "Password" => array("type" => "password"),
   "Confirm Password" => array("type" => "password"),
   "user_source" => array(
      "type"    => "select",
      "label"   => "How did you hear about us?",
      "options" => array(
         "friend" => "From a friend",
         "google" => "Google search",
         "other"  => "Other"
      ),
   ),
);

echo Squi\Form::make($fields)->attr("action", "users/new");';
eval($code); ?></div>
   </div>
</div>
<div class="row-fluid">
   <div class="span5">
      <h3>PHP</h3>
      <pre class="prettyprint lang-php">
<?php echo str_replace('"', "'", $code); ?>
</pre>
   </div>
   <div class="span7">
      <h3>Generated HTML</h3>
      <pre class="prettyprint lang-html grab-html" data-source="#simple-form"></pre>
   </div>
</div>
</section>


<section id="attr">
<div class="row-fluid">
   <div class="span12">
      <h2>Applying attributes to fields and labels</h2>
      <p>Adding a little flair to your Squi form is as easy as passing an array of attributes and their values.
         Everything in Squi is a subclass of <code>Squi\HTML_Element</code>, which gives you jQuery-style methods like <code>attr($attribute [, $value])</code> and <code>add_class($class)</code>.
         Here we'll use the object-oriented syntax to build the form, making the email field required and making a label bold. 
         Also note the syntax of radio fields is identical to that of &lt;select&gt;.</p>
      <div class="alert alert-info">
         <strong>Note:</strong> All Squi classes are namespaced, but you may use Laravel's <code>Autoloader::alias('Form', 'Squi\\Form')</code> helper to alias each class. 
         However, in the case of <code>Squi\Form</code>, this will require you to namespace references to Laravel's Form class (<code>Laravel\Form</code>).
      </div>
      <div class="output" id="attr-form"><?php
$code = '$form = new Squi\Form;
$form->text("name");
$form->email("email")->value("example@example.com")->required();
$form->password("password");
$form->password("password2")->label("Confirm Password");
$form->radio("user_source")
   ->label(Squi\Form_Label::make("How did you hear about us?")->attr("style", "font-weight:bold"))
   ->options(array(
      "friend" => "From a friend",
      "google" => "Google search",
      "other" => "Other"
   ));
$form->buttons(array(
   Squi\Form_Button::make("Sign Up")->add_class("btn btn-primary"),
));

echo $form;';
eval($code); ?></div>
   </div>
</div>
<div class="row-fluid">
   <div class="span5">
      <h3>PHP</h3>
      <pre class="prettyprint lang-php">
<?php echo str_replace('"', "'", $code); ?>
</pre>
   </div>
   <div class="span7">
      <h3>Generated HTML</h3>
      <pre class="prettyprint lang-html grab-html" data-source="#attr-form"></pre>
   </div>
</div>
</section>


<section id="layouts">
<div class="row-fluid">
   <div class="span12">
      <h2>Layouts</h2>
         <p>Using <code>$form->layout()</code> method, you can have Squi render the form with your desired layout, currently with support for <code>basic</code>, <code>table</code> and <code>bootstrap</code>. 
               These provide a solid base for extension by CSS, but you may specify an artitrary layout to use by either passing a View name or View instance to <code>layout()</code>.<br />
               The <code>bootstrap</code> layout is named after the Twitter Bootstrap, and can be tweaked by adding and removing classes such as <code>form-horizontal</code>.<br />
               The <code>basic</code> layout in conjunction with the proper classes also provides excellent support for the other bootstrap form styles.</p>
   </div>
</div>
<div class="row-fluid">
   <div class="span4">
      <h3>Basic</h3>
      <form>
         <fieldset>
            <label for="name">Name</label>
            <input data-test="result!" type="text" name="name" id="name">

            <label for="birth_date">Birthdate</label>
            <input data-test="result!" type="text" name="birth_date" id="birth_date">

            <label for="lead_source">How did you hear about us?</label>
            <select name="lead_source" id="lead_source">
               <option value="friend">From a friend</option>
               <option value="google">Google Search</option>
               <option value="other">Other</option>
            </select>

            <div class="form-buttons">
               <button type="submit" class="btn">Submit</button>
            </div>
         </fieldset>
      </form>
   </div>
   <div class="span4">
      <h3>Table</h3>
      <form>
         <fieldset>
            <table class="form">
               <tr>
                  <th><label for="name">Name</label></th>
                  <td><input type="text" name="name" id="name"></td>
               </tr>
               <tr>
                  <th><label for="birth_date">Birthdate</label></th>
                  <td><input type="text" name="birth_date" id="birth_date"></td>
               </tr>
               <tr>
                  <th><label for="lead_source">How did you hear about us?</label></th>
                  <td><select name="lead_source" id="lead_source">
                  <option value="friend">From a friend</option>
                  <option value="google">Google Search</option>
                  <option value="other">Other</option>
               </select></td>
               </tr>
               <tr>
                  <td colspan="2" class="form-actions"><button name="submit" class="btn">Sign Up</button></td>
               </tr>
            </table>
         </fieldset>
      </form>
   </div>
   <div class="span4">
      <h3>Bootstrap (Horizontal)</h3>
      <form class="form-horizontal">
         <fieldset>
            <div class="control-group">
               <label class="control-label" for="name">Name</label>
               <div class="controls">
                  <input type="text" name="name" id="name">
               </div>
            </div>
            <div class="control-group">
               <label class="control-label" for="birth_date">Birthdate</label>
               <div class="controls">
                  <input type="text" name="birth_date" id="birth_date">
               </div>
            </div>
            <div class="control-group">
               <label class="control-label" for="lead_source">How did you hear about us?</label>
               <div class="controls"><select name="lead_source" id="lead_source">
                  <option value="friend">From a friend</option>
                  <option value="google">Google Search</option>
                  <option value="other">Other</option>
               </select></div>
            </div>
            <div class="form-actions">
                  <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </fieldset>
      </form>
   </div>
</div>
</section>


<section id="model">
    <h2>Integration with Models</h2>
    <p>If you find yourself creating forms for a model in multiple controllers, you might consider storing the form configuration inside the model declaration. 
         This makes for lean, legible controllers, and doesn't preclude creating one-off forms where needed. 
         <code>Table::of($class, $method)</code> allows to pass a model class name, with Squi automatically querying a static method of that class for the form configuration. 
         The default method (if one is not specified) is <code>configure_form($form)</code>. 
         Notice that the method is passed a reference to the <code>$form</code> it is generating, allowing you to modify the form attributes as well as add fields and buttons as you wish.</p>
<div class="row-fluid">
    <div class="span5">
         <h3>Model Declaration</h3>
         <pre class="prettyprint lang-php">
public static function configure_form($form)
{
   $form->text('name')->label('Client Name');
   $form->text('birth_date')
      ->label('Birthdate')
      ->value(function($instance)
      {
         return date('d-M Y', strtotime($instance->birth_date));
      });
}
</pre>
    </div>
    <div class="span7">
         <h3>Controller Code</h3>
         <pre class="prettyprint lang-php">
$client = Client::first();

$form = Squi\Form::of($client);
// Which is equivalent to
$form = Squi\Form::of('Client')->values($client);
</pre>
         <p>You could also add additional arbitrary methods to your model to configure alternate form configurations. You would then call upon those methods like so:</p>
         <pre class="prettyprint lang-php">
$form = Squi\Form::of('Client', 'alternate_config');
</pre>
    </div>
</div>
</section>

<section id="alloptions">
    <h2>The kitchen sink</h2>
    <p>There are many different ways to declare the configuration of Squi Forms. Here's a jumbled mess of most of them.</p>
<div class="row-fluid">
    <div class="span6">
         <h3>Squi\Form methods</h3>
         <pre class="prettyprint lang-php">
$form = Squi\Form::make()
   // Choose the built-in bootstrap layout
   ->layout('bootstrap')

   // Set a single attribute
   ->attr('class', 'form-horizontal')

   // Set an array of attributes
   ->attr(array(
      'action' => URL::to('some/controller'),
      'class' => 'form form-horizontal',
   ))

   // Add a class (also remove_class())
   ->add_class('form-horizontal')

   // Add an array of fields
   ->fields(array(
      'Client Name' => array(
         'name' => 'customer_name',
         'value' => 'Name goes here',
         'attr' => array('class' => 'client-field'),
      ),
      'automagic_label_field',
      'Label' => 'field_name',
   ));
</pre>
   <div class="alert alert-info">
      <strong>Pro tip:</strong>
      If the field label should be the same as the field name, just omit the label. 
      <br />Squi will intelligently "labelize" your name for you. 
      For example, "customer_name" would show up as "Customer Name".
   </div>
    </div>
    <div class="span6">
         <h3>Field Creation</h3>
<pre class="prettyprint lang-php">
// The following all create a text field named 'customer_name' 
// with label 'Name'.
// Some also apply the css class 'classy' to the field
$form->field()
   ->name('customer_name')
   ->label('Name')
   ->attr('class', 'classy');
$form->field('customer_name')->label('Name');
$form->field('Name', 'customer_name');
$form->field('Name', array(
   'type' => 'text',
   'name' => 'customer_name',
   'attr' => array('class' => 'classy'),
));
$form->field(array(
   'label' => 'Name',
   'name' => 'customer_name'
));
$form->field('Name', Squi\Form_Field::make('customer_name'));
$form->field(Squi\Form_Label::make('Name'),
   Squi\Form_Field::make('customer_name'));
$form->text('Name', 'customer_name');
$form->text('customer_name')->label('Name');

// Field with type "email" (HTML5)
// Works for any type (number, url, etc)
$form->email('email_address');

// Creates a select field (same syntax for radio)
$form->select('choices')->options(array(
   'value' => 'Label', 
   // ...
));

// Checkbox
$form->checkbox('check_me');
</pre>
   </div>
</div>
</section>