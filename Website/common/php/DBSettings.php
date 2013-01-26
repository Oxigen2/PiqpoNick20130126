<?php
 
class DBSettings
{
	private static $settings;

	function __construct()
	{
	}
	
	static function getSettings($key)
	{
		return self::$settings[$key];
	}
	
	static function getKeys()
	{
		$output = array();
		foreach( self::$settings as $key => $value )
		{
			$output[] = $key;
		}
		return $output;
	}
	
	static function addSettings($key, $host, $user, $password, $database)
	{
		self::$settings[$key] = array(
		   "host"		=> $host,
		   "user"		=> $user,
		   "password"	=> $password,
		   "database"	=> $database);
	}
}

?>
