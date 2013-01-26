<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class SlideView
{
	public static function loadSingleFromDB( $slideViewId )
	{
		$queryArray = array( "slide_view_id" => $slideViewId );
		$result = SlideView::loadFromDB($queryArray);
		return (count($result) == 1) ? $result[0] : null;
	}

	public static function loadAllFromDB()
	{
		$queryArray = array( );
		return self::loadFromDB($queryArray);
	}

	public static function loadFromDB( $queryArray, $orderByArray = array())
	{
		$queryString = "SELECT * FROM slide_view ";
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
			$slideViewId = $myrow["slide_view_id"];
			$userId = $myrow["user_id"];
			$slideId = $myrow["slide_id"];
			$viewTime = $myrow["view_time"];
			$source = $myrow["source"];
			$output[] = new SlideView($slideViewId,$userId,$slideId,$viewTime,$source);
		}
		return $output;
	}

	public static function create($userId,$slideId,$viewTime,$source)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["user_id"] = $userId;
		$fields["slide_id"] = $slideId;
		$fields["view_time"] = $viewTime;
		$fields["source"] = $source;
		return $db->insert("slide_view", $fields, $nullableFields);
	}

	public function update($userId,$slideId,$viewTime,$source)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["user_id"] = $userId;
		$fields["slide_id"] = $slideId;
		$fields["view_time"] = $viewTime;
		$fields["source"] = $source;
		$db->update("slide_view", "slide_view_id", $this->_slideViewId, $fields, $nullableFields);
	}

	public static function deleteFromDB( $slideViewId )
	{
		$queryString = "DELETE FROM slide_view WHERE slide_view_id = $slideViewId ";
		$db = new DBManager("piqpo");
		$db->query($queryString);
	}

	function SlideView($slideViewId,$userId,$slideId,$viewTime,$source)
	{
		$this->_slideViewId = $slideViewId;
		$this->_userId = $userId;
		$this->_slideId = $slideId;
		$this->_viewTime = $viewTime;
		$this->_source = $source;
	}

	function slideViewId()
	{
		return $this->_slideViewId;
	}

	function userId()
	{
		return $this->_userId;
	}

	function slideId()
	{
		return $this->_slideId;
	}

	function viewTime()
	{
		return $this->_viewTime;
	}

	function source()
	{
		return $this->_source;
	}

	private $_slideViewId;
	private $_userId;
	private $_slideId;
	private $_viewTime;
	private $_source;
}
?>
