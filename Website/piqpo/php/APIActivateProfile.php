<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APIActivateProfile extends APICommand
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public final function run( $inputs )
    {
        $profileManager = new ProfileManager();
        
        $profile = $profileManager->activate($inputs['code']);
        
        if ( isset( $profile ) )
        {
            $apiOutput = APIOutput::success( array('id' => $profile->id()) ) ;            
        }
        else
        {
            $apiOutput = APIOutput::error( APIErrorCode::failure , "Failure to active profile" );
        }
        
        return $apiOutput;
    }
    
    public final function description()
    {
        return "Activates profile.";
    }
    
    public final function inputs()
    {
        $inputs = array( new APIParameter( 'code', 'Activation code', true ) );
        return $inputs;
    }    
}

?>
