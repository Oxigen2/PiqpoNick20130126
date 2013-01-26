<?php
class ExistingStream extends StreamType
{
	function __construct()
	{
		parent::__construct("Add new stream","addExistingStream");
	}
	
	function populateForm($formGenerator, $userId)
	{
		$queryString = "SELECT S.* FROM stream S WHERE S.stream_id NOT IN (SELECT U.stream_id FROM user_stream U WHERE user_id = {$userId})";
		
		$streams = Stream::processDBQuery($queryString);
		
		$values = array();
		foreach( $streams as $id => $stream )
		{
			$values[$stream->streamId()] = $stream->name();
		}
		if ( count($values) > 0 )
		{
			$formGenerator->addDropdown("Stream", $values, "name", "", 1);
		}
	}
	
	function getStreamFromForm()
	{
		$returnValue = new ReturnValue();
		$returnValue->setId($_POST["name"]);
		return $returnValue;
	}	
}
?>