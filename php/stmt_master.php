<?php error_reporting(0); ?>

<?php

// hier werden die Datengrundlagen, die in home.php für den Scrum Master genutzt werden, zusammengestellt
session_start();
include "../db_conn.php";

if (isset($_SESSION['username']) && isset($_SESSION['id']) && $_SESSION['role'] == 'scrummaster') {

    date_default_timezone_set("Europe/Berlin");
    $timestamp = time();
    $mid = $_SESSION['id'];

    // der zuletzt eröffnete Sprint wird als Basis für die anderen Werte genommen, es sei denn der Scrum Master wählt im Dashboard einen anderen Sprint aus
    $latest_Sprint = "SELECT MAX(Sprint.Sprint_ID) FROM Sprint WHERE Sprint.FK_ScrumMaster = '$mid'";
    $latestSprint_res = mysqli_query($conn, $latest_Sprint);
    $latestSprint_row = mysqli_fetch_assoc($latestSprint_res);
    $latestSprint = $latestSprint_row['MAX(Sprint.Sprint_ID)'];

    if (isset($_SESSION['chosenSprint'])) {
        $currentSprintID = $_SESSION['chosenSprint'];
    } else {
        $currentSprintID =  $latestSprint;
    }

    if (isset($_POST['selectSprint'])) {
        $_SESSION['chosenSprint'] = $_POST['selectSprint'];
        $currentSprintID = $_POST['selectSprint'];
        header("Location: ../home.php");
    }

    // Daten für Kachel: Verbleibende Zeit
    $sprint_enddate_sql = "SELECT Enddatum, Abgeschlossen FROM Sprint WHERE Sprint_ID='$currentSprintID'";
    $sprint_enddate_res = mysqli_query($conn, $sprint_enddate_sql);
    $sprint_enddate_row = mysqli_fetch_assoc($sprint_enddate_res);
    $sprint_enddate = strtotime($sprint_enddate_row['Enddatum']);
    $dueIn = diff_time($sprint_enddate - $timestamp);
    $finish = $sprint_enddate_row['Abgeschlossen'];

    // Daten für Kachel: Überstunden
    $overtime_sql = "SELECT SUM(m.Ueberstunden) FROM Mitarbeiter as m JOIN Team ON Team.Team_ID = m.FK_Team JOIN Sprint ON Sprint.FK_Team = Team.Team_ID WHERE Sprint.Sprint_ID = '$currentSprintID'";
    $overtime_res = mysqli_query($conn, $overtime_sql);
    $overtime_row = mysqli_fetch_assoc($overtime_res);
    $overtime = $overtime_row['SUM(m.Ueberstunden)'];

    // Daten für Kachel: Anforderungskorrektheit
    $correctness_sql = "SELECT AVG(Anforderungskorrektheit) FROM Sprint_Log WHERE Sprint_ID=$currentSprintID";
    $correctness_res = mysqli_query($conn, $correctness_sql);
    $correctness_row = mysqli_fetch_assoc($correctness_res);
    $correctness = round($correctness_row['AVG(Anforderungskorrektheit)'], 1);

    // Daten für Kachel: Zufriedenheit der Entwickler
    $devmood_sql = "SELECT AVG(Zufriedenheit_Entwickler) FROM Sprint_Log WHERE Sprint_ID=$currentSprintID";
    $devmood_res = mysqli_query($conn, $devmood_sql);
    $devmood_row = mysqli_fetch_assoc($devmood_res);
    $devmood = round($devmood_row['AVG(Zufriedenheit_Entwickler)'], 1);

    // Daten für Kachel: Prozesseinhaltung
    $compliance_sql = "SELECT AVG(Bewertung_Prozesseinhaltung) FROM Sprint WHERE Sprint_ID=$currentSprintID";
    $compliance_res = mysqli_query($conn, $compliance_sql);
    $compliance_row = mysqli_fetch_assoc($compliance_res);
    $compliance = round($compliance_row['AVG(Bewertung_Prozesseinhaltung)'], 1);

    // Daten für Tabelle: Sprint Backlog
    $sprintBacklog_sql = "SELECT Story_Points.Bezeichnung, Story_Points.Prioritaet, Mitarbeiter.Name, Sprint_Log.Status FROM Sprint_Log JOIN Story_Points ON Sprint_Log.StoryPoint_ID = 
   Story_Points.SP_ID LEFT JOIN Mitarbeiter ON Sprint_Log.Assignee = Mitarbeiter.Mitarbeiter_ID WHERE Sprint_Log.Sprint_ID='$currentSprintID'";
    $sprintBacklog_res = mysqli_query($conn, $sprintBacklog_sql);

    $smSprintId = "SELECT Sprint.Sprint_ID FROM Sprint WHERE Sprint.FK_ScrumMaster = '$mid'";
    $smSprintId_res = mysqli_query($conn, $smSprintId);

    $allButCurrentSprint = "SELECT Sprint.Sprint_ID FROM Sprint WHERE NOT Sprint.Sprint_ID = '$currentSprintID'";
    $allButCurrentSprint_res = mysqli_query($conn,  $allButCurrentSprint);

    $statusLatestSprint_sql = "SELECT Sprint.Abgeschlossen FROM Sprint WHERE Sprint.sprint_ID = '$currentSprintID'";
    $statusLatestSprint_res = mysqli_query($conn,  $statusLatestSprint_sql);
    $statusLatestSprint_row = mysqli_fetch_assoc($statusLatestSprint_res);
    $statusLatestSprint = $statusLatestSprint_row['Abgeschlossen'];

    $teamSelect_sql = "SELECT Team_ID FROM Team";
    $teamSelect_res = mysqli_query($conn, $teamSelect_sql);

   $productBacklog_sql = "SELECT Story_Points.SP_ID, Story_Points.Bezeichnung, Story_Points.BusinessValue, Story_Points.Prioritaet FROM Story_Points WHERE 
  	Story_Points.Status = 'offen' AND SP_ID NOT IN (SELECT DISTINCT StoryPoint_ID FROM Sprint_Log WHERE NOT Status = 'geschlossen')";
   $productBacklog_res = mysqli_query($conn, $productBacklog_sql);
   
}else{
	header("Location: ../index.php");
} 

function diff_time($differenz)
{
    $tag  = floor($differenz / (3600 * 24));
    $std  = floor($differenz / 3600 % 24);
    $min  = floor($differenz / 60 % 60);

    return array("min" => $min, "std" => $std, "tag" => $tag);
}
