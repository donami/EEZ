<?php

/**
 * Set the error reporting.
 *
 */
error_reporting(-1);              // Report all type of errors
ini_set('display_errors', 1);     // Display all errors 
ini_set('output_buffering', 0);   // Do not buffer outputs, write directly


define('DS', DIRECTORY_SEPARATOR);
define('PAGE_TITLE', 'Page title');

// Database location
define("DB_PATH", dirname(__FILE__) . "/Eez.sqlite");

define("EEZ_INCLUDE_PATH", __DIR__ . DS);
define("EEZ_TEMPLATE_PATH", dirname(__DIR__) . DS . "view");
define("EEZ_CONTROLLER_PATH", dirname(__DIR__) . DS . "controllers");
define("EEZ_CLASSES_PATH", dirname(__DIR__) . DS . "classes");
define("EEZ_MODELS_PATH", dirname(__DIR__) . DS . "models");
define("EEZ_PUBLIC_DIR", dirname(__DIR__) . DS . "public");
define("EEZ_MODULE_DIR", dirname(__DIR__) . DS . "modules");

/**
 * Include bootstrapping functions.
 *
 */
include(EEZ_INCLUDE_PATH . DS . 'bootstrap.php');

/**
 * Start the session.
 *
 */
session_name(preg_replace('/[^a-z\d]/i', '', __DIR__));
session_start();

/**
 * Create the Eez variable and assign default variables
 *
 */
$eez = array(
			'_stylesheets' => array('css/style.css'),
			'_favicon' => 'img/favicon.ico',
			'_title' => 'Page title'
		);


/**
 * Settings for JavaScript.
 *
 */

#$eez['_jquery'] = '//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js';	 	# Uncomment to include jQuery

