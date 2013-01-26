<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class Publisher
{
	private $_content;
	private $_userInfo;

	function Publisher()
	{
		$this->_content = "";
		$this->_userInfo = "";
	}
	
	function addLine($line, $divClass = "")
	{
		$pre = "";
		$post = "";
		if (strlen($divClass) > 0)
		{
			$pre = "<div class=\"{$divClass}\">";
			$post = "</div>";
		}
		$this->_content .= $pre.$line.$post."\n";
	}
	
	private function addUserLine($line)
	{
		$this->_userInfo .= $line."\n";
	}
		
	function publishPage($title)
	{
		$output = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\"\n";
		$output .= "    \"http://www.w3.org/TR/html4/loose.dtd\">\n";
		$output .= "<html>\n";
		$output .= "<head>\n";
		$output .= "<LINK href=\"/include/css/nickstricks.css\" type=text/css rel=stylesheet>\n";
		$output .= "<LINK href=\"/include/css/piqpo.css\" type=text/css rel=stylesheet>\n";
		$output .= "<title>Piqpo - {$title}</title>\n";
		$output .= "</head>\n";
		$output .= "<body>\n";
		$output .= "<div class=top>\n"; 
		$output .= "<div class=userinfo>\n"; 
		$output .= "{$this->_userInfo}\n";
		$output .= "</div>\n"; // userinfo
		$output .= "<div class=main>";
		//$output .= "<div class=t1>piqpo</div>\n";	
		$output .= "<img src='/include/images/piqpo_200x75.gif' />";		
		$output .= "<div class=content>";
		$output .= "<hr />";		
		$output .= $this->_content;
		$output .= "</div>\n"; // content
		$output .= "<div class=block><hr /></div>\n";		
		$output .= "</div>\n"; // main
		$output .= "</div>\n"; // top
		$output .= "</body>\n";
		$output .= "</html>\n";
		
		print $output;
	}
	
	function publishUserPage($title)
	{
		$userManager = new UserManager();		
		$linkManager = new PiqpoLinkManager();
	
		$userId = $userManager->getUserId(true);
		$user = User::loadSingleFromDB($userId);

		// Give user info	
		$this->addUserLine("Logged in as {$user->name()}");
		$this->addUserLine("(<a href='{$linkManager->logoutLink()}'>log out</a>)");
					
		$this->addUserLine(" - <a href='/view_streams.html?user_id={$user->userId()}' target='_blank'>View streams</a>");			
					
		$this->publishPage($title);
	}
}
?>
