<?php

// Just returns empty tag sets
class TagSourceBlank extends TagSource
{
	function __construct($size)
	{
		parent::__construct($size);
	}
	
	function prepareTags($slides)
	{
		return $slides;
	}
	
	function getTags($index)
	{
		return array();
	}
}

?>