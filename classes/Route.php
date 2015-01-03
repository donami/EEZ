<?php
class Route
{
	/**
	 * Array containing all the routes
	 *
	 * @var array
	 */
	protected static $_routes = array();

	/**
	 * Store the routes in the array
	 *
	 * @param string $url 	The url to the route
	 * @param array $options 	Array with options such as "action"
	 * @return void
	 */
	public static function get($url, $options = array())
	{
		self::$_routes[$url] = $options;
	}

	/**
	 * Parse the route to correct format
	 *
	 * @return string
	 */
	private static function parseRoute()
	{
		if (isset($_SERVER['PATH_INFO']))
		{
			$path = $_SERVER['PATH_INFO'];

			// We don't want to remove first or ending slash if path is only "/""
			if (strlen($path) > 1)
			{
				$path = ltrim($path, '/');
				$path = rtrim($path, '/');
			}			
		}
		else
		{
			$path = '/';
		}

		return $path;
	}

	/**
	 * Load the correct controller for this route.
	 *
	 * @return void
	 */
	public static function dispatch()
	{
		// Get the route in correct format
		$path = self::parseRoute();

		// If the route exists
		if (isset(self::$_routes[$path]))
		{
			$route = self::$_routes[$path];

			// Determine the correct controller and method to be called
			if (isset($route['action']))
			{
				// Split the action by the @-sign
				$action = explode('@', $route['action']);

				$method = $action[0];
				$controller = $action[1];
			}

			if (isset($route['module']))
			{
				include_once (EEZ_MODULE_DIR . DS . $route['module'] . DS . "/controller/" . DS . $controller . ".php");
			}

			// If the route has any specified filters
			if (isset($route['filter']))
			{
				// If the filter return false we redirect the user to the homepage
				if (Filter::run($route['filter']) == false)
				{
					header("location: index");
					exit();
				}
			}

			// Create an object of the controller and call the method
			$controller = new $controller();
			$controller->$method();
		}
		else
		{
			trigger_error("This page does not exist");
		}

	}
}