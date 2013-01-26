<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

$tags = array();

$templateManager = new TemplateManager();
$templateManager->publishAdminPage($tags, "admin_home.htm");

?>