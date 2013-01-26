<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class FeedQueue
{
	public static function loadSingleFromDB( $feedQueueId )
	{
		$queryArray = array( "feed_queue_id" => $feedQueueId );
		$result = FeedQueue::loadFromDB($queryArray);
		return (count($result) == 1) ? $result[0] : null;
	}

	public static function loadAllFromDB()
	{
		$queryArray = array( );
		return self::loadFromDB($queryArray);
	}

	public static function loadFromDB( $queryArray, $orderByArray = array())
	{
		$queryString = "SELECT * FROM feed_queue ";
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
			$feedQueueId = $myrow["feed_queue_id"];
			$feedId = $myrow["feed_id"];
			$nextPoll = $myrow["next_poll"];
			$output[] = new FeedQueue($feedQueueId,$feedId,$nextPoll);
		}
		return $output;
	}

	public static function create($feedId,$nextPoll)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["feed_id"] = $feedId;
		$fields["next_poll"] = $nextPoll;
		return $db->insert("feed_queue", $fields, $nullableFields);
	}

	public function update($feedId,$nextPoll)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["feed_id"] = $feedId;
		$fields["next_poll"] = $nextPoll;
		$db->update("feed_queue", "feed_queue_id", $this->_feedQueueId, $fields, $nullableFields);
	}

	public static function deleteFromDB( $feedQueueId )
	{
		$queryString = "DELETE FROM feed_queue WHERE feed_queue_id = $feedQueueId ";
		$db = new DBManager("piqpo");
		$db->query($queryString);
	}

	function FeedQueue($feedQueueId,$feedId,$nextPoll)
	{
		$this->_feedQueueId = $feedQueueId;
		$this->_feedId = $feedId;
		$this->_nextPoll = $nextPoll;
	}

	function feedQueueId()
	{
		return $this->_feedQueueId;
	}

	function feedId()
	{
		return $this->_feedId;
	}

	function nextPoll()
	{
		return $this->_nextPoll;
	}

	private $_feedQueueId;
	private $_feedId;
	private $_nextPoll;
}
?>
