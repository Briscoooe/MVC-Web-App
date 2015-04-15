<?php
class View {
	private $model;
	private $controller;
	public function __construct($controller, $model) {
		$this->controller = $controller;
		$this->model = $model;
	}
	public function output() {
		// set variables up from the model (for the template)
		$appName = $this->model->appName;
		$introMessage = $this->model->introMessage;
		$newUserErrorMessage = $this->model->newUserErrorMessage;
		
		$loginBox = "";
		$authenticationErrorMessage = "";
		$rightBox = "";
		
		if ($this->model->loginStatusString != null) {
			$loginBox = "<a href='index.php?action=logout'>" . $this->model->loginStatusString . "</a>";
			// list of options available to logged in user
			$rightBox = file_get_contents("templates/insert_new_concert_form.php") ;
			
			$usersConcertslist = "<h2>Your Concerts</h2>";
			foreach ($this->model->usersConcerts as $row)
				$usersConcertslist .= "<li><strong>" . $row ["cname"] . " @ " . $row["cvenue"] . "</strong></li>";
			$usersConcertslist = "<ul>" . $usersConcertslist . "</ul>";
		} 

		else {
			$authenticationErrorMessage = "";
			if ($this->model->hasAuthenticationFailed)
				$authenticationErrorMessage = $this->model->authenticationErrorMessage;
			
			$loginBox = file_get_contents ( "templates/login_form.php", FILE_USE_INCLUDE_PATH );
			$rightBox = $this->model->rightBox;

			$popularConcertsList = "<h2>Popular Concerts<h2>";
			foreach ($this->model->popularConcerts as $row)
				$popularConcertsList .= "<h3><li><strong>" . $row ["cname"] . "</strong></li></h3>";
			$popularConcertsList = "<ul>" . $popularConcertsList . "</ul>";
			
			$registrationForm = file_get_contents ( './templates/insert_new_user_form.php' );
			
			$confirmationMessage = "";
			if (! isset ( $this->model->hasRegistrationFailed )) {
				$rightBox = $registrationForm;
			} else if ($this->model->hasRegistrationFailed) {
				$rightBox = $newUserErrorMessage . $registrationForm;
			} else if ($this->model->hasRegistrationFailed == false) {
				$confirmationMessage = "<div class='alert alert-success'>" . $this->model->signUpConfirmation . "</div>";
				$rightBox = $confirmationMessage;
			}
		}
		
		include_once ("templates/template_index.php");
	}
}
?>