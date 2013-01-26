<?php

class TagSourceRSS extends TagSource
{
	function __construct($url, $maxSlides)
	{
		parent::__construct($maxSlides);
		
		$this->_url = $url;
	}
	
	function prepareTags($maxSlides)
	{
		$rssReader = new RSSReader( $this->_url );
		
		// pass in max slides when the reader implements it, more efficient than just filtering afterwards
		// hmm, but problematic, slides might error further down the line.
		// think more about this...
        $slideCount = 0;
        try
        {
            $this->_rssResult = $rssReader->processFeed();
            if ( !is_null( $this->_rssResult ) )
            {
                $slideCount = $this->_rssResult->itemsCount();
            }
		}
        catch (Exception $ee)
        {
            Logger::slideLogger()->LogError( "Failed processing RRS feed with url {$url} : {$ee->getMessage()}." );            
            
        }
            
		return $slideCount;
	}
	
	function getTags($index)
	{
		$publicLinks = array();

        // Revert this ?
        $link = $this->_rssResult->link($index);
        $guid = $this->_rssResult->guid($index);
        
		$publicLinks[ SlideInfo::$publicTagName_link ] = $link;
		$publicLinks[ SlideInfo::$publicTagName_guid ] = $guid;
		$publicLinks[ SlideInfo::$publicTagName_title ] = $this->_rssResult->title($index);
		$publicLinks[ SlideInfo::$publicTagName_date ] = $this->_rssResult->date($index);
		$publicLinks[ SlideInfo::$publicTagName_text ] = $this->_rssResult->text($index);

		return array_merge( $publicLinks, $this->_rssResult->getTags( $index ));
	}
	
	private $_url;
	private $_rssResult;
}

?>