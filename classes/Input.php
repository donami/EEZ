<?php
class Input
{
	public static function get($key)
	{
		if (isset($_GET[$key]))
			return $_GET[$key];

		elseif (isset($_POST[$key])) 
			return $_POST[$key];

		return null;
	}

	public static function has($key)
	{
		return (isset($_GET[$key]) || isset($_POST[$key])) or false;
	}
}