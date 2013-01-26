<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APIResetPassword extends APICommand
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public final function run( $inputs )
    {
        $userManager = new UserManager();
        
        $user = $userManager->getUser( $inputs['email'] );
        
        $apiOutput = null;
        if ( isset( $user ) )
        {
            $userManager->resetPassword($user->userId());
            
            $apiOutput = APIOutput::success(array());
        }
        else
        {
            $apiOutput = APIOutput::error( APIErrorCode::failure , "No user with that email address." );            
        }
                
        return $apiOutput;
    }
    
    public final function description()
    {
        return "Resets the password for user, emailing them the new password.";
    }
    
    public final function inputs()
    {
        $inputs = array( new APIParameter( 'email', 'Email address of the user', true ) );
        return $inputs;
    }    
}

?>
