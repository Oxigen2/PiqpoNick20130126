<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class ProfileManager
{
	function __construct()
	{
	}	
    
    function createEmptyProfile( )
    {        
        return Profile::create("Profile",null,"","orphan");
    }
    
    function createPendingUserProfile( $userId )
    {        
        // Come back to this, not checking existing profiles 
        return Profile::create("Profile", $userId, $this->generateCode(), "pending");
    }
    
    // Returns profile if found
    function activate( $code )
    {
        // Retrieve        
        $profile = $this->retrieveByCode( "'" . $code . "'" );
        
        if ( isset($profile) )
        {
            if ( $profile->status() != "active" )
            {
                // Set active
                $profile->update( $profile->name(), $profile->userId(), $profile->activationCode(), "active" );
                
                $userManager = new UserManager;
                $userManager->activateUser( $profile->userId() );
            }
        }
        
        return $profile;
    }
    
    function assignProfile( $profileId, $userId, $setPending )
    {
        $profile = Profile::loadSingleFromDB($profileId);
        
        if ( isset($profile) )
        {
            if ( $setPending )
            {
                $code = "";
                while( empty($code) )
                {
                    $ss = $this->generateCode();
                    if ( $this->retrieveByCode($ss) == null )
                    {
                        $code = $ss;
                    }
                }
                
                $profile->update( $profile->name(), $userId, $code, "pending" );
            }
            else
            {            
                $profile->update( $profile->name(), $userId, "", "active" );
            }
        }
        
        return $profile;
    }
    
    private function retrieveByCode( $code )
    {
        $query = array( "activation_code" => $code );
        
        $result = Profile::loadFromDB( $query );
        
     	return (count($result) == 1) ? $result[0] : null;
    }
    
    private function generateCode()
    {
        return CommonFunctions::generateCode( 8 );
    }

    function sendCode( $profileId )
    {
        $profile = Profile::loadSingleFromDB($profileId);
        
        $userManager = new UserManager();
        $emailManager = new EmailManager();
        
        $email = $userManager->getUserEmail( $profile->userId() );
        
        $subject = "Piqpo activation code";
        $message = "When installing Piqpo please insert this activation code when prompted: " . $profile->activationCode();            

        $emailManager = new EmailManager;
        $emailManager->send( $email, 'admin@piqpo.com', $subject, $message );        
    }
    
    function sendLink( $profileId )
    {
        $profile = Profile::loadSingleFromDB($profileId);
        
        $userManager = new UserManager();
        $emailManager = new EmailManager();
        $linkManager = new PiqpoLinkManager();
        
        $email = $userManager->getUserEmail( $profile->userId() );
        $link = $linkManager->getAPICommand( "activateAccount", array( "code" => $profile->activationCode() ) );
        
        $subject = "Piqpo account activation";
        $message = "Please click this link to activate your piqpo account: " . $link;            

        $emailManager = new EmailManager;
        $emailManager->send( $email, 'admin@piqpo.com', $subject, $message );        
    }
}
?>
