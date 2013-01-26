<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

$handler = new APIHandler();

$handler->handle();

?>
