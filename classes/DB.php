<?php
class DB
{
	public static $table;
	private static $instance = null;
	private static $numQueries = 0;
	private static $queries = array();
	public static $fields = '';
	public static $conn;
	public static $whereClause = '';
	public static $orderBy = '';
	public static $skip = 0;
	public static $take = 0;
	public static $join = '';
	public static $limit = '';
	public static $params;
	public static $lastInsertId;
	public static $pagination;
	public static $links;					# Pagination links

	const DEBUG = false;					// Set true for debugging

	const PREFIX = 'project_';				# Prefix for tables

	const DB_DSN 		= 'mysql:host=blu-ray.student.bth.se;dbname=mahv14;';
	const DB_USER 		= 'mahv14';
	const DB_PASSWORD 	= 't63HBh/2';

	public function __construct()
	{
		// Connect to a MySQL database using PHP PDO
		$dsn      = self::DB_DSN;
		$login    = self::DB_USER;
		$password = self::DB_PASSWORD;
		$options  = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'");
		try {
			self::$conn = new PDO($dsn, $login, $password, $options);
		}
		catch(Exception $e) {
			throw new PDOException('Could not connect to database, hiding connection details.'); // Hide connection details.
		}
	}


	/**
	* Set the table to run all queries on
	* 
	* @param string $table
	* @return object
	*/
	public static function table($table)
	{
		if (!isset(self::$instance)) {
			self::$instance = new DB();
		} 

		self::$table = self::PREFIX . $table;

		return self::$instance;
	}

	/**
	* Raw select query, use "?" as placeholders and add them to $params
	* 
	* @param string $statement
	* @param array $params
	* @return object
	*/
	public static function select($statement, $params = array())
	{
		if (!isset(self::$instance)) {
			self::$instance = new DB();
		} 	

		$sql = $statement;
		$sth = self::$conn->prepare($sql);
		$sth->execute($params);
		$res = $sth->fetchAll(PDO::FETCH_OBJ);
		self::init($sql);

		return $res;	
	}

	/**
	* Specifiy the fields to be selected
	* 
	* @param array $fields
	* @return object
	*/
	public static function selectFields($fields = array())
	{
		// If the fields is array we should loop through them and make the string
		if (is_array($fields))
		{
			$fieldsString = '';	

			foreach ($fields as $key => $value)
			{
				$fieldsString .= DB::PREFIX . $value . ", ";
			}

			// Remove the last comma
			self::$fields = rtrim($fieldsString, ", ");
		}
		else
		{
			// If it's not an array we simply used the value
			self::$fields = DB::PREFIX . $fields;
		}


		return self::$instance;		
	}

	/**
	 * Add a JOIN to th query
	 * 
	 * @param string $table 	The table to join
	 * @param string $where 	The where clause to be set in the ON statement
	 * @param string $operator	The operator in ON clause
	 * @param string $value 	The value to be compared to
	 * @return object
	 */
	public static function join($table, $where, $operator, $value)
	{
		$table = self::PREFIX . $table;
		$value = self::PREFIX . $value;
		$where = self::PREFIX . $where;


		self::$join = "INNER JOIN {$table} ON {$where} {$operator} {$value}";

		return self::$instance;
	}


	/**
	* Build a statement for comparing two values
	* 
	* @param array $options
	* @return object
	*/
	public static function where($options = array())
	{
		$statement = '';
		foreach ($options as $key => $value) 
		{
			// Check if it contains a dot (for specifing tables)
			if (strpos($key, '.') !== false) {
			    $key = DB::PREFIX . $key;
			}

			$statement .= " && {$key} =?";
			self::$params[] = $value;
		}

		return self::buildWhere($statement);
	}

	/**
	* Build a where statement for checking if a value is NOT
	* 
	* @param array $options
	* @return object
	*/
	public static function whereNot($options = array())
	{
		$statement = '';
		foreach ($options as $key => $value) 
		{
			$statement .= " && {$key} !=?";
			self::$params[] = $value;
		}

		return self::buildWhere($statement);
	}

	/**
	* Build a statement for comparing with the LIKE operator
	*
	* @param array $options 
	* @return object
	*/
	public static function whereLike($options = array())
	{
		$statement = '';
		foreach ($options as $key => $value) 
		{
			if (!empty($value)) {
				$statement .= " && {$key} LIKE ?";
				self::$params[] = '%'.$value.'%';
			}
		}

		return self::buildWhere($statement);
	}

	/**
	 * Build a where statement for checking if a value is not NULL
	 *	
	 * @param string $field
	 * @return object
	 */
	public static function whereNotNull($field)
	{
		$statement = $field . ' IS NOT NULL';

		return self::buildWhere($statement);
	}

