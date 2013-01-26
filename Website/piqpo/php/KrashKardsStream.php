<?php

class KrashKardsStream extends StreamType
{
	function __construct()
	{
		parent::__construct("Add KrashKards stream","addKrashKardsStream");
	}
	
	function populateForm($formGenerator, $userId)
	{
		$formGenerator->addText ("KrashKards email address", "email", "", 400);
	}
	
	function getStreamFromForm()
	{
	}	
}

?>