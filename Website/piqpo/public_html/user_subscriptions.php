<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

$userManager = new UserManager();
$streamManager = new StreamManager();

$userEmail = $_GET['user'];

$query = array("email" => "'".$userEmail."'");
$user = User::loadFromDB($query);

$output = "<items>\n";

if ( count($user) == 1 )
{
	$userId = $user[0]->userId();
	
	$streams = StreamManager::userStreams($userId);
	
	foreach ($streams as $id => $stream)
	{
		$output .= "<item src='{$stream->sourceUrl()}' link='{$stream->targetLink()}' pause='{$stream->pause()}' />\n";
	}
}
else
{
	$errorLink = "http://".$_SERVER['SERVER_NAME']."/piqpo/error.php";
	$userParam = (strlen($userEmail) == 0) ? "" : "?user={$userEmail}" ;
	$output .= "<item src='{$errorLink}{$userParam}' link='' pause='0' />\n";
}

$output .= "</items>\n";

header('Content-type: text/xml');
print $output;
?>