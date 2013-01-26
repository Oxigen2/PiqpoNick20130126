<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class FeedStream
{
	public static function loadSingleFromDB( $feedStreamId )
	{
		$queryArray = array( "feed_stream_id" => $feedStreamId );
		$result = FeedStream::loadFromDB($queryArray);
		return (count($result) == 1) ? $result[0] : null;
	}

	public static function loadAllFromDB()
	{
		$queryArray = array( );
		return self::loadFromDB($queryArray);
	}

	public static function loadFromDB( $queryArray, $orderByArray = array())
	{
		$queryString = "SELECT * FROM feed_stream ";
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
			$feedStreamId = $myrow["feed_stream_id"];
			$feedId = $myrow["feed_id"];
			$streamId = $myrow["stream_id"];
			$output[] = new FeedStream($feedStreamId,$feedId,$streamId);
		}
		return $output;
	}

	public static function create($feedId,$streamId)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["feed_id"] = $feedId;
		$fields["stream_id"] = $streamId;
		return $db->insert("feed_stream", $fields, $nullableFields);
	}

	public function update($feedId,$streamId)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["feed_id"] = $feedId;
		$fields["stream_id"] = $streamId;
		$db->update("feed_stream", "feed_stream_id", $this->_feedStreamId, $fields, $nullableFields);
	}

	public static function deleteFromDB( $feedStreamId )
	{
		$queryString = "DELETE FROM feed_stream WHERE feed_stream_id = $feedStreamId ";
		$db = new DBManager("piqpo");
		$db->query($queryString);
	}

	function FeedStream($feedStreamId,$feedId,$streamId)
	{
		$this->_feedStreamId = $feedStreamId;
		$this->_feedId = $feedId;
		$this->_streamId = $streamId;
	}

	function feedStreamId()
	{
		return $this->_feedStreamId;
	}

	function feedId()
	{
		return $this->_feedId;
	}

	function streamId()
	{
		return $this->_streamId;
	}

	private $_feedStreamId;
	private $_feedId;
	private $_streamId;
}
?>
