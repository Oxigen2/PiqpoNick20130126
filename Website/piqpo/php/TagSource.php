<?php

abstract class TagSource
{
	function __construct($maxSlides)
	{
		$this->_maxSlides = $maxSlides;
	}

	// Returns array( id => array(tagName, tagValue) )
	function generateTagSet()
	{
		$this->_tagSet = array();

		$count = $this->prepareTags($this->_maxSlides);
		
		$limit = ($this->_maxSlides == 0) ? $count : min($count, $this->_maxSlides);
		for( $ii = 0; $ii < $limit; ++$ii)
		{
			$this->_tagSet[] = $this->getTags($ii);
		}
		
		return $this->_tagSet;
	}
	
	// Sub-classes override this.
	// Called to prepare tags by performing any sort of query, each RSS.
	// Returns the number of slides generated
	abstract function prepareTags($maxSlides);
	
	// Sub-classes provide tags on each call to this.
	abstract function getTags($index);
	
	protected function addTags($tags)
	{
		$this->_tagSet[] = $tags;
	}
		
	private $_tagSet;
	private $_maxSlides;
}

?>