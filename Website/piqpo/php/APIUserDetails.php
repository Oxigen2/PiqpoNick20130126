<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APIUserDetails extends APICommandUserBase
{
    public final function description ()
    {
        return "Get user details";
    }
    
    public final function doWork( $inputs, $userId )
    {
        $userManager = new UserManager();
        
        $email =  $userManager->getUserEmail( $userId );
        
        return APIOutput::success(array('email' => $email));
    }
    
    public final function addInputs()
    {
        return array( );
    }    
}

?>
