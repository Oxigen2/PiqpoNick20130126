<?php

function createTableDefinition($dbManager, $database, $table)
{
	$query = "	SELECT COLUMN_NAME, IS_NULLABLE, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, COLUMN_KEY, EXTRA
				FROM COLUMNS
				WHERE TABLE_SCHEMA = '{$database}'
				AND TABLE_NAME = '{$table}'";
	
	$result = $dbManager->query($query);
	
	$columnNames = array();		
	$tableDefinition = new TableDefinition($database, $table);
	
	while ($myrow = mysql_fetch_array($result))
	{			
		$colName = $myrow["COLUMN_NAME"];
		$isNullable = ($myrow["IS_NULLABLE"] == "YES");
		$isPrimaryKey = ($myrow["COLUMN_KEY"] == "PRI");
		$columnDefinition = new ColumnDefinition($colName, $isNullable, $isPrimaryKey);
		$tableDefinition->addColumn($columnDefinition);
	}		
	return $tableDefinition;
}

function generateTable($tableDefinition)
{
	$targetDirectory = "C:\\Users\\Nick\\Documents\\temp\\db\\";

	$filename = $targetDirectory.$tableDefinition->className().".php";
	$fh = fopen($filename, 'w') or die ("Failed to open {$filename}: {$php_errormsg}"); 
	fwrite($fh, $tableDefinition->getClassDefinition()) or die ("Failed to write to {$filename}: {$php_errormsg}");
	fflush($fh) or die ("Failed to flush to {$filename}: {$php_errormsg}");	
}

$database = "";
$table = "";
$output = "";

$dbSettings = new DBSettings();
$linkManager = new LinkManager();
$dbManager = new DBManager("information_schema");

if (isset($_GET['database']))
{
	$database = $_GET['database'];
}
if (isset($_GET['table']))
{
	$table = $_GET['table'];
}
if (isset($_GET['generate']))
{
	$generate = true;
}

// Select database
if (strlen($database) == 0)
{
	$output .= "Select database:<br>";

	$query = "SELECT SCHEMA_NAME
	FROM `SCHEMATA`
	WHERE SCHEMA_NAME NOT
	IN ('information_schema', 'mysql')";
	
	$result = $dbManager->query($query);
	
	while ($myrow = mysql_fetch_array($result))
	{
		$db = $myrow["SCHEMA_NAME"];
		$link = "{$linkManager->currentLink()}?database={$db}";
		$output .= "<a href='{$link}'>{$db}</a><br>";
	}
}
else
{
	if (strlen($table) == 0)
	{
		// Select table
		$output .= "<a href='{$linkManager->currentLink()}'>Database</a>: {$database} | ";	
		$output .= "<a href='{$linkManager->currentLink()}?database={$database}&generate'>Generate</a><br>";	
		
		$query = "	SELECT TABLE_NAME
					FROM TABLES
					WHERE TABLE_SCHEMA = '{$database}'";
		
		$result = $dbManager->query($query);
		
		while ($myrow = mysql_fetch_array($result))
		{
			$table = $myrow["TABLE_NAME"];
			$link = "{$linkManager->currentLink()}?database={$database}&table={$table}";
			$output .= "<a href='{$link}'>{$table}</a><br>";
			
			if ($generate)
			{
				generateTable(createTableDefinition($dbManager, $database, $table));				
			}
		}
	}
	else
	{
		$output .= "<a href='{$linkManager->currentLink()}'>Database</a>: {$database} | ";	
		$output .= "<a href='{$linkManager->currentLink()}?database={$database}'>Table</a>: {$table} | ";	
		$output .= "<a href='{$linkManager->currentLink()}?database={$database}&table={$table}&generate'>Generate</a>";	
		
		$tableDefinition = createTableDefinition($dbManager, $database, $table);
		
		$output .= "<pre>".htmlentities($tableDefinition->getClassDefinition())."</pre>";
		
		if($generate)
		{
			generateTable($tableDefinition);
		}
	}
}

print $output;

?>