<?php
abstract class Model {

	protected static $query;

	protected static $instance;

	protected static $data = array();

	public function __construct( $data = array() )
	{
		// Loop through the credentials and set the class properties
		foreach ($data as $key => $value) 
		{
			if (empty($value))
				$this->$key = NULL;
			else
				$this->$key = $value;
		}
	}

	/**
	 * Return a resource by it's ID
	 *
	 * @param int $id
	 * @return object
	 */
	public static function find($id)
	{
		if (!$data = DB::table(static::$table)->where(['id' => $id])->first())
			return false;

		return new static($data);
	}

	/**
	 * Return the first found resource
	 *
	 * @return Object
	 */
	public static function first()
	{
		$item = false;
		// If the query has been set
		if (!empty(self::$query))
		{
			$item = new static(self::$query->first());
		}

		// This is the last call and we should destroy the instance
		self::$instance = NULL;

		return $item;
	}

	/**
	 * Specify the fields to be selected
	 *
	 * @return void
	 */
	public static function select($fields = array())
	{
		if (!isset(self::$instance)) {
			self::$instance = new static();
		}

		static::$query = DB::table(static::$table)->selectFields($fields);

		return self::$instance;		
	}

	/**
	 * Specify a where clause for the query, here we can use either 
	 * an array for multiple where statements or just a field and value
	 *
	 * @param mixed $field
	 * @param string $value
	 * @return object
	 */
	public static function where($field, $value = null)
	{
		if (!isset(self::$instance)) {
			self::$instance = new static();
		} 		

		// If we are using an array
		if (is_array($field))
		{
			$where = $field;
		}
		else
		{
			// Or use the field and value
			$where = [$field => $value];
		}

		$table = static::$table;

		static::$query = DB::table($table)->where($where);

		return self::$instance;
	}

	/**
	 * Specify a where like clause for the query
	 *
	 * @param string $field
	 * @param string $value
	 * @return object
	 */
	public static function whereLike($field, $value)
	{
		if (!isset(self::$instance)) {
			self::$instance = new static();
		} 		

		static::$query = DB::table(static::$table)->whereLike([$field => $value]);

		return self::$instance;
	}

	/**
	 * Specify a where not statement for the query
	 *
	 * @param string $field
	 * @param string $value
	 * @return object
	 */
	public static function whereNot($field, $value)
	{
		if (!isset(self::$instance)) {
			self::$instance = new static();
		} 		

		static::$query = DB::table(static::$table)->whereNot([$field => $value]);

		return self::$instance;
	}


	/**
	 * Check if a value is not NULL
	 *
	 * @param string $field
	 * @return object
	 */
	public static function whereNotNull($field)
	{
		if (!isset(self::$instance)) {
			self::$instance = new static();
		} 		

		static::$query = DB::table(static::$table)->whereNotNull($field);

		return self::$instance;
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
		if (!isset(self::$instance)) {
			self::$instance = new static();
		} 		

		static::$query = DB::table(static::$table)->whereIn($field, $values);

		return self::$instance;		
	}

	/**
	 * Specify a order by
	 *
	 * @param string $field
	 * @param string $value
	 * @return object
	 */
	public static function orderBy($column, $order = 'DESC')
	{
		if (!isset(self::$instance)) {
			self::$instance = new static();
		} 		
	
		$table = static::$table;

		static::$query = DB::table($table)->orderBy($column, $order);

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
		if (!isset(self::$instance)) {
			self::$instance = new static();
		}

		static::$query = DB::table(static::$table)->skip($skip);

		return self::$instance;
	}

	/**
	 * Limit the amount of rows to take
	 *
	 * @return Object
	 */
	public static function take($limit)
	{
		if (!isset(self::$instance)) {
			self::$instance = new static();
		}

		static::$query = DB::table(static::$table)->take($limit);

		return self::$instance;
	}


	/**
	 * Return a list of the resource
	 *
	 * @return object
	 */
	public static function all($sortBy = null, $order = null)
	{
		$table = static::$table;

		if (!$data = DB::table($table)->orderBy($sortBy, $order)->get())
			return false;
		
		foreach ($data as $item) 
			$list[] = new static($item);


		return $list;
	}

	/**
	 * Run the query and get the results 
	 *
	 * @return Object
	 */
	public static function get()
	{
		// List to store the objects
		$list = array();

		// If the query has been set
		if (!empty(self::$query))
		{
			// Loop through the results and add them to the list which we will return
			foreach (self::$query->get() as $item)
				$list[] = new static($item);
		}

		// This is the last call and we should destroy the instance
		self::$instance = NULL;

		return $list;
	}

	/**
	 * Get the number of rows
	 *
	 * @return Object
	 */
	public static function count()
	{
		// If the query has been set
		if (!empty(self::$query))
		{
			$count = count(self::$query->get());
		}
		else
		{
			$count = count(DB::table(static::$table)->get());
		}

		return $count;
	}

	/**
	 * Insert a row to the table
	 *	
	 *	@return boolean
	 */
	public function insert( $data = array() )
	{
		$table = static::$table;

		if (!DB::table($table)->insert($data)) 
			return false;

		return true;
	}


