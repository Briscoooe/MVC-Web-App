<?php
require_once("dao.php");
class concertsDAO extends BaseDAO {
	function messagesDAO($dbMng) {
		parent::BaseDAO($dbMng);
	}
	
	public function insertNewConcert($concertName, $concertVenue, $concertDate, $uID) {
		$sqlQuery = "INSERT INTO concerts (cname, cvenue, cdate, uID) ";
		$sqlQuery .= "VALUES ('$concertName', '$concertVenue', '$concertDate' , '$uID'); ";
		$result = $this->getDbManager()->executeQuery($sqlQuery);
		return $result;
	}

	function getConcerts() {
		$sqlQuery = "SELECT * ";
		$sqlQuery .= "FROM concerts ";
		$sqlQuery .= "ORDER BY cname; ";
		
		$result = $this->getDbManager ()->executeSelectQuery ( $sqlQuery );
		
		return $result;
	}
}
?>