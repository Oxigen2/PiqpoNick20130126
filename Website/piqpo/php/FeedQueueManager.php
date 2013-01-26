<?php

class FeedQueueManager
{
	function __construct()
	{
	}
	
	// Insert a new feed into the queue.
	function addNewFeed($feedId)
	{
		FeedQueue::create($feedId, date('Y-m-d H:i:s'));
	}
	
	// Call to indicate that a stream has been updated.
	function feedUpdated($feedId, $pollFrequency)
	{
		$nextPoll = date('Y-m-d H:i:s', strtotime("+ {$pollFrequency} seconds"));
	
		$query = "UPDATE feed_queue SET next_poll = '{$nextPoll}' WHERE feed_id = {$feedId}";
		
		$db = new PiqpoDBManager();
		$db->query($query);
	}
	
	function getAllFeeds()
	{
		return FeedQueue::loadFromDB( array() );
	}
	
	function pollOverdueFeeds()
	{
        $now = date('Y-m-d H:i:s');
        
		$query = "SELECT * FROM feed_queue WHERE next_poll < '{$now}'";
        
        $overdueFeeds = FeedQueue::processDBQuery($query);
        
        foreach ( $overdueFeeds as $feed )
        {
			Logger::slideLogger()->LogInfo( "Feed {$feed->feedId()} overdue." );            
            $feedSlideCreator = new FeedSlideCreator($feed->feedId());
            $feedSlideCreator->processFeed();
        }
	}
}

?>