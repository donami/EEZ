<?php
class Message 
{
	private $types = array('E', 'S');				# The allowed message types
	private $classes = array('error', 'success');	# Classes to be used

	public function __construct()
	{
		// If array isn't set we create one
		if (!isset($_SESSION['messages'])) $_SESSION['messages'] = array();
	}

	/**
   	 * Adds a message
   	 *
   	 * @param string $message   The message to be displayed
   	 * @param string $type 		Type of message
   	 * @param string $redirect 	Redirect user to this URL
   	 * @return void
   	 **/
	public  function add($message, $type, $redirect = null)
	{
		// If the message type is invalid, trigger error and return false
		if (!in_array($type, $this->types)) {
			trigger_error("Incorrect type value set for message");
			return false;
		}

		// Replace the short types (E, S) with the longer names (error, success)
		$type = str_replace($this->types, $this->classes, $type);

		if (is_array($message)) {
			foreach ($message as $key => $value) 
			{
				$_SESSION['messages'][$type][] = $value;
			}
		}
		else {
			// Set the message and use the type as key
			$_SESSION['messages'][$type][] = $message;			
		}


		// If redirect argument has been set, send the user to the specified page
		if (!is_null($redirect)) {
			if ($redirect == -1)
				header("location: " . $_SERVER['HTTP_REFERER']);
			else	
				Redirect::to($redirect);

			exit();
		}
	}

	/**
   	 * Display the messages
   	 *
   	 * @return string
   	 **/
	public function display()
	{	
		$html = '';

		// If there is no messages return false
		if (!isset($_SESSION['messages'])) return false;

		// Loop through the types
		foreach ($this->classes as $type) {

			// Loop through the message in current type and create a HTML element which we store in the HTML variable
			if (!empty($_SESSION['messages'][$type])) {

				$html .= '<div class="' . $type . '">';

				foreach ($_SESSION['messages'][$type] as $msg) {
					$html .= '<div>' . $msg . '</div>';
				}

				$html .= '</div>';

				// Clean up the session and remove the message as it has been viewed by our user
				$this->clear($type);				
			}

		}

		// Return the html variable to be printed in our template files
		return $html;
	}

	/**
   	 * Clear messages from session
   	 *
   	 * @param string $type
   	 * @return void
   	 **/
	public function clear($type)
	{
		unset($_SESSION['messages'][$type]);
	}

	/**
   	 * Check if there are any errors
   	 *
   	 * @return boolean
   	 **/
	public function hasErrors() 
	{ 
		return empty($_SESSION['messages']['error']) ? false : true;	
	}

}