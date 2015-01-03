<?php
class Controller
{
	public $msg;

	/**
	 * Default constructor
	 *
	 **/
	public function __construct() 
	{ 
		$this->msg = new Message();
	
		// Pass along any messages
		$this->setData('_messages', $this->msg->display());
	}


	/**
	 * Change the title of the page, if not used default title will be shown
	 *
	 * @param string $title 	The title of the page
	 * @return void
	 */
	public function setTitle($title)
	{
		// Change the title in eez variable
		$this->setData('_title', $title);

	}

	/**
	 * Add a stylesheet
	 *
	 * @param string $css   CSS file to be loaded 	
	 * @return void
	 */
	public function addCSS($css)
	{
		global $eez;

		$eez['_stylesheets'][] = $css;
	}

	/**
	 * Add a javascript file
	 *
	 * @param string $script   JS file to be loaded 	
	 * @return void
	 */
	public function addJS($script)
	{
		global $eez;

		$eez['_javascript'][] = $script;
	}


	/**
	 * Add or change data in the global EEZ variable
	 *
	 * @param string $key 	
	 * @param string $value
	 * @return void
	 */
	public function setData($key, $value)
	{
		// Load the eez variable
		global $eez;

		$eez[$key] = $value;
	}

	/**
	 * If the controller is called from a module assign value
	 *
	 * @param string $module
	 * @return void
	 **/
	public function setModule($module)
	{
		global $eez;

		$eez['_module'] = $module;
	}

	/**
	 * Return the variables stored in the object
	 *
	 * @return array
	 **/
	public function getContent()
	{
		return $this->_variables;
	}

	/**
	 * Access and display the messages if there is any
	 *
	 * @return string
	 **/
	public function displayMessage()
	{
		return $this->msg->display();
	}	
}