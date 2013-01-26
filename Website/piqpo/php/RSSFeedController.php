<?php

class RSSFeedController extends FeedController
{	
	// Create a new rss feed
	// Returns a ReturnValue with the feed id set if successful	
	static function create($name, $pause, $pollFrequency, $templateFile, $maxSlides, $sourceUrl, $linkTransformFile)
	{
		$returnValue = new ReturnValue();
		
		// Validate name
		if (strlen($name) == 0)
		{
			$returnValue->addError("Name must supplied.");
		}
		// TODO check name not already used
		
		// Validate url
		if (!((strlen($sourceUrl) > 7) && (substr($sourceUrl, 0, 7) == 'http://')))
		{
			$returnValue->addError("The URL must be supplied starting http://");
		}
		// TODO url not already used
		
		$slides = (strlen($maxSlides) == 0) ? 0 : $maxSlides;
		
		if ($returnValue->success())
		{
			// TODO - Load up the RSS link to check it works and get the default link.
			// actually, maybe just check it works, not sure we need a default link here, should take it from the feed when it's read.
			$defaultTargetLink = "";
			
			$feedTypeId = FeedTypeRss::create($sourceUrl, $linkTransformFile);
			$feedId = Feed::create($name, $pause, "rss", $feedTypeId, $pollFrequency, $templateFile, $defaultTargetLink, $slides);
			$returnValue->setId($feedId);
			
			if ($returnValue->success())
			{
				$feedQueueManager = new FeedQueueManager();
				$feedQueueManager->addNewFeed($feedId);				
			}
		}
		return $returnValue;
	}
	
	function __construct($feed)
	{
		parent::__construct($feed);

		$this->_feedTypeRSS = FeedTypeRss::loadSingleFromDB($feed->feedTypeId());
		
		if (!isset($this->_feedTypeRSS)) 
		{
			throw new Exception("Feed type id {$feed->feedTypeId()} does not correspond to RSS feed"); 
		}
	}

	function getSlideInfo()
	{
		$slideInfoSet = array();
		
		$reader = new RSSReader($this->_feedTypeRSS->sourceUrl());
		
		$transformManager = new TransformManager();
		foreach ($this->transforms() as $transform)
		{
			$transformFilename = $transformManager->formFullFilename($transform->transformFile());
			$reader->addElementTransform($transform->element(), $transformFilename);
		}
		
		$linkTransformFile = $this->_feedTypeRSS->linkTransformFile();
		if (isset($linkTransformFile) && !empty($linkTransformFile))
		{
			$transformFilename = $transformManager->formFullFilename($linkTransformFile);
			$reader->addLinkTransform($transformFilename);
		}
		
		$result = $reader->processFeed();
				
		for($ii = 0; $ii < $result->itemsCount(); ++$ii)
		{
			$slideInfo = new SlideInfo("rss", $this->feedId(), $result->guid($ii), $result->link($ii), "", date('Y-m-d H:i:s'), $result->getTags($ii));
			$slideInfoSet[] = $slideInfo;
		}
		
		return $slideInfoSet;		
	}

	function sourceUrl()
	{
		return $this->_feedTypeRSS->sourceUrl();
	}
	
	function linkTransformFile()
	{
		return $this->_feedTypeRSS->linkTransformFile();
	}

	function modify($name, $pause, $pollFrequency, $templateFile, $maxSlides, $sourceUrl, $linkTransformFile)
	{
		$this->feed()->update($name, $pause, $this->feed()->feedType(), $this->feed()->feedTypeId(), $pollFrequency, $templateFile, $this->feed()->defaultTargetLink(), $maxSlides); 
	
		$this->_feedTypeRSS->update($sourceUrl,$linkTransformFile);
	}
	
	function transforms()
	{
		if (!isset($this->_transforms))
		{
			$query = array("feed_type_rss_id" => $this->_feedTypeRSS->feedTypeRSSId());
			$this->_transforms = FeedTypeRssTransform::loadFromDB($query);
		}
		return $this->_transforms;
	}	
	
	// returns ReturnValue with the id set to rss feed type transform id
	function addTransform($element, $transformFile)
	{
		$returnValue = new ReturnValue;
		
		$returnValue->setId( FeedTypeRssTransform::create($this->_feedTypeRSS->feedTypeRSSId(), $element, $transformFile) );
		
		$this->setTransformsStale();
		
		return $returnValue;
	}
	
	function removeAllTransforms()
	{
		foreach ($this->transforms() as $transform)
		{
			FeedTypeRssTransform::deleteFromDB( $transform->feedTypeRssTransformId() );
		}
		$this->setTransformsStale();
	}
	
	private function setTransformsStale()
	{
		$this->_transforms = null;
	}
	
	private $_feedTypeRSS;
	private $_transforms;
}

?>