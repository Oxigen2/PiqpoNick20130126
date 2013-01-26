<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APIUnsubscribeFromStream extends APICommandUserBase
{
    public final function description ()
    {
        return "Remove user default profile stream subscription";
    }
    
    public final function doWork( $inputs, $userId )
    {
        $userManager = new UserManager;
        
        $defaultProfileId = $userManager->getDefaultProfile($userId);

        if ( isset($defaultProfileId) )
        {
            $streamManager = new StreamManager();

            $result = $streamManager->unassignProfileStream($defaultProfileId, $inputs['stream']);

            if ( $result->success() )
            {
                $apiResult = APIOutput::success(array());
            }
            else
            {
                $apiResult = APIOutput::error(APIErrorCode::failure, $result->errorText());
            }
        }
        else
        {
            $apiResult = APIOutput::error(APIErrorCode::failure, 'User profile not found');
        }
                
        return $apiResult;
    }
    
    public final function addInputs()
    {
        return array( new APIParameter( 'stream', 'Stream to add', true ) );
    }        
}

?>
