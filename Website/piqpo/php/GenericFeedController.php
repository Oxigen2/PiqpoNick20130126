<?php

// This needs refactoring, design has changed and this is a remanant.
// Need a class to wrap together the creation and modification of a feed in the db in an atomic way.
// Then have another class, independent of the db for separation of testing, that performs the query.
// This latter class is now FeedQuery.

class GenericFeedController extends FeedController
{	       
	// Create a new generic feed
	// Returns a ReturnValue with the feed id set if successful	
	static function create($type, $name, $pause, $pollFrequency, $maxSlides, $templateFile, $definitionFile, $parameters)
	{
		// Some things below a bit specific, but this is to become the only type of feed so that's fine.
		$returnValue = new ReturnValue();
		
		// Validate name
		if (strlen($name) == 0)
		{
			$returnValue->addError("Name must supplied.");
		}

		// Validate definition file exists.
		// TODO
		
		// Validate template file exists.
		// TODO
			
		if ($returnValue->success())
		{
			// TODO put the below in a transaction
			
			// Insert the feed
			
			$feedId = Feed::create($definitionFile, $name, $pause, $type, $pollFrequency, $templateFile, $maxSlides);
			$returnValue->setId($feedId);
			
			// Insert the parameters
			// TODO
			// Load up the definition file and get the list of required parameters, 
			// check against the supplied parameters.
			foreach ( $parameters as $paramName => $paramValue )
			{
				FeedParameter::create($feedId, $paramName, $paramValue);
			}

			// Add to the feed queue
			if ($returnValue->success())
			{
				$feedQueueManager = new FeedQueueManager();
				$feedQueueManager->addNewFeed($feedId);				
			}
		}
		return $returnValue;
	}

	// This is a bit rubbish, will simplify once there's only a single feed type
	function __construct($feed)
	{
		parent::__construct($feed);
		
		$this->_parameters = array();
	
		$query = array( "feed_id", $feed->feedId() );
		$dbParams = FeedParameter::loadFromDB( $query );
		foreach( $dbParams as $param )
		{
			$this->_parameters[ $param->parameterName() ] = $param->parameterValue();
		}		
	}

	function getSlideInfo()
	{
		$params = array();
		
		foreach ( $this->_parameters as $paramName => $parameter )
		{
			$params[ $parameter->parameterName() ] = $parameter->parameterValue();
		}
	
		$feedQuery = new FeedQuery($this->feed()->feedId(), $this->feed()->feedDefinitionFile(), $this->feed()->maxSlides() ,$params);
		
		return $feedQuery->peformQuery();
	}

    function modify($name, $pause, $pollFrequency, $maxSlides, $templateFile, $definitionFile, $parameters)
	{
        // TODO - validation
        $returnValue = new ReturnValue;
        
		$this->feed()->update($definitionFile, $name, $pause, $this->feed()->feedType(), $pollFrequency, $templateFile, $maxSlides); 

		// Update the parameters without just removing a re-adding which would be the easy way out but would change existing ids.
		// Bit of a faff, there's probably a more compact way
		$newParams = array();
		foreach ( $parameters as $paramName => $paramValue )
		{
			if ( !array_key_exists( $paramName, $this->_parameters ) )
			{
				// New item
				$newParams[ $paramName ] = FeedParameter::create($this->feed()->feedId, $paramName, $paramValue);
			}
			else
			{
				if ( $this->_parameters[ $paramName ] == $paramValue )
				{
					// No change
					$newParams[ $paramName ] = $this->_parameters[ $paramName ];
				}
				else
				{
					// Value has been modified
					$existingEntry = $this->_parameters[ $paramName ];
					$existingEntry->update( $existingEntry->feedId(), $existingEntry->parameterName(), $paramValue );
					$newParams[ $paramName ] = $existingEntry;
				}
			}
		}		
		foreach ( $this->_parameters as $currentKey => $currentFeedParameter )
		{
			if ( !array_key_exists( $currentKey, $newParams ) )
			{
				$currentFeedParameter->deleteFromDB();
			}
		}		
        
        return $returnValue;
	}
		
	private $_parameters;	// array( parameterName -> FeedParameter )
}

?>