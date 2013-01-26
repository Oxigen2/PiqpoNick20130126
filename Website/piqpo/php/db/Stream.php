<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class Stream
{
	public static function loadSingleFromDB( $streamId )
	{
		$queryArray = array( "stream_id" => $streamId );
		$result = Stream::loadFromDB($queryArray);
		return (count($result) == 1) ? $result[0] : null;
	}

	public static function loadAllFromDB()
	{
		$queryArray = array( );
		return self::loadFromDB($queryArray);
	}

	public static function loadFromDB( $queryArray, $orderByArray = array())
	{
		$queryString = "SELECT * FROM stream ";
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
			$streamId = $myrow["stream_id"];
			$name = $myrow["name"];
			$output[] = new Stream($streamId,$name);
		}
		return $output;
	}

	public static function create($name)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["name"] = $name;
		return $db->insert("stream", $fields, $nullableFields);
	}

	public function update($name)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["name"] = $name;
		$db->update("stream", "stream_id", $this->_streamId, $fields, $nullableFields);
	}

	public static function deleteFromDB( $streamId )
	{
		$queryString = "DELETE FROM stream WHERE stream_id = $streamId ";
		$db = new DBManager("piqpo");
		$db->query($queryString);
	}

	function Stream($streamId,$name)
	{
		$this->_streamId = $streamId;
		$this->_name = $name;
	}

	function streamId()
	{
		return $this->_streamId;
	}

	function name()
	{
		return $this->_name;
	}

	private $_streamId;
	private $_name;
}
?>
