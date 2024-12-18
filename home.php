<?php
session_start();
include "db_conn.php";

if (isset($_SESSION['username']) && isset($_SESSION['id'])) {   ?>

	<!DOCTYPE html>
	<html>

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width.initial-scale=1.maximum-scale=1">
		<title>HOME</title>
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
		<!---------------------------------Ansicht für Admin--------------------------------------------->
		<?php if ($_SESSION['role'] == 'administrator') {
			include "php/stmt_admin.php"; ?>
			<input type="checkbox" id="nav-toggle">
			<div class="sidebar">
				<div class="sidebar-brand">
					<h1><a href=""><span class="las la-binoculars"></span><span>AgileView</span></a></h1>
				</div>
				<div class="sidebar-menu">
					<ul>
						<li><a href="" class="active"><span class="las la-portrait"></span>
								<span>Mitglieder verwalten</span></a>
						</li>
						<li><a href="teams.php"><span class="las la-users"></span>
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
									<h3>Nutzerverwaltung</h3>
									<button onclick="toggleNutzerverwaltung()"><span class="las la-user-plus"></span> Neuer Nutzer</button>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<?php if (mysqli_num_rows($nutzer_res) > 0) { ?>
											<table id="user-table" style="width: 100%">
												<thead>
													<tr>
														<th scope="col"> ID </th>
														<th scope="col"> Username </th>
														<th scope="col"> Name </th>
														<th scope="col"> Rolle(n) </th>
														<th scope="col"> Status </th>
														<th scope="col"> Team </th>
														<th scope="col"> Mailadresse </th>
														<th scope="col"> Eintrittsdatum </th>
														<th scope="col"> Austrittsdatum </th>
														<th scope="col"> Überstunden </th>
														<th scope="col"> Fachgebiet </th>
														<th scope="col"> Produkt </th>
														<th scope="col"> bearbeiten </th>
													</tr>
												</thead>
												<tbody>
													<?php
													while ($rows = mysqli_fetch_assoc($nutzer_res)) { ?>
														<tr>
															<th scope="row"><?= $rows['Mitarbeiter_ID'] ?></th>
															<td><?= $rows['Username'] ?></td>
															<td><?= $rows['Name'] ?></td>
															<td>
																<?php
																$currentMAID = $rows['Mitarbeiter_ID'];
																$roles_sql = "SELECT Rollen.Rollenbezeichnung FROM Rollen JOIN Rollenzuweisung ON Rollen.ID = Rollenzuweisung.Rollen_ID WHERE Rollenzuweisung.MA_ID = '$currentMAID'";
																$roles_res = mysqli_query($conn, $roles_sql);

																while ($roles_rows = mysqli_fetch_assoc($roles_res)) {
																	echo $roles_rows['Rollenbezeichnung'] . "<br>";
																} ?>
															</td>
															<td><?= $rows['Status'] ?></td>
															<td><?= $rows['Bezeichnung'] ?></td>
															<td><?= $rows['Mailadresse'] ?></td>
															<td><?= $rows['Eintrittsdatum'] ?></td>
															<td><?= $rows['Austrittsdatum'] ?></td>
															<td><?= $rows['Ueberstunden'] ?></td>
															<td><?= $rows['Fachgebiet'] ?></td>
															<td><?= $rows['Product'] ?></td>
															<td>
																<form method="post">
																	<button class="las la-edit" type="submit" name="editMA" value="<?= $rows['Mitarbeiter_ID'] ?>"></button>
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
						<!----------------------------------- Bearbeiten eines Mitarbeiters --------------------------------------------------->
						<?php if (isset($_POST['editMA'])) {
							$Mitarbeiter_ID = $_POST['editMA'];
							$editMA_sql = "SELECT * FROM Mitarbeiter LEFT JOIN Team on Team.Team_ID = Mitarbeiter.FK_Team WHERE Mitarbeiter_ID='$Mitarbeiter_ID'";
							$editMA_res = mysqli_query($conn, $editMA_sql);
						?>
							<div class="card add-user ">
								<form action="php/input_admin.php" method="post">
									<?php $editMA_row = mysqli_fetch_assoc($editMA_res); ?>
									<h3>Mitarbeiter bearbeiten</h3>
									<input type="hidden" name="Mitarbeiter_ID" value="<?= $Mitarbeiter_ID ?>">
									<div class="inputBox">
										<input class="user-name" type="text" name="username" value="<?= $editMA_row['Username'] ?>" required>
									</div>
									<div class="inputBox">
										<input class="name" type="text" name="name" value="<?= $editMA_row['Name'] ?>" required>
									</div>
									<div class="inputBox">
										<input class="pwd" type="password" name="passwort" value="<?= md5($editMA_row['Passwort']) ?>" required>
									</div>
									<div class="inputBox">
										<input class="e-mail" type="email" name="email" value="<?= $editMA_row['Mailadresse'] ?>" required>
									</div>
									<div class="inputBox">
										<label for="eintritt">Eintrittsdatum:</label><br>
										<input type="date" id="eintritt" name="eintritt" value="<?= $editMA_row['Eintrittsdatum'] ?>" required>
									</div>
									<div class="inputBox">
										<label for="austritt">Austrittsdatum:</label><br>
										<input type="date" id="austritt" name="austritt" value="<?= $editMA_row['Austrittsdatum'] ?>">
									</div>
									<div class="inputBox">
										<label for="status">Status auswählen<br></label>
										<select name="statusselect" id="status" >
											<option value="<?= $editMA_row['Status'] ?>">aktuell: <?= $editMA_row['Status'] ?></option>
											<option value="aktiv">aktiv</option>
											<option value="inaktiv">inaktiv</option>
											<option value="ausgeschieden">ausgeschieden</option>
										</select>
									</div>
									<br><span>Rolle(n):</span><br>
									<div class="admin-checkbox">
										<?php
										$Rollenselect_sql = "SELECT ID, Rollenbezeichnung FROM Rollen";
										$Rollenselect_res = mysqli_query($conn, $Rollenselect_sql);

										$currentMA = array();
										$currentMA_sql = "SELECT Rollen.Rollenbezeichnung FROM Rollen JOIN Rollenzuweisung ON Rollen.ID = Rollenzuweisung.Rollen_ID WHERE Rollenzuweisung.MA_ID = '$Mitarbeiter_ID'";
										$currentMA_res = mysqli_query($conn, $currentMA_sql);

										while ($currentMA_rows = mysqli_fetch_assoc($currentMA_res)) {
											array_push($currentMA, $currentMA_rows['Rollenbezeichnung']);
										}

										while ($Rollenselect_rows = mysqli_fetch_assoc($Rollenselect_res)) {
											$currentName = $Rollenselect_rows['Rollenbezeichnung'];
											$currentID = $Rollenselect_rows['ID'];
											if (in_array($currentName, $currentMA)) {
												echo "<input type='checkbox' id='edRoleId' name='editRoleID[]' value=" . $currentID . " checked>";
											} else {
												echo "<input type='checkbox' id='edRoleId' name='editRoleID[]' value=" . $currentID . ">";
											}
											echo "<label for='edRoleId'>" . $currentName . "</label><br>";
										} ?>
									</div>
									<div class="inputBox">
										<label for="team">Einem Team zuordnen<br></label>
										<select name="teamselect" id="team">
											<option value="<?= $editMA_row['Team_ID'] ?>">aktuell: <?= $editMA_row['Bezeichnung'] ?></option>
											<?php
											$team_sql = "SELECT Bezeichnung, Team_ID FROM Team ORDER BY Bezeichnung ASC";
											$team_res = mysqli_query($conn, $team_sql);

											while ($team_rows = mysqli_fetch_assoc($team_res)) {
												$currentTeam = $team_rows['Bezeichnung'];
												$currentTeamID = $team_rows['Team_ID'];
												echo "<option value=" . $currentTeamID . ">" . $currentTeam . "</option>";
											} ?>
										</select>
									</div>
									<div class="inputBox">
										<input type="text" name="product" value="<?= $editMA_row['Product'] ?>" placeholder="Product Owner ist verantwortlich für">
									</div>
									<div class="inputBox">
										<input type="text" name="fachgebiet" value="<?= $editMA_row['Fachgebiet'] ?>" placeholder="Fachgebiet">
									</div>
									<div class="inputBox">
										<input type="number" name="ueberstunden" value="<?= $editMA_row['Ueberstunden'] ?>">
									</div>
									<button class="inputBox" type="submit" name="maEditSubmit">Bestätigen</button>
								</form>
							</div>
						<?php } ?>
						<div class="card add-user add-user-pop">
							<form action="php/input_admin.php" method="post">
								<h3>Neuen Nutzer anlegen</h3>
								<div class="inputBox">
									<input class="user-name" type="text" name="username" placeholder="User Name" required>
								</div>
								<div class="inputBox">
									<input class="name" type="text" name="name" placeholder="Name" required>
								</div>
								<div class="inputBox">
									<input class="pwd" type="password" name="passwort" placeholder="Passwort" required>
								</div>
								<div class="inputBox">
									<input class="e-mail" type="email" name="email" placeholder="E-Mail" required>
								</div>
								<div class="inputBox">
									<label for="eintritt">Eintrittsdatum:</label><br>
									<input type="date" id="eintrittd" name="eintritt" required>
								</div>
								<div class="inputBox">
									<label for="statuss">Status auswählen<br></label>
									<select name="statusselect" id="statuss">
										<option value="aktiv">aktiv</option>
										<option value="inaktiv">inaktiv</option>
										<option value="ausgeschieden">ausgeschieden</option>
									</select>
								</div>
								<br><span>Rolle(n):</span><br>
								<div class="admin-checkbox">
									<?php
									$Rollenselect_sql = "SELECT ID, Rollenbezeichnung FROM Rollen";
									$Rollenselect_res = mysqli_query($conn, $Rollenselect_sql);

									while ($Rollenselect_rows = mysqli_fetch_assoc($Rollenselect_res)) {
										$currentName = $Rollenselect_rows['Rollenbezeichnung'];
										$currentID = $Rollenselect_rows['ID'];
										echo "<input type='checkbox' id='rollen' name='rollen[]' value=" . $currentID . ">";
										echo "<label for='rollen'>" . $currentName . "</label><br>";
									} ?>
								</div>
								<div class="inputBox">
									<label for="teams">Einem Team zuordnen<br></label>
									<select name="teamselect" id="teams">
										<?php
										$team_sql = "SELECT Bezeichnung, Team_ID FROM Team ORDER BY Bezeichnung ASC";
										$team_res = mysqli_query($conn, $team_sql);

										while ($team_rows = mysqli_fetch_assoc($team_res)) {
											$currentTeam = $team_rows['Bezeichnung'];
											$currentTeamID = $team_rows['Team_ID'];
											echo "<option value=" . $currentTeamID . ">" . $currentTeam . "</option>";
										} ?>
									</select>
								</div>
								<div class="inputBox">
									<input type="text" name="product" placeholder="Product Owner ist verantwortlich für">
								</div>
								<div class="inputBox">
									<input type="text" name="fachgebiet" placeholder="Fachgebiet">
								</div>
								<button class="inputBox" type="submit" name="maSubmit">Bestätigen</button>
							</form>
						</div>
					</div>
				</main>
			</div>
			<script src="jquery-3.5.1.js"></script>
			<script src="DataTables/datatables.min.js"></script>
			<script src="js/admin.js"></script>

			<!---------------------------------Ansicht für Entwickler--------------------------------------------->
		<?php } else if ($_SESSION['role'] == 'entwickler') {
			include "php/stmt_dev.php"; ?>
			<input type="checkbox" id="nav-toggle">
			<div class="sidebar">
				<div class="sidebar-brand">
					<h1><a href=""><span class="las la-ghost"></span><span>AgileView</span></a></h1>
				</div>
				<div class="sidebar-menu">
					<ul>
						<li><a href="" class="active"><span class="las la-chart-area"></span>
								<span>Dashboard</span></a>
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
							<small>Entwickler</small>
						</div>
					</div>
				</header>
				<main>
					<div class="cards">
						<!---------------------------------KPI Kacheln--------------------------------------------->
						<div class="card-single-dev">
							<div>
								<h1><?php if ($finish == 'Abgeschlossen') {
										echo "Sprint bereits beendet";
									} else {
										echo $dueIn['tag'] . " Tag(e)<br>" . $dueIn['std'] . " Std<br>" . $dueIn['min'] . " Min";
									} ?></h3>
								<span>Verbleibende Zeit</span>
							</div>
							<div>
								<span class="las la-clipboard"></span>
							</div>
						</div>
						<div class="card-single-dev">
							<div>
								<h1><?php echo $overtime ?></h1>
								<span>Überstunden</span>
							</div>
							<div>
								<span class="las la-history"></span>
							</div>
						</div>
						<div class="card-single-dev">
							<div>
								<h1><?php echo $currentSprintID ?></h1>
								<span>Sprint Nummer</span>
							</div>
							<div>
								<span class="las la-running"></span>
							</div>
						</div>
					</div>
					<div class="view-grid">
						<div class="burndown">
							<div class="card">
								<canvas id="burndev"></canvas>
							</div>
						</div>
						<div class="velocity">
							<div class="card">
								<canvas id="velodev"></canvas>
							</div>
						</div>
						<div class="sprint-todo">
							<div class="card">
								<!---------------------------------Persönliche To Do Liste--------------------------------------------->
								<div id="todo">
									<div class="card-header">
										<h3>Todos</h3>
										<button id="spbtn" onclick="switchToBacklog()">
											Zu Backlog wechseln <span class="las la-arrow-right"></span>
										</button>
									</div>
									<form action="php/input_dev.php" method="post">
										<button type="submit" name="editTodo" >Werte aktualisieren<span class="las la-sync"></span></button>
										<div class="card-body">
											<div class="table-responsive">
												<table id="todo-table" style="width: 100%">
													<thead>
														<tr>
															<th>
																Story Point
															</th>
															<th>Status</th>
															<th>Geschätzter Aufwand</th>
															<th>Realer Aufwand</th>
															<th>Test Coverage</th>
														</tr>
													</thead>
													<tbody id="todo-body">

														<?php $i = 0;
														while ($todo_row = mysqli_fetch_assoc($todo_res)) {
															$name = "status" . strval($i); ?>
															<tr class="slct-status-row">
																<input type="hidden" name="slid[]" value="<?= $todo_row['SL_ID'] ?>">
																<td><?= $todo_row['Bezeichnung'] ?></td>
																<td data-order="<?= $todo_row['Status'] ?>" data-filter="<?= $todo_row['Status'] ?>">
																	<select name="status[]" id="<?= $todo_row['SL_ID'] ?>" onclick="saveStory(this.id)">
																		<option value='<?= $todo_row['Status'] ?>'><?= $todo_row['Status'] ?></option>
																		<?php if ($todo_row['Status'] == "zugewiesen") { ?>
																			<option value='inBearbeitung'>in Bearbeitung</option>
																			<option value='inReview' disabled>in Review</option>
																		<?php } elseif ($todo_row['Status'] == "inBearbeitung") { ?>
																			<option value='inReview' disabled>In Review</option>
																		<?php } ?>
																	</select>

																</td>
																<?php $i++; ?>
																<td data-order="<?= $todo_row['geschaetzterAufwand'] ?>" data-filter="<?= $todo_row['geschaetzterAufwand'] ?>"><span>Angabe in Stunden: <br></span><input type='number' min='0' name="estimatedEffort[]" value='<?= $todo_row['geschaetzterAufwand'] ?>' /></td>
																<td data-order="<?= $todo_row['realerAufwand'] ?>" data-filter="<?= $todo_row['realerAufwand'] ?>"><span>Angabe in Stunden: <br></span><input type='number' min='0' name="realEffort[]" value='<?= $todo_row['realerAufwand'] ?>' /></td>
																<td data-order="<?= $todo_row['Testcoverage'] ?>" data-filter="<?= $todo_row['Testcoverage'] ?>">
																	<input type='range' min='0' max='100' name="testcoverage[]" value='<?= $todo_row['Testcoverage'] ?>' oninput="this.parentElement.children[1].innerHTML = this.value" onchange="checkTest(this.value, this.parentElement.parentElement)" />
																	<label id="coverage0"><?= $todo_row['Testcoverage'] ?></label><span> %</span>
																</td>
															</tr>
														<?php } ?>
													</tbody>
													<tfoot id="todo-foot">
														<tr>
															<th>
																Story Point
															</th>
															<th>Status</th>
															<th>
																<span>Min: </span><input type="number" id="estimatedMinRange"><br>
																<span>Max: </span><input type="number" id="estimatedMaxRange">
															</th>
															<th>
																<span>Min: </span><input type="number" id="realMinRange"><br>
																<span>Max: </span><input type="number" id="realMaxRange">
															</th>
															<th>
																<span>Min: </span><input type="number" id="testMinRange"><br>
																<span>Max: </span><input type="number" id="testMaxRange">
															</th>
														</tr>
													</tfoot>
												</table>
											</div>
										</div>
									</form>
								</div>
								<!---------------------------------Sprint Backlog--------------------------------------------->
								<div id="backlog">
									<div class="card-header">
										<h3>Sprint Backlog</h3>

										<button id="blbtn" onclick="switchToTodo()">
											Zu Todo's wechseln <span class="las la-arrow-right"></span>
										</button>
									</div>
									<div class="card-body">
										<div class="table-responsive">
											<table id="backlog-table" style="width: 100%">
												<thead>
													<tr>
														<th>Story Point</th>
														<th>Priorität</th>
														<th>Verantwortlich</th>
														<th>Status</th>
													</tr>
												</thead>
												<tbody id="backlog-body">
													<?php while ($sprintBacklog_row = mysqli_fetch_assoc($sprintBacklog_res)) {
														echo "<tr>";
														echo "<td>" . $sprintBacklog_row['Bezeichnung'] . "</td>";
														echo "<td>" . $sprintBacklog_row['Prioritaet'] . "</td>";
														if ($sprintBacklog_row['Name'] != null) {
															echo "<td>" . $sprintBacklog_row['Name'] . "</td>";
														} else { ?>
															<td>
																<button id="assignBtn" onclick="assignDev(<?= $_SESSION['id'] ?>, <?= $sprintBacklog_row['SL_ID'] ?>)">Zuweisen</button>
															</td>
													<?php }
														echo "<td>" . $sprintBacklog_row['Status'] . "</td>";
														echo "</tr>";
													} ?>
												</tbody>
												<tfoot>
													<tr>
														<th>Story Point</th>
														<th>Priorität</th>
														<th>Verantwortlich</th>
														<th>Status</th>
													</tr>
												</tfoot>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!----------------------------------- Mood Input ----------------------------------------->
					<div class="mood">
						<div class="mood-box">
							<h4>Story Mood</h4>
							<div class="mood-container">
								<span class="las la-grin-beam mood-button clickable" onclick="calcMood(5, <?= $_SESSION['id'] ?>)"></span>
								<span class="las la-smile mood-button clickable" onclick="calcMood(4, <?= $_SESSION['id'] ?>)"></span>
								<span class="las la-meh mood-button clickable" onclick="calcMood(3, <?= $_SESSION['id'] ?>)"></span>
								<span class="las la-frown mood-button clickable" onclick="calcMood(2, <?= $_SESSION['id'] ?>)"></span>
								<span class="las la-sad-cry mood-button clickable" onclick="calcMood(1, <?= $_SESSION['id'] ?>)"></span>
							</div>
						</div>
					</div>
					<!---------------------------------------------------------------------------->
				</main>
			</div>
			<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
			<script src="js/charts_dev.js"></script>
			<script src="jquery-3.5.1.js"></script>
			<script src="DataTables/datatables.min.js"></script>
			<script src="js/filter_estimate.js"></script>
			<script src="js/filter_real.js"></script>
			<script src="js/filter_test.js"></script>
			<script src="js/dev.js"></script>


			<!---------------------------------Ansicht für Product Owner--------------------------------------------->
		<?php } else if ($_SESSION['role'] == 'productowner') {
			include "php/stmt_owner.php"; ?>
			<input type="checkbox" id="nav-toggle">
			<div class="sidebar">
				<div class="sidebar-brand">
					<h1><a href=""><span class="las la-ghost"></span><span>AgileView</span></a></h1>
				</div>
				<div class="sidebar-menu">
					<ul>
						<li><a href="" class="active"><span class="las la-chart-area"></span>
								<span>Dashboard</span></a>
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
							<small>Product Owner</small>
						</div>
					</div>
				</header>
				<main>
					<div class="cards">
						<!---------------------------------KPI Kacheln--------------------------------------------->
						<div class="card-single-owner">
							<div>
								<h1><?= $correctness ?></h1>
								<span>Anforderungskorrektheit</span>
							</div>
							<div>
								<span class="las la-certificate"></span>
							</div>
						</div>
						<div class="card-single-owner">
							<div>
								<h1>
									<?php if ($communication >= 4) {
										echo 'Gut';
									} elseif ($communication >= 1.5) {
										echo 'OK';
									} else {
										echo 'Schlecht';
									}
									?>
								</h1>
								<span>Kommunikationsintensität</span>
								<button onclick="toggleRateKomm()"><span class="las la-plus"></span>Neuen Score eingeben</button>
							</div>
							<div>
								<?php if ($communication >= 4) {
									echo "<span class='las la-laugh'></span>";
								} elseif ($communication >= 1.5) {
									echo "<span class='las la-meh'></span>";
								} else {
									echo "<span class='las la-frown'></span>";
								}
								?>
							</div>
						</div>
						<div class="card-single-owner">
							<div>
								<h1><?= $nps ?></h1>
								<span>Net Promoter Score</span>
								<button onclick="toggleRateNPS()"><span class="las la-plus"></span>Neuen NPS eingeben</button>
							</div>
							<div>
								<span class="las la-user-friends"></span>
							</div>
						</div>
					</div>
					<div class="owner-grid">
						<!---------------------------------KPI Charts--------------------------------------------->
						<div class="velocity">
							<div class="card">
								<canvas id="ownerburn"></canvas>
							</div>
						</div>
						<div class="ovelo-own">
							<div class="card">
								<canvas id="ovelo"></canvas>
							</div>
						</div>
						<div class="lead-time">
							<div class="card">
								<canvas id="lead-time"></canvas>
							</div>
						</div>
						<div class="spi">
							<div class="card">
								<canvas id="ownerSpi"></canvas>
							</div>
						</div>
						<!---------------------------------Sprint View--------------------------------------------->
						<div class="sprints-table">
							<div class="card">
								<div id="sprints">
									<div class="card-header">
										<h3>Sprints</h3>
										<button id="Sprints" onclick="switchToProducts()">
											Zu Backlog wechseln <span class="las la-arrow-right"></span>
										</button>
									</div>
									<div class="card-body">
										<div class="table-responsive">
											<table id="sprint-tbl" style="width: 100%;">
												<thead>
													<tr>
														<th>Story Points</th>
														<th>Sprint No#</th>
														<th>Status</th>
														<th>Anforderungskorrektheit</th>
														<th>Assignee</th>
														<th>Abnahme</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													<?php
													while ($sprints_row = mysqli_fetch_assoc($sprints_res)) { ?>
														<tr>
															<td><?= $sprints_row['Bezeichnung'] ?></td>
															<td><?= $sprints_row['Sprint_ID'] ?></td>
															<td><?= $sprints_row['Status'] ?></td>
															<td><?= $sprints_row['Anforderungskorrektheit'] ?></td>
															<td><?= $sprints_row['Name'] ?></td>
															<td><?= $sprints_row['Abnahme'] ?></td>
															<td>
																<?php if ($sprints_row['Status'] == "inReview") { ?>
																	<button class="las la-check" name="rateSP" onclick="setInputAndToggleRateStory(<?= $sprints_row['SL_ID'] ?>, <?= $sprints_row['SP_ID'] ?>)"></button>
																<?php }	?>
															</td>
														</tr>
													<?php } ?>
												</tbody>
												<tfoot>
													<tr>
														<th>Story Points</th>
														<th>Sprint No#</th>
														<th>Status</th>
														<th>Anforderungskorrektheit</th>
														<th>Assignee</th>
														<th>Abnahme</th>
														<th></th>
													</tr>
												</tfoot>
											</table>
										</div>
									</div>
								</div>
								<!---------------------------------Product Backlog--------------------------------------------->
								<div id="products">
									<div class="card-header">
										<h3>Product Backlog</h3>
										<button id="newStory" onclick="toggleNewStory()">
											Story Point hinzufügen <span class="las la-plus"></span>
										</button>
										<button id="switchToSprints" onclick="switchToSprints()">
											Zu Sprints wechseln <span class="las la-arrow-right"></span>
										</button>
									</div>
									<div class="card-body">
										<div class="table-responsive">
											<table id="backlog-table" style="width: 100%">
												<thead>
													<tr>
														<th>Story Point Bezeichnung</th>
														<th>ID</th>
														<th>Status</th>
														<th>Business Value</th>
														<th>Priorität</th>
														<th></th>
													</tr>
												</thead>
												<tbody id="backlog-body">
													<?php 
													while ($productBacklog_row = mysqli_fetch_assoc($productBacklog_res)) { ?>
														<tr>
															<td><?= $productBacklog_row['Bezeichnung'] ?></td>
															<td><?= $productBacklog_row['SP_ID'] ?></td>
															<td><?= $productBacklog_row['Status'] ?></td>
															<td><?= $productBacklog_row['BusinessValue'] ?></td>
															<td><?= $productBacklog_row['Prioritaet'] ?></td>

															<?php 
																$spId = $productBacklog_row['SP_ID'];
																$bezeichnung = $productBacklog_row['Bezeichnung'];
																$prio = $productBacklog_row['Prioritaet'];
																$businessValue = $productBacklog_row['BusinessValue'];
															?>

															<td>
																<button class="las la-edit" name="editSP" onclick="setInputAndToggleEditStory(<?= $spId ?>, '<?= $bezeichnung ?>' , '<?= $prio ?>' , <?= $businessValue ?>)"></button>
																<button class="las la-trash" type="submit" onclick="toggleCheckDeleteStory(<?= $spId ?>)"></button>
																
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
													</tr>
												</tfoot>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--------------------------------- Inputs --------------------------------------------->
					
					<!----------------------------------- New Story ----------------------------------------->
					<div class="add-story">
						<div class="add-story-box">
							<span class="close-button" onclick="toggleNewStory()">X</span>
							<h4>Neuen Story Point anlegen</h4>
							<div class="add-story-container">
								<form action="php/input_owner.php" method="post">
									<label for="story-bezeichnung">Story Bezeichnung</label>
									<input type="text" id="story-bezeichnung" name="storyBezeichnung" required>
									<br>
									<label for=selectPrio>Priorität auswählen</label><br>
									<select name="selectPrio" id="selectPrio">
										<option value="keine">keine</option>
										<option value="niedrig">niedrig</option>
										<option value="mittel">mittel</option>
										<option value="hoch">hoch</option>
									</select>
									<br>
									<label for="business-value">Business Value: </label>
									<input id="business-value" name="businessValue" type="number" min="0" max="10" required>
									<br>
									<input name="pid" type="hidden" value="<?= $_SESSION['id'] ?>">
									<button type="submit" name="submitNewStory">Erstellen</button>
								</form>
							</div>
						</div>
					</div>
					<!---------------------------- Rate Story ------------------------------->

					<div class="rate-story">
						<div class="rate-story-box">
							<span class="close-button" onclick="toggleRateStory()">X</span>
							<h4>Story Point Bewerten</h4>
							<div class="rate-story-container">
								<form action="php/input_owner.php" method="post">
									<label for="rateCorrectness">Anforderungskorrektheit bewerten</label><br>
									<input id="rateCorrectness" name="rateCorrectness" type="number" min=0 max=10 required>
									<br>
									<label for=abnahme>Abnahme auswählen</label><br>
									<select name="abnahme" id="abnahme">
										<option value="Akzeptiert">Akzeptiert</option>
										<option value="Abgelehnt">Abgelehnt</option>
									</select>
									<br>
									<input id="rateSP" name="rateSP_ID" type="hidden">
									<input id="rateSL" name="rateSL_ID" type="hidden">
									<button type="submit" name="submitRateStory">Bewerten</button>
								</form>
							</div>
						</div>
					</div>

					<!---------------------------- Edit Story ------------------------------->

					<div class="edit-story">
						<div class="edit-story-box">
							<span class="close-button" onclick="toggleEditStory()">X</span>
							<h4>Story Point bearbeiten</h4>
							<div class="edit-story-container">
								<form action="php/input_owner.php" method="post">
									<label for="edit_bezeichnung">Bezeichnung</label>
									<input id="edit_bezeichnung" name="editbezeichnung" type="text" required>
									<br>
									<label for="editselectPrio">Priorität auswählen</label>
									<select name="editselectPrio" id="editselectPrio">
										<option>-</option>
										<option value="keine">keine</option>
										<option value="niedrig">niedrig</option>
										<option value="mittel">mittel</option>
										<option value="hoch">hoch</option>
									</select>
									<br>
									<label for="edit-business-value">Bezeichnung</label>
									<input id="edit-business-value" name="editbusinessValue" type="number" required>
									<input id="edit_SP_ID" name="editSP_ID" type="hidden">
									<br>
									<button type="submit" name="editSPsubmit">Bearbeiten</button>
								</form>
							</div>
						</div>
					</div>

					<!---------------------------- Rate Kommunikationsintensität ------------------------------->

					<div class="rate-komm">
						<div class="rate-komm-box">
							<span class="close-button" onclick="toggleRateKomm()">X</span>
							<h4>Kommunikationsintensität Bewerten</h4>
							<div class="rate-komm-container">
								<form action="php/input_owner.php" method="post">
									<label>Zu bewertenden Sprint auswählen</label><br>
									<select name="SprintSelectCS">
									<?php
										$SprintSelectZufriedenheit_sql = "SELECT Sprint_ID FROM Sprint WHERE Zufriedenheit_Teamwork IS NULL ORDER BY Sprint_ID ASC";
										$SprintSelectZufriedenheit_res = mysqli_query($conn, $SprintSelectZufriedenheit_sql);
										if(mysqli_num_rows($SprintSelectZufriedenheit_res) == 0){
											echo "<option value='noSprint'>Alle Sprints bereits bewertet</option>";
										} else {
											while ($SprintSelectZufriedenheit_row = mysqli_fetch_assoc($SprintSelectZufriedenheit_res)) {
												$currentSprintZufriedenheit = $SprintSelectZufriedenheit_row['Sprint_ID'];
												echo "<option value=" . $currentSprintZufriedenheit . ">" . $currentSprintZufriedenheit . "</option>";
											} 
										}
									?>
									</select>
									<br>
									<label for="cs">Kommunikationsintensität: </label>
									<input id="cs" name="cs" type="number" min=1 max=10 required>
									<br>
									<Button type="submit" name="CSsubmit">Bestätigen</button>
								</form>
							</div>
						</div>
					</div>

					<!---------------------------- Rate NPS ------------------------------->

					<div class="rate-nps">
						<div class="rate-nps-box">
							<span class="close-button" onclick="toggleRateNPS()">X</span>
							<h4>NPS</h4>
							<div class="rate-nps-container">
							<form action="php/input_owner.php" method="post">
								<label for=SprintSelectNPS>Zu bewertenden Sprint auswählen</label><br>
									<select name="SprintSelectNPS" id="SprintSelectNPS">
										<?php
										$SprintSelect_sql = "SELECT Sprint_ID FROM Sprint WHERE NetPromoterScore IS NULL ORDER BY Sprint_ID ASC";
										$SprintSelect_res = mysqli_query($conn, $SprintSelect_sql);
										
										if(mysqli_num_rows($SprintSelect_res) == 0) {
											echo "<option value=noSprint>Alle Sprints bereits bewertet</option>";
										} else {
											while ($SprintSelect_rows = mysqli_fetch_assoc($SprintSelect_res)) {
												$currentSprint = $SprintSelect_rows['Sprint_ID'];
												echo "<option value=" . $currentSprint . ">" . $currentSprint . "</option>";
											} 
										}
										?>
									</select>
									<br>
									<label for="nps">Net Promoter Score: </label>
									<input  id="nps" name="nps" type="number" min=0 max=10 required>
									<br>
									<Button type="submit" name="NPSsubmit">Bewerten</button>
								</form>
							</div>
						</div>
					</div>
				<!---------------------------- Delete Story ------------------------------->
					<div class="check-delete-story">
						<div class="check-delete-story-box">
							<span class="close-button" onclick="toggleDeleteStory()">X</span>
							<h4>Wollen Sie die Story wirklich löschen?</h4>

							<button onclick="deleteStory()">Ja</button>
							<button onclick="toggleDeleteStory()">Nein</button>
						</div>
					</div>
				</main>

			</div>
			<script src="jquery-3.5.1.js"></script>
			<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
			<script src="js/charts_owner.js"></script>
			<script src="DataTables/datatables.min.js"></script>
			<script src="js/owner.js"></script>

			<!---------------------------------Ansicht für Scrum Master--------------------------------------------->
		<?php } else if ($_SESSION['role'] == 'scrummaster') {
			include "php/stmt_master.php"; ?>
			<input type="checkbox" id="nav-toggle">
			<div class="sidebar">
		<div class="sidebar-brand">
			<h1><a href=""><span class="las la-ghost"></span><span>AgileView</span></a></h1>
		</div>
		<div class="sidebar-menu">
			<ul>
				<li><a href="" class="active"><span class="las la-chart-area"></span>
						<span>Dashboard</span></a>
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
					<small>Scrum Master</small>
				</div>
			</div>
		</header>
		<main>
		<!-------------------------------New Sprint---------------------------------->
			<div class="new-sprint">
				<div class="new-sprint-box">
					<span class="close-button" onclick="toggleNewSprint()">X</span>
					<h4>Neuer Sprint</h4>
					<form id="new-sprint-form" method="post">
						<span>Start Date: </span>
						<input type="date" id="start-date" name="startDate" required>
						<input type="time" id="start-time" name="startTime" required>
						<br>
						<span>End Date: </span>
						<input type="date" id="end-date" name="endDate" required>
						<input type="time" id="end-time" name="endTime" required>
						<br>
						<span>Teams: </span>
						<select name="teamNewSprint" id="team-new-sprint">
							<?php
							while ($option = mysqli_fetch_assoc($teamSelect_res)) {
								echo "<option value=" . $option['Team_ID'] . ">" . $option['Team_ID'] . "</option>";
							}
							?>
						</select>

						<br>

						<table id="select-backlog-table" style="width: 100%">
							<thead>
								<tr>
									<th>ID</th>
									<th>Story Point Bezeichnung</th>
									<th>Business Value</th>
									<th>Priorität</th>
								</tr>
							</thead>
							<tbody id="select-backlog-body">
								<?php 
								while ($productBacklog_row = mysqli_fetch_assoc($productBacklog_res)) { ?>
									<tr>
										<td><?= $productBacklog_row['SP_ID'] ?></td>
										<td><?= $productBacklog_row['Bezeichnung'] ?></td>
										<td><?= $productBacklog_row['BusinessValue'] ?></td>
										<td><?= $productBacklog_row['Prioritaet'] ?></td>
									</tr>
								<?php } ?>
							</tbody>
							<tfoot>
								<tr>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
						
						<br>
						
						<input id="selected-sp" type="hidden" name="selectedSP[]">
						<button name="submitNewSprint" onclick="createSprint()">Erstellen</button>
					</form>
				</div>
			</div>
			<div class="check-end-sprint">
				<div class="check-end-sprint-box">
					<span class="close-button" onclick="toggleCheckEndSprint()">X</span>
					<h4>Wollen Sie wirklich den Sprint beenden?</h4>

					<button onclick="toggleProz()">Ja</button>
					<button onclick="toggleCheckEndSprint()">Nein</button>
				</div>
			</div>
			<div class="master-grid-top">
				<div class="card-single-master rem-time start-end">
					<div>
						<?php 
							if(empty($statusLatestSprint)){
								echo "<button onclick='toggleNewSprint()'>New Sprint</button>";
							} else if ($statusLatestSprint == 'Abgeschlossen') {
								echo "<button onclick='toggleNewSprint()'>New Sprint</button>";
							} else if ($statusLatestSprint == 'Offen') {
								echo "<button onclick='toggleCheckEndSprint()'>End Sprint</button>";
							}
						?>
						<h3>
							<?php 
								if ($finish == 'Abgeschlossen') {
									echo "Sprint bereits beendet";
								} else {
									echo $dueIn['tag'] . " Tag(e)<br>" . $dueIn['std'] . " Std<br>" . $dueIn['min'] . " Min";
								} 
							?>
						</h3>
						<span>Verbleibende Zeit</span>
						
					</div>
					<div>
						<span class="las la-clipboard"></span>
					</div>
				</div>
				<div class="card-single-master">
					<div>
						<form action="php/stmt_master.php" method="post">

							<select name="selectSprint" id="selectSprint" onchange="this.form.submit()">
								<?php
								echo "<option value=" . $currentSprintID . ">" . $currentSprintID . "</option>";
								while ($option = mysqli_fetch_assoc($allButCurrentSprint_res)) {
									echo "<option value=" . $option['Sprint_ID'] . ">" . $option['Sprint_ID'] . "</option>";
								}
								?>
							</select>
						</form>
						<span>Sprint</span>
					</div>
					<div>
						<span class="las la-running"></span>
					</div>
				</div>
				<div class="card-single-master">
					<div>
						<h2><?php echo $overtime ?></h2>
						<span>Überstunden</span>
					</div>
					<div>
						<span class="las la-history"></span>
					</div>
				</div>
				<div class="masterburn">
					<div class="card">
						<canvas id="masterburn"></canvas>
					</div>
				</div>
				<div class="card-single-master">
					<div>
						<h2><?php echo $correctness ?></h2>
						<span>Anforderungskorrektheit</span>
					</div>
					<div>
						<span class="las la-certificate"></span>
					</div>
				</div>
				<div class="card-single-master rem-time">
					<div>
						<h2>
							<?php if ($devmood >= 4) {
								echo 'Gut';
							} elseif ($devmood >= 1.5) {
								echo 'OK';
							} else {
								echo 'Schlecht';
							}
							?>
						</h2>
						<span>Entwicklerzufriedenheit</span>
					</div>
					<div>
						<?php if ($devmood >= 4) {
							echo "<span class='las la-laugh'></span>";
						} elseif ($devmood >= 1.5) {
							echo "<span class='las la-meh'></span>";
						} else {
							echo "<span class='las la-frown'></span>";
						}
						?>
					</div>
				</div>
				<div class="card-single-master">
					<div>
						<h2><?php echo $compliance ?></h2>
						<span>Prozesseinhaltung</span>
					</div>
					<div>
						<span class="las la-check-square"></span>
					</div>
				</div>
			</div>
			<div class="master-grid-bottom">
				<div class="velocity-master">
					<div class="card">
						<canvas id="veloma"></canvas>
					</div>
				</div>
				<div class="spi-master">
					<div class="card">
						<canvas id="spi-master"></canvas>
					</div>
				</div>
				<div class="lead-time-master">
					<div class="card">
						<canvas id="lead-time-master"></canvas>
					</div>
				</div>
				<div class="team-diversity">
					<div class="card">
						<canvas id="team-diversity"></canvas>
					</div>
				</div>
				<div class="sprints-master">
					<div class="card">
						<div class="card-header">
							<h3>Sprint Backlog</h3>
						</div>
						<div class="card-body">
							<div class="table-responsive">
								<table id="sm-table" style="width: 100%;">
									<thead>
										<tr>
											<th>Story Point</th>
											<th>Priorität</th>
											<th>Verantwortlich</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
										<?php while ($sprintBacklog_row = mysqli_fetch_assoc($sprintBacklog_res)) {
											echo "<tr>";
											echo "<td>" . $sprintBacklog_row['Bezeichnung'] . "</td>";
											echo "<td>" . $sprintBacklog_row['Prioritaet'] . "</td>";
											echo "<td>" . $sprintBacklog_row['Name'] . "</td>";
											echo "<td>" . $sprintBacklog_row['Status'] . "</td>";
											echo "</tr>";
										} ?>
									</tbody>
									<tfoot>
										<tr>
											<th>Story Point</th>
											<th>Priorität</th>
											<th>Verantwortlich</th>
											<th>Status</th>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!----------------------------------- Bewertung der Prozesseinhaltung ----------------------------------------->
			<div class="proz">
				<div class="proz-box">
					<div class="proz-container">
						<form action="php/input_master.php" method="post">
							<h4>Bewertung der Prozesseinhaltung</h4>
							<span>Ziel Verfehlt</span>
							<input type="range" min="0" max="10" value="5" name="prozEinhaltung" id="prozEinhaltung" required oninput="setProzEinhaltungLabel(this.value)">
							<span>Besser als Erwartet</span>
							<span id="prozEinhaltungVal"></span>
							<h4>Bewertung der Teamzufriedenheit</h4>
							<span>Sehr schlecht</span>
							<input type="range" min="0" max="10" value="5" name="teamBewertung" id="teamBewertung" required oninput="setZufriedenheitLabel(this.value)">
							<span>Sehr gut</span>
							<span id="teamBewertungVal"></span>
							<input type="hidden" value="" id="hiddenSprintNumber" name="endingSprint">
							<br>
							<button type="submit" name="beenden">Sprint beenden</button>
						</form>
					</div>
				</div>
			</div>
			<!---------------------------------------------------------------------------->
		</main>
	</div>
	<script src="jquery-3.5.1.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="js/charts_master.js"></script>
	<script type="text/javascript" src="DataTables/datatables.min.js"></script>
	<script src="js/sm.js"></script>

			<!---------------------------------Ansicht für Management--------------------------------------------->
		<?php } else if ($_SESSION['role'] == 'management') {
			include "php/stmt_mgmt.php"; ?>
			<input type="checkbox" id="nav-toggle">
	<div class="sidebar">
		<div class="sidebar-brand">
			<h1><a href=""><span class="las la-ghost"></span><span>AgileView</span></a></h1>
		</div>
		<div class="sidebar-menu">
			<ul>
				<li><a href="" class="active"><span class="las la-chart-area"></span>
						<span>Dashboard</span></a>
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
					<small>Führung</small>
				</div>
			</div>
		</header>
		<main>
			<div class="lead-grid-top">
				<div class="card-single-lead">
					<div>
						<h2><?php echo $overtime ?></h2>
						<span>Überstunden</span>
					</div>
					<div>
						<span class="las la-history"></span>
					</div>
				</div>
				<div class="card-single-lead">
					<div>
						<h2><?php echo $correctness ?></h2>
						<span>Anforderungskorrektheit</span>
					</div>
					<div>
						<span class="las la-file-certificate"></span>
					</div>
				</div>
				<div class="card-single-lead">
					<div>
						<h2>
							<?php if ($devmood >= 4) {
								echo 'Gut';
							} elseif ($devmood >= 1.5) {
								echo 'OK';
							} else {
								echo 'Schlecht';
							}
							?>
						</h2>
						<span>Zufriedenheit der Entwickler</span>
					</div>
					<div>
						<?php if ($devmood >= 4) {
							echo "<span class='las la-laugh'></span>";
						} elseif ($devmood >= 1.5) {
							echo "<span class='las la-meh'></span>";
						} else {
							echo "<span class='las la-frown'></span>";
						}
						?>
					</div>
				</div>
				<div class="card-single-lead">
					<div>
						<h2>
							<?php if ($communication >= 4) {
								echo 'Gut';
							} elseif ($communication >= 1.5) {
								echo 'OK';
							} else {
								echo 'Schlecht';
							}
							?>
						</h2>
						<span>Kommunikationsintensität</span>
					</div>
					<div>
						<?php if ($communication >= 4) {
							echo "<span class='las la-laugh'></span>";
						} elseif ($communication >= 1.5) {
							echo "<span class='las la-meh'></span>";
						} else {
							echo "<span class='las la-frown'></span>";
						}
						?>
					</div>
				</div>
				<div class="card-single-lead">
					<div>
						<h2><?= $devrating ?></h2>
						<span>Teambewertung</span>
					</div>
					<div>
						<span class="las la-balance-scale-right"></span>
					</div>
				</div>
				<div class="card-single-lead">
					<div>
						<h2><?= $nps ?></h2>
						<span>Net Promoter Score</span>
					</div>
					<div>
						<span class="las la-user-friends"></span>
					</div>
				</div>
				<div class="card-single-lead">
					<div>
						<h2><?= $devfluctuation ?></h2>
						<span>Mitarbeiterfluktuation</span>
					</div>
					<div>
						<span class="las la-door-open"></span>
					</div>
				</div>
			</div>
			<div class="lead-grid-bottom">
				<div class="burnlead">
					<div class="card">
						<canvas id="epicburn"></canvas>
					</div>
				</div>
				<div class="lead-cast">
					<div class="card">
						<canvas id="forecast"></canvas>
					</div>
				</div>
			</div>
		</main>
	</div>
	<script src="jquery-3.5.1.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="js/charts_lead.js"></script>
		<?php } ?>
	</body>

	</html>
<?php } else {
	header("Location: index.php");
} ?>