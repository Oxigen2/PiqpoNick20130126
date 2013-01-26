<?php

class RSSFeedQuery extends FeedQuery
{
	function __construct($feedId, $feedTypeRSSId)
	{
		parent::__construct($feedId);
	
		$this->_feedTypeRSS = FeedTypeRss::loadSingleFromDB($feedTypeRSSId);
	}

	function getSlideInfo()
	{
		$slideInfoSet = array();
		
		$reader = new RSSReader($this->_feedTypeRSS->sourceUrl());
		$result = $reader->processFeed();
				
		for($ii = 0; $ii < $result->itemsCount(); ++$ii)
		{
			$slideInfo = new SlideInfo("rss", $this->feedId(), $result->guid($ii), $result->link($ii), $result->getTags($ii));
			$slideInfoSet[] = $slideInfo;
		}
		
		return $slideInfoSet;		
	}

	private $_feedTypeRSS;
}

?>