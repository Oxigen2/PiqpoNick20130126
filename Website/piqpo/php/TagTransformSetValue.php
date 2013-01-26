<?php

// Just sets the value of a tag
class TagTransformSetValue extends TagTransform
{
	function __construct($tagName, $value, $mandatory)
	{
		$this->_tagName = $tagName;
		$this->_value = $value;
        $this->_mandatory = $mandatory;
	}
	
	function doTransform($existingTags)
	{
		$newTags = array( $this->_tagName => $this->_value );
		return $newTags;
	}
	
    function mandatoryTags() 
    {
        // Assuming for now that should be added to the output
        return array( $this->_target => $this->_mandatory );
    }

    function runBeforeGuidCheck()
    {
        return ($this->_target == SlideInfo::$publicTagName_guid);
    }
    
	private $_tagName;
	private $_value;
    private $_mandatory;
}

?>