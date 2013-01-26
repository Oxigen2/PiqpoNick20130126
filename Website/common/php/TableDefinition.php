<?php

class TableDefinition
{
	private $_database;
	private $_tableName;
	private $_columns;
	private $_primaryKey;
	private $_definition;
	
	function TableDefinition($database, $tableName)
	{
		$this->_database = strtolower($database);
		$this->_tableName = strtolower($tableName);
		$this->_columns = array();
	}
	
	function addColumn($column)
	{		
		if ($column->isPrimaryKey())
		{
			$this->_primaryKey = $column;
		}
		
		$this->_columns[] = $column;
	}
	
	function getColumns()
	{
		return $this->_columns;
	}
	
	function getClassDefinition()
	{
		$this->_definition = "";

		$this->classOpen();
		$this->addLoadSingleMethod();
		$this->addLoadAllMethod();
		$this->addLoadMethod();
		$this->addQueryProcessingMethod();
		$this->addCreateMethod();
		$this->createUpdateMethod();
		$this->addDeleteSingleMethod();
		$this->addConstructor();
		$this->callForAllColumns("addGetFunction");
		$this->callForAllColumns("addMemberDeclaration");		
		$this->classClose();
		
		return $this->_definition;
	}

	private function addCache()
	{
		$this->addLine("private static \$_fullCache;	// Cache of whole table", 1);	
	}
	
	private function addLoadMethod()
	{
		$this->addLine("public static function loadFromDB( \$queryArray, \$orderByArray = array())", 1);
		$this->addLine("{", 1);
		$this->addLine("\$queryString = \"SELECT * FROM {$this->_tableName} \";", 2);
		$this->addLine("if (count(\$queryArray) > 0)", 2);
		$this->addLine("{", 2);
		$this->addLine("\$first = true;", 3);
		$this->addLine("foreach (\$queryArray as \$field => \$value)", 3);
		$this->addLine("{", 3);
		$this->addLine("\$queryString .= \$first ? \" WHERE \" : \" AND \";", 4);
		$this->addLine("\$queryString .= \"\$field = \$value\";", 4);
		$this->addLine("\$first = false;", 4);		
		$this->addLine("}", 3);
		$this->addLine("}", 2);
		$this->addLine("if (count(\$orderByArray) > 0)", 2);
		$this->addLine("{", 2);
		$this->addLine("\$queryString .= \" ORDER BY \";", 3);
		$this->addLine("\$first = true;", 3);
		$this->addLine("foreach (\$orderByArray as \$id => \$orderField)", 3);
		$this->addLine("{", 3);
		$this->addLine("\$queryString .= \$first ? \"\" : \",\";", 4);		
		$this->addLine("\$queryString .= \$orderField;", 4);		
		$this->addLine("\$first = false;", 4);
		$this->addLine("}", 3);		
		$this->addLine("}", 2);		
		$this->addLine("return self::processDBQuery(\$queryString);", 2);
		$this->addLine("}", 1);		
 		$this->addLine();		
	}
	
	private function addQueryProcessingMethod()
	{
		$this->addLine("public static function processDBQuery( \$queryString )", 1);
		$this->addLine("{", 1);
		$this->addLine("\$output = array();", 2);
		$this->addLine("\$db = new DBManager(\"{$this->_database}\");", 2);
		$this->addLine("\$result = \$db->query(\$queryString);", 2);
        $this->addLine("while (\$myrow = mysql_fetch_array(\$result))", 2);
		$this->addLine("{", 2);
		$this->callForAllColumns("myrowAssignment", 3);
		$this->addLine("\$output[] = new {$this->className()}({$this->columnVarsAsList()});", 3);	
		$this->addLine("}", 2);		
		$this->addLine("return \$output;", 2);
		$this->addLine("}", 1);		
		$this->addLine();		
	}
	
	private function addLoadSingleMethod()
	{
		$this->addLine("public static function loadSingleFromDB( {$this->varName($this->_primaryKey)} )", 1);	
		$this->addLine("{", 1);
		$this->addLine("\$queryArray = array( \"{$this->_primaryKey->name()}\" => {$this->varName($this->_primaryKey)} );", 2);
		$this->addLine("\$result = {$this->className()}::loadFromDB(\$queryArray);", 2);
		$this->addLine("return (count(\$result) == 1) ? \$result[0] : null;", 2);
		$this->addLine("}", 1);		
		$this->addLine();		
	}
	
	private function addDeleteSingleMethod()
	{
		$this->addLine("public static function deleteFromDB( {$this->varName($this->_primaryKey)} )", 1);	
		$this->addLine("{", 1);
		$this->addLine("\$queryString = \"DELETE FROM {$this->_tableName} WHERE {$this->_primaryKey->name()} = {$this->varName($this->_primaryKey)} \";", 2);
		$this->addLine("\$db = new DBManager(\"{$this->_database}\");", 2);
		$this->addLine("\$db->query(\$queryString);", 2);
		$this->addLine("}", 1);		
		$this->addLine();		
	}
	
