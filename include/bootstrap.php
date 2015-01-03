<?php

/**
 * Default exception handler.
 *
 */
function myExceptionHandler($exception) {
  echo "EEZ: Uncaught exception: <p>" . $exception->getMessage() . "</p><pre>" . $exception->getTraceAsString(), "</pre>";
}
set_exception_handler('myExceptionHandler');


/** 
* Autoload classes
*
*/
function myAutoLoader($class)
{
	$filename = $class . '.php';

	if (file_exists(EEZ_CONTROLLER_PATH . DS . $filename))
	{
		include_once(EEZ_CONTROLLER_PATH . DS . $filename);
	}
	elseif (file_exists(EEZ_CLASSES_PATH . DS . $filename))
	{
		include_once(EEZ_CLASSES_PATH . DS . $filename);
	}
	elseif (file_exists(EEZ_MODELS_PATH . DS . $filename))
	{
		include_once(EEZ_MODELS_PATH . DS . $filename);
	}
	else
	{
		trigger_error("Class: {$class} not found!");
	}
}
spl_autoload_register('myAutoLoader');


/**
* Used for debugging
**/
function dump($var)
{
	echo "<pre>" . print_r($var, 1) . "</pre>";
}
