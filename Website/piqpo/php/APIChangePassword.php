<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APIChangePassword extends APICommandUserBase
{
    public final function description ()
    {
        return "Change user email";
    }
    
    public final function doWork( $inputs, $userId )
    {
        $res = new ReturnValue();
        $userManager = new UserManager();
        
        $res = $userManager->modifyPassword( $userId, $inputs['password'] );
        
        // Note the user isn't logged in.
        
        return $res->success()? APIOutput::success(array()) : APIOutput::error(APIErrorCode::failure, $res->errorText());
    }
    
    public final function addInputs()
    {
        return array( new APIParameter( 'password', 'New password', true ) );
    }    
}

?>
