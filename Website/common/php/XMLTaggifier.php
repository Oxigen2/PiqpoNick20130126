<?php

class XMLTaggifier
{
	private $_shortcuts;
	private $_tags;

	function __construct()
	{
		$this->_tags = array();
		$this->_shortcuts = array();
	}

	function tags()
	{
		return $this->_tags;
	}
	
	function addShortcut($longcut, $shortcut)
	{
		$this->_shortcuts[$longcut] = $shortcut;
	}
		
	function processItem($node, $xpath)
	{
		$this->processDocument($node, $xpath);
	}
	
	function processDom($dom)
	{
		$this->processDocument($dom->documentElement, new DOMXPath($dom));
	}
	
	private function processDocument($node, $xpath)
	{
		$this->processNode($node, $xpath, $this->_tags);
	}
	
	static function createTable($tags)
	{
		$output = "<table border=1>\n";
		foreach($tags as $name => $item)
		{
			self::printItem($item, $name, $output);
		}
		$output .= "</table>\n";
		
		return $output;
	}

	function processShortcuts()
	{
		foreach( $this->_shortcuts as $longcut => $shortcut)
		{
			$value = $this->getTag($longcut);
			
			if (isset($value))
			{
				$this->_tags[$shortcut] = $value;
			}
		}
	}

	function getTag($tag)
	{
		return self::evaluateDotSyntaxTag( $tag, $this->_tags );
	}
	
	// returns null if not found
	static function evaluateDotSyntaxTag($tag, $items)
	{
		$value = null;
		$parent = $items;
		foreach(explode('.', $tag) as $bit)
		{
			if (is_array($parent[$bit]))
			{                
				$parent = $parent[$bit];
                $value = $parent; 
			}
			elseif (isset($parent[$bit]))
			{
				$value = $parent[$bit];
			}
			else
			{
                $value = null;
				break;
			}
		}
		
		return $value;
	}
	
	private static function printItem($item, $name, &$output)
	{
		if (is_array($item))
		{
			foreach($item as $key => $value)
			{
				self::printItem($value, $name.".".$key, $output);
			}
		}
		else
		{
			$itemText = "";
			if (isset($item))
			{
				$itemText = $item;
				if( preg_match('#(?<=<)\w+(?=[^<]*?>)#', $item) )
				{
					$itemText = htmlentities($item);
				}
			}
			else
			{
				$itemText = "[null]";
			}
			$output .= "<tr><td>"."$".$name."</td><td>".$itemText."</td></tr>\n";
		}
	}	
	
	private function processNode($node, $xpath, &$parent)
	{
		$childNodes = $xpath->query('*', $node);

		if (isset($childNodes) && ($childNodes->length > 0))
		{
			// Child nodes
			$newNode = array();
			foreach($childNodes as $child)
			{
				$this->processNode($child, $xpath, $newNode);
			}
			$this->arrayInsert($node, $newNode, $parent);
		}
		else
		{
			// The node value itself
			$this->arrayInsert($node, $node->nodeValue, $parent);
		}
	}
	
	private function addAttributes($node, $index, &$parent)
	{
		if ($node->hasAttributes())
		{
			$attributes = $node->attributes;
			
			if (isset($attributes))
			{
				$attArray = array();
				foreach($attributes as $attribute)
				{
					$attArray[$attribute->name] = $attribute->value;
				}
				$attributeParentName = $this->formAttributeName($this->formNameRoot($node));
				
				// $index indicates if parent is indexed or not.
				if ($index == 0)
				{
					$parent[$attributeParentName] = $attArray;
				}
				else
				{
					$parent[$attributeParentName][$index] = $attArray;
				}
			}
		}
	}
	
	private function arrayInsert($node, $value, &$parent)
	{
		// First form the name root
		$index = 0;
		$nameRoot =	$this->formNameRoot($node);

		// See if the parent already has an element by this name
		if (isset($parent[$nameRoot]))
		{
			// Is it a single value?
			if(!is_array($parent[$nameRoot]))
			{
				// Need to replace the exising item as the zeroth item of the array
				$parent[$nameRoot] = array($parent[$nameRoot]);
				
				// Do the same with any attributes
				if (isset($parent[$this->formAttributeName($nameRoot)]))
				{
					$attributeName = $this->formAttributeName($nameRoot);
					$parent[$attributeName] = array($parent[$attributeName]);
				}
			}
			
			// Now is array so just add to the end of it.
			$parent[$nameRoot][] = $value;
			
			$index = count($parent[$nameRoot]) - 1; 
		}
		else
		{
			$parent[$nameRoot] = $value;
		}
		
		// Add attributes if necessary
		$this->addAttributes($node, $index, $parent);
	}

	private function formNameRoot($node)
	{
		return 	((isset($node->prefix) && (!empty($node->prefix))) ? $node->prefix.":" : "") . 
				($node->localName);
	}
	
	function formAttributeName($name)
	{
		return $name."_A";
	}	
	
	private function stripEmptyTags()
	{
		foreach($this->_tags as $key => $value)
		{
			$this->stripEmptyValues($this->_tags, $key);
		}
	}
	
	private function stripEmptyValues(&$parent, $key)
	{
		if (is_array($parent[$key]))
		{
			foreach($parent[$key] as $k => $v)
			{
				$this->stripEmptyValues($parent[$key], $k);
			}
		}
		else if (empty($parent[$key]))
		{
			unset($parent[$key]);
		}
	}
}

?>