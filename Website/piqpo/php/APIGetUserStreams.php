<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APIGetUserStreams extends APICommandUserBase
{
    public final function description ()
    {
        return "Get list of streams user default profile is subscribed to";
    }
    
    public final function doWork( $inputs, $userId )
    {
        $userManager = new UserManager;
        
        $defaultProfileId = $userManager->getDefaultProfile($userId);

        if ( isset($defaultProfileId) )
        {
            $streamManager = new StreamManager();

            $streamInfoList = array();

            $streams = $streamManager->profileStreams($defaultProfileId);

            foreach ($streams as $streamId => $stream)
            {
                $streamInfoList[] = $streamManager->streamInfoArray( $stream );             
            }
            
            $apiResult = APIOutput::success(array('streams' => $streamInfoList));
        }
        else
        {
            $apiResult = APIOutput::error(APIErrorCode::failure, 'User profile not found');            
        }
        
        return $apiResult;
    }
    
    public final function addInputs()
    {
        return array(  );
    }    
}

?>
