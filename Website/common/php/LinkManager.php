<?php
class LinkManager
{
	function LinkManager()
	{
	}
	
	// Reload the current script
	function reload()
	{
		$this->redirect($_SERVER["SCRIPT_NAME"]);
	}

	// Redirect to the given link.
	function redirect($link)
	{
		header('Location: '.$link);
		exit();
	}
	
	function currentLink()
	{
		return $_SERVER["SCRIPT_NAME"];
	}
	   
	static function extractGetParam($paramName)
	{
		$output = "";
		if (isset($_GET[$paramName]))
		{
			$output = $_GET[$paramName];
		}
		return $output;		
	}	
	
	// Returns given GET param if it is a valid id, that is an integer value of 0 or greater.
	// If it is missing or invalid an empty string is returned.
	static function extractGetParamAsId($paramName)
	{
		$id = self::extractGetParam($paramName);
		
		if ((strlen($id) > 0) && (!ctype_digit($id)))
		{
			$id = "";
		}
		
		return $id;
	}
	
	protected function serverRootURL()
	{
		return "http://{$_SERVER['SERVER_NAME']}";
	}
}
?>