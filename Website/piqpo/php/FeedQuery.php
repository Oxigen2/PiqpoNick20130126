<?php

// Does the work of what is currently termed the GenericFeed

class FeedQuery
{
	function __construct($feedId, $feedDefinitionFile, $maxSlides, $parameters, $existingGuids)
	{
		$this->_feedId = $feedId;
		$this->_feedDefinitionFile = $feedDefinitionFile;
		$this->_maxSlides = $maxSlides;
		$this->_parameters = $parameters;
        $this->_existingGuids = $existingGuids;
	}

	function performQuery( &$debugInfo )
	{
        $debugInfo = "Performing query with feed {$this->_feedId}, definition file {$this->_feedDefinitionFile}<br/>";
        
		// Load up definition file
		$fileString = file_get_contents( $this->_feedDefinitionFile );
		
		// Perform parameter substitution
		foreach( $this->_parameters as $name => $value )
		{
			$fileString = str_replace( '%'.$name.'%', $value, $fileString );
		}
	
		// Get the set of standard output tags, this can be incremented later by transforms
		$outputTagList = SlideInfo::outputTags();
		
		// Create DOM
		$definitionDOM = new DOMDocument;
		$definitionDOM->loadXML($fileString);
		$definitionXPath = new DOMXPath($definitionDOM);
		
		// Query for sources and build them
		$sourceObjects = array();
		$sourceNodes = $definitionXPath->query("/Feed/Source/*");
		foreach ($sourceNodes as $sourceNode)
		{
			$sourceObjects[] = $this->createSource($sourceNode);
		}		
		
		// Query for transforms and build them
		$preGuidCheckTransformObjects = array();
		$transformObjects = array();
		$transformNodes = $definitionXPath->query("/Feed/Transforms/*");
		foreach ($transformNodes as $transformNode)
		{
            $transform = $this->createTransform($transformNode);
            
            if ( $transform->runBeforeGuidCheck() )
            {
                $preGuidCheckTransformObjects[] = $transform;
            }
            else
            {
                $transformObjects[] = $transform;
            }
            
            // Overwrite with transform definition of what is mandatory
            $outputTagList = array_merge( $outputTagList, $transform->outputTags() );                    
		}		

		// This is array( id => array( tag => value ) )
		// Each entry in the top array corresponds to a slide
		$workingTagSet = array();
		
		// Query each source in turn.  They will return an array of SlideInfo objects.
		// In reality there is likely to only be a single source.
		// Example sources are RSS or HTML, the former creating SlideInfo objects for each RSS item, the later a single one for the page
		foreach ($sourceObjects as $sourceObject)
		{
			$newTagSet = $sourceObject->generateTagSet();
			$workingTagSet = array_merge($workingTagSet, $newTagSet);
		}
		
   		$debugInfo .= "Source returned " . count( $workingTagSet ) . " tag sets.<br/>";        
        
        $slideCount = 0;
		$slideInfoSet = array();
		foreach ($workingTagSet as $tags)
		{
            // Give 10 seconds to complete a slide.
            set_time_limit(10);

            if ($slideCount >= $this->_maxSlides)
            {
           		$debugInfo .= "Max slides limit {$this->_maxSlides} reached.<br/>";        
                break;
            }
            
            $debugInfo .= "<hr/>Processing new slide.  Memory is at " . memory_get_usage() . " Tags returned by source:<br/>";

            $debugInfo .= XMLTaggifier::createTable($tags);
            
            $debugInfo .= "<br/>Memory is at " . memory_get_usage() . "<p/>";
            
    		// Step through pre-guid check transforms
			foreach ($preGuidCheckTransformObjects as $transformObject)
			{                
				$newTags = $transformObject->doTransform($tags);
				$tags = array_merge( $tags, $newTags );
                
                $transformName = get_class( $transformObject );
                $debugInfo .= "<p/>Transform " . $transformName . " returned following tags.<br/>";
                $debugInfo .= XMLTaggifier::createTable($newTags);
                $debugInfo .= "<br/>Memory is at " . memory_get_usage() . "<p/>";
			}

            // Check if this slide already exists
            if ( !array_key_exists(SlideInfo::$publicTagName_guid, $tags) || empty($tags[ SlideInfo::$publicTagName_guid ] ) )
            {
				Logger::slideLogger()->LogWarning( "No guid generated on slide for feed {$this->_feedId}, skipping." );
				$debugInfo .= "<p/>No guid generated on slide for feed {$this->_feedId}, skipping.<br/>";
                continue;
            }
            if (array_key_exists($tags[SlideInfo::$publicTagName_guid], $this->_existingGuids))
            {
                ++$slideCount;
                $debugInfo .= "<p/>Skipping existing slide with guid {$tags[SlideInfo::$publicTagName_guid]}.<br/>";
                continue;
            }            
            
            // Perform remaining transforms
			foreach ($transformObjects as $transformObject)
			{
				$newTags = $transformObject->doTransform($tags);
				$tags = array_merge( $tags, $newTags );
                
                $transformName = get_class( $transformObject );
                $debugInfo .= "<p/>Transform " . $transformName . " returned following tags.<br/>";
                $debugInfo .= XMLTaggifier::createTable($newTags);
                $debugInfo .= "<br/>Memory is at " . memory_get_usage() . "<p/>";
			}
            
			// Create the slide output
            $slideValid = true;
			$slideTags = array();
			foreach( $outputTagList as $outputTagName => $mandatory )
			{
				if ( array_key_exists( $outputTagName, $tags ) && isset( $tags[ $outputTagName ] ) && ( !empty($tags[ $outputTagName ]) ) )
				{
					$slideTags[ $outputTagName ] = $tags[ $outputTagName ];
				}
				else if ( $mandatory )
				{
					$debugInfo .= "<p/>Mandatory field {$outputTagName} not found on slide, skipping.<br/>";
                    $slideValid = false;
                    break;
				}
			}
			
            if ( $slideValid )
            {
                $slideInfo = new SlideInfo($this->_feedId, $slideTags);
                $slideInfoSet[] = $slideInfo;
                ++$slideCount;
				$debugInfo .= "<p/>Slide " . count( $slideInfoSet ) . " added with guid {$slideInfo->guid()} and following tags.<br/>";
                $debugInfo .= XMLTaggifier::createTable($slideTags);
            }
		}
		return $slideInfoSet;		
	}
	
