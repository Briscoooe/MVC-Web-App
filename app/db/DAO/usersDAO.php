<?php
require_once("dao.php");
class usersDAO extends BaseDAO {
	function messagesDAO($dbMng) {
		parent::BaseDAO($dbMng);
	}
	
	public function isUserExisting($username){
		$sqlQuery = "SELECT count(*) as isExisting ";
		$sqlQuery .= "FROM users ";
		$sqlQuery .= "WHERE name='$username' ";		
		$result = $this->getDbManager()->executeSelectQuery($sqlQuery);
		
		if ($result[0]["isExisting"] == 1) return (true);
		else return (false);
	}
	
	public function getUserId($username){
		$sqlQuery = "SELECT id ";
		$sqlQuery .= "FROM users ";
		$sqlQuery .= "WHERE name='$username' ";	
					
		$result = $this->getDbManager()->executeSelectQuery($sqlQuery);
			
		if (!empty($result[0]["id"])) return ($result[0]["id"]);
		else return (false);
	}
	
	public function getUserPasswordDigest($username) {
		$sqlQuery = "SELECT password ";
		$sqlQuery .= "FROM users ";
		$sqlQuery .= "WHERE name='$username'";
		
		$result = $this->getDbManager()->executeSelectQuery($sqlQuery);
		
		if ($result != NULL) return $result[0]["password"];
		return (NULL);
	}
	
	public function insertNewUser($username, $passwordHash) {
		$sqlQuery = "INSERT INTO users (name, password) ";
		$sqlQuery .= "VALUES ('$username', '$passwordHash') ";
		$result = $this->getDbManager()->executeQuery($sqlQuery);
		return $result;
	}
}
?>