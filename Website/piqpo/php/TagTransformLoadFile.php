<?php

class TagTransformLoadFile extends TagTransformXsltBase
{
	function __construct($sourceTag, $xslt)
	{
		$this->_sourceTag = $sourceTag;
		parent::__construct($xslt);
	}
	
	function loadTidyString($existingTags)
	{
		$sourceFile = XMLTaggifier::evaluateDotSyntaxTag($this->_sourceTag, $existingTags);
        return HtmlTidier::tidyFile($sourceFile);
	}
	
	private $_sourceTag;	
}

?>