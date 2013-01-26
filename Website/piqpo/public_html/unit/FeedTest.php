<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" 
    "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>	
<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

$feedId = null;
$slideIndex = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$feedId = $_POST['feed_id'];
	if (isset($_POST['slide_index']))
	{
		$slideIndex = $_POST['slide_index'];
	}
}
		
// Get slide info
$slideCount = null;	
$tags = null;
$slideOutput = "";
$slideOutput = "";	

if (isset($feedId))
{
	$feedController = FeedControllerFactory::createFeedController($feedId);

	if (isset($feedController))
	{
		$slideInfoSet = $feedController->getSlideInfo();
	
		if (isset($slideInfoSet) && (count($slideInfoSet) > $slideIndex))
		{
			$slideInfo = $slideInfoSet[$slideIndex];
			
			$tags = $slideInfo->getTags();
		
			$slideOutput .= "guid: " . $slideInfo->getGuid() . "<br>";
			$slideOutput .= "link: " . $slideInfo->getTargetLink() . "<br>";

			$slideCount = count($slideInfoSet);
		}
	}
}
	
// FORM
print "<form action='{$_SERVER["SCRIPT_NAME"]}' method='POST'>";

// Feed input
$feedManager = new FeedManager();
$feeds = $feedManager->allFeeds();
print "Feed:<select name='feed_id'>";
foreach($feeds as $feed)
{
	$id = $feed->feedId();
	$name = $feed->name();
	$selected = (isset($feedId) && ($feedId == $id)) ? "selected" : ""; 
	print "<option value={$id} {$selected}>{$name}</option>";
}
print '</select><br>';

// Index input
if (isset($slideCount))
{
	print 'Slide:<select name="slide_index">';
	for($ii = 0; $ii < $slideCount; ++$ii)
	{
		$selected = ($ii == $slideIndex) ? "selected" : ""; 
		print "<option value={$ii} {$selected}>{$ii}</option>";
	}
	print '</select><br>';		
}

// Form end
print '<input type=submit value="Update" />';
print '</form>';

print '<hr/>';

function copyArrayRemoveNulls($inputTags, &$outputTags)
{
	if (is_array($inputTags))
	{
		$outputTags = array();
		foreach ($inputTags as $key => $value)
		{
			$outputTags[$key] = null;
			copyArray($value, $outputTags[$key]);
			if ( $outputTags[$key] == null )
			{
				unset($outputTags[$key]);
			}
		}
	}
	else if (!(empty($inputTags)))
	{
		$outputTags = $inputTags;
	}
}

if (isset($tags))
{
	$taggifier = new XMLTaggifier;
	print $taggifier->createTable($tags);	
}

print "<hr />{$slideOutput}";

  
?>
</body>
</html>
