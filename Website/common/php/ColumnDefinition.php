<?php

class ColumnDefinition
{
	function ColumnDefinition($name, $nullable, $primaryKey)
	{
		$this->_name = strtolower($name);
		$this->_nullable = $nullable;
		$this->_primaryKey = $primaryKey;
	}
	
	function name()
	{
		return $this->_name;
	}
	
	function isPrimaryKey()
	{
		return $this->_primaryKey;
	}
	
	function isNullable()
	{
		return $this->_nullable;
	}
	
	function properName()
	{
		$words = explode('_', $this->_name);
		$output = "";
		$first = true;
		foreach ($words as $id => $word)
		{
			if ($first)
			{
				$first = false;
				$output .= strtolower($word);	// though should be lc anyway
			}
			else
			{
				$output .= ucwords($word);
			}
		}
		return $output;
	}
	
	private $_name;
	private $_primaryKey;
	private $_nullable;
}

?>