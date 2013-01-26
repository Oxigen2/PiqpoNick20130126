<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class ProfileStream
{
	public static function loadSingleFromDB( $profileStreamId )
	{
		$queryArray = array( "profile_stream_id" => $profileStreamId );
		$result = ProfileStream::loadFromDB($queryArray);
		return (count($result) == 1) ? $result[0] : null;
	}

	public static function loadAllFromDB()
	{
		$queryArray = array( );
		return self::loadFromDB($queryArray);
	}

	public static function loadFromDB( $queryArray, $orderByArray = array())
	{
		$queryString = "SELECT * FROM profile_stream ";
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
			$profileStreamId = $myrow["profile_stream_id"];
			$profileId = $myrow["profile_id"];
			$streamId = $myrow["stream_id"];
			$output[] = new ProfileStream($profileStreamId,$profileId,$streamId);
		}
		return $output;
	}

	public static function create($profileId,$streamId)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["profile_id"] = $profileId;
		$fields["stream_id"] = $streamId;
		return $db->insert("profile_stream", $fields, $nullableFields);
	}

	public function update($profileId,$streamId)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["profile_id"] = $profileId;
		$fields["stream_id"] = $streamId;
		$db->update("profile_stream", "profile_stream_id", $this->_profileStreamId, $fields, $nullableFields);
	}

	public static function deleteFromDB( $profileStreamId )
	{
		$queryString = "DELETE FROM profile_stream WHERE profile_stream_id = $profileStreamId ";
		$db = new DBManager("piqpo");
		$db->query($queryString);
	}

	function ProfileStream($profileStreamId,$profileId,$streamId)
	{
		$this->_profileStreamId = $profileStreamId;
		$this->_profileId = $profileId;
		$this->_streamId = $streamId;
	}

	function profileStreamId()
	{
		return $this->_profileStreamId;
	}

	function profileId()
	{
		return $this->_profileId;
	}

	function streamId()
	{
		return $this->_streamId;
	}

	private $_profileStreamId;
	private $_profileId;
	private $_streamId;
}
?>
