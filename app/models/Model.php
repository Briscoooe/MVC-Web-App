<?php
include_once './conf/config.inc.php';
include_once './db/DAO_factory.php';
include_once 'validation_factory.php';
include_once 'authentication_factory.php';
class Model {
	public $DAO_Factory, $validationFactory, $authenticationFactory; // factories
	private $usersDAO; // DAOs
	public $appName = "", $introMessage = "", $loginStatusString = "", $rightBox = "", $signUpConfirmation=""; // strings
	public $newUserErrorMessage = "", $authenticationErrorMessage = "";	//error messages
	public $hasAuthenticationFailed = false, $hasRegistrationFailed=null;	//control variables
	
	
	public function __construct() {
		$this->DAO_Factory = new DAO_Factory ();
		$this->DAO_Factory->initDBResources ();
		$this->usersDAO = $this->DAO_Factory->getUsersDAO ();
		$this->authenticationFactory = new authentication_factory ( $this->usersDAO );
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
	public function insertNewUser($username, $hashedPassword) {
		return ($this->usersDAO->insertNewUser ( $username, $hashedPassword ));
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