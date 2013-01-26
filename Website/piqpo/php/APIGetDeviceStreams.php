<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APIGetDeviceStreams extends APICommandUserBase
{
    public final function description ()
    {
        return "Get list of streams for device profile";
    }
    
    public final function doWork( $inputs, $userId )
    {
        $deviceManager = new DeviceManager;
        
        $profileId = $deviceManager->getProfileId( $inputs['guid'] );
        
        if ( isset($profileId) )
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
        return array( new APIParameter( 'guid', 'Device guid', true ) );
    }    
}

?>
