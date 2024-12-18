<?php

// hier werden die Datengrundlagen, die in home.php für den Entwickler genutzt werden, zusammengestellt

include "../db_conn.php";

if (isset($_SESSION['username']) && isset($_SESSION['id']) && $_SESSION['role'] == 'entwickler') {

    date_default_timezone_set("Europe/Berlin");
    $timestamp = time();
    $mid = $_SESSION['id'];

    // ist der Entwickler aktuell in einem offenen Sprint tätig, wird dieser als Basis für die anderen Werte genommen, ansonsten wird der zuletzt bearbeitete Sprint ausgewählt
    $latest_Sprint = "SELECT MAX(Sprint_ID) FROM Sprint WHERE FK_team=(SELECT FK_Team From Mitarbeiter WHERE Mitarbeiter_ID='$mid')";
    $latestSprint_res = mysqli_query($conn, $latest_Sprint);
    $latestSprint_row = mysqli_fetch_assoc($latestSprint_res);
    $latestSprint = $latestSprint_row['MAX(Sprint_ID)'];

    $currentSprintID_sql = "SELECT Sprint_ID FROM Sprint WHERE FK_team=(SELECT FK_Team From Mitarbeiter WHERE Mitarbeiter_ID='$mid') AND 
    Abgeschlossen='offen'";
    $currentSprintID_res = mysqli_query($conn, $currentSprintID_sql);
    if (mysqli_num_rows($currentSprintID_res) != 0) {
        $currentSprintID_row = mysqli_fetch_assoc($currentSprintID_res);
        $currentSprintID = $currentSprintID_row['Sprint_ID'];
    } else {
        $currentSprintID = $latestSprint;
    }

    // Daten für Kachel: Verbleibende Zeit
    $sprint_enddate_sql = "SELECT Enddatum, Abgeschlossen FROM Sprint WHERE Sprint_ID=$currentSprintID";
    $sprint_enddate_res = mysqli_query($conn, $sprint_enddate_sql);
    $sprint_enddate_row = mysqli_fetch_assoc($sprint_enddate_res);
    $sprint_enddate = strtotime($sprint_enddate_row['Enddatum']);
    $dueIn = diff_time($sprint_enddate - $timestamp);
    $finish = $sprint_enddate_row['Abgeschlossen'];

    // Daten für Kachel: Überstunden
    $overtime_sql = "SELECT Ueberstunden FROM Mitarbeiter WHERE Mitarbeiter_ID='$mid'";
    $overtime_res = mysqli_query($conn, $overtime_sql);
    $overtime_row = mysqli_fetch_assoc($overtime_res);
    $overtime = $overtime_row['Ueberstunden'];

    $devFilter_sql = "SELECT DISTINCT Mitarbeiter.Name FROM Sprint_Log JOIN 
    Story_Points ON Sprint_Log.StoryPoint_ID = Story_Points.SP_ID LEFT JOIN Mitarbeiter ON Sprint_Log.Assignee = Mitarbeiter.Mitarbeiter_ID 
    WHERE Sprint_Log.Sprint_ID='$currentSprintID' AND NOT Sprint_Log.Status='offen'";
    $devFilter_res = mysqli_query($conn, $devFilter_sql);

    // Daten für Tabelle: Sprint Backlog
    $sprintBacklog_sql = "SELECT Story_Points.Bezeichnung, Story_Points.Prioritaet, Mitarbeiter.Name, Sprint_Log.Status, Sprint_Log.SL_ID FROM Sprint_Log JOIN 
    Story_Points ON Sprint_Log.StoryPoint_ID = Story_Points.SP_ID LEFT JOIN Mitarbeiter ON Sprint_Log.Assignee = Mitarbeiter.Mitarbeiter_ID 
    WHERE Sprint_Log.Sprint_ID='$currentSprintID'";
    $sprintBacklog_res = mysqli_query($conn, $sprintBacklog_sql);

    // Daten für Tabelle: Persönliche To-Do Liste
    $todo_sql = "SELECT Story_Points.Bezeichnung, Sprint_Log.Status, Sprint_Log.geschaetzterAufwand, Sprint_Log.realerAufwand, 
    Sprint_Log.Testcoverage, Sprint_Log.SL_ID FROM Sprint_Log JOIN Story_Points ON Sprint_Log.StoryPoint_ID = Story_Points.SP_ID LEFT JOIN Mitarbeiter ON 
    Sprint_Log.Assignee = Mitarbeiter.Mitarbeiter_ID WHERE Sprint_Log.Sprint_ID='$currentSprintID' AND Mitarbeiter.Mitarbeiter_ID='$mid' AND NOT Sprint_Log.Status='geschlossen' AND NOT Sprint_Log.Status='inReview'";
    $todo_res = mysqli_query($conn, $todo_sql);
} else {
    header("Location: ../index.php");
}

function diff_time($differenz)
{
    $tag  = floor($differenz / (3600 * 24));
    $std  = floor($differenz / 3600 % 24);
    $min  = floor($differenz / 60 % 60);

    return array("min" => $min, "std" => $std, "tag" => $tag);
}
