<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class User
{
	public static function loadSingleFromDB( $userId )
	{
		$queryArray = array( "user_id" => $userId );
		$result = User::loadFromDB($queryArray);
		return (count($result) == 1) ? $result[0] : null;
	}

	public static function loadAllFromDB()
	{
		$queryArray = array( );
		return self::loadFromDB($queryArray);
	}

	public static function loadFromDB( $queryArray, $orderByArray = array())
	{
		$queryString = "SELECT * FROM user ";
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
			$userId = $myrow["user_id"];
			$password = $myrow["password"];
			$email = $myrow["email"];
			$token = $myrow["token"];
			$status = $myrow["status"];
			$output[] = new User($userId,$password,$email,$token,$status);
		}
		return $output;
	}

	public static function create($password,$email,$token,$status)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["password"] = $password;
		$fields["email"] = $email;
		$fields["token"] = $token;
		$fields["status"] = $status;
		return $db->insert("user", $fields, $nullableFields);
	}

	public function update($password,$email,$token,$status)
	{
		$db = new DBManager("piqpo");
		$fields=array();
		$nullableFields=array();
		$fields["password"] = $password;
		$fields["email"] = $email;
		$fields["token"] = $token;
		$fields["status"] = $status;
		$db->update("user", "user_id", $this->_userId, $fields, $nullableFields);
	}

	public static function deleteFromDB( $userId )
	{
		$queryString = "DELETE FROM user WHERE user_id = $userId ";
		$db = new DBManager("piqpo");
		$db->query($queryString);
	}

	function User($userId,$password,$email,$token,$status)
	{
		$this->_userId = $userId;
		$this->_password = $password;
		$this->_email = $email;
		$this->_token = $token;
		$this->_status = $status;
	}

	function userId()
	{
		return $this->_userId;
	}

	function password()
	{
		return $this->_password;
	}

	function email()
	{
		return $this->_email;
	}

	function token()
	{
		return $this->_token;
	}

	function status()
	{
		return $this->_status;
	}

	private $_userId;
	private $_password;
	private $_email;
	private $_token;
	private $_status;
}
?>
