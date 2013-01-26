<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class UserStream
{
	public static function loadSingleFromDB( $userStreamId )
	{
		$queryArray = array( "user_stream_id" => $userStreamId );
		$result = UserStream::loadFromDB($queryArray);
		return (count($result) == 1) ? $result[0] : null;
	}

	public static function loadAllFromDB()
	{
		$queryArray = array( );
		return self::loadFromDB($queryArray);
	}

	public static function loadFromDB( $queryArray, $orderByArray = array())
	{
		$queryString = "SELECT * FROM user_stream ";
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
			$userStreamId = $myrow["user_stream_id"];
			$userId = $myrow["user_id"];
			$streamId = $myrow["stream_id"];
			$output[] = new UserStream($userStreamId,$userId,$streamId);
		}
		return $output;
	}

	public static function create($userId,$streamId)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["user_id"] = $userId;
		$fields["stream_id"] = $streamId;
		return $db->insert("user_stream", $fields, $nullableFields);
	}

	public function update($userId,$streamId)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["user_id"] = $userId;
		$fields["stream_id"] = $streamId;
		$db->update("user_stream", "user_stream_id", $this->_userStreamId, $fields, $nullableFields);
	}

	public static function deleteFromDB( $userStreamId )
	{
		$queryString = "DELETE FROM user_stream WHERE user_stream_id = $userStreamId ";
		$db = new DBManager("piqpo");
		$db->query($queryString);
	}

	function UserStream($userStreamId,$userId,$streamId)
	{
		$this->_userStreamId = $userStreamId;
		$this->_userId = $userId;
		$this->_streamId = $streamId;
	}

	function userStreamId()
	{
		return $this->_userStreamId;
	}

	function userId()
	{
		return $this->_userId;
	}

	function streamId()
	{
		return $this->_streamId;
	}

	private $_userStreamId;
	private $_userId;
	private $_streamId;
}
?>
