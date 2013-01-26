<?php
require_once(getenv("DOCUMENT_ROOT")."/inc.php");

require_once (getenv("DOCUMENT_ROOT")."/local/DBSettings.php");

class PDOManager
{
	static function create($key)
	{
		if (!isset(self::$handles))
		{
			self::$handles = array();
		}
		
		if (!array_key_exists($key, self::$handles))
		{
			self::$handles[$key] = new PDOManager($key);
		}
		
		return self::$handles[$key];
	}
	
	private function __construct($key)
	{
		$dbs = new DBSettings();
		$dbSettings = $dbs->getSettings($key);
		
		if (!isset($dbSettings))
		{
			throw new Exception("Invalid database key: ".$key);
		}

		try
		{
			$this->handle = new PDO("mysql:host={$dbSettings['host']};dbname={$dbSettings['database']}", 
								$dbSettings["user"], 
								$dbSettings["password"],
								array(PDO::ATTR_PERSISTENT => true));
		}
		catch (Exception $ee)
		{
			throw new Exception("Failed to connect to database with key {$key}");
		}
	}
	
    function query($string)
	{
		$result = mysql_query($string, $this->handle);
		if (!$result)
		{
			throw new Exception("Failed to execute query: $string ".mysql_error());
		}
		return $result;
	}
	
	function insert($table, $fields, $nullableFields = array())
	{	
		$fieldCount = 0;
		$fieldNames = "";
		$fieldValues = "";
		foreach( $fields as $field => $value )
		{
			if ( 0 !== $fieldCount++ )
			{
				$fieldNames .= ", ";
				$fieldValues .= ", ";
			}
			$fieldNames 	.= "`{$field}`";
			$fieldValues 	.= $this->formFieldValue($field, $value, $nullableFields);
		}
	
		$sql 	= " INSERT INTO `{$table}` "
		 		. " ({$fieldNames}) "
				. " VALUES "
				. " ({$fieldValues}) ";
		
		$this->query( $sql );
		
		return mysql_insert_id();
	}

	private function formFieldValue($field, $value, $nullableFields)
	{
		$fieldValue = "";
		if (!isset($value))
		{
			if (array_key_exists($field, $nullableFields))
			{
				$fieldValue = "NULL";
			}
			else
			{
				throw new Exception("Null value passed for field {$field} but it is not nullable");
			}
		}
		else
		{
			$fieldValue = "'".$value."'";	
		}
		return $fieldValue;
	}
	
	function update($table, $keyField, $keyFieldValue, $fields, $nullableFields = array())
	{
		$fieldCount = 0;
		$fieldValues = "";
		foreach( $fields as $field => $value )
		{
			if ( 0 !== $fieldCount++ )
			{
				$fieldValues .= ", ";
			}
			$val = $this->formFieldValue($field, $value, $nullableFields);
			$fieldValues .= " {$field} = {$val} ";
		}
	
		$sql	= " UPDATE `{$table}` "
				. " SET {$fieldValues} "
				. " WHERE {$keyField} = {$keyFieldValue} ";
				
		$this->query( $sql );
	}
	
	private static $handles;	
	private $handle;	
}
?>