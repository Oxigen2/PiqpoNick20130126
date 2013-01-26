<?php

class PiqpoLinkManager extends LinkManager
{
	function __construct()
	{
		$this->root = "";
	}

	function homeLink()
	{
		return $this->root."/index.html";
	}

	function logoutLink()
	{
		return $this->homeLink()."?logout";
	}
	
	// Returns true if log out parameter is set
	function logoutParam()
	{
		return isset($_GET['logout']);
	}
	
	function userHomeLink()
	{
		return $this->root."/user_home.html";
	}
	
	function slideUrl($slideId)
	{
		$vars = array( 'slide' => $slideId );
		return $this->serverRootUrl() . $this->root . "/slide.php?" . http_build_query($vars);
	}	
	
	// returns the slide id if it is set, otherwise returns the empty string
	function slideId()
	{
		return self::extractGetParamAsId('slide');
	}

    function getAPICommand( $command, $params )
    {
        $apiManager = new APIHandler();
        
        $link = $apiManager->createGETCommandString($this->serverRootURL().$this->root."/api/handler.php", $command, $params);

        return $link;
    }
    
	private $root;	
}

?>