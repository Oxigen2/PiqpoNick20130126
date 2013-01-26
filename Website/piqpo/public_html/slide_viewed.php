<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

$slideId = $_GET["slide"];
$guid = $_GET["guid"];
$follow = $_GET["follow"];
$user = $_GET["user"];

if (isset($guid))
{
    $slideStatsManager = new SlideStatsManager();    
    if ( isset( $follow ) )
    {
        $slideStatsManager->recordDeviceSlideFollow($slideId, $guid);
    }
    else
    {
        $slideStatsManager->recordDeviceSlideView($slideId, $guid);
    }
}
else if (isset($user))
{
    $slideStatsManager = new SlideStatsManager();
    if ( isset( $follow ) )
    {
        $slideStatsManager->recordUserSlideFollow($slideId, $user);
    }
    else
    {
        $slideStatsManager->recordUserSlideView($slideId, $user);
    }    
}

?>