	private function addLoadAllMethod()
	{
		$this->addLine("public static function loadAllFromDB()", 1);	
		$this->addLine("{", 1);
		$this->addLine("\$queryArray = array( );", 2);
		$this->addLine("return self::loadFromDB(\$queryArray);", 2);
		$this->addLine("}", 1);		
		$this->addLine();		
	}
	
	private function myrowAssignment($column, $indent)
	{
		$this->addLine("\${$column->properName()} = \$myrow[\"{$column->name()}\"];", $indent);			
	}
	
	private function addConstructor()
	{
		$this->addLine("function ".$this->className()."(".$this->columnVarsAsList().")", 1);
		$this->addLine("{", 1);
		$this->callForAllColumns("addAssignment", 2);
		$this->addLine("}", 1);
		$this->addLine();
	}
	
	private function addCreateMethod()
	{
		$this->addLine("public static function create(".$this->columnVarsAsList(false).")", 1);
		$this->addLine("{", 1);
		$this->addLine("\$db = new DBManager(\"{$this->_database}\");", 2);
		$this->addLine("\$fields=array();", 2);
		$this->addLine("\$nullableFields=array();", 2);

		foreach($this->_columns as $id => $column)
		{
			if (!($column->isPrimaryKey()))
			{
				$this->addLine("\$fields[\"{$column->name()}\"] = {$this->varName($column)};", 2);

				if ($column->isNullable()) 
				{
					$this->addLine("\$nullableFields[\"{$column->name()}\"] = {$this->varName($column)};", 2);				
				}
			}
		}
		
		$this->addLine("return \$db->insert(\"{$this->_tableName}\", \$fields, \$nullableFields);", 2);
		$this->addLine("}", 1);
		$this->addLine();
	}
	
	private function createUpdateMethod()
	{
		$this->addLine("public function update(".$this->columnVarsAsList(false).")", 1);
		$this->addLine("{", 1);
		$this->addLine("\$db = new DBManager(\"{$this->_database}\");", 2);
		$this->addLine("\$fields=array();", 2);
		$this->addLine("\$nullableFields=array();", 2);

		foreach($this->_columns as $id => $column)
		{
			if (!($column->isPrimaryKey()))
			{
				$this->addLine("\$fields[\"{$column->name()}\"] = {$this->varName($column)};", 2);

				if ($column->isNullable()) 
				{
					$this->addLine("\$nullableFields[\"{$column->name()}\"] = {$this->varName($column)};", 2);				
				}
			}
		}
		
		$this->addLine("\$db->update(\"{$this->_tableName}\", \"{$this->_primaryKey->name()}\", {$this->thisMemberVarName($this->_primaryKey)}, \$fields, \$nullableFields);", 2);
		$this->addLine("}", 1);
		$this->addLine();
	}

	private function addAssignment($column, $indent)
	{
		$this->addLine($this->thisMemberVarName($column)." = ".$this->varName($column).';', $indent);
	}
		
	private function callForAllColumns($functionName, $indent = 0)
	{
		$callArray = array($this, $functionName);
		foreach($this->_columns as $id => $column)
		{
			call_user_func($callArray, $column, $indent);
		}
	}
	
	private function addGetFunction($column, $indent)
	{
		$this->addLine("function ".$column->properName()."()", 1);
		$this->addLine("{", 1);
		$this->addLine("return ".$this->thisMemberVarName($column).";", 2);		
		$this->addLine("}", 1);
		$this->addLine();
	}
	
	private function addMemberDeclaration($column, $indent)
	{
		$this->addLine("private ".$this->memberVarName($column).";",1);
	}
	
	private function classOpen()
	{
		$this->addLine("<?php", 0);
		$this->addLine("require_once(getenv(\"DOCUMENT_ROOT\").\"/inc_".strtolower($this->_database).".php\");", 0);
		$this->addLine();
		$this->addLine("class ".$this->className(), 0);
		$this->addLine("{", 0);
	}
	
	private function classClose()
	{
		$this->addLine("}");
		$this->addLine("?>");
	}
	
	private function columnVarsAsList($includePrimaryKey = true)
	{
		$output = "";
		$first = true;
		
		foreach ($this->_columns as $id => $column)
		{
			if (!($column->isPrimaryKey() && (!$includePrimaryKey)))
			{
				if ($first)
				{
					$first = false;
				}
				else
				{
					$output .= ",";
				}
				$output .= $this->varName($column);
			}
		}
		return $output;
	}
	
	private function varName($column)
	{
		return '$'.$column->properName();
	}
	
	private function memberVarName($column)
	{
		return '$_'.$column->properName();
	}
	
	private function thisMemberVarName($column)
	{
		return '$this->_'.$column->properName();
	}
	
	private function addLine($text = "", $indent = 0)
	{
		$this->_definition .= str_repeat ( "\t", $indent ).$text."\n";
	}
	
	public function className()
	{
		$words = explode('_', strtolower($this->_tableName));
		$output = "";
		foreach ($words as $id => $word)
		{
			$output .= ucwords($word);
		}
		return $output;	
	}
}

?>