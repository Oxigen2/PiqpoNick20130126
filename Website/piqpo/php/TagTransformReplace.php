<?php

class TagTransformReplace extends TagTransform
{
	function __construct($target, $source, $matchPattern, $replacement)
	{
		$this->_target = $target;
		$this->_source = $source;
        $this->_matchPattern = $matchPattern;
        $this->_replacement = $replacement;
	}
	
	function doTransform($existingTags)
	{
		$subject = XMLTaggifier::evaluateDotSyntaxTag($this->_source, $existingTags);
        $result = preg_replace( $this->_matchPattern, $this->_replacement, $subject ); 
		return array( $this->_target => $result );
	}
    
    function outputTags() 
    {
        return array( $this->_target => false );
    }
    
    function runBeforeGuidCheck()
    {
        return false;
    }

	private $_target;
	private $_source;
    private $_matchPattern;
    private $_replacement;
}

?>