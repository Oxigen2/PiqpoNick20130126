<?php

class RSSStream extends StreamType
{
	function __construct()
	{
		parent::__construct("Add RSS feed as a stream","addRSSStream");
	}
	
	function populateForm($formGenerator, $userId)
	{
		$formGenerator->addText ("Name", "name", "", 400);
		$formGenerator->addText ("RSS address", "link", "", 400);
		$formGenerator->addText ("Max slides", "max", "", 400);
	}
	
	function getStreamFromForm()
	{
		$returnValue = new ReturnValue();
		$url  = $_POST["link"];
		$name = $_POST["name"];
		$max  = $_POST["max"];
		
		if (!((strlen($url) > 7) && (substr($url, 0, 7) == 'http://')))
		{
			$returnValue->addError("The URL must be supplied starting http://");
		}
		if (strlen($name) == 0)
		{
			$returnValue->addError("The stream name must be supplied.");
		}
		if (!ctype_digit($max))
		{
			$returnValue->addError("The maximum slides must be a positive number or left blank.");			
		}
		
		if ($returnValue->success())
		{
			// Look to see if this exists as a stream already.
			$queryArray = array("source_url" => "'".$url."'");
			$check = StreamTypeRss::loadFromDB($queryArray);
			
			if (count($check) > 0)
			{
				$returnValue->addError("This stream already exists.");
			}
			else
			{
				// See if name is already being used.
				$queryArray = array("name" => "'".$name."'");
				$check = Stream::loadFromDB($queryArray);

				if (count($check) > 0)
				{
					$returnValue->addError("Stream name {$check[0]->name()} already exists.");
				}
				else
				{				
					// Load the page to check it exists.
					$fetch = file_get_contents($url);
					if (!$fetch)
					{
						$returnValue->addError("Failed to load document from {$url}.");
					}
					else
					{
						// Create stream
						$returnValue = StreamManager::addStream($name, $url, $url);					
					}
				}
			}
		}	
		return $returnValue;
	}	
}

?>