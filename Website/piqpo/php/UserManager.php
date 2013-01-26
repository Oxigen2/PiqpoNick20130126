<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class UserManager
{
	var $db;
	var $cookieManager;
	var $linkManager;

	function UserManager()
	{
		$this->db = new PiqpoDBManager();
		$this->cookieManager = new PiqpoCookieManager();
		$this->linkManager = new PiqpoLinkManager();
	}	
   
    function getUserEmail( $userId )
    {
        $user = User::loadSingleFromDB($userId);

        $email = "";
        if ( isset( $user ) )
        {
            $email = $user->email();
        }
        
        return $email;        
    }
    
    function setActive( $userId )
    {
        $user = User::loadSingleFromDB($userId);
        
        if ( isset( $user ) && ( $user->status() != "active" ) )
        {
            $user->update( $user->password(), $user->email(), $user->token(), "active" );
        }

        return $user;
    }

	// Gets the user id from the cookie.
	// Returns blank if missing.
	// If redirectIfMissing is set true, will perform a redirect to the sign-in page.
	function getUserId($redirectIfMissing)
	{
		$redirect = false;
			
		$userId = $this->cookieManager->getUserCookie();
		
		if ($redirectIfMissing)
		{		
			if (empty($userId))
			{
				$redirect = true;
			}
			else
			{
				// Check that user id is valid, clear cookie if not
				$user = User::loadSingleFromDB($userId);				
				if (!isset($user))
				{
					$this->cookieManager->clearUserCookie();
					$redirect = true;
				}
			}
		}
		
		if ( $redirect )
		{
			$this->linkManager->redirect($this->linkManager->homeLink());
		}
		
		return $userId;
	}

	function addUserForm($submitPage)
	{
		$existingValues = $_POST;
	
		$form = new FormGenerator($submitPage,"post", "", "<p>", "form", "form_item", "form_prompt", "form_input");
		$form->addText("Name", "name",  isset($existingValues["name"]) ? $existingValues["name"] : "", 200);
		$form->addPassword("Password", "password", "", 200);
		$form->addText("Email", "email", isset($existingValues["email"]) ? $existingValues["email"] : "", 200);
				
		$form->addSubmit("Add User", "adduser");
		
		return $form->getOutput();
	}

	// Returns true if add user form has been submitted
	function addUserFormSubmitted()
	{
		return isset($_POST['adduser']);
	}
	
	// Returns a ReturnValue
	function processAddUserForm()
	{
        $name = $_POST["name"];
        $password = $_POST["password"];
        $email = $_POST["email"];
		return $this->createUser($name, $password, $email);
	}
    
    private function generateHash( $password )
    {
        return sha1( "Adam Yauch" . $password );
    }
    
    function testUserPassword( $user, $password )
    {
        return ( $user->password() == $this->generateHash( $password ) );
    }

	// Gets the user id from the cookie.
	// Returns null if missing.
	// If redirectIfMissing is set true, will perform a redirect to the sign-in page.
	function getUserIdFromCookieToken($redirectIfMissing)
	{
		$redirect = false;
        $userId = null;
		$userToken = $this->cookieManager->getUserCookie();
		
        if (empty($userToken))
        {
            $redirect = true;
        }
        else
        {
            // Check that user id is valid, clear cookie if not
            $user = $this->getUserFromToken($userToken);				
            if (!isset($user))
            {
                $this->cookieManager->clearUserCookie();
                $redirect = true;
            }
            else
            {
                $userId = $user->userId();
            }
        }
		
		if ( $redirect && $redirectIfMissing )
		{
			$this->linkManager->redirect($this->linkManager->homeLink());
		}
		
		return $userId;
	}

    
    function getUserFromToken( $token )
    {
        $queryArray = array( 'token' => "'".$token."'" );
        $user = User::loadFromDB($queryArray);				
        return count( $user ) == 1 ? $user[0] : null;
    }
    
    function loginUser($email, $password)
    {
        $returnValue = new ReturnValue();
        
        $user = $this->getUser( $email );
        
        if ( !isset($user) )
        {
            $returnValue->addError("Login failed");
        }
        else 
        {
            if ($this->testUserPassword($user, $password))
            {
                $this->cookieManager->setUserCookie( $user->token() );
                $returnValue->setId($user->token());
            }
            else
            {
                $returnValue->addError("Login failed");            
            }
        }
        
        return $returnValue;
    }
    
    function logoutUser()
    {
        $this->cookieManager->clearUserCookie();
    }
    
    function createUser($email, $password, $setPending)
    {
        $returnValue = new ReturnValue();
        		
		// Validate the email address
		if (!CommonFunctions::is_valid_email_address($email))
		{
			$returnValue->addError("Email format is not valid");			
		}
		else
		{
            $user = $this->getUser( $email );
            
			if (isset($user))
			{
				$returnValue->addError("Account already exists with email address");			
			}
            else
            {
                if ( !$this->isValidPassword($password) )
                {
    				$returnValue->addError("Password must be at least 8 characters");			
                }
            }
		}
		
		if ( $returnValue->success() )
		{        
    		// Insert the user
            $token = com_create_guid();
            $status = $setPending ? 'pending' : 'active';
			$returnValue->setId(User::create($this->generateHash( $password ), $email, $token, $status));
		}
		
		return $returnValue;
    }
    
    function activateUser( $userId )
    {
        $user = User::loadSingleFromDB($userId);
        
        if ( isset( $user ) && ($user->status() != "active") )
        {
            $user->update($user->password(), $user->email(), $user->token(), "active");
        }
    }
                
    
    // Returns user class or null
    function getUser( $email )
    {
        $user = null;
        
        $queryArray = array("email" => "'{$email}'");

        $output = User::loadFromDB($queryArray);

        if (count($output) == 1)
        {
            $user = $output[0];			
        }
        
        return $user;
    }
	
	function loginUserForm($submitPage)
	{
		$existingValues = $_POST;
	
		$form = new FormGenerator($submitPage,"post", "", "<p>", "form", "form_item", "form_prompt", "form_input");
		$form->addText("Email", "email", isset($existingValues["email"]) ? $existingValues["email"] : "", 200);
		$form->addPassword("Password", "password", "", 200);
		$form->addSubmit("Log in", "login");
		
		return $form->getOutput();	
	}

	function loginUserFormSubmitted()
	{
		return isset($_POST['login']);
	}
	
	// Returns ReturnValue with user id set if successful.
	function processLoginUserForm()
	{
		$postInfo = $_POST;		
		$returnValue = new ReturnValue();

		if (isset($postInfo["email"]))
		{
			$queryArray = array("email" => "'".$postInfo["email"]."'");
			
			$output = User::loadFromDB($queryArray);
			
			if (isset($output) && (count($output) == 1))
			{
                $user = $output[0];
                $password = $postInfo["password"];
                
                if ( $this->testUserPassword($user, $password) )
                {
    				$returnValue->setId($user->userId());
                }
                else
                {
                    $returnValue->addError("Incorrect password.");   
                }
			}
			else
			{
				$returnValue->addError("User email not found.");
			}
		}
		else
		{
			$returnValue->addError("Email address not supplied");
		}
		
		return $returnValue;
	}
    
    function modifyEmail($userId, $email)
    {
        $user = User::loadSingleFromDB( $userId );
        
        if ( isset( $user ) )
        {
            $user->update( $user->password(), $email, $user->token(), $user->status() );
        }
    }
    
    function modifyPassword($userId, $password)
    {
        $res = new ReturnValue();
        
        $user = User::loadSingleFromDB( $userId );
        
        if ( isset( $user ) )
        {
            $res = $this->updatePassword($user, $password);
        }
        else
        {
            $res->addError("Invalid user");
        }
        
        return $res;
    }

    function resetPassword( $userId )
    {
        $user = User::loadSingleFromDB( $userId );
        
        $password = CommonFunctions::generateCode( $this->minPasswordLength() );
        
        if( isset($user) )
        {
            $this->updatePassword( $user, $password );
            
            $to = $user->email();
            $subject = "Piqpo password reset";
            $message = "Your piqpo password has been reset to " . $password;            
            $message .= " Login at " . $this->linkManager->homeLink() . ".";
            
            $emailManager = new EmailManager;
            $emailManager->send( $to, 'admin@piqpo.com', $subject, $message );
        }
    }
    
    private function updatePassword( $user, $password )
    {
        $res = new ReturnValue();
        if (!$this->isValidPassword( $password ))
        {
            $res->addError("Invalid password");            
        }
        else
        {
            $token = com_create_guid();
            $user->update( $this->generateHash( $password ), $user->email(), $token, $user->status() );
            $res->setId($token);
        }
        return $res;
    }
        
    private function isValidPassword( $password )
    {
        return strlen( $password ) >= $this->minPasswordLength();        
    }

    private function minPasswordLength()
    {
        return 8;
    }
}
?>
