<style>
.output table:not(.table) th, 
.output table:not(.table) td {
   padding: 0.2em 1em;
   border: 1px solid #ccc;
}
</style>
<?php
// Helper for reusing the unit-testing markup
$markup = function($path)
{
   return file_get_contents(Bundle::path('squi').'tests/markup/table/'.$path.'.html');
};
?>
<h1>Squi\Table</h1>
<p class="lead">Easily display database results and Eloquent/Model objects in <em>really</em> customizable tables.</p>
<div class="subnav">
   <ul class="nav nav-pills" id="pagenav">
         <li><a href="#table-simple">Simple Table</a></li>
         <li><a href="#table-custom">Advanced Customization</a></li>
         <li><a href="#table-vertical">Left-Oriented Headings</a></li>
         <li><a href="#table-tableable">Model Integration</a></li>
         <li><a href="#table-alloptions">All Options</a></li>
    </ul>
</div>
<section id="table-simple">
<div class="row-fluid">
   <div class="span12">
         <h2>Simple table from DB results</h2>
         <p>Let's start with the simplest use case. Assume our data objects (these can be Model instances, DB query results, or simply an array of associative arrays) have the properties <code>name</code> and <code>birth_date</code>. We'll specify that <code>birth_date</code> should have the heading "Birthdate", and leave Squi to assume that the <code>name</code> column should just be capitalized to "Name". Here's what our table will look like with three sample rows:</p>
         <div class="output" id="simple-table"><?php echo $markup('simple_horizontal'); ?></div>
   </div>
</div>
<div class="row-fluid">
   <div class="span5">
      <h3>PHP</h3>
      <pre class="prettyprint lang-php">
$clients = Client::all();

$table = Squi\Table::make(array(
         'name',
         'birth_date' => 'Birthdate',
      ), $clients);
</pre>
   </div>
   <div class="span7">
      <h3>Generated HTML</h3>
      <pre class="prettyprint lang-html grab-html" data-source="#simple-table"></pre>
   </div>
</div>
</section>
<section id="table-custom">
<div class="row-fluid">
   <div class="span12">
      <h2>Table with attributes and dynamic values</h2>
      <p>So often when you're displaying a table, you aren't just spitting out the contents of a database table. You probably want to format that data, maybe concatenate some fields, add conditional CSS classes, etc. This example shows how this can be done with Squi.</p>
         <p>Many parameters allow you to provide closures, which you can use to return parameters based on their context. The <code>value</code> parameter of a column can be specified as a closure accepting a reference to the <code>$row</code> object belonging to the current table row. The same goes for most of the <code>_attr</code> parameters, opening up some neat possibilities for customization.</p>
      <div class="output" id="attr-table"><?php echo $markup('attribute_horizontal'); ?></div>
   </div>
</div>
<div class="row-fluid">
   <div class="span5">
      <h3>PHP</h3>
      <pre class="prettyprint lang-php">
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

$table = Squi\Table::make($columns)
   ->with('row_attr', function($row)
   {
      return array('data-uri' => 'clients/'.$row->id);
   })
   ->with('class', 'table table-striped')
   ->with('rows', $clients);
</pre>
   </div>
   <div class="span7">
      <h3>Generated HTML</h3>
      <pre class="prettyprint lang-html grab-html" data-source="#attr-table"></pre>
   </div>
</div>
</section>


<section id="table-vertical">
<div class="row-fluid">
   <div class="span12">
      <h2>Left-oriented headings</h2>
         <p>Squi\Table allows for two layouts: <code>horizontal</code> (default) and <code>vertical</code>. Just specify the <code>layout</code> parameter and Squi does the rest.</p>
         <p>Also notice the use of the <code>add_class()</code> helper that allows you to easily add and remove (with <code>remove_class()</code>) CSS classes from the table.</p>
      <div class="output" id="vert-table"><?php echo $markup('classconfig_vertical'); ?></div>
   </div>
</div>
<div class="row-fluid">
   <div class="span5">
      <h3>PHP</h3>
      <pre class="prettyprint lang-php">
$table = Squi\Table::make(array(
      'name',
      'Birthdate' => function($row)
      {
         return date('d-M Y', strtotime($row->birth_date));
      },
      'lead_source' => 'Lead Source',
   ), $clients)
   ->with('layout', 'vertical');
</pre>
   </div>
   <div class="span7">
      <h3>Generated HTML</h3>
      <pre class="prettyprint lang-html grab-html" data-source="#vert-table"></pre>
   </div>
</div>
</section>


<section id="table-tableable">
   <h2>Integration with Models</h2>
   <p>If you find yourself displaying a particular model in a table multiple times, you might consider storing the column configuration inside the model declaration. <code>Squi::of($class, $rows)</code> allows to pass a model class name, with Squi automatically querying a static method of that class for the column configuration array. The default method (if one is not specified) is <code>table_columns($table)</code>. Notice that the method is passed a reference to the <code>$table</code> it is generating, allowing you to modify the column configuration based on table configuration such as layout or class.</p>
