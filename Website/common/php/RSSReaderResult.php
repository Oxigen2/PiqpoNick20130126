<?php

class RSSReaderResult
{
	function __construct($isAtom)
	{
		$this->_channel = array();
		$this->_items = array();
		
		$this->_success = true;
		$this->_isAtom = $isAtom;
	}
			
	// Returns the information for the channel
	function channelInfo()
	{
		return $this->_channel;
	}

	// Returns all the items as an array
	function items()
	{
		return $this->_items;
	}
	
	// Returns the number of item retrieved by the feed
	function itemsCount()
	{
		return count($this->_items);
	}
	
	function getItem($index)
	{
		$this->checkItemBounds($index);
		
		return $this->_items[$index];
	}

	function link($index)
	{	
		return $this->getItemTagValue($this->_isAtom ? XmlTaggifier::formAttributeName("link").".0.href" : "link" , $index);
	}

	// can be missing.  just returning null here if so.
	function guid($index)
	{	
		return $this->getItemTagValue($this->_isAtom ? "id" : "guid" , $index);
	}
	
	function title($index)
	{	
		return $this->getItemTagValue("title", $index);
	}
	
	function date($index)
	{	
		// just returning text for now, could validate and standardise format
		return $this->getItemTagValue($this->_isAtom ? "updated" : "pubDate" , $index);
	}
	
	function text($index)
	{	
		return $this->getItemTagValue($this->_isAtom ? "content" : "description" , $index);
	}
	
	private function getItemTagValue($tag, $index)
	{
		$this->checkItemBounds($index);
		
		return XmlTaggifier::evaluateDotSyntaxTag($tag, $this->_items[$index]);
	}
		
	private function getChannelTagValue($tag)
	{
		return XmlTaggifier::evaluateDotSyntaxTag($tag, $this->_channel);
	}
		
	function getRawItem($index)
	{
		$this->checkItemBounds($index);
	
		return $this->_rawItems[$index];
	}

	private function checkItemBounds($index)
	{
		if (!(array_key_exists($index, $this->_items)))
		{	
			throw new Exception('Illegal index '.$index.' used to access items');
		}
	}
	
	function getTags($itemIndex)
	{
		return array("channel" => $this->channelInfo(), "item" => $this->getItem($itemIndex));
	}
	
	// Add channel information
	function setChannelInfo($info)
	{
		$this->_channel = $info;
	}
		
	function addItem($tags, $link, $guid, $rawXml)
	{
		$this->_items[] = $tags;
		$this->_links[] = $link;
		$this->_guids[] = $guid;
		$this->_rawItems[] = $rawXml;
	}
		
	function errorText()
	{
		return $_errorText;
	}
	
	function success()
	{
		return $_success;
	}
	
	function setError($errorText)
	{
		$_success = false;
		$_errorText = $errorText;
	}
	
	private $_success;
	private $_errorText;
	private $_channel;
	private $_items;	// tags
	private $_links;	// item click through links
	private $_guids;	
	private $_rawItems;	// xml
	private $_isAtom;
}

?>