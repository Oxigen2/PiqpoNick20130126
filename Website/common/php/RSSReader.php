<?php

class RSSReader
{
	function __construct($url)
	{	
		$this->_url = $url;
		$this->_xsltProcessors = array();
		$this->_isAtom = false;
	}
	
	private $_isAtom;
	private $_url;
	private $_dom;
	private $_linkProcessor;
	private $_xsltProcessors;

	function addLinkTransform($file)
	{
		$xsltDom = new DOMDocument;
		$xsltDom->load($file);
		$xsltProcessor = new XSLTProcessor;
		$xsltProcessor->importStylesheet($xsltDom);

		$this->_linkProcessor = $xsltProcessor;		
	}
	
	function addElementTransform($element, $file)
	{
		$xsltDom = new DOMDocument;
		$xsltDom->load($file);
		$xsltProcessor = new XSLTProcessor;
		$xsltProcessor->importStylesheet($xsltDom);

		$this->_xsltProcessors[ $element ] = $xsltProcessor;
	}
	
	function url()
	{
		return $this->_url;
	}
	
	// Returns an RSSReaderResult generated from reading the RSS feed
	// Throws an exception on error
	function processFeed()
	{
		$this->_dom = new DOMDocument;
		$this->_dom->load($this->_url);
		$documentXPath = new DOMXPath($this->_dom);
		
		$result = null;
		
		$rootNodeName = $this->_dom->documentElement->tagName;
		if ($rootNodeName == "rss")
		{
			// RSS
			$this->_isAtom = false;
			$result = new RSSReaderResult(false);
			$this->processDocument('/rss/channel/*', 'item', $documentXPath, $result);
		}
		else if ($rootNodeName == "feed")
		{
			// Atom
			$this->_isAtom = true;
			$result = new RSSReaderResult(true);
			$this->processDocument('/*/*', 'entry', $documentXPath, $result);	// due to the default namespace '/feed/*' doesn't pick up anything, get away with this for now
		}
								
		return $result;
	}
	
	private function processDocument($query, $itemName, $xpath, &$result)
	{
		$channelTaggifier = new XMLTaggifier;
	
		$childNodes = $xpath->query($query);
		
		foreach($childNodes as $childNode)
		{
			if ( $childNode->localName == $itemName ) 
			{
				$this->processItemNode($childNode, $xpath, $this->_dom->saveXml($childNode), $result);
			}
			else
			{
				$channelTaggifier->processItem($childNode, $xpath);
			}
		}	

		$result->setChannelInfo($channelTaggifier->tags());
	}
	
	private function processItemNode($node, $xpath, $rawXml, &$result)
	{
		$itemTaggifier = new XMLTaggifier;
		
		// Process all sub elements
		foreach($xpath->query('*', $node) as $child)
		{			
			$itemTaggifier->processItem($child, $xpath);
		
			$this->processElementTransform($child, $itemTaggifier);
		}				
		
		$this->processLinkTransform($itemTaggifier);
		
		$result->addItem($itemTaggifier->tags(), $this->getLink($itemTaggifier), $this->getGuid($itemTaggifier), $rawXml);
	}
	
	private function processLinkTransform(&$itemTaggifier)
	{
		if (isset($this->_linkProcessor))
		{
			$link = $this->getLink($itemTaggifier);			

			if (isset($link))
			{
				// Prepare the source HTML
				$fixed = HtmlTidier::tidyFile($link);
				
				// Perform the transform
				if (isset($fixed) && !empty($fixed))
				{
					$xmlDom = new DOMDocument;
					$xmlDom->loadXML($fixed);			
					$results = $this->_linkProcessor->transformToXML($xmlDom);
					
					// Generate tags from the output
					if (!empty($results))
					{
						$resultsDom = new DOMDocument;
						$resultsDom->loadXML($results);
						
						$itemTaggifier->processDom($resultsDom);
					}	
				}
			}
		}
	}
	
	private function processElementTransform($node, &$itemTaggifier)
	{
		// Does this element have a transform associated with it.
		$elementName = $node->tagName;
		if (isset($this->_xsltProcessors[$elementName]))
		{
			// Load transform
			$xsltProcessor = $this->_xsltProcessors[$elementName];
			
			// Prepare the source HTML
			$fixed = HtmlTidier::tidyString($node->nodeValue, true);
			
			// Perform the transform
			$xmlDom = new DOMDocument;
			$xmlDom->loadXML($fixed);		
			$results = $xsltProcessor->transformToXML($xmlDom);
			
			// Generate tags from the output
			$resultsDom = new DOMDocument;
			$resultsDom->loadXML($results);
	
			$itemTaggifier->processDom($resultsDom);
		}	
	}

	// Returns link if found, null if not.
	private function getLink($taggifier)
	{
		$tags = $taggifier->tags();
		$link = null;
		if ($this->_isAtom)
		{
			// TODO not sure what to do here
			$link = $tags["link_A"][0]["href"];
		}
		else
		{
			if (isset($tags["link"]))
			{
				$link = $tags["link"];
			}
		}
		
		return $link;		
	}
	
	// Returns guid if found, null if not.
	private function getGuid($taggifier)
	{
		$tag = "";
		if ($this->_isAtom)
		{
			$tag = "id";
		}
		else
		{
			$tag = "guid";
		}
		return $taggifier->getTag($tag);
	}		
}

?>
