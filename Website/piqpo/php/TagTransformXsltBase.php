<?php

abstract class TagTransformXsltBase extends TagTransform
{
	function __construct($xslt)
	{
		$this->_xslt = $xslt;
        
        $this->_xsltDom = new DOMDocument;
        $this->_xsltDom->loadXML($this->_xslt);

        $this->_xsltProcessor = new XSLTProcessor;
        $this->_xsltProcessor->importStylesheet($this->_xsltDom);        
        
		$this->_htmlDom = new DOMDocument;	
		$this->_resultsDom = new DOMDocument;
	}
	
	abstract function loadTidyString($existingTags);
	
	function doTransform($existingTags)
	{
		$this->_tidyString = $this->loadTidyString($existingTags);

		$this->_htmlDom->loadXML($this->_tidyString);
		
		// Load the transform if there is one
		if (isset($this->_xslt) && !empty($this->_xslt))
		{
			$this->_results = $this->_xsltProcessor->transformToXML($this->_htmlDom);
			
			$this->_resultsDom->loadXML($this->_results);
        }

		$taggifier = new XMLTaggifier;
		$taggifier->processDom($this->_resultsDom);

        $tags = $taggifier->tags();
	            
        unset( $this->_tidyString );
        unset( $this->_results );

		return $tags;
	}
	
	private $_xslt;
    
    private $_xsltDom;
    private $_xsltProcessor;
    private $_tidyString;
    private $_htmlDom;
    private $_resultsDom;
    private $_results;
}

?>