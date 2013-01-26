<?php
abstract class StreamType
{
	protected function __construct($addSectionText, $formSubmitValue)
	{
		$this->_addSectionText = $addSectionText;
		$this->_formSubmitValue = $formSubmitValue;	
	}
	
	function titleText()
	{
		return $this->_addSectionText;
	}
	
	function formSubmitValue()
	{
		return $this->_formSubmitValue;
	}
	
	function generateForm($link, $userId)
	{
		$formGenerator = new FormGenerator($link, "POST", "", "<p>", "form", "form_item", "form_prompt", "form_input");
		
		$this->populateForm($formGenerator, $userId);
		
		$formGenerator->addSubmit("Add Stream", $this->_formSubmitValue);
		
		return $formGenerator->getOutput();
	}
	
	// Processes the form to add a stream.
	// Returns a ReturnValue with the id set to the id of the stream. 
	function processForm($userId)
	{
		$returnValue = $this->getStreamFromForm();
		
		if ($returnValue->success())
		{
			// Assign the stream to the user
			$streamId = $returnValue->id();
			$returnValue = StreamManager::assignStream($userId, $streamId);

			if ($returnValue->success())
			{
				// Set id to that of the stream, more useful than that of the user stream.
				$returnValue->setId($streamId);
			}
		}
		
		return $returnValue;
	}
	
	// Implemented by derived class.
	// Must use formGenerator to populate form.
	protected abstract function populateForm($formGenerator, $userId);
	
	// Implemented by derived class.
	// Uses form submitted values to find or create a stream.
	// Returns a ReturnValue with the id of the stream set if successful.
	protected abstract function getStreamFromForm();
	
	private $_addSectionText;
	private $_formSubmitValue;
}
?>