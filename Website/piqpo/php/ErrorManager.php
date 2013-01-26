<?php

class ErrorManager
{
	function ErrorManager()
	{
	}

	function errorHTML($error)
	{
		// A placeholder to allow errors to be formatted consistently
		return "<html><body><h1>An error has occurred</h1><p>{$error}</p></body></html>";
	}
}

?>