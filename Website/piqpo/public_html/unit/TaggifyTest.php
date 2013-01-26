<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

function processString($buffer)
{
	$xmlDom = new DOMDocument;
	$xmlDom->loadXML($buffer);			

	$taggifier = new XMLTaggifier($xmlDom);

	$taggifier->processDom($xmlDom);
	
	$taggifier->addShortcut("html.body.p.0", "firstpara");
	$taggifier->processShortcuts();

	print $taggifier->createTable($taggifier->tags());
	
	$tag = $taggifier->evaluateDotSyntaxTag("html.body.p.0", $taggifier->tags());
	print isset($tag) ? $tag : "not defined";
}

$bar = <<<EOT
<root xmlns:b="http://www.w3.org/1999/xhtml">
	<player rank="1" name="Djokovic,Novak" points="13,630"/>
	<player rank="2" name="Nadal,Rafael" points="9,595"/>
	<player rank="3" name="Federer,Roger" points="8,010"/>
</root>	
EOT;
processString($bar);

