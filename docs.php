<?php
/**
 * Documentation for Squi
 * (To disable, comment out the line in squi/start.php that loads this bundle)
 */

Route::get('squi/docs', function()
{
	return View::make('squi::docs.template')
		->nest('content', 'squi::docs.index')
		->with('title', 'Docs');
});

Route::get('squi/docs/form', function()
{
	return View::make('squi::docs.template')
		->nest('content', 'squi::docs.form')
		->with('title', 'Form');
});

Route::get('squi/docs/table', function()
{
	return View::make('squi::docs.template')
		->nest('content', 'squi::docs.table')
		->with('title', 'Table');
});