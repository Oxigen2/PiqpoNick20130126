<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class Feed
{
	public static function loadSingleFromDB( $feedId )
	{
		$queryArray = array( "feed_id" => $feedId );
		$result = Feed::loadFromDB($queryArray);
		return (count($result) == 1) ? $result[0] : null;
	}

	public static function loadAllFromDB()
	{
		$queryArray = array( );
		return self::loadFromDB($queryArray);
	}

	public static function loadFromDB( $queryArray, $orderByArray = array())
	{
		$queryString = "SELECT * FROM feed ";
		if (count($queryArray) > 0)
		{
			$first = true;
			foreach ($queryArray as $field => $value)
			{
				$queryString .= $first ? " WHERE " : " AND ";
				$queryString .= "$field = $value";
				$first = false;
			}
		}
		if (count($orderByArray) > 0)
		{
			$queryString .= " ORDER BY ";
			$first = true;
			foreach ($orderByArray as $id => $orderField)
			{
				$queryString .= $first ? "" : ",";
				$queryString .= $orderField;
				$first = false;
			}
		}
		return self::processDBQuery($queryString);
	}

	public static function processDBQuery( $queryString )
	{
		$output = array();
		$db = new DBManager("piqpo");
		$result = $db->query($queryString);
		while ($myrow = mysql_fetch_array($result))
		{
			$feedId = $myrow["feed_id"];
			$feedDefinitionFile = $myrow["feed_definition_file"];
			$name = $myrow["name"];
			$pause = $myrow["pause"];
			$feedType = $myrow["feed_type"];
			$pollFrequency = $myrow["poll_frequency"];
			$templateFile = $myrow["template_file"];
			$maxSlides = $myrow["max_slides"];
			$output[] = new Feed($feedId,$feedDefinitionFile,$name,$pause,$feedType,$pollFrequency,$templateFile,$maxSlides);
		}
		return $output;
	}

	public static function create($feedDefinitionFile,$name,$pause,$feedType,$pollFrequency,$templateFile,$maxSlides)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["feed_definition_file"] = $feedDefinitionFile;
		$fields["name"] = $name;
		$fields["pause"] = $pause;
		$fields["feed_type"] = $feedType;
		$fields["poll_frequency"] = $pollFrequency;
		$fields["template_file"] = $templateFile;
		$fields["max_slides"] = $maxSlides;
		return $db->insert("feed", $fields, $nullableFields);
	}

	public function update($feedDefinitionFile,$name,$pause,$feedType,$pollFrequency,$templateFile,$maxSlides)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["feed_definition_file"] = $feedDefinitionFile;
		$fields["name"] = $name;
		$fields["pause"] = $pause;
		$fields["feed_type"] = $feedType;
		$fields["poll_frequency"] = $pollFrequency;
		$fields["template_file"] = $templateFile;
		$fields["max_slides"] = $maxSlides;
		$db->update("feed", "feed_id", $this->_feedId, $fields, $nullableFields);
	}

	public static function deleteFromDB( $feedId )
	{
		$queryString = "DELETE FROM feed WHERE feed_id = $feedId ";
		$db = new DBManager("piqpo");
		$db->query($queryString);
	}

	function Feed($feedId,$feedDefinitionFile,$name,$pause,$feedType,$pollFrequency,$templateFile,$maxSlides)
	{
		$this->_feedId = $feedId;
		$this->_feedDefinitionFile = $feedDefinitionFile;
		$this->_name = $name;
		$this->_pause = $pause;
		$this->_feedType = $feedType;
		$this->_pollFrequency = $pollFrequency;
		$this->_templateFile = $templateFile;
		$this->_maxSlides = $maxSlides;
	}

	function feedId()
	{
		return $this->_feedId;
	}

	function feedDefinitionFile()
	{
		return $this->_feedDefinitionFile;
	}

	function name()
	{
		return $this->_name;
	}

	function pause()
	{
		return $this->_pause;
	}

	function feedType()
	{
		return $this->_feedType;
	}

	function pollFrequency()
	{
		return $this->_pollFrequency;
	}

	function templateFile()
	{
		return $this->_templateFile;
	}

	function maxSlides()
	{
		return $this->_maxSlides;
	}

	private $_feedId;
	private $_feedDefinitionFile;
	private $_name;
	private $_pause;
	private $_feedType;
	private $_pollFrequency;
	private $_templateFile;
	private $_maxSlides;
}
?>
