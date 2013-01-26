<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class StreamSlide
{
	public static function loadSingleFromDB( $streamSlideId )
	{
		$queryArray = array( "stream_slide_id" => $streamSlideId );
		$result = StreamSlide::loadFromDB($queryArray);
		return (count($result) == 1) ? $result[0] : null;
	}

	public static function loadAllFromDB()
	{
		$queryArray = array( );
		return self::loadFromDB($queryArray);
	}

	public static function loadFromDB( $queryArray, $orderByArray = array())
	{
		$queryString = "SELECT * FROM stream_slide ";
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
			$streamSlideId = $myrow["stream_slide_id"];
			$streamId = $myrow["stream_id"];
			$slideId = $myrow["slide_id"];
			$output[] = new StreamSlide($streamSlideId,$streamId,$slideId);
		}
		return $output;
	}

	public static function create($streamId,$slideId)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["stream_id"] = $streamId;
		$fields["slide_id"] = $slideId;
		return $db->insert("stream_slide", $fields, $nullableFields);
	}

	public function update($streamId,$slideId)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["stream_id"] = $streamId;
		$fields["slide_id"] = $slideId;
		$db->update("stream_slide", "stream_slide_id", $this->_streamSlideId, $fields, $nullableFields);
	}

	public static function deleteFromDB( $streamSlideId )
	{
		$queryString = "DELETE FROM stream_slide WHERE stream_slide_id = $streamSlideId ";
		$db = new DBManager("piqpo");
		$db->query($queryString);
	}

	function StreamSlide($streamSlideId,$streamId,$slideId)
	{
		$this->_streamSlideId = $streamSlideId;
		$this->_streamId = $streamId;
		$this->_slideId = $slideId;
	}

	function streamSlideId()
	{
		return $this->_streamSlideId;
	}

	function streamId()
	{
		return $this->_streamId;
	}

	function slideId()
	{
		return $this->_slideId;
	}

	private $_streamSlideId;
	private $_streamId;
	private $_slideId;
}
?>
