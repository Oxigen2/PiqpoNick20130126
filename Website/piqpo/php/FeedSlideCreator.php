<?php

class FeedSlideCreator
{
	function __construct($feedId)
	{
		$this->_feedController = new FeedController( $feedId );
   		$this->_db = new PiqpoDBManager();
	}

	function processFeed()
	{
		$slideInfoSet = $this->_feedController->getSlideInfo();
				
		// Create the new set.
		$this->createSlides($slideInfoSet);
        
        // Remove no longer needed slides
        $this->ageSlides();
		
		// Update the queue
		$feedQueueManager = new FeedQueueManager();
		$feedQueueManager->feedUpdated($this->_feedController->feedId(), $this->_feedController->pollFrequency());
		Logger::slideLogger()->LogInfo( "Feed {$this->_feedController->feedId()} updated." );            
	}

	// Removes all slides for this feed from the slides table and from stream_slides
	function purgeFeed()
	{
		// No messing about
		$query = "DELETE FROM stream_slide WHERE slide_id IN ( SELECT slide_id FROM slide WHERE feed_id = {$this->_feedController->feedId()} )";		
		$this->_db->query($query);

		$query = "DELETE FROM slide WHERE feed_id = {$this->_feedController->feedId()}";		
		$this->_db->query($query);		
        
    	Logger::adminLogger()->LogInfo( "Feed {$this->_feedController->feedId()} has been purged." );
	}
		
	private function createSlides($slideInfoSet)
	{	
		foreach($slideInfoSet as $slideInfo)
		{
			$this->insertNewSlide($slideInfo);            
		}
	}
	
	// Removes slides from all streams, doesn't actually delete them at present.
	private function ageSlides()
	{       
        $query = "SELECT slide_id FROM slide WHERE feed_id = {$this->_feedController->feedId()} AND active = 1 ORDER BY publication_date DESC LIMIT {$this->_feedController->maxSlides()}, 1000000000";
		$result = $this->_db->query($query);

        $ids = array();
    	while ($myrow = mysql_fetch_array($result))
		{
			$ids[] = $myrow["slide_id"];
        }
        if ( count( $ids ) > 0 )
        {
            $idList = implode(',', $ids);
            
            $query = "UPDATE slide SET active = 0 WHERE slide_id IN ( $idList )";
            $this->_db->query($query);		
            
            $query = "DELETE FROM stream_slide WHERE slide_id IN ( $idList )";		
            $this->_db->query($query);
            
    		Logger::slideLogger()->LogInfo( "The following slides have been aged from feed {$this->_feedController->feedId()} : {$idList}." );
        }
	}
		
	private function insertNewSlide($slideInfo)
	{
		$templateManager = new TemplateManager();
			
		$html = $templateManager->createSlide($slideInfo->tags(), $this->_feedController->templateFile());			
		
		// may have failed
		if (isset($html))
		{
			$slideId = Slide::create(	addslashes($html), 
										$slideInfo->link(), 
										$slideInfo->guid(),
										addslashes($slideInfo->title()),
										$slideInfo->date(),
                                        date( "Y-m-d H:i:s" ),
										$this->_feedController->feedId(),
										$this->_feedController->pause(),
                                        true
									);
										
			if(isset($slideId))
			{
				// Add the slide to the streams it is in.
				$query = array( "feed_id" => $this->_feedController->feedId() );			
				$feedStreams = FeedStream::loadFromDB( $query );
				
				foreach( $feedStreams as $feedStream )
				{
					StreamSlide::create($feedStream->streamId(), $slideId);				
				}
                
           		Logger::slideLogger()->LogInfo( "Slide {$slideId} added to feed {$this->_feedController->feedId()} with guid {$slideInfo->guid()}." );
			}
		}	
	}
	
	private $_feedController;
    private $_db;
}

?>