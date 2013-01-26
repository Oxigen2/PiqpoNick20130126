<?php

class FeedDefinitionManager
{
	function __construct()
	{
		$this->_fileRoot = $GLOBALS['LOCAL_ROOT']."/definition_files/";
	}
	
    static $feed_type_rss = "rss";
    static $feed_type_plain = "plain";    
    
	// Returns an array of transform file names, only the leaf name, not directory
	function getDefinitionFiles( $feedType )
	{
        $directory = $this->_fileRoot . "/" . $feedType;
		$files = array();
				
		foreach(new DirectoryIterator( $directory ) as $file)
		{
			if (!$file->isDot())
			{
				$files[] = $file->getFilename();
			}
		}
		
		return $files;
	}
	
	function formFullFilename( $feedType, $filename )
	{
		return $this->_fileRoot . $feedType . "/" . $filename;
	}

	private $_fileRoot;
}

?>