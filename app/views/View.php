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
		$newUserErrorMessage = $this->model->newUserErrorMessage;
		$newConcertErrorMessage = $this->model->newConcertErrorMessage;
		
		$loginBox = "";
		$authenticationErrorMessage = "";
		$rightBox = "";
		$bottomBox= "";
		
		if ($this->model->loginStatusString != null) {
			$popularConcertsList = "<h2>Popular Concerts</h2>";
			$tableBeginning = file_get_contents("templates/table_beginning.php");
			$popularConcertsList .= $tableBeginning;
			
			/*
			foreach ($this->model->popularConcerts as $row) {
				$popularConcertsList .= "<form method='post' action='index.php'><input id='action' type='hidden' value='addToUserList'/><tr><th>" . $row ["cname"] . "</th>";
				$popularConcertsList .= "<th>" . $row["cvenue"] . "</th>";
				$popularConcertsList .= "<th>" . $row["cdate"] . "</th>";
				$popularConcertsList .=	"<th><input type='text' id='addCID' name='addCID'/>";
				$popularConcertsList .= "<button type='submit' class='btn btn-primary'>Add</button></th></form>";
			}*/

			foreach ($this->model->popularConcerts as $row) {
				$popularConcertsList .= "<tr><form action='index.php' method='post'>";
				$popularConcertsList .= "<input id='action' type='hidden' name='action' value='addToUserList'/>";
				$popularConcertsList .= "<th>" . $row ["cname"] . "</th>";
				$popularConcertsList .= "<th>" . $row["cvenue"] . "</th>";
				$popularConcertsList .= "<th>" . $row["cdate"] . "</th>";
				$popularConcertsList .= "<th><input type='hidden' id='cID' name='cID' value='" . $row["concertID"] . "'/></th>";
				$popularConcertsList .= "<th><input type='submit' value='Add' class='btn btn-primary'/></th></form>";
			}

			$popularConcertsList = $popularConcertsList . "</tbody></table>";

			$leftBox = $popularConcertsList;

			$loginBox = "<a href='index.php?action=logout'>" . $this->model->loginStatusString . "</a>";
			// list of options available to logged in user
			$addConcertForm = file_get_contents("templates/insert_new_concert_form.php") ;
			$rightBox = $this->model->rightBox;

			$bottomBox = "<h2>Add an existing concert to your list</h2>";


			$confirmationMessage = "";
			if (! isset ( $this->model->hasNewConcertFailed )) {
				$rightBox = $addConcertForm;
			} else if ($this->model->hasNewConcertFailed) {
				$rightBox = $newConcertErrorMessage . $addConcertForm;
			} else if ($this->model->hasNewConcertFailed == false) {
				$confirmationMessage = "<div class='alert alert-success'>" . $this->model->newConcertConfirmation . "</div>";
				$rightBox = $confirmationMessage . $addConcertForm;
			}
			
			$usersConcertslist = "<h2>Your Concerts</h2>";
			$tableBeginning = file_get_contents("templates/table_beginning.php");
			$usersConcertslist .= $tableBeginning;

			foreach ($this->model->usersConcerts as $row) {
				$usersConcertslist .= "<tr><th>" . $row ["cname"] . "</th>";
				$usersConcertslist .= "<th>" . $row["cvenue"] . "</th>";
				$usersConcertslist .= "<th>" . $row["cdate"] . "</th>";
			}

			$usersConcertslist = $usersConcertslist . "</tbody></table>";
			$middleBox = $usersConcertslist;
		} 

		else {
			$leftBox = $this->model->introMessage;
			$authenticationErrorMessage = "";
			if ($this->model->hasAuthenticationFailed)
				$authenticationErrorMessage = $this->model->authenticationErrorMessage;
			
			$loginBox = file_get_contents ( "templates/login_form.php", FILE_USE_INCLUDE_PATH );
			$rightBox = $this->model->rightBox;

			$popularConcertsList = "<h2>Popular Concerts</h2>";
			$tableBeginning = file_get_contents("templates/table_beginning.php");
			$popularConcertsList .= $tableBeginning;

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