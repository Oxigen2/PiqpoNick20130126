<?php
 
class FeedController
{
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

            Logger::adminLogger()->LogInfo( "Feed {$feedId} has been created." );            
		}
		return $returnValue;
	}
       
	function __construct($feedId)
	{
		$this->_feed = Feed::loadSingleFromDB($feedId);
        
   		$this->_parameters = array();
	
		$query = array( "feed_id" => $feedId );
		$dbParams = FeedParameter::loadFromDB( $query );
		foreach( $dbParams as $param )
		{
			$this->_parameters[ $param->parameterName() ] = $param;
		}		
	}

    function modify($name, $pause, $pollFrequency, $maxSlides, $templateFile, $definitionFile, $parameters)
	{
        // TODO - validation
        $returnValue = new ReturnValue;
        
		$this->_feed->update($definitionFile, $name, $pause, $this->_feed->feedType(), $pollFrequency, $templateFile, $maxSlides); 

		// Update the parameters without just removing a re-adding which would be the easy way out but would change existing ids.
		// Bit of a faff, there's probably a more compact way
		$newParams = array();
		foreach ( $parameters as $paramName => $paramValue )
		{
			if ( !array_key_exists( $paramName, $this->_parameters ) )
			{
				// New item
				$newParams[ $paramName ] = FeedParameter::create($this->_feed->feedId, $paramName, $paramValue);
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
        
        Logger::adminLogger()->LogInfo( "Feed {$this->_feed->feedId} has been modified." );            
        
        return $returnValue;
	}
		
	function getSlideInfo()
	{	
        // Get the latest slide guids        
        // TODO - put this somewhere else
        $query = "SELECT * FROM slide WHERE feed_id = " . $this->feedId() . " ORDER BY publication_date DESC LIMIT 0," . $this->maxSlides();
        
		$slideIds = array();
		$slides = Slide::processDBQuery($query);

		foreach ($slides as $slide)
		{
			$slideIds[$slide->guid()] = $slide->guid();
		}

		$feedDefinitionManager = new FeedDefinitionManager;
		$feedFilename = $feedDefinitionManager->formFullFilename( $this->feedType(), $this->feedDefinitionFile() );
        
		$feedQuery = new FeedQuery($this->_feed->feedId(), $feedFilename, $this->_feed->maxSlides(), $this->parameters(), $slideIds);
		
        // TODO - Write debug info to file.
        $debugOutput = "";
		return $feedQuery->performQuery( $debugOutput );
	}    
    
	function feedId()
	{
		return $this->_feed->feedId();
	}
	
	function name()
	{
		return $this->_feed->name();
	}

	function pause()
	{
		return $this->_feed->pause();
	}

	function feedType()
	{
		return $this->_feed->feedType();
	}

	function feedTypeId()
	{
		return $this->_feed->feedTypeId();
	}

	function pollFrequency()
	{
		return $this->_feed->pollFrequency();
	}

	function templateFile()
	{
		return $this->_feed->templateFile();
	}

	function feedDefinitionFile()
	{
		return $this->_feed->feedDefinitionFile();
	}

	function defaultTargetLink()
	{
		return $this->_feed->defaultTargetLink();
	}

	function maxSlides()
	{
		return $this->_feed->maxSlides();
	}
    
    function parameters()
    {
		$params = array();
		
		foreach ( $this->_parameters as $parameter )
		{
			$params[ $parameter->parameterName() ] = $parameter->parameterValue();
		}
        
        return $params;
    }
	
	public static function defaultSlidePause()
	{
		return self::$defaultSlidePause;
	}
	public static function defaultPollFrequency()
	{
		return self::$defaultPollFrequency;
	}
	public static function defaultMaxSlides()
	{
		return self::$defaultMaxSlides;
	}
		
	private $_feed;
	private $_parameters;	// array( parameterName -> FeedParameter )  
	private static $defaultMaxSlides = 20;
	private static $defaultSlidePause = 10;
	private static $defaultPollFrequency = 3600;
}

?>