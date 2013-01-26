<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class Slide
{
	public static function loadSingleFromDB( $slideId )
	{
		$queryArray = array( "slide_id" => $slideId );
		$result = Slide::loadFromDB($queryArray);
		return (count($result) == 1) ? $result[0] : null;
	}

	public static function loadAllFromDB()
	{
		$queryArray = array( );
		return self::loadFromDB($queryArray);
	}

	public static function loadFromDB( $queryArray, $orderByArray = array())
	{
		$queryString = "SELECT * FROM slide ";
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
			$slideId = $myrow["slide_id"];
			$content = $myrow["content"];
			$targetLink = $myrow["target_link"];
			$guid = $myrow["guid"];
			$title = $myrow["title"];
			$publicationDate = $myrow["publication_date"];
			$generationDate = $myrow["generation_date"];
			$feedId = $myrow["feed_id"];
			$pause = $myrow["pause"];
			$active = $myrow["active"];
			$output[] = new Slide($slideId,$content,$targetLink,$guid,$title,$publicationDate,$generationDate,$feedId,$pause,$active);
		}
		return $output;
	}

	public static function create($content,$targetLink,$guid,$title,$publicationDate,$generationDate,$feedId,$pause,$active)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["content"] = $content;
		$fields["target_link"] = $targetLink;
		$fields["guid"] = $guid;
		$fields["title"] = $title;
		$fields["publication_date"] = $publicationDate;
		$fields["generation_date"] = $generationDate;
		$fields["feed_id"] = $feedId;
		$fields["pause"] = $pause;
		$fields["active"] = $active;
		return $db->insert("slide", $fields, $nullableFields);
	}

	public function update($content,$targetLink,$guid,$title,$publicationDate,$generationDate,$feedId,$pause,$active)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["content"] = $content;
		$fields["target_link"] = $targetLink;
		$fields["guid"] = $guid;
		$fields["title"] = $title;
		$fields["publication_date"] = $publicationDate;
		$fields["generation_date"] = $generationDate;
		$fields["feed_id"] = $feedId;
		$fields["pause"] = $pause;
		$fields["active"] = $active;
		$db->update("slide", "slide_id", $this->_slideId, $fields, $nullableFields);
	}

	public static function deleteFromDB( $slideId )
	{
		$queryString = "DELETE FROM slide WHERE slide_id = $slideId ";
		$db = new DBManager("piqpo");
		$db->query($queryString);
	}

	function Slide($slideId,$content,$targetLink,$guid,$title,$publicationDate,$generationDate,$feedId,$pause,$active)
	{
		$this->_slideId = $slideId;
		$this->_content = $content;
		$this->_targetLink = $targetLink;
		$this->_guid = $guid;
		$this->_title = $title;
		$this->_publicationDate = $publicationDate;
		$this->_generationDate = $generationDate;
		$this->_feedId = $feedId;
		$this->_pause = $pause;
		$this->_active = $active;
	}

	function slideId()
	{
		return $this->_slideId;
	}

	function content()
	{
		return $this->_content;
	}

	function targetLink()
	{
		return $this->_targetLink;
	}

	function guid()
	{
		return $this->_guid;
	}

	function title()
	{
		return $this->_title;
	}

	function publicationDate()
	{
		return $this->_publicationDate;
	}

	function generationDate()
	{
		return $this->_generationDate;
	}

	function feedId()
	{
		return $this->_feedId;
	}

	function pause()
	{
		return $this->_pause;
	}

	function active()
	{
		return $this->_active;
	}

	private $_slideId;
	private $_content;
	private $_targetLink;
	private $_guid;
	private $_title;
	private $_publicationDate;
	private $_generationDate;
	private $_feedId;
	private $_pause;
	private $_active;
}
?>
