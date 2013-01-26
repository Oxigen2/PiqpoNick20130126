<?php

class TagTransformCopyValue extends TagTransform
{
	function __construct($target, $source, $output, $mandatory)
	{
		$this->_target = $target;
		$this->_source = $source;
        $this->_output = $output;
        $this->_mandatory = $mandatory;
	}
	
	function doTransform($existingTags)
	{
		$value = XMLTaggifier::evaluateDotSyntaxTag($this->_source, $existingTags);
		return array( $this->_target => $value );
	}
    
    function outputTags() 
    {
        return ($this->_output ? array( $this->_target => $this->_mandatory ) : array());
    }
    
    function runBeforeGuidCheck()
    {
        return ($this->_target == SlideInfo::$publicTagName_guid);
    }

	private $_target;
	private $_source;
    private $_output;
    private $_mandatory;
}

?>