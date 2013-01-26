<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APILogIn extends APICommand
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public final function run( $inputs )
    {
        $userManager = new UserManager();
        
        $result = $userManager->loginUser($inputs['email'], $inputs['password']);
        
        $apiOutput = null;
        
        if ( $result->success() )
        {
            $apiOutput = APIOutput::success( array('id' => $result->id()) );   
        }
        else
        {
            $apiOutput = APIOutput::error( APIErrorCode::failure , $result->errorText() );
        }
        
        return $apiOutput;
    }
    
    public final function description()
    {
        return "Log in user.";
    }
    
    public final function inputs()
    {
        $inputs = array( new APIParameter( 'email', 'Email address of new user', true ),
                         new APIParameter( 'password', 'User password', true ) );
        return $inputs;
    }    
}

?>
