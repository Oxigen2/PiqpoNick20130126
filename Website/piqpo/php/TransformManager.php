<?php

class TransformManager
{
	function __construct()
	{
		$this->_fileRoot = $GLOBALS['LOCAL_ROOT']."/xslt/";
	}
	
	// Returns an array of transform file names, only the leaf name, not directory
	function getTransformFiles()
	{
		$files = array();
				
		foreach(new DirectoryIterator($this->_fileRoot) as $file)
		{
			if (!$file->isDot())
			{
				$files[] = $file->getFilename();
			}
		}
		
		return $files;
	}
	
	function formFullFilename($filename)
	{
		return $this->_fileRoot . $filename;
	}

	private $_fileRoot;
}

?>