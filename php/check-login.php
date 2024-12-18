<?php

// hier wird überprüft, ob der User korrekte Anmeldedaten eingegeben hat

session_start();
include "../db_conn.php";

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['role'])) {

	function test_input($data)
	{
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	$username = test_input($_POST['username']);
	$password = test_input($_POST['password']);
	$role = test_input($_POST['role']);

	if (empty($username)) {
		header("Location: ../index.php?error=Kein Nutzername eingegeben");
	} else if (empty($password)) {
		header("Location: ../index.php?error=Kein Passwort eingegeben");
	} else {

		// Passwort entschlüsseln
		$password = md5($password);

		$sql = "SELECT Mitarbeiter_ID, Name, Username, Passwort FROM Mitarbeiter WHERE Username='$username' AND Passwort='$password'";
		$result = mysqli_query($conn, $sql);
		$row = mysqli_fetch_assoc($result);

		// zuerst wird auf richtige Username & Passwortkombination überprüft
		if (!($row['Passwort'] === $password)) {
			header("Location: ../index.php?error=Falscher Nutzername oder Passwort");
		} else {
			// dann wird überprüft, ob der User eine ihm zugewiesene Rolle ausgewählt hat
			$sql = "SELECT Mitarbeiter_ID, Name, Username, Passwort, Rollenbezeichnung FROM Mitarbeiter JOIN Rollenzuweisung ON 
			Mitarbeiter.Mitarbeiter_ID = Rollenzuweisung.MA_ID JOIN Rollen ON Rollen.ID = Rollenzuweisung.Rollen_ID 
			WHERE Username='$username' AND Passwort='$password'";
			$result = mysqli_query($conn, $sql);

			while ($row = mysqli_fetch_assoc($result)) {
				if ($row['Passwort'] === $password && $row['Rollenbezeichnung'] == $role) {
					$_SESSION['name'] = $row['Name'];
					$_SESSION['id'] = $row['Mitarbeiter_ID'];
					$_SESSION['role'] = $row['Rollenbezeichnung'];
					$_SESSION['username'] = $row['Username'];

					header("Location: ../home.php");
				}
			}
			header("Location: ../index.php?error=Falsches Passwort oder falsche Rolle");
		}
	}
} else {
	header("Location: ../index.php");
}
