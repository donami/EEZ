<?php

include("../include/config.php");


/* ------------------------------------------------------------------------ /

	Filters below

/ -------------------------------------------------------------------------*/

/**
 * Filter for pages that should only be accessed by administrators
 */
Filter::bind('admin', function() {
	return Auth::is_admin();
});


/**
 * Filter for pages that should only be accessed by authenticated users
 */
Filter::bind('authed', function() {
	return Auth::is_authed();
});


/* ------------------------------------------------------------------------ /

	Routes below

/ -------------------------------------------------------------------------*/


Route::get('/', array('action' => 'index@HomeController'));
Route::get('index', array('action' => 'index@HomeController'));

Route::dispatch();
