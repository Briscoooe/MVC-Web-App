<!DOCTYPE html>
<?php include_once 'html_doctype_and_head.php'; ?>
<body>

	<div class="container-fluid">
		<div class="row-fluid" style="margin-top: 20px">
			<div class="span6">
				<h2><?php echo $introMessage; ?></h2>
			</div>
						<div class="span6" id="rcorners2">
				<h2>Concerts you have attended</h2>
				<?php echo $usersConcertslist;?>
			</div>
			<div class="span6"><?php echo $rightBox;?></div>
		</div>
		<div class='navbar navbar-fixed-top navbar-inverse'>
			<div class='navbar-inner'>
				<div class='container-fluid'>
					<a class='brand'><?php echo $appName;?> </a>
					<div class="navbar-form pull-right">
						<div class="navbar-form pull-left"> 
							<?php echo "<font color='red'>" . $authenticationErrorMessage . "</font>"; ?>
						</div>
						<div class="navbar-form pull-right"> <?php echo $loginBox; ?> </div>
					</div>
				</div>
			</div>
		</div>

	</div>
	<hr>
</body>
</html>
