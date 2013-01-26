<?php

date_default_timezone_set('Europe/London');

function __autoloader($classname, $path)
{
	foreach($path as $dir)
	{
		$filename = $dir . '/' . $classname. '.php';
		if (file_exists($filename))
		{
			require_once($filename);
			break;
		}	
	}
}

function __autoload_nt($class_name) 
{
	__autoloader($class_name, array( $GLOBALS['COMMON_ROOT'] . "/php" ));
}

spl_autoload_register('__autoload_nt');

?>