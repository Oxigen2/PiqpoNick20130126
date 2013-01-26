<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

$output = "";

$piqpoLinkManager = new PiqpoLinkManager();
$slideId = $piqpoLinkManager->slideId();

if (strlen($slideId) == 0)
{
	$errorManager = new ErrorManager();
	$output = $errorManager->errorHTML("Invalid slide id received.");
}
else
{
	$slideManager = new SlideManager();
	$output = $slideManager->getSlideHTML($slideId);
}

// Set caching on
$expires = 60*60*24*365;
header("Pragma: public");
header("Cache-Control: maxage=".$expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');

print $output;
?>
