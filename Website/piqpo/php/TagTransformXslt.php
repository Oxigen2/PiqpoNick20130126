<?php

class TagTransformXslt extends TagTransformXsltBase
{
	function __construct($sourceTag, $xslt)
	{
		$this->_sourceTag = $sourceTag;
		parent::__construct($xslt);
	}
	
	function loadTidyString($existingTags)
	{
		$sourceXml = XMLTaggifier::evaluateDotSyntaxTag($this->_sourceTag, $existingTags);

		return HtmlTidier::tidyString($sourceXml, true);
	}
	
	private $_sourceTag;
}

?>