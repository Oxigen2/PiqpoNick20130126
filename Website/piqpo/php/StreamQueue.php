<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class StreamQueue
{
	public static function loadSingleFromDB( $streamQueueId )
	{
		$queryArray = array( "stream_queue_id" => $streamQueueId );
		$result = StreamQueue::loadFromDB($queryArray);
		return (count($result) == 1) ? $result[0] : null;
	}

	public static function loadAllFromDB()
	{
		$queryArray = array( );
		return self::loadFromDB($queryArray);
	}

	public static function loadFromDB( $queryArray, $orderByArray = array())
	{
		$queryString = "SELECT * FROM STREAM_QUEUE ";
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
			$streamQueueId = $myrow["stream_queue_id"];
			$streamId = $myrow["stream_id"];
			$nextPoll = $myrow["next_poll"];
			$output[] = new StreamQueue($streamQueueId,$streamId,$nextPoll);
		}
		return $output;
	}

	public static function create($streamId,$nextPoll)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["stream_id"] = $streamId;
		$fields["next_poll"] = $nextPoll;
		return $db->insert("STREAM_QUEUE", $fields, $nullableFields);
	}

	public static function deleteFromDB( $streamQueueId )
	{
		$queryString = "DELETE FROM STREAM_QUEUE WHERE stream_queue_id = $streamQueueId ";
		$db = new DBManager("piqpo");
		$db->query($queryString);
	}

	function StreamQueue($streamQueueId,$streamId,$nextPoll)
	{
		$this->_streamQueueId = $streamQueueId;
		$this->_streamId = $streamId;
		$this->_nextPoll = $nextPoll;
	}

	function streamQueueId()
	{
		return $this->_streamQueueId;
	}

	function streamId()
	{
		return $this->_streamId;
	}

	function nextPoll()
	{
		return $this->_nextPoll;
	}

	private $_streamQueueId;
	private $_streamId;
	private $_nextPoll;
	private static $_fullCache;	// Cache of whole table
}
?>
