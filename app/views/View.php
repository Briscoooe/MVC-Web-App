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

			$bottomBox = "<h4>Once created, the concert will appear on the left hand list. Click 'Add' beside it to add
			it to your list.</h4>";


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
				$usersConcertslist .= "<tr><form action='index.php' method='post'>";
				$usersConcertslist .= "<input id='action' type='hidden' name='action' value='pressButton'/>";
				$usersConcertslist .= "<th>" . $row ["cname"] . "</th>";
				$usersConcertslist .= "<th>" . $row["cvenue"] . "</th>";
				$usersConcertslist .= "<th>" . $row["cdate"] . "</th>";
				$usersConcertslist .= "<input type='hidden' id='cID' name='cID' value='" . $row["concertID"] . "'/>";
				$usersConcertslist .= "<input type='hidden' id='cname' name='cname' value='" . $row["cname"] . "'/>";
				$usersConcertslist .= "<input type='hidden' id='cvenue' name='cvenue' value='" . $row["cvenue"] . "'/>";
				$usersConcertslist .= "<input type='hidden' id='cdate' name='cdate' value='" . $row["cdate"] . "'/>";
				$usersConcertslist .= "<th><input type='submit' value='Edit' class='btn btn-info' name='editConcert' /></th></form>";
			}

			$usersConcertslist = $usersConcertslist . "</tbody></table>";
			$middleBox = $usersConcertslist;

			if (! isset ( $this->model->editButtonPressed )) {
				$middleBox = $usersConcertslist;
			} else if ($this->model->editButtonPressed) {
				$cID = $_POST['cID'];
				$cName = $_POST['cname'];
				$cVenue = $_POST['cvenue'];
				$cDate = $POST['cdate'];
				$editConcertForm = '<h2>Edit a concert</h2>
				<form action="index.php" method="post">
					<fieldset>
						<input id="action" type="hidden" name="action" value="editConcert" />
						<p>
							<label for="cName">Artist Name</label> 
							<input type="text" id="cName" name="cName" placeholder="Artist Name" maxlength="50" value="' . $cName . '" required />
						</p>
						<p>
							<label for="cVenue">Venue</label> 
							<input type="text" id="cVenue" name="cVenue" placeholder="Venue" maxlength="50" value="' . $cVenue . '" required />
						</p>
						<p>
							<label for="cDate">Date</label> 
							<input type="date"id="cDate" name="cDate" placeholder="Date" maxlength="50" value="' . $cDate . '" required />
						</p>
							<input type="hidden" id="cID" name="cID" value="' . $cID . '"/>
						<p>
						<div class="form-group">
							<div class="controls">
								<button type="submit" class="btn btn-success">Done editing</button>
							</div>
						</div>
						</p>
					</fieldset>
				</form>';
				$middleBox = $newConcertErrorMessage . $editConcertForm;
			} else if ($this->model->editButtonPressed == false) {
				$confirmationMessage = "<div class='alert alert-success'>" . $this->model->editConcertConfirmation . "</div>";
				$middleBox = $confirmationMessage . $usersConcertslist;
			}
			
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