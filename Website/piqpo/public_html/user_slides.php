<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

// For now just fail silently
$output = "<items></items>";

$userId = LinkManager::extractGetParam("user_id");

if ( empty($userId) )
{
    $userEmail = LinkManager::extractGetParam("user");
    $guid = LinkManager::extractGetParam("guid");

    // TODO - check valid syntax
    if (strlen($guid) > 0)
    {
        $query = array("guid" => "'".$guid."'");
        $devices = Device::loadFromDB($query);

        if ( count($devices) == 1 )
        {
            $userId = $devices[0]->userId();
        }    
    }
    else if ((strlen($userEmail) > 0) && (CommonFunctions::is_valid_email_address($userEmail)))
    {
        // Can remove this when screen saver has been changed to send guid
        $query = array("email" => "'".$userEmail."'");
        $user = User::loadFromDB($query);

        if ( count($user) == 1 )
        {
            $userId = $user[0]->userId();
        }
    }
}    

if (isset($userId) && ctype_digit($userId))
{
    $slideManager = new SlideManager();
    $output = $slideManager->getUserSlideXML($userId);
}

header('Content-type: text/xml');
print $output;
?>
