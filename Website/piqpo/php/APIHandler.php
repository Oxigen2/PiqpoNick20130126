<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APIHandler
{    
    public function __construct()
    {
    }
    
    public function handle()
    {
        $output = $this->callAPI();
        
        print $output->jsonEncode();
    }
    
    public function createGETCommandString( $root, $command, $params )
    {
        $link = "{$root}?command=" . $command;
        
        foreach( $params as $param => $value )
        {
            $link .= "&{$param}={$value}"; 
        }
        
        return $link;
    }
    
    private function callAPI()
    {
        $apiOutput = null;
        try
        {
            $apiError = APIErrorCode::success;
            $errorMessage = "";
            $inputs = array();

            // Find the command
            $command = array_key_exists('command', $_GET) ? $_GET['command'] : $_POST['command'];

            if ( !isset($command) )
            {
                $apiError = APIErrorCode::illegalCommand;
                $errorMessage = "Missing command";
            }
            else
            {
                $apiCommand = $this->createCommand( $command );

                if ( !isset( $apiCommand )  )
                {
                    $apiError = APIErrorCode::illegalCommand;            
                    $errorMessage = "No such command " . $command;
                }
                else
                {
                    foreach ($apiCommand->inputs() as $apiParameter)
                    {
                        if ( array_key_exists($apiParameter->name(), $_GET) )
                        {
                            $inputs[$apiParameter->name()] = $_GET[$apiParameter->name()];
                        }
                        else if ( array_key_exists($apiParameter->name(), $_POST) )
                        {
                            $inputs[$apiParameter->name()] = $_POST[$apiParameter->name()];
                        }
                        else if ( $apiParameter->required() )
                        {
                            $apiError = APIErrorCode::missingArguments;
                            $errorMessage = "Required argument missing: " . $apiParameter->name();
                            break;
                        }
                    }
                }
            }

            if ( $apiError == APIErrorCode::success )
            {
                $apiOutput = $apiCommand->run( $inputs );
            }
            else
            {
                $apiOutput = APIOutput::error( $apiError, $errorMessage );
            }
        }
        catch ( Exception $exception )
        {
            $apiOutput = APIOutput::error( APIErrorCode::processingException, "Exception at " . $exception->getFile() . " line " . $exception->getLine() . " : " . $exception->getMessage() );
        }
        
        return $apiOutput;
    }
    
    private function createCommand( $command )
    {
        $apiCommand = null;
        
        if ($command == 'hello')
        {
            $apiCommand = new APIHello();            
        }
        elseif ($command == 'createAccount')
        {
            $apiCommand = new APICreateAccount();
        }
        elseif ($command == 'logIn')
        {
            $apiCommand = new APILogIn();
        }
        elseif ($command == 'logOut')
        {
            $apiCommand = new APILogOut();
        }
        elseif ($command == 'changeEmail')
        {
            $apiCommand = new APIChangeEmail();
        }
        elseif ($command == 'changePassword')
        {
            $apiCommand = new APIChangePassword();
        }
        elseif ($command == 'resetPassword')
        {
            $apiCommand = new APIResetPassword();
        }
        elseif ($command == 'userStreams')
        {
            $apiCommand = new APIGetUserStreams();
        }
        elseif ($command == 'deviceStreams')
        {
            $apiCommand = new APIGetDeviceStreams();
        }
        elseif ($command == 'availableStreams')
        {
            $apiCommand = new APIGetAvailableStreams();
        }
        elseif ($command == 'subscribeToStream')
        {
            $apiCommand = new APISubscribeToStream();
        }
        elseif ($command == 'unsubscribeFromStream')
        {
            $apiCommand = new APIUnsubscribeFromStream();
        }
        elseif ($command == 'getDeviceSlides')
        {
            $apiCommand = new APIGetDeviceSlides();
        }
        elseif ($command == 'getProfileSlides')
        {
            $apiCommand = new APIGetDeviceSlides();
        }
        elseif ($command == 'addDevice')
        {
            $apiCommand = new APIAddDevice();
        }
        elseif ($command == 'getUserDetails')
        {
            $apiCommand = new APIUserDetails();
        }
        elseif ($command == 'activateProfile')
        {
            $apiCommand = new APIActivateProfile();
        }
        
        return $apiCommand;
    }

    public function getAllCommands()
    {
        return array_merge( $this->getSimpleCommands(), $this->getUserCommands(), $this->getAdminCommands() ); ;
    }
    
    // Returns an array of all simple (unauthorised) commands
    public function getSimpleCommands()
    {
        return $this->createCommandArray( array( "hello", "createAccount", "logIn", "logOut", "resetPassword", "getDeviceSlides", "getProfileSlides", "activateProfile" ) );
    }
    
    // Returns an array of all user commands
    public function getUserCommands()
    {
        return $this->createCommandArray( array( "changeEmail", "changePassword", "userStreams", "availableStreams", "subscribeToStream", "unsubscribeFromStream", "addDevice", "getUserDetails" ) );
    }
    
    // Returns an array of all admin commands
    public function getAdminCommands()
    {
        return array();
    }
    
    private function createCommandArray( $commandList )
    {
        $commands = array();
        
        foreach ( $commandList as $command  )
        {
            $commands[ $command ] = $this->createCommand( $command );
        }
        return $commands;
    }
}

?>

