<?php

class HtmlFeedController extends FeedController
{	
	// TODO - this is basically the same as the RSS controller, need a better way of doing this
	// Create a new rss feed
	// Returns a ReturnValue with the feed id set if successful	
	static function create($name, $pause, $pollFrequency, $templateFile, $maxSlides, $sourceUrl, $transformFile)
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
			// TODO - Load up the link to check it works		
			$feedTypeId = FeedTypeHtml::create($sourceUrl, $transformFile);
			$feedId = Feed::create($name, $pause, "html", $feedTypeId, $pollFrequency, $templateFile, "", $slides);
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

		$this->_feedTypeHtml = FeedTypeHtml::loadSingleFromDB($feed->feedTypeId());
		
		if (!isset($this->_feedTypeHtml)) 
		{
			throw new Exception("Feed type id {$feed->feedTypeId()} does not correspond to HTML feed"); 
		}
	}

	function getSlideInfo()
	{
		$slideInfoSet = array();
		
		$url = $this->_feedTypeHtml->sourceUrl();
		
		// Can have just a completely empty feed for a test template.
		if (isset($url) && !empty($url))
		{
			// Load and tidy the html page
			$tidyString = HtmlTidier::tidyFile($url);

			$htmlDom = new DOMDocument;	
			$htmlDom->loadXML($tidyString);
			
			// Load the transform if there is one
			$resultsDom = $htmlDom;
			$xslt = $this->_feedTypeHtml->transformFile();
			if (isset($xslt) && !empty($xslt))
			{
				$transformManager = new TransformManager();
				$transformFilename = $transformManager->formFullFilename($xslt);
			
				$xsltDom = new DOMDocument;
				$xsltDom->load($transformFilename);
				$xsltProcessor = new XSLTProcessor;
				$xsltProcessor->importStylesheet($xsltDom);
				
				$results = $xsltProcessor->transformToXML($htmlDom);
				
				$resultsDom = new DOMDocument;
				$resultsDom->loadXML($results);
			}

			$itemTaggifier = new XMLTaggifier;
			$itemTaggifier->processDom($resultsDom);
			
			// Setting guid and link to the url for now
			$slideInfo = new SlideInfo("html", $this->feedId(), $url, $url, $itemTaggifier->tags());
			
			// Single item array
			$slideInfoSet = array($slideInfo);
		}
		
		return $slideInfoSet;		
	}

	function sourceUrl()
	{
		return $this->_feedTypeHtml->sourceUrl();
	}
	
	function transformFile()
	{
		return $this->_feedTypeHtml->transformFile();
	}

	function modify($name, $pause, $pollFrequency, $templateFile, $maxSlides, $sourceUrl, $transformFile)
	{
		$this->feed()->update($name, $pause, $this->feed()->feedType(), $this->feed()->feedTypeId(), $pollFrequency, $templateFile, $this->feed()->defaultTargetLink(), $maxSlides); 
	
		$this->_feedTypeHtml->update($sourceUrl,$transformFile);
	}
	
	private $_feedTypeHtml;
}

?>