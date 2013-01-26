<?php
// Set the globals that hold the common and local code base roots.
require_once(getenv("DOCUMENT_ROOT")."/local/environment.php");

// Include the common class loader and db settings classes
require_once($GLOBALS['COMMON_ROOT']."/php/inc_common.php");

// Configure db settings.
require_once(getenv("DOCUMENT_ROOT")."/local/db_settings.php");

// Set up the local class autoloader
function __autoload_piqpo($class_name) 
{
    $piqpoPHPRoot = $GLOBALS['LOCAL_ROOT'] . "/php";
	__autoloader($class_name, array( $piqpoPHPRoot, $piqpoPHPRoot . "/db"));
}

spl_autoload_register('__autoload_piqpo');
?>