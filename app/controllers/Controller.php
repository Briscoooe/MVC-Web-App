 <?php
	class Controller {
		private $model;
		public function __construct($model, $action = null, $parameters) {
			$this->model = $model;
			switch ($action) {
				case "insertNewUser" :
					$this->insertNewUser ( $parameters );
					break;
				case "loginUser" :
					$this->loginUser ( $parameters );
					break;
				case "logout" :
					$this->logoutUser ();
					break;
				case "insertNewConcert" :
					$this->insertNewConcert ($parameters);
					break;
				case "getUsersConcerts" :
					$this->getUsersConcerts ();
					break;
				case "addToUserList" :
					$this->addToUserList ($parameters);
					break;
				case "getConcertInfo" :
					$this->getConcertInfo ($parameters);
					break;
				case "editConcert" :
					$this->editConcert ($parameters);
					break;
				case "pressButton" :
					$this->pressButton ();
					break;
				default :
					break;
			}
			
			$this->updateHeader ();
			$this->model->prepareIntroMessage ();
			$this->model->prepareUsersConcerts ();
			$this->model->preparePopularConcerts ();
		}
		
		/**
		 * Validate the input parameters, and if successful, and user does not exist,
		 * insert the new user in the database
		 *
		 * @param : $parameters
		 *        	- array containing the parameters to be validated
		 */
		function insertNewUser($parameters) {
			$email = $parameters ["fEmail"];
			$username = $parameters ["fUsername"];
			$password = $parameters ["fPassword"];
			
			if (! empty ( $username ) && ! empty ( $password ) && ! empty ( $email )) {
				if ($this->model->validationFactory->isLengthStringValid ( $username, NEW_USER_FORM_MAX_USERNAME_LENGTH ) && $this->model->validationFactory->isLengthStringValid ( $password, NEW_USER_FORM_MAX_PASSWORD_LENGTH ) && $this->model->validationFactory->isEmailValid ( $email )) {
					
					if (! $this->model->authenticationFactory->isUserExisting ( $username )) {
						$hashedPassword = $this->model->authenticationFactory->getHashValue ( $password );
						if ($this->model->insertNewUser ( $username, $hashedPassword )) {
							$this->model->hasRegistrationFailed = false;
							$this->model->setConfirmationMessage();
							return (true);
						}
					} else
						$this->model->setUpNewUserError ( NEW_USER_FORM_EXISTING_ERROR_STR );
				} else
					$this->model->setUpNewUserError ( NEW_USER_FORM_ERRORS_STR );
			} else
				$this->model->setUpNewUserError ( NEW_USER_FORM_ERRORS_COMPULSORY_STR );
			
			$this->model->hasRegistrationFailed = true;
			$this->model->updateLoginErrorMessage ();
			return (false);
		}

		/**
		* Ensure that all fields have been filled and add the information in the fields
		* to the concert table in the database
		*
		* @param : $parameters
		* 			- An array containing the parameters to be added into the concert entry
		*			- These are the name, venue, date and the ID of the user logged in
		*/
		function insertNewConcert($parameters) {
			$concertName = $parameters ["cName"];
			$concertVenue = $parameters ["cVenue"];
			$concertDate = $parameters ["cDate"];
			$uID = $this->model->authenticationFactory->getIDLoggedIn();
			
			if (! empty ( $concertName ) && ! empty ( $concertVenue ) && ! empty ( $concertDate )){
				if (! $this->model->authenticationFactory->hasUserAttended ( $concertName, $concertVenue, $concertDate, $uID)){
					if (! $this->model->authenticationFactory->isConcertExisting ( $concertName, $concertVenue, $concertDate )){					
						$this->model->insertNewConcert($concertName, $concertVenue, $concertDate, $uID);
						$this->model->hasNewConcertFailed = false;
						$this->model->setConcertConfirmationMessage();
						return (true);
					} else {
						$this->model->newConcertError(NEW_CONCERT_FORM_EXISTING_ERROR_STR);
					}	
				} else {
					$this->model->newConcertError(NEW_CONCERT_FORM_ALREADY_ATTENDED_ERROR_STR);

				}
			}

			$this->model->hasNewConcertFailed = true;
			$this->model->setConcertConfirmationMessage ();
			return (false);
		}

		function addToUserList($parameters) {
			$concertID = $parameters['cID'];
			$uID = $this->model->authenticationFactory->getIDLoggedIn();

			if (! $this->model->authenticationFactory->hasUserAttended($uID, $concertID)) {
				$this->model->addToExistingConcert($uID, $concertID);
				$this->model->hasNewConcertFailed = false;
				$this->model->setConcertConfirmationMessage();
				return (true);
			} else {
				$this->model->newConcertError(NEW_CONCERT_FORM_ALREADY_ATTENDED_ERROR_STR);
			}

			$this->model->hasNewConcertFailed = true;
			$this->model->setConcertConfirmationMessage ();
			return (false);
		}

		/**
		* Get the list of concerts that the user who is logged in has entered into the database
		*/
		function getUsersConcerts() {
			$uID = $_SESSION ['user_id'];
			$this->model->getUsersConcerts($uID);
		}
		
		/**
		 * Validate the input parameters, and if successful, authenticate the user.
		 * If authentication process is ok, login the user.
		 *
		 * @param : $parameters
		 *        	- array containing the parameters to be validated. 
		 *        This is the $_REQUEST super global array.
		 */
		function loginUser($parameters) {
			$username = $parameters ["fUser"];
			$password = $parameters ["fPassword"];
			
			if (! (empty ( $username ) && empty ( $password ))) {
				if ($this->model->validationFactory->isLengthStringValid ( $username, NEW_USER_FORM_MAX_USERNAME_LENGTH ) && $this->model->validationFactory->isLengthStringValid ( $password, NEW_USER_FORM_MAX_PASSWORD_LENGTH )) {
					
					$databaseHashedPassword = $this->model->getUserPasswordDigest ( $username );
					$userHashedPassword = $this->model->authenticationFactory->getHashValue ( $password );
					if ($databaseHashedPassword == $userHashedPassword) {
						$userId = $this->model->getUserId ( $username );
						$this->model->loginUser ( $userId, $username );
						$this->model->updateLoginStatus ();
						$this->model->hasAuthenticationFailed = false;
						return;
					}
				}
			}
			$this->model->updateLoginErrorMessage ();
			$this->model->hasAuthenticationFailed = true;
			return;
		}

		function getConcertInfo($parameters){
			$this->model->getConcertInfo($CID);
		}

		function pressButton(){
			$this->model->editButtonPressed = true;
		}

		function editConcert($parameters) {
			$concertName = $parameters["cName"];
			$concertVenue = $parameters["cVenue"];
			$concertDate = $parameters["cDate"];
			$CID = $parameters["cID"];

			$this->model->editConcert($concertName, $concertVenue, $concertDate , $CID);
			$this->model->editButtonPressed = false;
		}

		function logoutUser() {
			$this->model->logoutUser ();
		}
		function updateHeader() {
			if ($this->model->isUserLoggedIn ())
				$this->model->updateLoginStatus ();
		}	
	}
?>