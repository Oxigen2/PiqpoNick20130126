<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APILogOut extends APICommand
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public final function run( $inputs )
    {
        $userManager = new UserManager();
        
        $userManager->logoutUser( );
        
        return APIOutput::success( array() );
    }
    
    public final function description()
    {
        return "Log out user.";
    }
    
    public final function inputs()
    {
        $inputs = array( );
        return $inputs;
    }    
}

?>
