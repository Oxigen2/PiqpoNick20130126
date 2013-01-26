<?php

class PiqpoCookieManager extends CookieManager
{
	function __construct()
	{
	}
		
	function clearUserCookie()
	{
		$this->clearCookie(self::m_userId);
	}
	
	function setUserCookie($value)
	{
		$this->setcookie(self::m_userId, $value);
	}
	
	function getUserCookie()
	{
		return $this->getCookie(self::m_userId);
	}
	const m_userId = 'user_id';	
}

?>