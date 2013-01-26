<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class DeviceManager
{
	function __construct()
	{
	}	

    function getProfileId( $guid )
    {
        $profileId = null;
        
        $query = array( "guid" => "'".$guid."'" );
        
        $devices = Device::loadFromDB( $query );
        if ( count( $devices ) == 1 )
        {
            $profileId = $devices[0]->profileId();
        }
        
        return $profileId;
    }
    
    // Returns user id associated with guid, or null.
    function getUserIdFromGUID( $guid )
    {
        $userId = null;
        
        $query = array( "guid" => $guid );
        
        $devices = Device::loadFromDB( $query );
        if ( count( $devices ) == 1 )
        {
            $userId = $devices[0]->userId();
        }
        
        return $userId;
    }
    
    function setUpDevice( $deviceName, $deviceType, $activationCode )
    {
        $res = new ReturnValue();
        
        $profileManager = new ProfileManager;
        $userManager = new UserManager;
        
        $profile = $profileManager->activate( $activationCode );

        if ( isset( $profile ) )
        {
            $userManager->activate( $profile->userId() );
            
            $res = $this->createDevice( $deviceName, $deviceType, $profile->userId(), $profile->profileId() );
        }        
        else
        {
            $res->addError("Failed to activate profile");
        }
        
        return $res;
    }
    
    private function createDevice( $deviceName, $deviceType, $userId, $profileId )
    {
        $res = new ReturnValue();
                
        $guid = com_create_guid();
        
        if ( !$this->isValidType($deviceType) )
        {
            $res->addError('Invalid device type');
        }
        
        if ( $res->success() )
        {
            Device::create($guid, $userId, $deviceType, $profileId, $deviceName);

            $res->setId($guid);
        }
        
        return $res;
    }
    
    function isValidType( $type )
    {
        return $type == 'winpc';
    }
}
?>
