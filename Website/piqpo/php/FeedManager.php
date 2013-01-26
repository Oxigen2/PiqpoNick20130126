<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class FeedManager
{
	function __construct()
	{
	}
	
	function feedSlides($feedId, $active)
	{
		$query = array('feed_id' => $feedId, 'active' => $active);
        $orderByArray = array('publication_date');
		
		$slides = Slide::loadFromDB($query, $orderByArray);
		        
		return $slides;
	}	
	
	function feedSlideIds($feedId)
	{
		$slideIds = array();
		
		$query = array('feed_id' => $feedId);
		
		$slides = Slide::loadFromDB($query);
		
		foreach ($slides as $slide)
		{
			$slideIds[] = $slide->slideId();
		}
		
		return $slideIds;
	}	
	
	// Returns an array of all the feeds
	function allFeeds()
	{
		return Feed::loadAllFromDB();
	}
}

?>
