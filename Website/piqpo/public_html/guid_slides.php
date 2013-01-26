<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

// For now just fail silently
$output = "<items></items>";

$guid = LinkManager::extractGetParam('guid');

if ( !empty($guid) )
{
    $deviceManager = new DeviceManager();
    
    $userId = $deviceManager->getUserIdFromGUID($guid);
    
	if ( isset( $userId ) )
	{
		$slideManager = new SlideManager();
		$output = $slideManager->getUserSlideXML($userId);
	}
}

header('Content-type: text/xml');
print $output;
?>
