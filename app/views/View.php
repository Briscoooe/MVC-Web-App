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
		$newConcertErrorMessage = $this->model->newConcertErrorMessage;
		
		$loginBox = "";
		$authenticationErrorMessage = "";
		$rightBox = "";
		
		if ($this->model->loginStatusString != null) {
			$loginBox = "<a href='index.php?action=logout'>" . $this->model->loginStatusString . "</a>";
			// list of options available to logged in user
			$addConcertForm = file_get_contents("templates/insert_new_concert_form.php") ;
			$rightBox = $this->model->rightBox;


			$confirmationMessage = "";
			if (! isset ( $this->model->hasNewConcertFailed )) {
				$rightBox = $addConcertForm;
			} else if ($this->model->hasNewConcertFailed) {
				$rightBox = $newConcertErrorMessage . $addConcertForm;
			} else if ($this->model->hasNewConcertFailed == false) {
				$confirmationMessage = "<div class='alert alert-success'>" . $this->model->newConcertConfirmation . "</div>";
				$rightBox = $confirmationMessage . $addConcertForm;
			}
			
			$usersConcertslist = "<h2>Your Concerts</h2>
			<table class='table table-striped'>
				<thead>
					<tr>
						<th>Artist Name</th>
						<th>Venue</th>
						<th>Date</th>
						<th> </th>
					</tr>
				</thead>
			<tbody>";
			foreach ($this->model->usersConcerts as $row) {
				$usersConcertslist .= "<tr><th>" . $row ["cname"] . "</th>";
				$usersConcertslist .= "<th>" . $row["cvenue"] . "</th>";
				$usersConcertslist .= "<th>" . $row["cdate"] . "</th>";
				$usersConcertslist .= '<th>';
			}

			$usersConcertslist = $usersConcertslist . "</tbody></table>";
			$middleBox = $usersConcertslist;
		} 

		else {
			$authenticationErrorMessage = "";
			if ($this->model->hasAuthenticationFailed)
				$authenticationErrorMessage = $this->model->authenticationErrorMessage;
			
			$loginBox = file_get_contents ( "templates/login_form.php", FILE_USE_INCLUDE_PATH );
			$rightBox = $this->model->rightBox;

			$popularConcertsList = "<h2>Popular Concerts</h2>
			<table class='table table-striped'>
				<thead>
					<tr>
						<th>Artist Name</th>
						<th>Venue</th>
						<th>Date</th>
					</tr>
				</thead>
			<tbody>";
			foreach ($this->model->popularConcerts as $row) {
				$popularConcertsList .= "<tr><th>" . $row ["cname"] . "</th>";
				$popularConcertsList .= "<th>" . $row["cvenue"] . "</th>";
				$popularConcertsList .= "<th>" . $row["cdate"] . "</th>";
			}

			$popularConcertsList = $popularConcertsList . "</tbody></table>";

			$middleBox = $popularConcertsList;
			
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