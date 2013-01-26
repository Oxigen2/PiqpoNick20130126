<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

$tags = array();

$apiHandler = new APIHandler();
$apiCommands = $apiHandler->getAllCommands();

foreach ( $apiCommands as $command => $apiCommand )
{
    $commandInfo = array( "command" => $command, "description" => $apiCommand->description() );
    
    $commandInfo[ "inputs" ] = array();
    foreach ( $apiCommand->inputs() as $apiParameter )
    {       
        $commandInfo[ "inputs" ][] = array(  "name" => $apiParameter->name(), 
                                             "description" => $apiParameter->description(),
                                             "required" => $apiParameter->required() ) ;
    }
     
    $tags["commands"][] = $commandInfo; 
}

$templateManager = new TemplateManager();
$templateManager->publishTestPage($tags, "api_test.htm");

?>