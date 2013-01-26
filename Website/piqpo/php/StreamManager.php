<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class StreamManager
{
	function StreamManager()
	{
	}
	
	function getStream($streamId)
	{
		return Stream::loadSingleFromDB($streamId);
	}
	
	// Returns a ReturnValue with id set to the id of the new stream if successful
	function addStream($name)
	{
		$returnValue = new ReturnValue();
	
		$query = array( "name" => "'".$name."'" );
		$streams = Stream::loadFromDB( $query );
		
		if (count($streams) > 0)
		{
			$returnValue->addError("A stream named {$name} already exists.");
		}
		else
		{
			$id = Stream::create($name);
			
			if (isset($id))
			{
				$returnValue->setId($id);
                Logger::adminLogger()->LogInfo( "Stream {$id} has been created." );            
			}
			else
			{
				$returnValue->addError("Stream not added");
			}
		}
		
		return $returnValue;
	}
	
	// Returns an array of the slides from the given stream
	function streamSlideIds($streamId)
	{
		
		$queryString = "SELECT S.* 
                        FROM slide S, feed_stream FS 
						WHERE FS.stream_id = {$streamId}
						AND FS.feed_id = S.feed_id 
                        AND S.active = 1 ";                
                
		$streamSlides = Slide::processDBQuery($queryString);
		
		return $streamSlides;
	}
	
	// Returns an array of streams for the given profile
	function profileStreams($profileId)
	{
		$queryString = "SELECT S.* FROM stream S, profile_stream PS WHERE PS.profile_id = {$profileId} AND S.stream_id = PS.stream_id ";
		return Stream::processDBQuery($queryString);
	}
    
	function allStreams()
	{
		return Stream::loadAllFromDB();
	}
	

	// Returns an array of feeds that are assigned to the given stream.
	function getFeeds($streamId)
	{
		$query = "SELECT F.* FROM feed F, feed_stream FS WHERE FS.stream_id = {$streamId} AND F.feed_id = FS.feed_id";
		
		return Feed::processDBQuery( $query );
	}
	
	// Assigns the feed to the stream.
	// Returns a ReturnValue with the new feed_stream_id if successful
	function setFeed($streamId, $feedId)
	{
		$returnValue = new ReturnValue();
	
		$id = FeedStream::create($feedId,$streamId);
		
		if (isset($id))
		{
			$returnValue->setId($id);
            Logger::adminLogger()->LogInfo( "Feed {$feedId} has been assigned to stream {$streamId}." );            
		}
		else
		{
			$returnValue->addError("Failed to assign feed");
		}
		
		return $returnValue;
	}
	
	// Removes the given feed from the stream.
	function removeFeed($streamId, $feedId)
	{
		$db = new PiqpoDBManager();

		$query = "DELETE FROM feed_stream WHERE stream_id = {$streamId} AND feed_id = {$feedId}";		
		$db->query($query);		
        
        Logger::adminLogger()->LogInfo( "Feed {$feedId} has been removed from stream {$streamId}." );            
	}
    
    function streamInfoArray( $stream )
    {
        return array( "id" => $stream->streamId(), "name" => $stream->name() );             

    }
    
   	// Returns a ReturnValue
	// The id is set to the profile stream value if successful.
	function unassignProfileStream($profileId, $streamId)
	{
		$returnValue = new ReturnValue();
		$queryArray = array( "profile_id" => $profileId, "stream_id" => $streamId );
		
		$check = ProfileStream::loadFromDB($queryArray);
		if (count($check) == 0)
		{
			$returnValue->addError("Profile not subscribed to this stream.");
		}
		else
		{
			$profileStreamId = $check[0]->profileStreamId();
			ProfileStream::deleteFromDB($profileStreamId);
			$returnValue->setId($profileStreamId);		
            
            Logger::userLogger()->LogInfo( "Profile {$profileId} has been unassigned from stream {$streamId}." );            
		}
		return $returnValue;
	}
		
	// Assigns the given stream to the given profile.
	// Returns a ReturnValue with profileStreamId set if successful
	function assignProfileStream($profileId, $streamId)
	{
		$returnValue = new ReturnValue();
	
		// Check assignment hasn't already been performed.
		$queryArray = array( "profile_id" => $profileId, "stream_id" => $streamId );
		$check = ProfileStream::loadFromDB($queryArray);
		if (count($check) > 0)
		{
			$returnValue->addError("Profile is already subscribed to this stream.");
		}
		
		// Perform the assignment.
		if ($returnValue->success())
		{
			$profileStreamId = ProfileStream::create($profileId, $streamId);
			$returnValue->setId($profileStreamId);
            
            Logger::userLogger()->LogInfo( "Profile {$profileId} has been assigned stream {$streamId}." );            
		}
		
		return $returnValue;
	}

    // Remove below when done.
    
    // Returns a ReturnValue
	// The id is set to the user stream value if successful.
	function unassignStream($userId, $streamId)
	{
		$returnValue = new ReturnValue();
		$queryArray = array( "user_id" => $userId, "stream_id" => $streamId );
		
		$check = UserStream::loadFromDB($queryArray);
		if (count($check) == 0)
		{
			$returnValue->addError("User not subscribed to this stream.");
		}
		else
		{
			$userStreamId = $check[0]->userStreamId();
			UserStream::deleteFromDB($userStreamId);
			$returnValue->setId($userStreamId);		
            
            Logger::userLogger()->LogInfo( "User {$userId} has been unassigned from stream {$streamId}." );            
		}
		return $returnValue;
	}
		
	// Assigns the given stream to the given user.
	// Returns a ReturnValue with the userStreamId set if successful
	function assignStream($userId, $streamId)
	{
		$returnValue = new ReturnValue();
	
		// Check assignment hasn't already been performed.
		$queryArray = array( "user_id" => $userId, "stream_id" => $streamId );
		$check = UserStream::loadFromDB($queryArray);
		if (count($check) > 0)
		{
			$returnValue->addError("User is already subscribed to this stream.");
		}
		
		// Perform the assignment.
		if ($returnValue->success())
		{
			$userStreamId = UserStream::create($userId, $streamId, 0);
			$returnValue->setId($userStreamId);
            
            Logger::userLogger()->LogInfo( "User {$userId} has been assigned stream {$streamId}." );            
		}
		
		return $returnValue;
	}
}
?>
