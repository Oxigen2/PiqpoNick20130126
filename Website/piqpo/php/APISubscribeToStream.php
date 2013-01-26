<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APISubscribeToStream extends APICommandUserBase
{
    public final function description ()
    {
        return "Subscribe profile to stream";
    }
    
    public final function doWork( $inputs, $userId )
    {
        $streamManager = new StreamManager();

        $result = $streamManager->assignProfileStream($inputs['profile'], $inputs['stream']);

        if ( $result->success() )
        {
            $apiResult = APIOutput::success(array());
        }
        else
        {
            $apiResult = APIOutput::error(APIErrorCode::failure, $result->errorText());
        }
                
        return $apiResult;
    }
    
    public final function addInputs()
    {
        return array( new APIParameter( 'profile', 'Profile to add to', true ),
                      new APIParameter( 'stream', 'Stream to add', true ) );
    }        
}

?>
