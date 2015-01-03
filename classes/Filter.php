<?php
class Filter {

	/**
	 * Contains all added filters
	 */
	public static $filters = array();

	/**
	 * Bind the filter to a name 
	 *
	 * @param string $name
	 * @param function $callback
	 * @return void
	 */
	public static function bind($name, $callback)
	{
		if (!isset(self::$filters[$name]))
			self::$filters[$name] = $callback;
	}

	/**
	 * Run the filter by it's name and return it's value
	 *
	 * @param string $name 	Name of the filter
	 * @return boolean
	 */
	public static function run($name)
	{
		return call_user_func(self::$filters[$name]);
	}
}