<?php

Autoloader::map(array(
	'Squi\\HTML_Element' => __DIR__.DS.'html_element.php',
	'Squi\\Table' => __DIR__.DS.'table.php',
	'Squi\\Table_Column' => __DIR__.DS.'table'.DS.'column.php',
	'Squi\\Table_Row' => __DIR__.DS.'table'.DS.'row.php',
	'Squi\\Table_Rowdata' => __DIR__.DS.'table'.DS.'rowdata.php',
	'Squi\\Form' => __DIR__.DS.'form.php',
	'Squi\\Form_Field' => __DIR__.DS.'form'.DS.'field.php',
	'Squi\\Form_Label' => __DIR__.DS.'form'.DS.'label.php',
	'Squi\\Form_Button' => __DIR__.DS.'form'.DS.'button.php',
));

// Comment or remove the following line to disable documentation
include 'docs.php';