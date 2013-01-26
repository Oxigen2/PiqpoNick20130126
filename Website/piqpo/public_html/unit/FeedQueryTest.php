<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

// Get POST values
$type = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $type = isset($_POST['rss']) ? "rss" : "plain";
	$url = $_POST['url'];
    $definitionFile = $_POST['definition_file'];
}

// Begin plain Form
print "<form action='{$_SERVER["SCRIPT_NAME"]}' method='POST'>";

// Definition file
$feedDefinitionManager = new FeedDefinitionManager();
$files = $feedDefinitionManager->getDefinitionFiles('plain');
print "Definition file:<br/><select style='width:600px;' name='definition_file'>";
foreach($files as $file)
{
	$selected = (isset($definitionFile) && ($definitionFile == $file)) ? "selected" : ""; 
	print "<option value={$file} {$selected}>{$file}</option>";
}
print '</select><p/>';

print "<input type='submit' name='plain' value='Plain' />";
print '</form>';
// End of plain form

print '<hr/>';

// Begin RSS Form
print "<form action='{$_SERVER["SCRIPT_NAME"]}' method='POST'>";

// Url
print "Url:<br/><input type=text style='width:600px;' name='url' value='{$url}' /><br/>";

// Definition file
$feedDefinitionManager = new FeedDefinitionManager();
$files = $feedDefinitionManager->getDefinitionFiles('rss');
print "Definition file:<br/><select style='width:600px;' name='definition_file'>";
foreach($files as $file)
{
	$selected = (isset($definitionFile) && ($definitionFile == $file)) ? "selected" : ""; 
	print "<option value={$file} {$selected}>{$file}</option>";
}
print '</select><p/>';

print "<input type='submit' name='rss' value='RSS' />";
print '</form>';
// End of RSS Form

print '<hr/>';

if ( (($type == 'rss') && (isset($url)) && (isset($definitionFile))) || (( $type == "plain" ) && isset($definitionFile) ) )
{
    $parameters = array();
    
    if ( $type == 'rss' )
    {
        $parameters = array('url' => $url );
    }

    $feedDefinitionManager = new FeedDefinitionManager;
    $feedFilename = $feedDefinitionManager->formFullFilename( $type, $definitionFile );

    $feedQuery = new FeedQuery('none', $feedFilename, 100, $parameters, array());

    $debugOutput = "";
    $result = $feedQuery->performQuery( $debugOutput );

    print "<p/>Generated " . count( $result ) . " slides:";
    foreach($result as $id => $slideInfo)
    {
        print "<p/>Slide: {$id}<br/>";
        print XMLTaggifier::createTable( $slideInfo->tags() );
    }

    print "<hr/>Debug output:<p/>" . $debugOutput;
}

?>
</body>
</html>
