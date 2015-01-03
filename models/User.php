<?php
class User extends Model {
	protected static $table = 'user';

	public function __construct( $data = array() )
	{
		parent::__construct($data);
	}
	
}