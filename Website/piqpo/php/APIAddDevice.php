<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APIAddDevice extends APICommand
{
    public final function description ()
    {
        return "Adds a new device and assigns it to user and profile based on the code.";
    }
    
    public final function run( $inputs )
    {   
        $deviceManager = new DeviceManager();

        $res = $deviceManager->setUpDevice($inputs['name'], $inputs['type'], $inputs['code']);                
        
        return $res->success() ? APIOutput::success(array('deviceGuid' => $res->id() )) :
                                 APIOutput::error(APIErrorCode::failure, $res->errorText() );
    }
    
    public final function inputs()
    {
        return array( new APIParameter( 'type', 'Type of device', true ),
                      new APIParameter( 'name', 'Device name', true ),
                      new APIParameter( 'code', 'Activation code', true) );
    }
}

?>
