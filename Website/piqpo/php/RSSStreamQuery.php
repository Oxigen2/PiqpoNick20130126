<?php

require_once(getenv("DOCUMENT_ROOT").'/3rd_party/magpierss/rss_fetch.inc');

class RSSStreamQuery extends StreamQuery
{
	function __construct($streamId, $streamTypeRSSId)
	{
		parent::__construct($streamId);
	
		$this->_streamTypeRSS = StreamTypeRss::loadSingleFromDB($streamTypeRSSId);
	}

	function getSlideInfo()
	{
		$slideInfoSet = array();
		
		$reader = new RSSReader();
		$result = $reader->processFeed($this->_streamTypeRSS->sourceUrl());
		
		$maxSlides = min($this->_streamTypeRSS->maxSlides(), $result->itemsCount());
		
		for($ii = 0; $ii < $maxSlides; ++$ii)
		{
			$slideInfo = new SlideInfo("rss", $this->streamId(), $result->guid($ii), $result->link($ii), $result->getTags($ii));
			$slideInfoSet[] = $slideInfo;
		}
		
		return $slideInfoSet;		
	}
	/*
	function getSlideInfo()
	{
		// Set of SlideInfo objects to return.
		$slideInfoSet = array();
	
		// Perform the rss query
		$feed = $this->_streamTypeRSS->sourceUrl();
		$rss = fetch_rss($feed);		
		
		// Image url
		$streamImage = "";
		if (isset($rss->image) && isset($rss->image['url']))
		{
			$streamImage = $rss->image['url'];
		}
		
		if (isset($rss->items))
		{
			// Trim the list if a max has been set.
			$maxSlides = $this->_streamTypeRSS->maxSlides();
			$items = ($maxSlides > 0) ? array_slice( $rss->items, 0, $maxSlides ) : $rss->items;
			
			// Add items to the info set.
			foreach ($items as $id => $item)
			{
				if (isset($item['link']) && isset($item['guid']))
				{
					$slideInfo = new SlideInfo("rss", $this->streamId(), $item['guid'], $item['link']);
					
					foreach($item as $index => $value)
					{
						$slideInfo->addNameValue("item_".$index, $value);
					}
					if (strlen($streamImage) > 0)
					{
						$slideInfo->addNameValue("image_link", $streamImage);
					}
					
					$slideInfoSet[] = $slideInfo;
				}
			}
		}
		
		return $slideInfoSet;
	}
	*/
	private $_streamTypeRSS;
}

?>