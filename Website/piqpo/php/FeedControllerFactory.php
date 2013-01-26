<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class FeedControllerFactory
{	
	static function createFeedController($feedId)
	{
		$feedController = null;
		
		$feed = Feed::loadSingleFromDB($feedId);
		
		if (isset($feed))
		{
			if ($feed->feedType() == "rss")
			{
				$feedController = new RSSFeedController($feed);
			}
			else if ($feed->feedType() == "html")
			{
				$feedController = new HtmlFeedController($feed);
			}
		}
		else
		{
			throw new Exception("Feed not found for feed id {$feedId}"); 
		}
		
		return $feedController;
	}
}

?>
