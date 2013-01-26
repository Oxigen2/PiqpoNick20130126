<?php

abstract class StreamQuery
{
	function __construct($streamId)
	{
		$this->_streamId = $streamId;
	}

	protected function streamId()
	{
		return $this->_streamId;
	}
	
	abstract function getSlideInfo();
	
	private $_streamId;
}

?>