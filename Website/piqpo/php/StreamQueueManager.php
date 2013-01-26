<?php

class StreamQueueManager
{
	function __construct()
	{
	}
	
	// Insert a new stream into the queue.
	function addNewStream($streamId)
	{
		StreamQueue::create($streamId, date('Y-m-d H:i:s'));
	}
	
	// Call to indicate that a stream has been updated.
	function streamUpdated($stream)
	{
		$nextPoll = date('Y-m-d H:i:s', strtotime("+ {$stream->pollFrequency()} seconds"));
	
		$query = "UPDATE STREAM_QUEUE SET next_poll = '{$nextPoll}' WHERE stream_id = {$stream->streamId()}";
		
		$db = new DBManager("piqpo");
		$db->query($query);
	}
	
	function getAllStreams()
	{
		return StreamQueue::loadFromDB( array() );
	}
	
	function getOverdueStreams()
	{
		
	}
	
	function getNonOverdueStreams()
	{
	}
}

?>