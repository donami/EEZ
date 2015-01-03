<?php
class Breadcrumb {

	/**
	 * Contains all breadcrumbs
	 */
	private static $breadcrumbs = array();

	/**
	 * Add a breadcrumb
	 *
	 * @param string $name  	Name of the breadcrumb
	 * @param string $link  	The page we should link to
	 * @param string $label  	The text that will be displayed
	 * @return void
	 */	
	public static function add($name, $link, $label = null)
	{
		// If no label is specified we use the name
		if (is_null($label)) 
			$label = $name;

		self::$breadcrumbs[] = array('name' => $name, 'link' => $link, 'label' => $label);
	}

	/**
	 * Display the breadcrumbs
	 *
	 * @return string
	 */
	public static function display()
	{
		$breadcrumbs = '<div class="breadcrumb">';

		$breadcrumbs .= '<a href="index" class="active">Hem</a>';

		// If there is one or more breadcrumbs in the array
		if (!empty(self::$breadcrumbs))
		{
			foreach (self::$breadcrumbs as $key => $value) 
			{
				$breadcrumbs .= '<a href="' . $value['link'] . '">' . $value['label'] . '</a>';  
			}
		}

		$breadcrumbs .= '</div>';

		return $breadcrumbs;
	}

	/**
	 * Return true if there is any added breadcrumbs 
	 *
	 * @return boolean
	 */
	public static function hasBreadcrumbs()
	{
		if (empty(self::$breadcrumbs))
			return false;

		return true;
	}
}