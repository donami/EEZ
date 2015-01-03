<?php
class Auth
{
	static $id;
	static $username;
	static $password;
	static $authed = false;
	static $admin;

	/**
	 * Try to auth a user with specifed username and password
	 *
	 * @param string $username
	 * @param string $password
	 * @return boolean
	 **/
	public static function authenticate($username, $password) 
	{
		// If a user was found
		if ($user = DB::table('user')->where(['username' => $username])->first()) 
		{
			// Check the hash that has been created with password_hash() function
			if (password_verify($password, $user->hash)) 
			{
				self::$authed = true;
				self::$id = $user->id;
				self::$username = $username;
				self::$password = $password;
				self::$admin = $user->admin;
				$_SESSION['username'] = $username;
				$_SESSION['password'] = $password;

				return true;
			}
		}

		// If login was unsuccessful we return false and make sure that the session is destroyed
		self::logout();
		return false;
	}

	/**
	 * Check if user is authed
	 *
	 * @return boolean
	 **/
	public static function is_authed() {
		return self::$authed or false;
	}

	/**
	 * Return the user ID of authed user
	 *
	 * @return int
	 */
	public static function id() {
		return self::$id or false;
	}	

	/**
	 * Return username
	 *
	 * @return string
	 **/
	public static function username() {
		return self::$username;
	}

	/**
	 * Check if user has admin rights
	 *	
	 * @return boolean
	 */
	public static function is_admin()
	{
		return self::$admin or false;
	}

	/**
	 * Logout, clear session
	 *
	 * @return void
	 **/
	public static function logout() {
		$_SESSION['username'] = null;
		$_SESSION['password'] = null;
	}
}
if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
	Auth::authenticate($_SESSION['username'], $_SESSION['password']);
}