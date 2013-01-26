<?php

class ReturnValue
{
	function __construct()
	{
		$this->_errors = array();
	}
	
	function setId($id)
	{
		$this->_id = $id;
	}
	
	function addError($error)
	{
		$this->_errors[] = $error;
	}
	
	function success()
	{
		return (count($this->_errors) == 0);
	}
	
	function id()
	{
		return $this->_id;
	}
	
	function errors()
	{
		return $this->_errors;
	}
	
	function errorText()
	{
		$output = "";
		foreach ($this->_errors as $id => $error)
		{
			$output .= "$error<br>";
		}
		return $output;
	}
	
	private $_id;
	private $_errors;
}

?>