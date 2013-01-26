<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APIChangeEmail extends APICommandUserBase
{
    public final function description ()
    {
        return "Change user email";
    }
    
    public final function doWork( $inputs, $userId )
    {
        $userManager = new UserManager();
        
        $userManager->modifyEmail( $userId, $inputs['email'] );
        
        return APIOutput::success(array('email' => $inputs['email']));
    }
    
    public final function addInputs()
    {
        return array( new APIParameter( 'email', 'New email address', true ) );
    }    
}

?>
