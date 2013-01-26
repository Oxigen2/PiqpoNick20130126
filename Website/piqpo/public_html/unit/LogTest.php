<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

Logger::slideLogger()->LogInfo("Test info log");
Logger::slideLogger()->LogError("Test error log");

?>
