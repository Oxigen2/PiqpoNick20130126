<?php

require_once($GLOBALS['COMMON_ROOT'] . '/3rd_party/smarty/libs/Smarty.class.php');

class TemplateManager
{
	function __construct()
	{
		$templateRoot = $GLOBALS['LOCAL_ROOT']."/templates/";
		$this->adminRoot = $templateRoot . "admin/";
		$this->testRoot = $templateRoot . "unit/";
		$this->slideRoot = $templateRoot . "slides/";
	}
	
	// Returns an array of slide template filenames
	function getSlideTemplates()
	{
		$files = array();
				
		foreach(new DirectoryIterator($this->slideRoot) as $file)
		{
            $ff = $file->getFilename();
			if ($ff[0] != '.')
			{
				$files[] = $ff;
			}
		}
		
		return $files;
	}
	
	function publishTestPage($tags, $templateFile)
	{
		$smarty = $this->populateSmarty($tags, $this->testRoot);
				
		$smarty->display($this->testRoot . $templateFile);
	}

	function publishAdminPage($tags, $templateFile)
	{
		$smarty = $this->populateSmarty($tags, $this->adminRoot);
				
		$smarty->display($this->adminRoot . $templateFile);
	}

	function copyArrayRemoveNulls($inputTags, &$outputTags)
	{
		if (is_array($inputTags))
		{
			$outputTags = array();
			foreach ($inputTags as $key => $value)
			{
				$outputTags[$key] = null;
				$this->copyArrayRemoveNulls($value, $outputTags[$key]);
				if ( $outputTags[$key] == null )
				{
					unset($outputTags[$key]);
				}
			}
		}
		else if (!(empty($inputTags)))
		{
			$outputTags = $inputTags;
		}
	}
	
	function createSlide($tags, $templateFile)	
	{
		$tagsToUse = array();
		$this->copyArrayRemoveNulls($tags, $tagsToUse);
		
		$smarty = $this->populateSmarty($tagsToUse, $this->slideRoot);

		// set the error handler temporarily
		$smarty->error_reporting = E_ALL | E_STRICT;
		$smarty->error_unassigned = true;
		$this->errorDetected = false;
		
		//set_error_handler(array($this, 'myErrorHandler'));
		$result = $smarty->fetch($templateFile);
		//restore_error_handler();

		if ($this->errorDetected)
		{
    		Logger::slideLogger()->LogWarning( "Error generating slide." );
		}
		
		return ($this->errorDetected ? null : $result );
	}
	
	private function populateSmarty($tags, $directory)
	{
		$smarty = new Smarty();
		
		$smarty->template_dir = $directory;
		$smarty->left_delimiter='{--'; 
		$smarty->right_delimiter='--}'; 

		foreach($tags as $name => $value)
		{
			$smarty->assign($name, $value);
		}		
		
		return $smarty;
	}
	
	function myErrorHandler($errno, $errstr)
	{
		$this->errorDetected = true;
		$this->errorText = $errstr;
	}	
	
	private $errorText;
	private $errorDetected;
	private $adminRoot;
	private $slideRoot;
	private $testRoot;
}

?>