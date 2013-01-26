<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

$publisher = new Publisher();

$user = LinkManager::extractGetParam("user");

$output = (strlen($user) == 0) ? "Please configure the user login details." : "User email {$user} does not have an account.";

$publisher->addLine($output);

$publisher->publishPage("Oxigen - Error");
?>