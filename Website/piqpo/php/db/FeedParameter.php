<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class FeedParameter
{
	public static function loadSingleFromDB( $feedParameterId )
	{
		$queryArray = array( "feed_parameter_id" => $feedParameterId );
		$result = FeedParameter::loadFromDB($queryArray);
		return (count($result) == 1) ? $result[0] : null;
	}

	public static function loadAllFromDB()
	{
		$queryArray = array( );
		return self::loadFromDB($queryArray);
	}

	public static function loadFromDB( $queryArray, $orderByArray = array())
	{
		$queryString = "SELECT * FROM feed_parameter ";
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
			$feedParameterId = $myrow["feed_parameter_id"];
			$feedId = $myrow["feed_id"];
			$parameterName = $myrow["parameter_name"];
			$parameterValue = $myrow["parameter_value"];
			$output[] = new FeedParameter($feedParameterId,$feedId,$parameterName,$parameterValue);
		}
		return $output;
	}

	public static function create($feedId,$parameterName,$parameterValue)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["feed_id"] = $feedId;
		$fields["parameter_name"] = $parameterName;
		$fields["parameter_value"] = $parameterValue;
		return $db->insert("feed_parameter", $fields, $nullableFields);
	}

	public function update($feedId,$parameterName,$parameterValue)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["feed_id"] = $feedId;
		$fields["parameter_name"] = $parameterName;
		$fields["parameter_value"] = $parameterValue;
		$db->update("feed_parameter", "feed_parameter_id", $this->_feedParameterId, $fields, $nullableFields);
	}

	public static function deleteFromDB( $feedParameterId )
	{
		$queryString = "DELETE FROM feed_parameter WHERE feed_parameter_id = $feedParameterId ";
		$db = new DBManager("piqpo");
		$db->query($queryString);
	}

	function FeedParameter($feedParameterId,$feedId,$parameterName,$parameterValue)
	{
		$this->_feedParameterId = $feedParameterId;
		$this->_feedId = $feedId;
		$this->_parameterName = $parameterName;
		$this->_parameterValue = $parameterValue;
	}

	function feedParameterId()
	{
		return $this->_feedParameterId;
	}

	function feedId()
	{
		return $this->_feedId;
	}

	function parameterName()
	{
		return $this->_parameterName;
	}

	function parameterValue()
	{
		return $this->_parameterValue;
	}

	private $_feedParameterId;
	private $_feedId;
	private $_parameterName;
	private $_parameterValue;
}
?>
