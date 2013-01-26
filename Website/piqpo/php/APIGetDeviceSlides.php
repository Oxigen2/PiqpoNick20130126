<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APIGetDeviceSlides extends APICommand
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public final function run( $inputs )
    {
        $deviceManager = new DeviceManager;
        
        $profileId = $deviceManager->getProfileId( $inputs['guid'] );
        
        if ( isset($profileId) )
        {
            $slideManager = new SlideManager();
            
            $slides = $slideManager->getProfileSlides($profileId);
            
            $apiOutput = APIOutput::success(array('slides' => $slides));
        }
        else
        {
            $apiOutput = APIOutput::error(APIErrorCode::failure, 'Failed to look up device profile');
        }
        
        return $apiOutput;
    }
    
    public final function inputs()
    {
        return array( new APIParameter( 'guid', 'Device guid', true ) );
    }    
    
    public final function description ()
    {
        return "Get list of slides for the device.";
    }
    
}

?>
