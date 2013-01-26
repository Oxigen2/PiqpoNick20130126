<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APIGetAvailableStreams extends APICommandUserBase
{
    public final function description ()
    {
        return "Get list of streams available to the user.";
    }
    
    public final function doWork( $inputs, $userId )
    {
        // for now all users can get all streams
        $streamManager = new StreamManager();
               
        $streamInfoList = array();
        
        $streams = $streamManager->allStreams($userId);
        
        foreach ($streams as $streamId => $stream)
        {
            $streamInfoList[] = $streamManager->streamInfoArray( $stream );             
        }
        
        return APIOutput::success(array('streams' => $streamInfoList));
    }
    
    public final function addInputs()
    {
        return array(  );
    }        
}

?>
