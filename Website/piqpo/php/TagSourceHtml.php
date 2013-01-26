<?php

class TagSourceHtml extends TagSource
{
	function __construct($url, $xslt = null)
	{
		parent::__construct(1);	// can only have 1 slide
		
		$this->_url = $url;
		$this->_xslt = $xslt;
	}
	
	function prepareTags($maxSlides)
	{
		$tidyString = HtmlTidier::tidyFile($this->_url);

		$htmlDom = new DOMDocument;	
		$htmlDom->loadXML($tidyString);
		
		// Load the transform if there is one
		$resultsDom = $htmlDom;
		$xslt = $this->_xslt;
		if (isset($xslt) && !empty($xslt))
		{
			$xsltDom = new DOMDocument;
			$xsltDom->loadXML($xslt);
			$xsltProcessor = new XSLTProcessor;
			$xsltProcessor->importStylesheet($xsltDom);
			
			$results = $xsltProcessor->transformToXML($htmlDom);
			
			$resultsDom = new DOMDocument;
			$resultsDom->loadXML($results);
		}

		// form the guid from the tag source string, want to regenerate only when it changes
		$this->_guid = md5($resultsDom->saveXML());
	
		$this->_itemTaggifier = new XMLTaggifier;
		$this->_itemTaggifier->processDom($resultsDom);			
			
		return 1;
	}
	
	function getTags($index)
	{
		// add the guid to the return tags.
		$publicTags = array(SlideInfo::$publicTagName_link => $this->_url, 
		                    SlideInfo::$publicTagName_guid => $this->_guid);
							
		return array_merge( $publicTags, $this->_itemTaggifier->tags() );
	}
	
	private $_url;
	private $_xslt;
	private $_itemTaggifier;
	private $_guid;
}

?>