<div class="row-fluid">
   <div class="span5">
      <h3>Model Declaration</h3>
      <pre class="prettyprint lang-php">
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
            return $row->lead_source;
         },
      ),
   );
}
</pre>
   </div>
   <div class="span7">
         <h3>Controller Code</h3>
         <pre class="prettyprint lang-php">
$clients = Client::all();

$table = Squi\Table::of('Client', $clients);
</pre>
         <p>You could also add additional methods to your model to configure alternate column configurations, like maybe a summary table that only includes a few columns. You would then call upon those methods like so:</p>
         <pre class="prettyprint lang-php">
$table = Squi\Table::of('Client', $clients, 'summary_table');
</pre>
    </div>
</div>
</section>

<section id="table-alloptions">
   <h2>The kitchen sink</h2>
   <p>There are a bunch of different ways to declare the configuration of Squi Tables, so here's a jumbled mess of most of them. A more organized listing of available options follows.</p>
<div class="row-fluid">
   <div class="span6">
         <h3>Using with( ) options</h3>
         <pre class="prettyprint lang-php">
$table = Squi\Table::make()
   ->with('layout', 'horizontal')
   ->with('table_attr', array('class' => 'table table-striped'))
   ->with('row_attr', function($row)
   {
      return array('data-uri' => 'clients/'.$row->id);
   })
   ->with('heading_attr', array('style' => 'color:red;'))
   ->with('norecords_message', 'Sorry, no rows to display')
   ->with('columns', array(
      'name' => array(
         'heading' => 'Client Name',
         'heading_attr' => array('width' => 300),
         'value' => function($row)
         {
            return $row->firstname.' '.$row->lastname;
         },
         'cell_attr' => array('class' => 'client-cell'),
      ),
   ));
</pre>
   </div>
   <div class="span6">
         <h3>Shorthand</h3>
         <pre class="prettyprint lang-php">
Squi\Table::make($columns, $clients, 'vertical');

Squi\Table::horizontal($columns, $clients);

Squi\Table::vertical($columns, $clients); 

Squi\Table::of('Client', $clients, 'summary_columns');

Squi\Table::of($clients);
</pre>
         <p>All of these shorthand methods return the Table instance to allow method chaining.</p>
         <p><strong>Please Note:</strong> Using the syntax <code>Squi\Table::of($clients)</code> requires the existence of at least one <code>Client</code> object in the <code>$clients</code> array, so it should only be used when that prerequisite is guaranteed to be met.</p>
   </div>
</div>
<div class="row-fluid">
   <div class="span6">
      <h3>Table Parameters</h3>
      <table class="table table-striped">
         <thead>
            <tr>
               <th>Parameter</th>
               <th>Type</th>
               <th>Default</th>
            </tr>
         </thead>
         <tbody>
            <tr>
               <td>layout</td>
               <td>string</td>
               <td>horizontal</td>
            </tr>
            <tr>
               <td>table_attr</td>
               <td>array</td>
               <td>array()</td>
            </tr>
            <tr>
               <td>heading_attr</td>
               <td>array</td>
               <td>array()</td>
            </tr>
            <tr>
               <td>row_attr</td>
               <td>array | closure($row)</td>
               <td>array()</td>
            </tr>
            <tr>
               <td>cell_attr</td>
               <td>array | closure($col, $row)</td>
               <td>array()</td>
            </tr>
            <tr>
               <td>columns</td>
               <td>array</td>
               <td>array()</td>
            </tr>
            <tr>
               <td>rows</td>
               <td>array</td>
               <td>array()</td>
            </tr>
            <tr>
               <td>norecords_message</td>
               <td>string | false</td>
               <td>false</td>
            </tr>
            <tr>
               <td>class</td>
               <td>string | array</td>
               <td></td>
            </tr>
         </tbody>
      </table>
   </div>
   <div class="span6">
      <h3>Column Parameters</h3>
      <table class="table table-striped">
         <thead>
            <tr>
               <th>Parameter</th>
               <th>Type</th>
               <th>Default</th>
            </tr>
         </thead>
         <tbody>
            <tr>
               <td>heading</td>
               <td>string</td>
               <td>ucfirst ({column key})</td>
            </tr>
            <tr>
               <td>heading_attr</td>
               <td>array</td>
               <td>array()</td>
            </tr>
            <tr>
               <td>cell_attr</td>
               <td>array | closure($row)</td>
               <td>array()</td>
            </tr>
            <tr>
               <td>value</td>
               <td>string | closure($row)</td>
               <td></td>
            </tr>
         </tbody>
      </table>
   </div>
</div>
</section>