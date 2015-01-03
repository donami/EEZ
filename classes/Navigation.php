<?php
class Navigation 
{
	private static $menu = array();

	/**
	 * Display the menu 
	 *
	 * @return string
	 **/
	public static function display() 
	{
		$html = "<nav id=\"menu\">\n";
		$html .= "<ul>\n";

		foreach(self::$menu as $item) {
			$html .= '<li><a href="' . $item['url'] . '">' . $item['text'] . '</a></li>';
		}

		$html .= "</ul>\n";

		$html .= "</nav>\n";

		return $html;
	}

	/**
	 * Add a menu item 
	 *
	 * @param string $name 	The name of the link
	 * @param string $text 	The text to be displayed in the menu
	 * @param string $url 	The url we want to be sent to when we click the link
	 * @return void
	 **/
	public static function add($name, $text, $url)
	{
		self::$menu[$name] = array('text' => $text, 'url' => $url);
	}
}