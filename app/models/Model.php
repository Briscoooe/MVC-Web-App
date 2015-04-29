<?php
include_once './conf/config.inc.php';
include_once './db/DAO_factory.php';
include_once 'validation_factory.php';
include_once 'authentication_factory.php';
class Model {
	public $DAO_Factory, $validationFactory, $authenticationFactory; // factories
	private $usersDAO, $concertsDAO; // DAOs
	public $appName = "", $introMessage = "", $loginStatusString = "", $rightBox = "", $signUpConfirmation="", $newConcertConfirmation="", $editConcertConfirmation=""; // strings
	public $newUserErrorMessage = "", $authenticationErrorMessage = "", $newConcertErrorMessage = "", $editConcertErrorMessage="";	//error messages
	public $hasAuthenticationFailed = false, $hasRegistrationFailed=null, $hasNewConcertFailed=null, $editButtonPressed=null, $hasEditConcertFailed=null, $removedFromList=null; //control variables
	public $usersConcerts=null, $popularConcerts=null, $concertInfo=null; //Cursor variables
	
	
	public function __construct() {
		$this->DAO_Factory = new DAO_Factory ();
		$this->DAO_Factory->initDBResources ();
		$this->usersDAO = $this->DAO_Factory->getUsersDAO ();
		$this->concertsDAO = $this->DAO_Factory->getConcertsDAO ();
		$this->authenticationFactory = new authentication_factory ( $this->usersDAO, $this->concertsDAO );
		$this->validationFactory = new validation_factory ();
		$this->appName = APP_NAME;
	}

	public function loginUser($userID, $username) {
		$this->authenticationFactory->loginUser ( $userID, $username );
	}

	public function getUserPasswordDigest($username) {
		return ($this->usersDAO->getUserPasswordDigest ( $username ));
	}

	public function getUserID($username) {
		return ($this->usersDAO->getUserId ( $username ));
	}

	public function prepareIntroMessage() {
		$this->introMessage = INDEX_INTRO_MESSAGE_STR;
	}

	public function setUpNewUserError($errorString) {
		$this->newUserErrorMessage = "<div class='alert alert-error'>" . $errorString . "</div>";
	}

	public function newConcertError($concertExistsString){
		$this->newConcertErrorMessage = "<div class='alert alert-error'>" . $concertExistsString . "</div>";
	}

	public function editConcertError($editConcertErrorMessage) {
		$this->editConcertErrorMessage = "<div class='alert alert-error'>" . $editConcertErrorMessage . "</div>";
	}

	public function updateLoginStatus() {
		$this->loginStatusString = LOGIN_USER_FORM_WELCOME_STR . " " . $this->authenticationFactory->getUsernameLoggedIn () . " | " . LOGIN_USER_FORM_LOGOUT_STR;
		$this->authenticationErrorMessage = "";
	}

	public function updateLoginErrorMessage() {
		$this->authenticationErrorMessage = LOGIN_USER_FORM_AUTHENTICATION_ERROR;
		$this->loginStatusString = "";
	}

	public function setConfirmationMessage(){
		$this->signUpConfirmation = NEW_USER_FORM_REGISTRATION_CONFIRMATION_STR;
	}

	public function setConcertConfirmationMessage(){
		$this->newConcertConfirmation = NEW_CONCERT_FORM_REGISTRATION_CONFIRMATION_STR;
	}

	public function setEditConcertConfirmationMessage(){
		$this->editConcertConfirmation = EDIT_CONCERT_CONFIRMATION_STR;
	}

	public function setRemoveConcertConfirmationMessage(){
		$this->removeConcertConfirmation = EDIT_CONCERT_REMOVE_CONFIRMATION_STR;
	}

	public function getConcertInfo($CID){
		return ($this->concertsDAO->getConcertInfo($CID));
	}

	public function insertNewUser($username, $hashedPassword) {
		return ($this->usersDAO->insertNewUser ( $username, $hashedPassword ));
	}

	public function insertNewConcert($concertName, $concertVenue, $concertDate, $uID){
		return ($this->concertsDAO->insertNewConcert ($concertName, $concertVenue, $concertDate, $uID));
	}

	public function editConcert($concertName, $concertVenue, $concertDate ,$CID ) {
		return ($this->concertsDAO->editConcert($concertName, $concertVenue, $concertDate ,$CID ));
	}

	public function addToExistingConcert($uID, $cID){
		return ($this->concertsDAO->addToExistingConcert($uID, $cID));
	}

	public function prepareUsersConcerts() {
		$uID = $this->authenticationFactory->getIDLoggedIn();
		$this->usersConcerts = $this->concertsDAO->getUsersConcerts($uID);
	}

	public function prepareConcertInfo($cID) {
		$this->concertInfo = $this->concertsDAO->getConcertInfo($cID);
	}

	public function removeFromList($uID, $cID) {
		return ($this->concertsDAO->removeFromList($uID, $cID));
	}

	public function preparePopularConcerts() {
		$this->popularConcerts = $this->concertsDAO->getPopularConcerts();
	}

	public function logoutUser() {
		$this->authenticationFactory->logoutUser ();
		$this->loginStatusString = null;
		$this->authenticationErrorMessage = "";
	}

	public function isUserLoggedIn() {
		return ($this->authenticationFactory->isUserLoggedIn ());
	}

	public function __destruct() {
		$this->DAO_Factory->clearDBResources ();
	}
}
?>