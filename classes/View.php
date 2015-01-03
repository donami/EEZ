<?php
class View extends Controller
{
	protected $_variables = array();

	private $_layout = 'layout.tpl.php';

	private static $view;

	/**
	 * Class constructor.
	 *
	 * @return void
	 */	
	public function __construct() 
	{
		global $eez;

		$this->_variables = $eez;
	}

	/**
	 * Class destructor
	 *
	 * @return void
	 */
	public function __destruct()
	{
		if (isset($this->_variables['_module']))
		{
			$path = dirname(__DIR__) . DS . 'modules' . DS . $this->_variables['_module'] . DS . 'view' . DS .self::$view . '.tpl.php';
		}
		else 
		{
			$path = EEZ_TEMPLATE_PATH . DS . self::$view . '.tpl.php';
		}
		


		// Make sure that the view exists
		if (file_exists($path))
		{
			// Start buffer
			ob_start();

			// Extract the variables so we can use them directly in the view
			extract($this->_variables);

			// Include the view and store it in $content
			include_once($path);

			$content = ob_get_clean();

			// Include the layout
			include_once(EEZ_TEMPLATE_PATH . DS . $this->_layout);
		}
		else
		{
			trigger_error('View: "' . $path . '" does not exist!');
		}
	}

	/**
	 * Fetches the title from the objects variables, if it's not set we return the default title
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return (isset($this->_variables['_title'])) ? $this->_variables['_title'] : PAGE_TITLE;
	}


	/**
	 * Creates an object of the view we want to display
	 *
	 * @param string $view 	The name of the view to load
	 * @return object
	 */
	public static function make($view)
	{
		self::$view = $view;

		return new View();
	}

	/**
	 * Assign variables to the views object
	 *
	 * @param array $data 
	 * @return void
	 */
	public function with($data = array())
	{
		$this->_variables = array_merge($this->_variables, $data);
	}

}