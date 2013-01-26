<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class SlideFollow
{
	public static function loadSingleFromDB( $slideFollowId )
	{
		$queryArray = array( "slide_follow_id" => $slideFollowId );
		$result = SlideFollow::loadFromDB($queryArray);
		return (count($result) == 1) ? $result[0] : null;
	}

	public static function loadAllFromDB()
	{
		$queryArray = array( );
		return self::loadFromDB($queryArray);
	}

	public static function loadFromDB( $queryArray, $orderByArray = array())
	{
		$queryString = "SELECT * FROM slide_follow ";
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
			$slideFollowId = $myrow["slide_follow_id"];
			$userId = $myrow["user_id"];
			$slideId = $myrow["slide_id"];
			$followTime = $myrow["follow_time"];
			$source = $myrow["source"];
			$output[] = new SlideFollow($slideFollowId,$userId,$slideId,$followTime,$source);
		}
		return $output;
	}

	public static function create($userId,$slideId,$followTime,$source)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["user_id"] = $userId;
		$fields["slide_id"] = $slideId;
		$fields["follow_time"] = $followTime;
		$fields["source"] = $source;
		return $db->insert("slide_follow", $fields, $nullableFields);
	}

	public function update($userId,$slideId,$followTime,$source)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["user_id"] = $userId;
		$fields["slide_id"] = $slideId;
		$fields["follow_time"] = $followTime;
		$fields["source"] = $source;
		$db->update("slide_follow", "slide_follow_id", $this->_slideFollowId, $fields, $nullableFields);
	}

	public static function deleteFromDB( $slideFollowId )
	{
		$queryString = "DELETE FROM slide_follow WHERE slide_follow_id = $slideFollowId ";
		$db = new DBManager("piqpo");
		$db->query($queryString);
	}

	function SlideFollow($slideFollowId,$userId,$slideId,$followTime,$source)
	{
		$this->_slideFollowId = $slideFollowId;
		$this->_userId = $userId;
		$this->_slideId = $slideId;
		$this->_followTime = $followTime;
		$this->_source = $source;
	}

	function slideFollowId()
	{
		return $this->_slideFollowId;
	}

	function userId()
	{
		return $this->_userId;
	}

	function slideId()
	{
		return $this->_slideId;
	}

	function followTime()
	{
		return $this->_followTime;
	}

	function source()
	{
		return $this->_source;
	}

	private $_slideFollowId;
	private $_userId;
	private $_slideId;
	private $_followTime;
	private $_source;
}
?>
