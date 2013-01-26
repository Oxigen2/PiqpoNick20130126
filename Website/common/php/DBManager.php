<?php

class DBManager
{
	var $handle;
	
	public function __construct($key)
	{
		$dbSettings = DBSettings::getSettings($key);
		
		if (!isset($dbSettings))
		{
			throw new Exception("Invalid database key: ".$key);
		}
		
		$this->handle = mysql_connect(	$dbSettings["host"],
										$dbSettings["user"],
										$dbSettings["password"]);
 		if (!($this->handle))
		{
			throw new Exception("Could not connect : " . mysql_error());
		}
					  
    	if (!mysql_select_db($dbSettings["database"],$this->handle))
		{
			throw new Exception("Could not select db : " . $dbSettings["database"]. mysql_error());
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
			if ($value === false)
			{
				$fieldValue = '0';
			}
			else
			{
				$fieldValue = "'".$value."'";	
			}
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
    
    static function now()
    {
        return self::formatDate( time() );
    }
    
    static function formatDate( $timestamp )
    {
        return date('Y-m-d H:i:s', $timestamp);
    }
}
?>