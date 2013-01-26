<?php

abstract class TagTransform
{
	function __construct()
	{
	}
	
	// Should return a new set of array( name => value).  Does not update the tags.
	abstract function doTransform($existingTags);
    
    // Returns an array of tags that should be added to the output and whether they are mandatory in the form
    // tagname => mandatory
    function outputTags()
    {
        return array();
    }
    
    // Returns true if transform should be run before the guid check.
    // So should only return true if it sets or might set the guid value
    function runBeforeGuidCheck()
    {
        return false;
    }
}

?>