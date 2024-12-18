<?php
session_start();
if (!isset($_SESSION['username']) && !isset($_SESSION['id'])) {   ?>
	<!DOCTYPE html>
	<html>

	<head>
		<title>Anmeldebildschirm</title>
		<link rel="shortcut icon" type="image/x-icon" href="img/favicon.png">
		<!--Bootstrap Einbindung 
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous"> -->
		<!-- Stylesheet -->
		<link rel="stylesheet" href="css/styles.css">
	</head>

	<body id="login-body">
		<div class="headwrap">
			<div class="main-header">
				<a href="index.php" class="logo">AgileView</a>
			</div>
		</div>
		<section>

			<div class="color"></div>
			<div class="color"></div>
			<div class="color"></div>

			<div class="box">
				<!-- Im Folgenden kommen die animierten Quadrate -->
				<div class="square" style="--i:0;"></div>
				<div class="square" style="--i:1;"></div>
				<div class="square" style="--i:2;"></div>
				<div class="square" style="--i:3;"></div>
				<div class="square" style="--i:4;"></div>
				<div class="container">
					<div class="formdiv">
						<h2>ANMELDEN</h1>
							<form action="php/check-login.php" method="post">
								<?php if (isset($_GET['error'])) { ?>
									<div class="alert alert-danger" role="alert">
										<?= $_GET['error'] ?>
									</div>
								<?php } ?>
								<div class="inputBox">
									<input type="text" class="form-control" name="username" id="username" placeholder="User Name">
								</div>
								<div class="inputBox">
									<input type="password" name="password" class="form-control" id="password" placeholder="Passwort">
								</div>
								<select class="roleselect" name="role" aria-label="Default select example">
									<option value="" disabled selected>Bitte Rolle auswählen..</option>
									<option value="entwickler">Entwickler</option>
									<option value="administrator">Systemadministrator</option>
									<option value="scrummaster">SCRUM Master</option>
									<option value="productowner">Product Owner</option>
									<option value="management">Management</option>
								</select>
								<div class="inputBox">
									<input type="submit" value="Login">
								</div>

							</form>
					</div>
				</div>
			</div>
		</section>
		<script type="text/javascript" src="js\vanilla-tilt.min.js"></script>
		<script type="text/javascript">
			VanillaTilt.init(document.querySelectorAll(".square"), {
				max: 25,
				speed: 400,
				glare: true,
				"max-glare": 1,
			});
			VanillaTilt.init(document.querySelector(".container"), {
				max: 5,
				speed: 400,
				glare: true,
				"max-glare": 0.5,
			});
		</script>
	</body>

	</html>
<?php } else {
	header("Location: home.php");
} ?>