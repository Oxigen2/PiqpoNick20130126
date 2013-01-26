<?php

class HtmlTidier
{
	static function tidyString($string, $bodyOnly)
	{
		$tidy = new tidy();
		$tidy->parseString($string, self::getConfig($bodyOnly), 'utf8');
		$tidy->cleanRepair();
		
		return ($bodyOnly ? "<root>".$tidy."</root>" : $tidy);
	}
	
	static function tidyFile($url)
	{
		$tidy = new tidy();
		$tidy->parseFile($url, self::getConfig(false), 'utf8');
		$tidy->cleanRepair();
		
		return $tidy;	
	}
	
	private static function getConfig($bodyOnly)
	{
		return $bodyOnly 
			?
			array('output-xhtml' => true, 'char-encoding' => 'utf-8', 'numeric-entities' => true, 'show-body-only' => true)
			:
			array('output-xhtml' => true, 'char-encoding' => 'utf-8', 'numeric-entities' => true);			
	}
}

?>