<?php

class TagTransformImageSize extends TagTransform
{
	function __construct($source)
	{
		$this->_source = $source;
        $this->_widthTag = $source . "_width";
        $this->_heightTag = $source . "_height";
	}
	
	function doTransform($existingTags)
	{
        $returnVal = array();
		$imageLink = XMLTaggifier::evaluateDotSyntaxTag($this->_source, $existingTags);
        
        if ( isset( $imageLink ) && !empty($imageLink) )
        {        
            $result = getimagesize($imageLink); 
        
            if ( $result && ($result[0] > 0) && ($result[1] > 0) )
            {
                $returnVal = array( $this->_widthTag => $result[0], $this->_heightTag => $result[1] );
            }
        }
        
		return $returnVal;
	}
    
    function outputTags() 
    {
        return array( $this->_widthTag => false, $this->_heightTag => false );
    }
    
    function runBeforeGuidCheck()
    {
        return false;
    }

	private $_source;
    private $_widthTag;
    private $_heightTag;
}

?>