	/**
	 * Update a row in the table
	 *	
	 *	@return boolean
	 */
	public function update( $data = array() )
	{
		$table = static::$table;
		
		if (!DB::table($table)->where(['id' => $this->id])->update($data)) 
			return false;

		return true;
	}

	/**
	 * Delete the specified resource
	 *	
	 *	@return boolean
	 */
	public function delete()
	{
		$table = static::$table;
		
		if (!DB::table($table)->where(['id' => $this->id])->delete()) 
			return false;

		return true;
	}

	/**
	 * Create pagination for the model
	 *
	 * @param int $perPage
	 * @return object
	 */
	public static function paginate($perPage)
	{
		// List to store the objects
		$list = array();

		// If the query has been set, else we need to set the table before running the query
		if (!empty(self::$query))
		{
			$contents = self::$query->paginate($perPage);
		}
		else
		{
			$contents = DB::table(static::$table)->paginate($perPage);
		}

		// Loop through the results and add them to the list which we will return
		foreach ($contents['data'] as $item)
			$list['data'][] = new static($item);

		// Add the pagination links to the new array
		$list['links'] = $contents['links'];
		$list['hits']  = $contents['hits'];

		// This is the last call and we should destroy the instance
		self::$instance = NULL;

		return $list;		
	}

	/**
	 * Specify a has-one relationship
	 *
	 * @param string $model
	 * @param object $object
	 * @param string $foreignKey
	 * @return object
	 */
	public static function hasOne($model, $object, $foreignKey = NULL)
	{
		$class = get_called_class();								// Name of the called class
		$table = static::$table;									// This table
		$modelClassname = self::generateClassName($model);			// Class name of the specified model
		$modelTablename = $modelClassname::$table;					// The table name specified in the model

		$field = $modelTablename . '.id';

		if (!is_null($foreignKey))
			$value = $table .'.'. $foreignKey;
		else
			$value = $modelTablename . '.id';

		$whereTable = $table . '.id';
		
		$data = DB::table($table)
			->join($modelTablename, $field, '=', $value)
			->selectFields($modelTablename . '.*')
			->where([$whereTable => $object->id])
			->first();
	
		return $data;
	}

	/**
	 * Specify a has-many relationship and retrieve the related items
	 *
	 * @param string $model
	 * @param object $object
	 * @param string $foreignKey
	 * @param array $args
	 * @return array
	 */
	public static function hasMany($model, $object, $foreignKey = null, $args = array())
	{
		$class = get_called_class();						// Name of the called class
		$table = static::$table;							// Table property of the called class
		$modelClassname = self::generateClassName($model);	// Class name of the specified model
		$modelTablename = $modelClassname::$table;			// The table name specified in the model

		// If the foreign key has not been set we should try to make a default one
		if (is_null($foreignKey))
		{
			$foreignKey = $table . '_id';
		}

		// The foreign key field of the model that should be equal to $field defined below
		$value = $modelTablename .'.'. $foreignKey;

		// Get the ID field that we are going to compare with the $value
		$field = $table . '.id';

		// Execute the query
		$data = DB::table($table)
			->join($model, $field, '=', $value)
			->selectFields($modelTablename . '.*')
			->where([$field => $object->id])
			->get();

		// Where we should store the items as the class objects
		$items = array();

		// Loop through the retrieved data
		foreach ($data as $key => $value) 
		{
			// Insert the objects as an instance of the model class
			$items[] = new $modelClassname($value);
		}
	
		return $items;
	}

	/**
	 * Specifiy a many-to-many relationship using a pivot table and retrieve the related items
	 *
	 * @param string $model
	 * @param string $pivotTable
	 * @param object $object
	 * @return array
	 */
	public static function belongsToMany($model, $pivotTable = null, $object = null)
	{
		$class = get_called_class();				// Name of the called class
		$table = static::$table;					// The table to work with
		$modelClassname = ucfirst($model);			// Class name of the specified model
		$modelTablename = $modelClassname::$table;	// The table name specified in the model


		// If the pivot table has not been set we try to use the default format
		if (is_null($pivotTable))
		{
			$pivotTable = lcfirst($model) . '_' . lcfirst($class);
		}

		$field = $model . '.id';
		$value = $pivotTable . '.' . $model . '_id';

		$whereTable = $pivotTable.'.'.lcfirst($class).'_id';
		$where = [$whereTable => $object->id];

		$data = DB::table($model)
			->join($pivotTable, $field, '=', $value)
			->selectFields($modelTablename . '.*')
			->where($where)
			->get();


		$list = array();
		$initClass = ucfirst($model);
		foreach ($data as $key => $value) {
			$list[$key] = new $initClass($value);
		}

		return $list;
	}

	/**
	 * Generate the class name for a model
	 *
	 * @param string $model
	 * @return string
	 */
	public static function generateClassName($model)
	{
		// First we replace all underscores with spaces
		$classname = str_replace('_', ' ', $model);

		// Then we capitalize the first letter of each word
		$classname = ucwords($classname);

		// And now we remove the spaces
		$classname = str_replace(' ', '', $classname);

		return $classname;
	}	
}