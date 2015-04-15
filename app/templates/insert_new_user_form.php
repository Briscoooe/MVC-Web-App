<h2>Sign up</h2>
<h4>It's free and always will be.</h4>
<form action="index.php" method="post">
	<fieldset>
		<input id='action' type='hidden' name='action' value='insertNewUser' />
		<p>
			<label for="fUsername">Username</label> <input type="text"
				id="fUsername" name="fUsername" placeholder="username"
				maxlength="25" required />
		</p>
		<p>
			<label for="fPassword">Password</label> <input type="password"
				id="fPassword" name="fPassword" placeholder="password"
				maxlength="25" required />
		</p>
		<p>
			<label for="fEmail">Email</label> <input type="email"
				id="fEmail" name="fEmail" placeholder="email"
				maxlength="50" required />
		</p>
		
		<p>
		<div class="form-group">
			<div class="controls">
				<button type="submit" class="btn btn-success">Sign up</button>
			</div>
		</div>
		</p>
	</fieldset>
</form>