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
				default :
					break;
			}
			
			$this->model->prepareIntroMessage ();
			$this->updateHeader ();
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

		function insertNewConcert($parameters) {
			$concertName = $parameters ["cName"];
			$concertVenue = $parameters ["cVenue"];
			$concertDate = $parameters ["cDate"];
			
			if (! empty ( $concertName ) && ! empty ( $concertVenue ) && ! empty ( $concertDate )) {					
				$this->model->insertNewConcert ($concertName, $concertVenue, $concertDate);
			}
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
		function logoutUser() {
			$this->model->logoutUser ();
		}
		function updateHeader() {
			if ($this->model->isUserLoggedIn ())
				$this->model->updateLoginStatus ();
		}	
	}
	?>