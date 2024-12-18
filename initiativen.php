<?php
session_start();
include "db_conn.php";
include "php/stmt_admin.php";
include "php/input_admin.php";
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
	<title>Initiativenverwaltung</title>
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
				<li><a href="teams.php"><span class="las la-users"></span>
						<span>Teams verwalten</span></a>
				</li>
				<li><a href="initiativen.php" class="active"><span class="las la-users"></span>
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
							<h3>Initiativenverwaltung</h3>
							<button onclick="toggleInitativen()"><span class="las la-user-plus"></span> Neue Initiative</button>
						</div>
						<div class="card-body">
							<div class="table-responsive">
								<?php if (mysqli_num_rows($teams_res) > 0) { ?>
									<table id="init-table" style="width:100%">
										<thead>
											<tr>
												<th scope="col"> ID </th>
												<th scope="col"> Bezeichnung </th>
												<th scope="col"> Dauer </th>
												<th scope="col"> Budget </th>
												<th scope="col"> Beteiligte Teams </th>
												<th scope="col"></th>
											</tr>
										</thead>
										<tbody>
											<?php
											while ($initiative_row = mysqli_fetch_assoc($initiative_res)) { ?>
												<tr>
													<th scope="row"><?= $initiative_row['Initiative_ID'] ?></th>
													<td><?= $initiative_row['Bezeichnung'] ?></td>
													<td><?= $initiative_row['Dauer'] ?></td>
													<td><?= $initiative_row['Budget'] ?></td>
													<td>
														<?php
														$currentID = $initiative_row['Initiative_ID'];
														$teams_sql = "SELECT Team.Bezeichnung FROM Team JOIN team_initiative ON Team.Team_ID = team_initiative.Team_ID WHERE team_initiative.Initiative_ID = $currentID ORDER BY Team.Bezeichnung ASC";
														$teams_res = mysqli_query($conn, $teams_sql);

														while ($teams_row = mysqli_fetch_assoc($teams_res)) {
															echo $teams_row['Bezeichnung'] . "<br>";
														} ?>
													</td>
													<td>
														<form method="post">
															<button class="las la-edit" type="submit" name="initID" value="<?= $initiative_row['Initiative_ID'] ?>"></button>
														</form>
														<form action="php/input_admin.php" method="post">
															<button class="las la-trash" type="submit" name="deleteinitID" value="<?= $initiative_row['Initiative_ID'] ?>"></button>
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
											</tr>
										</tfoot>
									</table>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
				<!----------------------------------- Bearbeiten einer Initiative --------------------------------------------------->
				<?php if (isset($_POST['initID'])) {
					$initID = $_POST['initID'];
					$editInitiative_sql = "SELECT * FROM Initiative WHERE Initiative_ID='$initID'";
					$editInitiative_res = mysqli_query($conn, $editInitiative_sql);
				?>
					<div class="card add-user">
						<form action="php/input_admin.php" method="post">
							<?php $editInitiative_row = mysqli_fetch_assoc($editInitiative_res);	?>
							<h3>Initiative bearbeiten</h3>
							<input type="hidden" name="Initiative_ID" value="<?= $editInitiative_row['Initiative_ID'] ?>">
							<div class="inputBox">
								<input type="text" name="initEditBezeichnung" value="<?= $editInitiative_row['Bezeichnung'] ?>" required>
							</div>
							<div class="inputBox">
								<label for="initDauer">Geplantes Abschlussdatum:</label><br>
								<input type="date" name="initEditDauer" value="<?= $editInitiative_row['Dauer'] ?>" required>
							</div>
							<div class="inputBox">
								<input type="number" name="initEditBudget" value="<?= $editInitiative_row['Budget'] ?>" required>
							</div>
							<br><span>Beteiligte Teams:</span><br>
							<div class="admin-checkbox">
								<?php
								$beteiligteTeams = array();
								$currentID = $editInitiative_row['Initiative_ID'];
								$teams_sql = "SELECT Team.Team_ID FROM Team JOIN team_initiative ON Team.Team_ID = team_initiative.Team_ID WHERE team_initiative.Initiative_ID = $currentID ORDER BY Team.Bezeichnung ASC";
								$teams_res = mysqli_query($conn, $teams_sql);

								while ($teams_row = mysqli_fetch_assoc($teams_res)) {
									array_push($beteiligteTeams, $teams_row['Team_ID']);
								}

								$teamSelect_sql = "SELECT Bezeichnung, Team_ID FROM Team ORDER BY Bezeichnung ASC";
								$teamSelect_res = mysqli_query($conn, $teamSelect_sql);

								while ($teamSelect_row = mysqli_fetch_assoc($teamSelect_res)) {
									$currentName = $teamSelect_row['Bezeichnung'];
									$currentID = $teamSelect_row['Team_ID'];
									echo "<label for='initEditteamID[]'>" . $currentName . "</label>";
									if (in_array($currentID, $beteiligteTeams)) {
										echo "<input type='checkbox' name='initEditteamID[]' value=" . $currentID . " checked><br>";
									} else {
										echo "<input type='checkbox' name='initEditteamID[]' value=" . $currentID . "><br>";
									}
								} ?>
							</div>
							<button class="inputBox" type="submit" name="initEditSubmit">Bestätigen</button>
						</form>
					</div>
				<?php } ?>
				<!----------------------------------- Anlegen einer neuen Initiative --------------------------------------------------->
				<div class="card add-user add-initiative-pop">
					<form action="php/input_admin.php" method="post">
						<h3>Neue Initiative anlegen</h3>
						<div class="inputBox">
							<input type="text" name="initBezeichnung" placeholder="Bezeichnung" required>
						</div>
						<div class="inputBox">
							<label for="initDauer">Geplantes Abschlussdatum:</label><br>
							<input type="date" name="initDauer" id="initDauer" required>
						</div>
						<div class="inputBox">
							<input type="number" name="initBudget" placeholder="Budget" min=0 required>
						</div>
						<br><span>Beteiligte Teams:</span><br>
						<div class="admin-checkbox">
							<?php
							$teamSelect_sql = "SELECT Bezeichnung, Team_ID FROM Team ORDER BY Bezeichnung ASC";
							$teamSelect_res = mysqli_query($conn, $teamSelect_sql);

							while ($teamSelect_row = mysqli_fetch_assoc($teamSelect_res)) {
								$currentName = $teamSelect_row['Bezeichnung'];
								$currentID = $teamSelect_row['Team_ID'];
								echo "<label for='teamID[]'>" . $currentName . "</label>";
								echo "<input type='checkbox' name='teamID[]' value=" . $currentID . "><br>";
							} ?>
						</div>
						<button class="inputBox" type="submit" name="initSubmit">Bestätigen</button>
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