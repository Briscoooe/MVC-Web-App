<?php
require_once("dao.php");
class concertsDAO extends BaseDAO {
	function messagesDAO($dbMng) {
		parent::BaseDAO($dbMng);
	}

	public function isConcertExisting($concertName, $concertVenue, $concertDate){
		$sqlQuery = "SELECT count(*) as isExisting ";
		$sqlQuery .= "FROM concerts ";
		$sqlQuery .= "WHERE cname='$concertName'";
		$sqlQuery .= "AND cvenue='$concertVenue'";
		$sqlQuery .= "AND cdate='$concertDate';";	
		$result = $this->getDbManager()->executeSelectQuery($sqlQuery);
		
		if ($result[0]["isExisting"] == 1) return (true);
		else return (false);
	}

	public function hasUserAttended($uID, $cID) {
		$sqlQuery = "SELECT count(*) as isExisting ";
		$sqlQuery .= "FROM concertsAttended ";
		$sqlQuery .= "WHERE uID='$uID'";
		$sqlQuery .= "AND concertID='$cID';";
		$result = $this->getDbManager()->executeSelectQuery($sqlQuery);
		
		if ($result[0]["isExisting"] == 1) return (true);
		else return (false);
	}
	
	public function insertNewConcert($concertName, $concertVenue, $concertDate, $uID) {
		$sqlQuery = "INSERT INTO concerts (cname, cvenue, cdate) ";
		$sqlQuery .= "VALUES ('$concertName', '$concertVenue', '$concertDate'); ";
		$result = $this->getDbManager()->executeQuery($sqlQuery);

		return $result;
	}

	function getPopularConcerts() {
		$sqlQuery = "SELECT * ";
		$sqlQuery .= "FROM concerts; ";
		
		$result = $this->getDbManager ()->executeSelectQuery ( $sqlQuery );
		
		return $result;
	}

	function getUsersConcerts($uID) {
		$sqlQuery = "SELECT * ";
		$sqlQuery .= "FROM concerts ";
		$sqlQuery .= "LEFT JOIN concertsAttended ON ";
		$sqlQuery .= "concerts.concertID = concertsAttended.concertID ";
		$sqlQuery .= "LEFT JOIN users ON ";
		$sqlQuery .= "concertsAttended.uID = users.id ";
		$sqlQuery .= "WHERE concertsAttended.uID = '$uID';";
		
		$result = $this->getDbManager ()->executeSelectQuery ( $sqlQuery );
		
		return $result;
	}

	function getConcertInfo($cID){
		$sqlQuery = "SELECT * ";
		$sqlQuery .= "FROM concerts ";
		$sqlQuery .= "WHERE concertID = '$cID';";
		$result = $this->getDbManager ()->executeSelectQuery ( $sqlQuery );
		
		return $result;
	}

	function addToExistingConcert($uID, $cID) {
		$sqlQuery = "INSERT INTO concertsAttended ";
		$sqlQuery .= "(uID, concertID) VALUES ";
		$sqlQuery .= "('$uID', '$cID');";

		$result = $this->getDbManager ()->executeQuery( $sqlQuery );

		return $result;
	} 

	function editConcert($concertName, $concertVenue, $concertDate, $cID){
		$sqlQuery = "UPDATE concerts ";
		$sqlQuery .= "SET cname='$concertName', cvenue='$concertVenue', cdate='$concertDate' ";
		$sqlQuery .= "WHERE concertID='$cID';";

		$result = $this->getDbManager ()->executeQuery( $sqlQuery );

		return $result;
	}

	function removeFromList($uID, $cID) {
		$sqlQuery = "DELETE FROM concertsAttended ";
		$sqlQuery .= "WHERE uID = '$uID' ";
		$sqlQuery .= "AND concertID = '$cID'";

		$result = $this->getDbManager ()->executeQuery ($sqlQuery);

		return $result;
	}

	/*

	remove concert from users list
	1. delete * from concerts where cID = x AND uID = y

	executeQuery NOT executeSelectQuery
	*/

}
?>