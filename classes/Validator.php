<?php
class Validator
{
	private $errors = array();
	private $messages = array();
	private $values  = array();

	const F_REQUIRED 	= 'Du kan inte lämna obligatoriska fält tomma.';
	const F_MIN 		= 'Strängen måste vara minst :min tecken';
	const F_NUMERIC		= 'Input måste vara numerisk';
	const F_EQUAL 		= 'De två fälten stämmer ej överens';
	const F_EMAIL 		= 'Fältet är ingen korrekt e-postadress';

	public function __construct() { }

	public function make($values = array(), $rules = array(), $messages = array())
	{
		$this->messages = $messages;
		$this->values = $values;

		foreach ($values as $key => $value) 
		{
			// Make sure that the value has a rule set, otherwise we should not do anything with it
			if (!empty($rules[$key]))
			{
				$flags = explode('|', $rules[$key]);

				foreach ($flags as &$flag) 
					$flag = 'flag'.ucfirst($flag);

				foreach ($flags as $k => $v) 
				{
					$v = explode(':', $v);
					$method = $v[0];
					$v[0] = $key;
					array_unshift($v, $value);
					call_user_func_array(array(__CLASS__, $method), $v);
				}
			}
		}

	}
	private function getMessage($key, $flag)
	{
		$flag = strtolower(ltrim($flag, 'flag'));

		if (array_key_exists($key.'.'.$flag, $this->messages)) {
			$msg = $this->messages[$key.'.'.$flag];
		}
		else {
			$constant = strtoupper('F_' . $flag);
			$msg =  constant('Validator::' . $constant);
		}

		return $msg;
	}

	/**
	 * Make sure that the string variable is not empty
	 *
	 * @param string $string
	 * @param string $key
	 * @return boolean
	 */
	private function flagRequired($string, $key)
	{
		if (!empty($string))
			return true;

		$this->errors[] = $this->getMessage($key, __FUNCTION__);
		return false;
	}

	/**
	 * Validate if the string has more than the specified amount of characters
	 *
	 * @param string $string
	 * @param string $key
	 * @param int $mint
	 * @return boolean
	 */
	private function flagMin($string, $key, $min)
	{
		if (strlen($string) >= $min)
			return true;
		else {
			$msg = $this->getMessage($key, __FUNCTION__);
			$this->errors[] = str_replace(':min', $min, $msg);
			return false;
		}
	}

	/**
	 * Check if the value is equal to another
	 *
	 * @param string $string
	 * @param string $key
	 * @param string $equalTo
	 * @return boolean
	 */
	private function flagEqual($string, $key, $equalTo)
	{
		// The value that we should compare with
		$equalTo = $this->values[$equalTo];

		if ($string == $equalTo)
			return true;

		$this->errors[] = $this->getMessage($key, __FUNCTION__);

		return true;
	}

	/**
	 * Check if the value is an email address
	 *
	 * @param string $string
	 * @param string $key
	 * @return boolean
	 */
	private function flagEmail($string, $key)
	{
		if (filter_var($string, FILTER_VALIDATE_EMAIL) !== false)
			return true;

		$this->errors[] = $this->getMessage($key, __FUNCTION__);

		return true;
	}

	private function flagNumeric($var, $key)
	{
		if (!is_numeric($var)) $this->errors[] = $this->getMessage($key, __FUNCTION__);

		return is_numeric($var);
	}

	public function fails()
	{
		if (empty($this->errors)) {
			return false;
		}
		return true;
	}

	public function errors()
	{
		return $this->errors;
	}
}