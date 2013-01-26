<?php

class CookieManager
{
	function CookieManager()
	{
	}
	
	function clearCookie($name)
	{
		setcookie($name, '', 1);
	}
	
	function setCookie($name, $value)
	{
		setcookie($name, $value);
	}
	
	function getCookie($name)
	{
		$value = "";
		
		if (isset($_COOKIE[$name]))
		{
			$value = $_COOKIE[$name];			
		}

		return $value;
	}
}

?>