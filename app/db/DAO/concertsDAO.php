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
	
	public function insertNewConcert($concertName, $concertVenue, $concertDate, $uID) {
		$sqlQuery = "INSERT INTO concerts (cname, cvenue, cdate, uID) ";
		$sqlQuery .= "VALUES ('$concertName', '$concertVenue', '$concertDate' , '$uID'); ";
		$result = $this->getDbManager()->executeQuery($sqlQuery);
		return $result;
	}

	function getPopularConcerts() {
		$sqlQuery = "SELECT * ";
		$sqlQuery .= "FROM concerts ";
		$sqlQuery .= "GROUP BY cname ";
		$sqlQuery .= "HAVING COUNT(concertID) > 1 ";
		$sqlQuery .= "ORDER BY COUNT(*) desc;";
		
		$result = $this->getDbManager ()->executeSelectQuery ( $sqlQuery );
		
		return $result;
	}

	function getUsersConcerts($uID) {
		$sqlQuery = "SELECT * ";
		$sqlQuery .= "FROM concerts ";
		$sqlQuery .= "WHERE uID = '$uID'";
		$sqlQuery .= "ORDER BY cname; ";
		
		$result = $this->getDbManager ()->executeSelectQuery ( $sqlQuery );
		
		return $result;
	}
}
?>