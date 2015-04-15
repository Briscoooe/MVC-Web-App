<?php
/**
 * @author: luca
 * Base class for DAOs
 */
class BaseDAO {
	
	var $dbManager = null;
	
	function BaseDAO($dbMng) {
		$this->dbManager = $dbMng;	
	}
	
	function getDbManager() {
		return $this->dbManager;
	}
}

?>
