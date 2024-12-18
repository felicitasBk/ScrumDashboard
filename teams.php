<?php
session_start();
include "db_conn.php";
include "php/stmt_admin.php";
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
	<title>Teamverwaltung</title>
	<!-- Bootstrap
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    -->
	<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
	<link rel="stylesheet" href="css/styles.css">
	<link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css" />
	<script src="jquery-3.5.1.js"></script>
	<script src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script>
</head>

<body>
<input type="checkbox" id="nav-toggle">
	<div class="sidebar">
		<div class="sidebar-brand">
			<h1><a href=""><span class="las la-binoculars"></span><span>AgileView</span></a></h1>
		</div>
		<div class="sidebar-menu">
			<ul>
				<li><a href="home.php"><span class="las la-portrait"></span>
						<span>Mitglieder verwalten</span></a>
				</li>
				<li><a href="teams.php" class="active"><span class="las la-users"></span>
						<span>Teams verwalten</span></a>
				</li>
				<li><a href="initiativen.php"><span class="las la-users"></span>
						<span>Initiativen verwalten</span></a>
				</li>
				<li><a href="logout.php"><span class="las la-power-off"></span>
						<span>Log Out</span></a>
				</li>
			</ul>
		</div>
	</div>
	<div class="main-content">
		<header>
			<h2>
				<label for="nav-toggle">
					<span class="las la-bars"></span>
					<span>Dashboard</span>
				</label>
			</h2>

			<div class="user-wrapper">
				<img src="img/user-default.png" width="30" height="30" alt="">
				<div>
					<h4><?= $_SESSION['name'] ?></h4>
					<small>Admin</small>
				</div>
			</div>
		</header>
		<main>
			<div class="admin-grid">
				<div class="members">
					<div class="card">
						<div class="card-header">
							<h3>Teamverwaltung</h3>
							<button onclick="toggleTeam()"><span class="las la-user-plus"></span> Neues Team</button>
						</div>
						<div class="card-body">
							<div class="table-responsive">
								<?php if (mysqli_num_rows($teams_res) > 0) { ?>
									<table id="teams-table" style="width:100%">
										<thead>
											<tr>
												<th scope="col"> ID </th>
												<th scope="col"> Bezeichnung </th>
												<th scope="col"> Spezialisierung </th>
												<th scope="col"> Verantwortlicher Scrum Master </th>
												<th scope="col"> Mitglieder </th>
												<th scope="col"> Beteiligt an </th>
												<th scope="col"></th>
											</tr>
										</thead>
										<tbody>
											<?php
											while ($teams_rows = mysqli_fetch_assoc($teams_res)) { ?>
												<tr>
													<th scope="row"><?= $teams_rows['Team_ID'] ?></th>
													<td><?= $teams_rows['Bezeichnung'] ?></td>
													<td><?= $teams_rows['Spezialisierung'] ?></td>
													<td><?= $teams_rows['Name'] ?></td>
													<td>
														<div>
															<?php
															$currentTeamID = $teams_rows['Team_ID'];
															$teamdevs_sql = "SELECT Mitarbeiter.Name FROM Mitarbeiter JOIN Team ON Team.Team_ID = Mitarbeiter.FK_Team WHERE Team.Team_ID = $currentTeamID";
															$teamdevs_res = mysqli_query($conn, $teamdevs_sql);

															echo "<ul class='scroll'>";
															while ($teamdevs_rows = mysqli_fetch_assoc($teamdevs_res)) {
																echo "<li>". $teamdevs_rows['Name'] . "</li>";
															} 
															echo "</ul>";
															
															?>
														</div>
													</td>
													<td>
														<div>
															<?php
															$initiative_sql = "SELECT Initiative.Bezeichnung FROM Initiative JOIN team_initiative ON team_initiative.Initiative_ID = Initiative.Initiative_ID WHERE team_initiative.Team_ID = $currentTeamID ORDER BY Team_ID ASC";
															$initiative_res = mysqli_query($conn, $initiative_sql);
															
															echo "<ul class='scroll'>";
															while ($initiative_rows = mysqli_fetch_assoc($initiative_res)) {
																echo "<option>". $initiative_rows['Bezeichnung'] . "</option>";
															} 
															echo "</ul>";
															?>
														</div>
													</td>
													<td>
														<form method="post">
															<button class="las la-edit" type="submit" name="editTeam" value="<?= $teams_rows['Team_ID'] ?>"></button>
														</form>
														<form action="php/input_admin.php" method="post">
															<button class="las la-trash" type="submit" name="deleteTeam" value="<?= $teams_rows['Team_ID'] ?>"></button>
														</form>
													</td>
												</tr>
											<?php } ?>
										</tbody>
										<tfoot>
											<tr>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
											</tr>
										</tfoot>
									</table>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
				<!----------------------------------- Bearbeiten eines Teams --------------------------------------------------->
				<?php if (isset($_POST['editTeam'])) {
					$teamID = $_POST['editTeam'];
					$editTeamID_sql = "SELECT * FROM Team WHERE Team_ID='$teamID'";
					$editTeamID_res = mysqli_query($conn, $editTeamID_sql);
				?>
					<div class="card add-user">
						<form action="php/input_admin.php" method="post">
							<?php $editTeamID_row = mysqli_fetch_assoc($editTeamID_res); ?>
							<h3>Team bearbeiten</h3>
							<input type="hidden" name="Team_ID" value="<?= $teamID ?>">
							<div class="inputBox">
								<label for="EditTeamBezeichnung">Bezeichnung:</label><br>
								<input type="text" id="EditTeamBezeichnung" name="EditTeamBezeichnung" value="<?= $editTeamID_row['Bezeichnung'] ?>" required>
							</div>
							<div class="inputBox">
								<label for="EditTeamSpezi">Spezialisierung:</label><br>
								<input type="text" id="EditTeamSpezi" name="EditTeamSpezi" value="<?= $editTeamID_row['Spezialisierung'] ?>" required>
							</div>
							<div class="inputBox">
								<label for="ScrumSelect">Verantwortlicher Scrum Master:</label><br>
								<select name="EditScrumMasterselect" id="ScrumSelect">
									<?php
									$scrummaster_sql = "SELECT Mitarbeiter_ID, Name FROM Mitarbeiter JOIN Rollenzuweisung ON 
									Mitarbeiter.Mitarbeiter_ID = Rollenzuweisung.MA_ID JOIN Rollen ON Rollen.ID = Rollenzuweisung.Rollen_ID 
									WHERE Rollenbezeichnung='scrummaster' ORDER BY Name ASC";
									$scrummaster_res = mysqli_query($conn, $scrummaster_sql);

									$currentscrummaster_sql = "SELECT Mitarbeiter.Mitarbeiter_ID, Mitarbeiter.Name FROM Mitarbeiter JOIN Team ON Team.FK_ScrumMaster = Mitarbeiter.Mitarbeiter_ID WHERE Team.Team_ID='$teamID'";
									$currentscrummaster_res = mysqli_query($conn, $currentscrummaster_sql);
									$currentscrummaster_row = mysqli_fetch_assoc($currentscrummaster_res);
									$currentMasterName = $currentscrummaster_row['Name'];
									$currentMasterID = $currentscrummaster_row['Mitarbeiter_ID'];
									echo "<option value=" . $currentMasterID . ">" . $currentMasterName . "</option>";

									while ($scrummaster_rows = mysqli_fetch_assoc($scrummaster_res)) {
										$currentName = $scrummaster_rows['Name'];
										$currentID = $scrummaster_rows['Mitarbeiter_ID'];
										echo "<option value=" . $currentID . ">" . $currentName . "</option>";
									} ?>
								</select>
							</div>
							<br><span>Mitglieder:</span><br>
							<div id="team-checkbox" class="admin-checkbox">
								<?php
								$MAselect_sql = "SELECT Name, Mitarbeiter_ID FROM Mitarbeiter ORDER BY Name ASC";
								$MAselect_res = mysqli_query($conn, $MAselect_sql);

								$currentMA = array();
								$currentMA_sql = "SELECT Mitarbeiter.Mitarbeiter_ID FROM Mitarbeiter JOIN Team ON Team.Team_ID = Mitarbeiter.FK_Team WHERE Team.Team_ID = $teamID ORDER BY Team_ID ASC";
								$currentMA_res = mysqli_query($conn, $currentMA_sql);

								while ($currentMA_rows = mysqli_fetch_assoc($currentMA_res)) {
									array_push($currentMA, $currentMA_rows['Mitarbeiter_ID']);
								}

								while ($MAselect_rows = mysqli_fetch_assoc($MAselect_res)) {
									$currentName = $MAselect_rows['Name'];
									$currentID = $MAselect_rows['Mitarbeiter_ID'];
									if (in_array($currentID, $currentMA)) {
										echo "<input type='checkbox' id='editMaID' name='editMaID[]' value=" . $currentID . " checked>";
									} else {
										echo "<input type='checkbox' id='editMaID' name='editMaID[]' value=" . $currentID . ">";
									}
									echo "<label for='editMaID'>" . $currentName . "</label><br>";
								} ?>
							</div>
							<button class="inputBox" type="submit" name="teamEditSubmit">Bestätigen</button>
						</form>
					</div>
				<?php } ?>
				<!----------------------------------- Anlegen eines neuen Teams --------------------------------------------------->
				<div class="card add-user add-team-pop">
					<form action="php/input_admin.php" method="post">
						<h3>Neues Team anlegen</h3>
						<div class="inputBox">
							<input class="team-name" type="text" name="bezeichnung" placeholder="Team Bezeichnung" required>
						</div>
						<div class="inputBox">
							<input class="Spezialisierung" type="text" name="spezi" placeholder="Spezialisierung" required>
						</div>
						<div class="inputBox">
							<label for="ScrumSelects">Verantwortlichen Scrum Master auswählen</label>
							<select name="ScrumMasterselect" id="ScrumSelects">
								<?php
								$scrummaster_sql = "SELECT Mitarbeiter_ID, Name FROM Mitarbeiter JOIN Rollenzuweisung ON 
								Mitarbeiter.Mitarbeiter_ID = Rollenzuweisung.MA_ID JOIN Rollen ON Rollen.ID = Rollenzuweisung.Rollen_ID 
								WHERE Rollenbezeichnung='scrummaster' ORDER BY Name ASC";
								$scrummaster_res = mysqli_query($conn, $scrummaster_sql);

								while ($scrummaster_rows = mysqli_fetch_assoc($scrummaster_res)) {
									$currentName = $scrummaster_rows['Name'];
									$currentID = $scrummaster_rows['Mitarbeiter_ID'];
									echo "<option value=" . $currentID . ">" . $currentName . "</option>";
								} ?>
							</select>
						</div>
						<div id="team-checkbox" class="admin-checkbox">
							<?php
							$MAselect_sql = "SELECT Name, Mitarbeiter_ID FROM Mitarbeiter ORDER BY Name ASC";
							$MAselect_res = mysqli_query($conn, $MAselect_sql);

							while ($MAselect_rows = mysqli_fetch_assoc($MAselect_res)) {
								$currentName = $MAselect_rows['Name'];
								$currentID = $MAselect_rows['Mitarbeiter_ID'];
								echo "<input type='checkbox' id='maID' name='maID[]' value=" . $currentID . ">";
								echo "<label for='maID'>" . $currentName . "</label><br>";
							} ?>
						</div>

						<button class="inputBox" type="submit" name="teamSubmit">Bestätigen</button>
					</form>
				</div>
			</div>
		</main>
	</div>
	<script src="jquery-3.5.1.js"></script>
	<script src="DataTables/datatables.min.js"></script>
	<script src="js/admin.js"></script>
</body>

</html>