	private function createSource($sourceNode)
	{
		$source = null;        
		switch ($sourceNode->localName)
		{
			case "BlankSource":
			{
				$fileSlides = $this->getAttribute($sourceNode, "count", 1);				
				$slides = ($this->_maxSlides == 0) ? $fileSlides : min($fileSlides, $this->_maxSlides);				
				$source = new TagSourceBlank($slides);
				break;
			}
			case "RSSSource":
			{
				$url = $this->getAttribute($sourceNode, "url");
				$source = new TagSourceRSS($url, 0);
				break;
			}
			case "HtmlSource":
			{
				$url = $this->getAttribute($sourceNode, "url");
				$xslt = $this->getChildNodeXml($sourceNode, "Xslt");
				$source = new TagSourceHtml($url, $xslt);
				break;
			}
			default:
			{
				throw new Exception("Unexpected source {$sourceNode->localName} in {$this->_feedDefinitionFile}");
			}
		}
		return $source;
	}
	
	private function createTransform($transformNode)
	{
		$transform = null;
        $name = $transformNode->localName;
		switch ( $name )
		{
			case "SetValueTransform":
			{
				$tag = $this->getAttribute($transformNode, "tag");
				$value = $this->getAttribute($transformNode, "value");
				$mandatory = $this->getBoolAttribute($transformNode, "mandatory", false);
				$transform = new TagTransformSetValue($tag, $value, $mandatory);
				break;
			}
			case "CopyValueTransform":
			{
				$target = $this->getAttribute($transformNode, "target");
				$source = $this->getAttribute($transformNode, "source");
            	$output = $this->getBoolAttribute($transformNode, "output", true);
            	$mandatory = $this->getBoolAttribute($transformNode, "mandatory", false);
				$transform = new TagTransformCopyValue($target, $source, $output, $mandatory);
				break;
			}
			case "XsltTransform":
			{
				$source = $this->getAttribute($transformNode, "source");
				$xslt = $this->getChildNodeXml($transformNode, "Xslt");
				$transform = new TagTransformXslt($source, $xslt);
				break;
			}
			case "LoadFileTransform":
			{
				$source = $this->getAttribute($transformNode, "source");
				$xslt = $this->getChildNodeXml($transformNode, "Xslt");
				$transform = new TagTransformLoadFile($source, $xslt);
				break;
			}
			case "LoadLinkTransform":
			{
				$source = SlideInfo::$publicTagName_link;
				$xslt = $this->getChildNodeXml($transformNode, "Xslt");
				$transform = new TagTransformLoadFile($source, $xslt);
				break;
			}
			case "SetDateTransform":
			{
				$tag = SlideInfo::$publicTagName_date;
				$value = time();
				$transform = new TagTransformSetValue($tag, $value, true);
				break;
			}
            case "CustomTransform":
            {
                $target = $this->getAttribute($transformNode, "target");
                $function = $this->getAttribute($transformNode, "function");
                $transform = new TagTransformCustom($target, $function);
                break;
            }
			case "ImageSizeTransform":
			{
				$source = $this->getAttribute($transformNode, "source");
				$transform = new TagTransformImageSize($source);
				break;
			}
			case "ReplaceTransform":
			{
                $target = $this->getAttribute($transformNode, "target");
				$source = $this->getAttribute($transformNode, "source");
				$pattern = $this->getAttribute($transformNode, "pattern");
				$replacement = $this->getAttribute($transformNode, "replacement", "");
				$transform = new TagTransformReplace($target, $source, $pattern, $replacement);
				break;
			}
			default:
			{
				throw new Exception("Unexpected source {$name} in feed definition {$this->_feedDefinitionFile}");
			}
		}
		return $transform;	
	}
   
