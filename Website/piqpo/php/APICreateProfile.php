<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APICreateProfile extends APICommand
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public final function run( $inputs )
    {
        $profileManager = new ProfileManager();
        
        $result = $profileManager->createEmptyProfile();
        
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
        return "Creates a new empty profile.";
    }
    
    public final function inputs()
    {
        return array( );
    }    
}

?>
