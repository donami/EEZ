<?php
class Redirect extends Route
{
	/**
	 * Redirects the user to a specified route
	 *
	 * @param string $route 	Route to be redirected to
	 * @return void
	 */
	public static function to($route)
	{
		if (isset(self::$_routes[$route]))
		{
			header("location: " . $route);
		}
	}
}