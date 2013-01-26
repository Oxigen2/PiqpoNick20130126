<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class SlideManager
{
	function SlideManager()
	{
	}
    
    function getSlide($slideId)
    {
        return Slide::loadSingleFromDB($slideId);
    }
	
    function getProfileSlides($profileId)
    {
		$queryString = "SELECT S.* FROM slide S, profile_stream PS, feed_stream FS 
						WHERE PS.profile_id = {$profileId} 
						AND PS.stream_id = FS.stream_id
						AND FS.feed_id = S.feed_id 
                        AND S.active = 1 ";
                        
        $slides = Slide::processDBQuery($queryString);
        
        $slideInfo = array();
		foreach ($slides as $id => $slide)
		{
			$slideInfo[] = $this->formSlideInfoArray($slide);
		}
        
        return $slideInfo;
    }
    
	function getUserSlideXML($userId)
	{
		$xml = "<items>";
		
		$slides = $this->getUserSlides($userId);
		foreach ($slides as $id => $slide)
		{
			$xml .= $this->formSlideXMLItem($slide);
		}
		
		$xml .= "</items>";
		
		return $xml;
	}
	
	function getSlideHTML($slideId)
	{
		$html = "";
		
		$slide = Slide::loadSingleFromDB($slideId);
		
		if (isset($slide))
		{
			$html = $slide->content();
		}
		else
		{
			throw new Exception("Didn't find slide ".$slideId);
		}
		
		return $html;
	}
	
	// Returns an array of slides for the given user
	private function getUserSlides($userId)
	{
		$queryString = "SELECT S.* FROM slide S, user_stream US, feed_stream FS 
						WHERE US.user_id = {$userId} 
						AND US.stream_id = FS.stream_id
						AND FS.feed_id = S.feed_id 
                        AND S.active = 1 ";
		return Slide::processDBQuery($queryString);
	}

	private function formSlideInfoArray($slide)
	{
		$linkManager = new PiqpoLinkManager();
		
		$sourceUrl = $linkManager->slideUrl($slide->slideId());
		$encodedLink = urlencode($slide->targetLink());
		
		return array( 'src' => $sourceUrl, 'link' => $encodedLink, 'pause' => $slide->pause(), 'id' => $slide->slideId() );
	}
    
	private function formSlideXMLItem($slide)
	{
		$linkManager = new PiqpoLinkManager();
		
		$sourceUrl = $linkManager->slideUrl($slide->slideId());
		$encodedLink = urlencode($slide->targetLink());
		
		return "<item src='{$sourceUrl}' link='{$encodedLink}' pause='{$slide->pause()}' id='{$slide->slideId()}' />";
	}
}
?>
