<?php

class SlideInfo
{
	// mandatory
	static $publicTagName_title = "title";
	static $publicTagName_date = "date";
	static $publicTagName_guid = "guid";
	static $publicTagName_link = "link";
	
	// optional
	static $publicTagName_text = "text";
	static $publicTagName_item_image = "item_image";
	static $publicTagName_feed_image = "feed_image";
	static $publicTagName_background_image = "background_image";
	static $publicTagName_item_image_credit = "item_image_credit";
	static $publicTagName_item_image_caption = "item_image_caption";

	function SlideInfo($feedId, $tags)
	{
        $this->_feedId = $feedId;
		$dateText = XMLTaggifier::evaluateDotSyntaxTag( self::$publicTagName_date, $tags );
		$this->_date = date( "Y-m-d H:i:s", strtotime( $dateText ) );	
		$this->_guid = XMLTaggifier::evaluateDotSyntaxTag( self::$publicTagName_guid, $tags );	
		$this->_link = XMLTaggifier::evaluateDotSyntaxTag( self::$publicTagName_link, $tags );
		$this->_title = XMLTaggifier::evaluateDotSyntaxTag( self::$publicTagName_title, $tags );
		$this->_tags = $tags;
	}
	
	// Returns the standard set of output tags.
	// array ( tagName => mandatory? )
	static function outputTags()
	{
		return array (	self::$publicTagName_title => true,
						self::$publicTagName_date => true,
						self::$publicTagName_guid => true,
						self::$publicTagName_link => true,
						self::$publicTagName_text => false,
						self::$publicTagName_item_image => false,
						self::$publicTagName_feed_image => false,
						self::$publicTagName_background_image => false,
						self::$publicTagName_item_image_credit => false,
						self::$publicTagName_item_image_caption => false
					);
	}
	
	function guid()
	{
		return $this->_guid;
	}
	
	// Tags are an array()
	// Either name => value, or
	//     or name => array(name => value, name => value)
	// eg array( n1 => v1, n2 => array(m1 => w1, m2 => w2 ) )
	function tags()
	{
		return $this->_tags;
	}
	
	function link()
	{
		return $this->_link;
	}

	function title()
	{
		return $this->_title;
	}

	function date()
	{
		return $this->_date;
	}

	private $_link;
	private $_title;
	private $_date;
	private $_tags;
	private $_guid;
    private $_feedId;
}

?>