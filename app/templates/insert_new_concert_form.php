<h2>Add a new concert</h2>
<form action="index.php" method="post">
	<fieldset>
		<input id='action' type='hidden' name='action' value='insertNewConcert' />
		<p>
			<label for="cName">Artist Name</label> 
			<input type="text" id="cName" name="cName" placeholder="Artist Name" maxlength="50" required />
		</p>
		<p>
			<label for="cVenue">Venue</label> 
			<input type="text" id="cVenue" name="cVenue" placeholder="Venue" maxlength="50" required />
		</p>
		<p>
			<label for="cDate">Date</label> 
			<input type="date"id="cDate" name="cDate" placeholder="Date" maxlength="50" required />
		</p>
		
		<p>
		<div class="form-group">
			<div class="controls">
				<button type="submit" class="btn btn-success">Add concert</button>
			</div>
		</div>
		</p>
	</fieldset>
</form>