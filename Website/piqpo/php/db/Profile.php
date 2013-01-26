<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class Profile
{
	public static function loadSingleFromDB( $profileId )
	{
		$queryArray = array( "profile_id" => $profileId );
		$result = Profile::loadFromDB($queryArray);
		return (count($result) == 1) ? $result[0] : null;
	}

	public static function loadAllFromDB()
	{
		$queryArray = array( );
		return self::loadFromDB($queryArray);
	}

	public static function loadFromDB( $queryArray, $orderByArray = array())
	{
		$queryString = "SELECT * FROM profile ";
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
			$profileId = $myrow["profile_id"];
			$name = $myrow["name"];
			$userId = $myrow["user_id"];
			$activationCode = $myrow["activation_code"];
			$status = $myrow["status"];
			$output[] = new Profile($profileId,$name,$userId,$activationCode,$status);
		}
		return $output;
	}

	public static function create($name,$userId,$activationCode,$status)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["name"] = $name;
		$fields["user_id"] = $userId;
		$nullableFields["user_id"] = $userId;
		$fields["activation_code"] = $activationCode;
		$fields["status"] = $status;
		return $db->insert("profile", $fields, $nullableFields);
	}

	public function update($name,$userId,$activationCode,$status)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["name"] = $name;
		$fields["user_id"] = $userId;
		$nullableFields["user_id"] = $userId;
		$fields["activation_code"] = $activationCode;
		$fields["status"] = $status;
		$db->update("profile", "profile_id", $this->_profileId, $fields, $nullableFields);
	}

	public static function deleteFromDB( $profileId )
	{
		$queryString = "DELETE FROM profile WHERE profile_id = $profileId ";
		$db = new DBManager("piqpo");
		$db->query($queryString);
	}

	function Profile($profileId,$name,$userId,$activationCode,$status)
	{
		$this->_profileId = $profileId;
		$this->_name = $name;
		$this->_userId = $userId;
		$this->_activationCode = $activationCode;
		$this->_status = $status;
	}

	function profileId()
	{
		return $this->_profileId;
	}

	function name()
	{
		return $this->_name;
	}

	function userId()
	{
		return $this->_userId;
	}

	function activationCode()
	{
		return $this->_activationCode;
	}

	function status()
	{
		return $this->_status;
	}

	private $_profileId;
	private $_name;
	private $_userId;
	private $_activationCode;
	private $_status;
}
?>
