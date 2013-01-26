<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class Device
{
	public static function loadSingleFromDB( $deviceId )
	{
		$queryArray = array( "device_id" => $deviceId );
		$result = Device::loadFromDB($queryArray);
		return (count($result) == 1) ? $result[0] : null;
	}

	public static function loadAllFromDB()
	{
		$queryArray = array( );
		return self::loadFromDB($queryArray);
	}

	public static function loadFromDB( $queryArray, $orderByArray = array())
	{
		$queryString = "SELECT * FROM device ";
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
			$deviceId = $myrow["device_id"];
			$guid = $myrow["guid"];
			$userId = $myrow["user_id"];
			$deviceType = $myrow["device_type"];
			$profileId = $myrow["profile_id"];
			$deviceName = $myrow["device_name"];
			$output[] = new Device($deviceId,$guid,$userId,$deviceType,$profileId,$deviceName);
		}
		return $output;
	}

	public static function create($guid,$userId,$deviceType,$profileId,$deviceName)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["guid"] = $guid;
		$fields["user_id"] = $userId;
		$fields["device_type"] = $deviceType;
		$fields["profile_id"] = $profileId;
		$fields["device_name"] = $deviceName;
		return $db->insert("device", $fields, $nullableFields);
	}

	public function update($guid,$userId,$deviceType,$profileId,$deviceName)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["guid"] = $guid;
		$fields["user_id"] = $userId;
		$fields["device_type"] = $deviceType;
		$fields["profile_id"] = $profileId;
		$fields["device_name"] = $deviceName;
		$db->update("device", "device_id", $this->_deviceId, $fields, $nullableFields);
	}

	public static function deleteFromDB( $deviceId )
	{
		$queryString = "DELETE FROM device WHERE device_id = $deviceId ";
		$db = new DBManager("piqpo");
		$db->query($queryString);
	}

	function Device($deviceId,$guid,$userId,$deviceType,$profileId,$deviceName)
	{
		$this->_deviceId = $deviceId;
		$this->_guid = $guid;
		$this->_userId = $userId;
		$this->_deviceType = $deviceType;
		$this->_profileId = $profileId;
		$this->_deviceName = $deviceName;
	}

	function deviceId()
	{
		return $this->_deviceId;
	}

	function guid()
	{
		return $this->_guid;
	}

	function userId()
	{
		return $this->_userId;
	}

	function deviceType()
	{
		return $this->_deviceType;
	}

	function profileId()
	{
		return $this->_profileId;
	}

	function deviceName()
	{
		return $this->_deviceName;
	}

	private $_deviceId;
	private $_guid;
	private $_userId;
	private $_deviceType;
	private $_profileId;
	private $_deviceName;
}
?>
