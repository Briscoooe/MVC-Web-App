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
				case "removeFromList" :
					$this->removeFromList ($parameters);
					break;
				default :
					break;
			}
			
			$this->updateHeader ();
			$this->model->prepareIntroMessage ();
			$this->model->prepareUsersConcerts ();
			$this->model->preparePopularConcerts ();
			$this->model->setEditConcertConfirmationMessage();
		}
		
		/**
		 * Validate the input parameters, and if successful, and user does not exist,
		 * insert the new user in the database
		 *
		 * @param : $parameters - the $_REQUEST super global array. This contains: 
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
		* Validate input. Once validated, Add a new concert to the database
		*
		* @param : $parameters - the $_REQUEST super global array. This contains: 
		* 			- An array containing the parameters to be added into the concert entry
		*			- These are the name, venue, date and the ID of the user logged in
		*/
		function insertNewConcert($parameters) {
			$concertName = $parameters ["cName"];
			$concertVenue = $parameters ["cVenue"];
			$concertDate = $parameters ["cDate"];
			$uID = $this->model->authenticationFactory->getIDLoggedIn();
			
			if (! empty ( $concertName ) && ! empty ( $concertVenue ) && ! empty ( $concertDate )){
				if ($this->model->validationFactory->isLengthStringValid ( $concertName, CONCERT_NAME_MAX_LENGTH ) && $this->model->validationFactory->isLengthStringValid ( $concertVenue, CONCERT_VENUE_MAX_LENGTH ) && $this->model->validationFactory->isDateValid($concertDate)) {
					if (! $this->model->authenticationFactory->hasUserAttended ( $concertName, $concertVenue, $concertDate, $uID)){
						if (! $this->model->authenticationFactory->isConcertExisting ( $concertName, $concertVenue, $concertDate )){					
							$this->model->insertNewConcert($concertName, $concertVenue, $concertDate, $uID);
							$this->model->hasNewConcertFailed = false;
							$this->model->setConcertConfirmationMessage();
							return (true);
						} else
							$this->model->newConcertError(NEW_CONCERT_FORM_EXISTING_ERROR_STR);
					} else
						$this->model->newConcertError(NEW_CONCERT_FORM_ALREADY_ATTENDED_ERROR_STR);

				} else
					$this->model->newConcertError(NEW_USER_FORM_ERRORS_STR);

			} else
				$this->model->newConcertError(NEW_USER_FORM_ERRORS_COMPULSORY_STR);

			$this->model->hasNewConcertFailed = true;
			$this->model->setConcertConfirmationMessage ();
			return (false);
		}


		/**
		* Validate input. Once validated, Add a new entry to the concertsAttended table
		* which is simply the ID of the user logged in, and the concert of the ID they
		* wish to add to their list
		*
		* @param : $parameters - the $_REQUEST super global array. This contains: 
		*			- The concert ID passed in through the form
		*
		*/
		function addToUserList($parameters) {
			$concertID = $parameters['cID'];
			$uID = $this->model->authenticationFactory->getIDLoggedIn();

			if (! $this->model->authenticationFactory->hasUserAttended($uID, $concertID)) {
				$this->model->addToExistingConcert($uID, $concertID);
				$this->model->hasConcertFailed = false;
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
		* Get the list of concerts that the user who is logged has attended
		*/
		function getUsersConcerts() {
			$uID = $this->model->authenticationFactory->getIDLoggedIn();
			$this->model->getUsersConcerts($uID);
		}
		
		/**
		 * Validate the input parameters, and if successful, authenticate the user.
		 * If authentication process is ok, login the user.
		 *
		 * @param : $parameters - the $_REQUEST super global array. This contains: 
		 *			- The username and password supplied by the user
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

		/**
		* Press the button that changes the middlebox in the view to a new form 
		*
		* For this function there is absolutely no user input so I didn't feel validation was necessary
		*/
		function pressButton(){
			$this->model->editButtonPressed = true;
		}

		/**
		 * Remove the concert from the users list of concerts and revert the middle box back
		 * to the list of users concerts.
		 *
		 * For this function there is absolutely no user input so I didn't feel validation was necessary
		 *
		 * @param : $parameters - the $_REQUEST super global array. This contains: 
		 *        	- The ID of the concert that the user wishes to remove from their attending list
		 */
		function removeFromList($parameters) {
			$cID = $parameters['cID'];
			$uID = $this->model->authenticationFactory->getIDLoggedIn();
			$this->model->removeFromList($uID, $cID);
			$this->model->editButtonPressed = false;
			$this->model->hasEditConcertFailed = false;
			$this->model->removedFromList = true;
			$this->model->setRemoveConcertConfirmationMessage();
		}

		/**
		 * Validate input. Once validated, Update the row in the table, that matches the rowID sent by
		 * the form, to contain the information newly inserted by the user
		 *
		 * @param : $parameters - the $_REQUEST super global array. This contains: 
		 *        	- The name of the concert entered by the user
		 *			- The venye of the concert entered by the user
		 *			- The date of the concert entered by the user
		 *			- The ID of the concert sent by the form
		 */
		function editConcert($parameters) {
			$concertName = $parameters["cName"];
			$concertVenue = $parameters["cVenue"];
			$concertDate = $parameters["cDate"];
			$CID = $parameters["cID"];

			if (! empty ( $concertName ) && ! empty ( $concertVenue ) && ! empty ( $concertDate )) {
				if ($this->model->validationFactory->isLengthStringValid ( $concertName, CONCERT_NAME_MAX_LENGTH ) && $this->model->validationFactory->isLengthStringValid ( $concertVenue, CONCERT_VENUE_MAX_LENGTH ) && $this->model->validationFactory->isDateValid($concertDate)) {
					$this->model->editConcert($concertName, $concertVenue, $concertDate , $CID);
					$this->model->editButtonPressed = false;
					$this->model->hasEditConcertFailed = false;
					$this->model->setEditConcertConfirmationMessage ();
					return (true);
				} else
					$this->model->editConcertError(NEW_USER_FORM_ERRORS_STR);
			} else
				$this->model->editConcertError(NEW_USER_FORM_ERRORS_COMPULSORY_STR);

			$this->model->hasEditConcertFailed = true;
			$this->model->editButtonPressed = false;
			$this->model->setEditConcertConfirmationMessage ();
			return (false);
		}

		/**
		* Logout the user by destroying their session
		*/
		function logoutUser() {
			$this->model->logoutUser ();
		}
		function updateHeader() {
			if ($this->model->isUserLoggedIn ())
				$this->model->updateLoginStatus ();
		}	
	}
?>