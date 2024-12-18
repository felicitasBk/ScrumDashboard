<?php

// hier werden die Daten, die der Scrum Master aus home.php absendet, weiterverarbeitet

include "../db_conn.php";
session_start();

date_default_timezone_set("Europe/Berlin");

$mid = $_SESSION['id'];

// Beenden eines Sprints
if (isset($_POST['beenden']) && isset($_POST['prozEinhaltung']) && isset($_POST['teamBewertung']) && isset($_POST['endingSprint'])) {
	$prozWert = $_POST['prozEinhaltung'];
	$endingSprint = $_POST['endingSprint'];
	$teamBewertung = $_POST['teamBewertung'];
	$proz_sql = "UPDATE Sprint SET Enddatum=now(), Abgeschlossen = 'Abgeschlossen', Bewertung_Prozesseinhaltung = '$prozWert', Teambewertung = '$teamBewertung'WHERE Sprint.Sprint_ID = '$endingSprint'";
	mysqli_query($conn, $proz_sql);

	// alle Story Points, die noch nicht inReview oder abgeschlossen sind, werden auf geschlossen & abgelehnt gesetzt
	$close_sql = "UPDATE Sprint_Log SET Status='geschlossen', Abnahme='Abgelehnt' WHERE Sprint_ID = '$endingSprint' AND (Status='offen' OR Status='zugewiesen' OR Status='inBearbeitung')";
	mysqli_query($conn, $close_sql);
}

// Anlegen eines neuen Sprints
if (isset($_POST['startDate']) && isset($_POST['startTime']) && isset($_POST['endDate']) && isset($_POST['endTime']) && isset($_POST['teamNewSprint']) && isset($_POST['selectedSP'])) {
	$datetimeStartInput = $_POST['startDate'] . ' ' . $_POST['startTime'];
	$datetimeEndInput = $_POST['endDate'] . ' ' . $_POST['endTime'];

	$datetimeStart = date("Y-m-d H:i:s", strtotime($datetimeStartInput));
	$datetimeEnd = date("Y-m-d H:i:s", strtotime($datetimeEndInput));

	$secsBetween = $datetimeEnd - $datetimeStart;
	$daysBewtween = $secsBetween / 86400;

	$team = $_POST['teamNewSprint'];

	$newSprint_sql = "INSERT INTO Sprint (geplanteDauerInTagen, Enddatum, Startdatum, Abgeschlossen, FK_ScrumMaster, FK_team) VALUES ('$daysBewtween', '$datetimeEnd', '$datetimeStart', 'Offen', '$mid', '$team')";
	mysqli_query($conn, $newSprint_sql);

	$spid_sql = "SELECT MAX(Sprint_ID) FROM Sprint WHERE FK_ScrumMaster = $mid";
	$spid_res = mysqli_query($conn, $spid_sql);
	$spid_row = mysqli_fetch_assoc($spid_res);
	$spid = $spid_row['MAX(Sprint_ID)'];

	$_SESSION['chosenSprint'] = $spid;

	$sps = $_POST['selectedSP'];
	foreach( $sps as $p) {
		$newStory_sql = "INSERT INTO Sprint_Log (Status, StoryPoint_ID, Sprint_ID) VALUES ('Offen', '$p', '$spid')";
		mysqli_query($conn, $newStory_sql);
	}
}

if (isset($_POST['prozEinhaltung']) && isset($_POST['teamZufriedenheit']) && isset($_POST['endingSprint'])) {
	$prozWert = $_POST['prozEinhaltung'];
	$endingSprint = $_POST['endingSprint'];
	$teamZufriedenheit = $_POST['teamZufriedenheit'];
	$proz_sql = "UPDATE Sprint SET Enddatum=now(), Bewertung_Prozesseinhaltung = '$prozWert', Zufriedenheit_Teamwork = '$teamZufriedenheit'WHERE Sprint.Sprint_ID = '$endingSprint'";
	mysqli_query($conn, $proz_sql);
}

header("Location: ../home.php");