	// Returns the inner xml of the first child node with the given name, or null
	private function getChildNodeXml($node, $childName)
	{
		$output = null;
		$child = $this->getChildNode($node, $childName);
		if (isset($child))
		{
			$output = $this->getInnerXml($child);
		}
		return $output;
	}
	
	// returns first child called childName, or null if there is none.
	private function getChildNode($node, $childName)
	{
		$val = null;
		
		$children = $node->childNodes;
		if (isset($children))
		{
			foreach ($children as $child) 
			{
				if ( $child->localName == $childName )
				{
					$val = $child;
					break;
				}
			}
		}
		
		return $val;
	}
	
	// returns content of node, or empty if nothing
	private function getInnerXml($node)
	{
		$xml = "";
		
		$children = $node->childNodes;
		if (isset($children))
		{
			foreach ($children as $child) 
			{
				$xml .= $child->ownerDocument->saveXML( $child );
			}
		}
		return $xml;
	}
	
    private function getBoolAttribute($node, $attribute, $default)
    {
        $attr = $this->getAttribute($node, $attribute, $default ? "true" : "false" );
        
        return $attr == "true";
    }
    
	private function getAttribute($node, $attribute, $default = null)
	{
		$val = $default;
		if ( $node->hasAttributes() )
		{
			$attr = $node->attributes->getNamedItem($attribute);
			if (isset($attr))
			{
				$val = $attr->value;
			}
		}
		
		if (!isset($val))
		{
			throw new Exception("Missing attribute {$attribute} on node {$node->localName} in feed definition {$this->_feedDefinitionFile}");
		}
		
		return $val;
	}
	
	private $_feedId;
	private $_feedDefinitionFile;
    private $_existingGuids;
	private $_maxSlides;
  	private $_parameters;
}

?>