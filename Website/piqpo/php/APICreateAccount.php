<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APICreateAccount extends APICommand
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public final function run( $inputs )
    {
        $profileManager = new ProfileManager();
        $userManager = new UserManager();
        
        $result = $userManager->createUser($inputs['email'], $inputs['password'], true);
        
        if ( $result->success() )
        {
            $profile = null;
            $userId = $result->id();
            
            // Assuming that if a profile exists then later a device will be 
            // assigned to it, so need to mail an activation code.
            // If no profile is passed then we are creating an empty profile 
            // and account is to be activated by a link.
            // Probably want to separate these workflows more explicitly than this.
            if ( isset( $inputs['profile'] ) )
            {
                $profile = $profileManager->assignProfile($inputs['profile'], $userId, true);
                
                if ( isset( $profile ) )
                {
                    $profileManager->sendCode( $profile );                    
                }
                else
                {
                    $result->addError( "Failed to assign profile" );
                }
            }
            else
            {
                $profile = $profileManager->createPendingUserProfile( $userId );               
                $profileManager->sendLink( $profile );
            }
        }
            
        $apiOutput = null;
        
        if ( $result->success() )
        {
            $apiOutput = APIOutput::success( array('id' => $result->id()) ) ;            
        }
        else
        {
            $apiOutput = APIOutput::error( APIErrorCode::failure , $result->errorText() );
        }
        
        return $apiOutput;
    }
    
    public final function description()
    {
        return "Creates a new account.";
    }
    
    public final function inputs()
    {
        $inputs = array( new APIParameter( 'email', 'Email address of new user', true ),
                         new APIParameter( 'password', 'User password', true ),
                         new APIParameter( 'profile', 'Profile id', false ) );
        return $inputs;
    }    
}

?>