	/**
	 * Build a where IN statement
	 *
	 * @param string $field
	 * @param array $values
	 * @return object
	 */
	public static function whereIn($field, $values = array())
	{
		$val = '';
		foreach ($values as $value) 
		{
			$val .= $value . ', ';
		}

		// Remove the last comma
		$val = rtrim($val, ', ');
		$statement = " && {$field} IN ({$val})";

		return self::buildWhere($statement);
	}

	/**
	* Where statement for getting rows with values between two numbers
	* 
	* Note: To use min / max values only, set the other value to null
	* @param array $options
	* @return object
	*/
	public static function whereBetween($options = array())
	{
		$statement = '';
		foreach ($options as $key => $value) 
		{
			if (!is_array($value))
				break;

			if (empty($value[0]) && empty($value[1]))
				break;

			if (empty($value[0])) {
				$statement .= " && {$key} <=?";
				self::$params[] = $value[1];
			}
			elseif (empty($value[1])) {
				$statement .= " && {$key} >=?";
				self::$params[] = $value[0];
			}
			else {
				$statement .= " && {$key} >=? && {$key} <=?";
				self::$params[] = $value[0];
				self::$params[] = $value[1];
			}
		}

		return self::buildWhere($statement);
	}

	/**
	* Build the where statemtent
	* 
	* @param string $statement
	* @return object
	*/
	private static function buildWhere($statement)
	{
		if (!empty($statement))
		{
			// Check if we want to prepend "WHERE " or not
			if (empty(self::$whereClause)) {
				self::$whereClause = 'WHERE ' . ltrim($statement, ' &&');
			}
			else {
				self::$whereClause = self::$whereClause . $statement;
			}
		}

		return self::$instance;
	}

	/**
	* Raw query, use "?" as placeholders and add them to $params
	* 
	* @param string $statement
	* @param array $params
	* @return object
	*/
	public static function raw($statement, $params = array())
	{
		if (!isset(self::$instance)) {
			self::$instance = new DB();
		} 	

		$sql = $statement;
		$sth = self::$conn->prepare($sql);
		$sth->execute($params);
		$res = $sth->fetchAll(PDO::FETCH_OBJ);
		self::init($sql);

		return $res;			
	}

	/**
	* Assign the order to sort by
	* 
	* @param string $column
	* @param string $order    // 'ASC' or 'DESC')
	* @return object
	*/
	public static function orderBy($column, $order = 'DESC')
	{
		if (!is_null($column) && !is_null($order))
			self::$orderBy = "ORDER BY {$column} {$order}";

		return self::$instance;
	}

	/**
	* Amount of rows to skip in the limit
	* 
	* @param int $skip
	* @return object
	*/
	public static function skip($skip)
	{
		self::$skip = $skip;

		return self::buildLimit();
	}

	/**
	* Set the amount of rows to take
	* 
	* @param int $take
	* @return object
	*/
	public static function take($take)
	{
		self::$take = $take;

		return self::buildLimit();
	}

	/**
	* Build a limit to be used in the SQL queries
	* 
	* @return object
	*/
	private static function buildLimit()
	{
		if (self::$skip != 0 && self::$take != 0)
		{
			self::$limit = "LIMIT " . self::$skip . ", " . self::$take;
		}
		elseif (self::$take != 0)
		{
			self::$limit = 'LIMIT ' . self::$take;
		}

		return self::$instance;
	}


	/**
	* Get a single row from the database
	* 
	* @return true
	*/
	public static function first()
	{
		if (empty(self::$fields)) $fields = '*';
		else $fields = self::$fields;

		if (!empty(self::$join))
			$sql = "SELECT {$fields} FROM ".self::$table." " . self::$join . " " . self::$whereClause; 
		else
			$sql = "SELECT {$fields} FROM ".self::$table." " . self::$whereClause . " " . self::$orderBy . " " . self::$limit;

		$sth = self::$conn->prepare($sql);
		$sth->execute(self::$params);
		$res = $sth->fetch(PDO::FETCH_OBJ);
		self::init($sql);

		if (self::DEBUG == true) dump($sql);

		return $res;		
	}

	/**
	* Get rows from the table
	* 
	* @return object
	*/
	public static function get()
	{
		if (empty(self::$fields)) $fields = '*';
		else $fields = self::$fields;

		if (!empty(self::$join))
			$sql = "SELECT {$fields} FROM ".self::$table." " . self::$join . " " . self::$whereClause; 
		else
			$sql = "SELECT {$fields} FROM ".self::$table." " . self::$whereClause . " " . self::$orderBy . " " . self::$limit;

		$sth = self::$conn->prepare($sql);
		$sth->execute(self::$params);
		$res = $sth->fetchAll(PDO::FETCH_OBJ);

		self::init($sql);

		if (self::DEBUG == true) dump($sql);


		return $res;	
	}

