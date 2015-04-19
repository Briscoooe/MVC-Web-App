<?php
class authentication_factory {
	private $usersDAO, $concertsDAO;
	public function __construct($usersDAO, $concertsDAO) {
		$this->usersDAO = $usersDAO;
		$this->concertsDAO = $concertsDAO;
	}

	public function isUserExisting($username) {
		return ($this->usersDAO->isUserExisting ( $username ));
	}

	public function insertNewUser($username, $password) {
		$hashedPassword = hash ( "sha1", $password );
		return ($this->usersDAO->insertNewUser ( $username, $hashedPassword ));
	}

	public function isConcertExisting($concertName, $concertVenue, $concertDate){
		return ($this->concertsDAO->isConcertExisting($concertName, $concertVenue ,$concertDate));
	}

	public function hasUserAttended($uID, $cID){
		return ($this->concertsDAO->hasUserAttended($uID, $cID));
	}

	public function getHashValue($string) {
		return (hash ( "sha1", $string ));
	}

	public function loginUser($userId, $username) {
		$_SESSION ['user_id'] = $userId;
		$_SESSION ['username'] = $username;
	}

	public function isUserLoggedIn() {
		return (! empty ( $_SESSION ['user_id'] ));
	}

	public function getUsernameLoggedIn() {
		if ($this->isUserLoggedIn ())
			return $_SESSION ['username'];
		
		return (null);
	}

	# I created this function to store the user ID in the new concert entry
	# as adding a varchar type username rather than int type ID would use
	# a lot more database space in a large scale operation
	public function getIDLoggedIn() {
		if ($this->isUserLoggedIn ())
			return $_SESSION ['user_id'];

		return (null);
	}
	public function logoutUser() {
		unset ( $_SESSION ['user_id'] );
		unset ( $_SESSION ['username'] );
		session_destroy ();
	}
}
?>