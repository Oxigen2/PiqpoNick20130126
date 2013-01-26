<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

$slideId = $_GET["slide"];
$guid = $_GET["guid"];
$user = $_GET["user"];

$slideStatsManager = new SlideStatsManager();    
$slideManager = new SlideManager();

if ( isset( $slideId ) )
{  
    $slide = $slideManager->getSlide($slideId);

    if ( isset( $slide ) )
    {
        if (isset($guid))
        {
            $slideStatsManager->recordDeviceSlideFollow($slideId, $guid);
        }
        else if (isset($user))
        {
            $slideStatsManager->recordUserSlideFollow($slideId, $user);
        }

        header('Location: '.$slide->targetLink());
        exit();    
    }
}

?>