	/**
	* Return a row count from the database table
	* 
	* @param boolean $killInstance
	* @return int
	*/
	public static function count($killInstance = true)
	{
		$sql = "SELECT COUNT(*) FROM ".self::$table." " . self::$whereClause . " " . self::$orderBy . " " . self::$limit;
		$sth = self::$conn->prepare($sql);
		$sth->execute(self::$params);
		$res = $sth->fetch(PDO::FETCH_NUM);

		if ($killInstance)
			self::init($sql);

		if (self::DEBUG == true) dump($sql);

		return $res[0];
	}

	/**
	* Insert a row to the database table
	* 
	* @param array $data
	* @return true
	*/
	public static function insert($data = array())
	{
		$fields = '';
		$placeholders = '';
		$values = array();

		// Loop through the data
		foreach ($data as $key => $value) 
		{
			$fields .= $key . ', ';
			$placeholders .= '?' . ', ';
			$values[] = $value;
		}

		// Remove the starting symbols
		$fields = rtrim($fields, ', ');
		$placeholders = rtrim($placeholders, ', ');

		$sql = "INSERT INTO ".self::$table." ({$fields}) VALUES ({$placeholders})";
		$sth = self::$conn->prepare($sql);
		$sth->execute($values);
		self::$lastInsertId = self::$conn->lastInsertId();
		self::init($sql);

		return true;		
	}

	/**
	* Initialize all class variables
	* 
	* @return void
	*/
	private static function init($sql = null)
	{
		self::$table = null;
		self::$whereClause = '';
		self::$orderBy = '';
		self::$skip = 0;
		self::$take = 0;
		self::$limit = '';
		self::$params = null;
		self::$join = '';
		self::$fields = '';

		if (!is_null($sql)) self::$queries[] = $sql;
		self::$numQueries++;
	}

	/**
	* Run an update query on the table
	* 
	* @param array $data
	* @return true
	*/
	public static function update($data = array())
	{
		$fields = '';
		$values = array();

		foreach ($data as $key => $value) 
		{
			$fields .= "{$key} =?, ";
			$values[] = $value;
		}
		$fields = rtrim($fields, ', ');

		$sql = "UPDATE ".self::$table." SET {$fields} " . self::$whereClause;

		self::$params = array_merge($values, self::$params);
		$sth = self::$conn->prepare($sql);

		$res = $sth->execute(self::$params);

		self::init($sql);

		return $res;
	}

	/**
	* Run a delete query on the table
	* 
	* @return true
	*/
	public static function delete()
	{
		$sql = "DELETE FROM ".self::$table." " . self::$whereClause . " LIMIT 1";
		$sth = self::$conn->prepare($sql);
		$sth->execute(self::$params);
		self::init($sql);

		if (self::DEBUG == true) dump($sql);

		return true;		
	}


	/**
	* Get a html representation of all queries made, for debugging and analysing purpose.
	* 
	* @return string with html.
	*/
	public static function dump()
	{
		$html  = '<p><i>You have made ' . self::$numQueries . ' database queries.</i></p><pre>';

		foreach(self::$queries as $key => $value) 
		{
			$params = empty(self::$params[$key]) ? null : htmlentities(print_r(self::$params[$key], 1)) . '<br/></br>';
			$html .= $value . '<br/></br>' . $params;
		}

		return $html . '</pre>';		
	}

	/**
	 * Create pagination for the rows
	 *
	 * @param int $perPage
	 * @param array $hits 		Array containing all different hits per page 
	 * @return array
	 */
	public static function paginate($perPage, $hits = array(2, 4, 6, 8))
	{	
		// Get the total rows
		$countSQL = "SELECT * FROM ".self::$table . " " . self::$whereClause;
		$conn = new PDO(self::DB_DSN, self::DB_USER, self::DB_PASSWORD);
		$sth = $conn->prepare($countSQL);
		$sth->execute(self::$params);
		$totalItems = count($sth->fetchAll(PDO::FETCH_OBJ));

		$page = Input::has('page')? (int)Input::get('page') : 1;
		self::skip(($page - 1) * $perPage);
		self::take($perPage);

		self::$pagination = new Pagination($perPage, $totalItems);
		self::$pagination->checkForItems($page);
		self::setLinks();

		$items = self::get();

		$data = array(
				'data' 	=> $items,										# The items to display
				'links' => self::$links, 								# Pagination links
				'hits' 	=> self::$pagination->getHitsPerPage($hits)		# Pagination hits
			);


		return $data;
	}

	/**
	 * Set the links for the pagination
	 *
	 * @return void
	 */
	private static function setLinks()
	{
		$page = Input::has('page')? (int)Input::get('page') : 1;
		self::$links = self::$pagination->getPageNavigation($page);
	}

	/**
	 * Retrieve the pagination links
	 *
	 * @return string 
	 */
	public static function links()
	{
		return self::$links;
	}

	/**
	 * Get the last insert ID
	 *
	 * @return int
	 */
	public static function lastInsertId()
	{
		return self::$lastInsertId;
	}

}