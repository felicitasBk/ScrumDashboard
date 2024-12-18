<?php

// hier werden die Daten, die der Entwickler aus home.php absendet, weiterverarbeitet

include "../db_conn.php";
include "sendmails.php";

date_default_timezone_set("Europe/Berlin");


// nach In Review setzen einer Story, werden Status & Zufriedenheit geupdatet sowie mögliche Ueberstunden addiert
if (isset($_POST['mood']) && isset($_COOKIE['slidCookie'])) {
    $mood = $_POST['mood'];
    $mood_slid = $_COOKIE['slidCookie'];
    $mid = $_POST['mid'];

    // gibt es Abweichungen beim Aufwand fließt das in die Überstunden ein
    $differenz_sql = "SELECT realerAufwand, geschaetzterAufwand FROM Sprint_Log WHERE Sprint_Log.SL_ID = '$mood_slid'";
    $differenz_res = mysqli_query($conn, $differenz_sql);
    $differenz_row = mysqli_fetch_assoc($differenz_res);
    $differenz = $differenz_row['realerAufwand'] - $differenz_row['geschaetzterAufwand'];

    $currentOvertime_sql = "SELECT Mitarbeiter.Ueberstunden FROM Mitarbeiter WHERE Mitarbeiter_ID = '$mid'";
    $currentOvertime_res = mysqli_query($conn, $currentOvertime_sql);
    $currentOvertime_row = mysqli_fetch_assoc($currentOvertime_res);
    $currentOvertime = $currentOvertime_row['Ueberstunden'];

    $overtime = $currentOvertime + $differenz;

    $overtime_sql = "UPDATE Mitarbeiter SET Ueberstunden='$overtime' WHERE Mitarbeiter_ID = '$mid'";
    mysqli_query($conn, $overtime_sql);

    $mood_sql = "UPDATE Sprint_Log SET Zufriedenheit_Entwickler = '$mood', Status='inReview', Zeitstempel_inReview=now() WHERE Sprint_Log.SL_ID = '$mood_slid'";
    mysqli_query($conn, $mood_sql);

    // überprüfen, ob der neue Zufriedenheitswert den Grenzwert von unter 2 erreicht hat
    $betreuer_sql = "SELECT FK_ScrumMaster FROM Sprint JOIN Sprint_Log ON Sprint.Sprint_ID = Sprint_Log.Sprint_ID WHERE Sprint_Log.SL_ID = '$mood_slid'";
    $betreuer_res = mysqli_query($conn, $betreuer_sql);
    $betreuer_row = mysqli_fetch_assoc($betreuer_res);
    $betreuer = $betreuer_row['FK_ScrumMaster'];

    $devmood_sql = "SELECT AVG(Zufriedenheit_Entwickler) FROM Sprint_Log JOIN Sprint ON Sprint.Sprint_ID = Sprint_Log.Sprint_ID 
    WHERE Sprint.FK_ScrumMaster='$betreuer'";
    $devmood_res = mysqli_query($conn, $devmood_sql);
    $devmood_row = mysqli_fetch_assoc($devmood_res);
    $devmood = round($devmood_row['AVG(Zufriedenheit_Entwickler)'], 1);

    // bei zu niedrigem Wert wird eine Alertmail an den betreuenden Scrum Master verschickt
    if ($devmood < 2) {
        $betreff = "Entwicklerzufriedenheit unter 2";
        $text = "Der Entwicklerzufriedenheit ist mit dem Rating von Story Point " . $mood_slid . " auf einen kritischen Wert von unter 2 gesunken. \nUm weitere Details zu erhalten melden Sie sich unter https://web06.iis.uni-bamberg.de/WIP/wip21_g2/ in AgileView an.";
        $mailadresse_sql = "SELECT Mailadresse FROM Mitarbeiter WHERE Mitarbeiter_ID = '$betreuer'";
        $mailadresse_res = mysqli_query($conn, $mailadresse_sql);
        $mailadresse_row = mysqli_fetch_assoc($mailadresse_res);
        $mailadresse = $mailadresse_row['Mailadresse'];
        sendmails($text, $mailadresse, $betreff);
    }

    // überprüfen, ob der neue Gesamtzufriedenheitswert den Grenzwert von unter 2 erreicht hat
    $devmood_sql = "SELECT AVG(Zufriedenheit_Entwickler) FROM Sprint_Log";
    $devmood_res = mysqli_query($conn, $devmood_sql);
    $devmood_row = mysqli_fetch_assoc($devmood_res);
    $devmood = round($devmood_row['AVG(Zufriedenheit_Entwickler)'], 1);

    // bei zu niedrigem Wert werden Alertmails an das Management verschickt
    if ($devmood < 2) {
        $betreff = "Entwicklerzufriedenheit unter 2";
        $text = "Der Entwicklerzufriedenheit ist mit dem Rating von Story Point " . $mood_slid . " auf einen kritischen Wert von unter 2 gesunken. \nUm weitere Details zu erhalten melden Sie sich unter https://web06.iis.uni-bamberg.de/WIP/wip21_g2/ in AgileView an.";
        $mailadresse_sql = "SELECT Mailadresse FROM Mitarbeiter JOIN Rollenzuweisung ON 
        Mitarbeiter.Mitarbeiter_ID = Rollenzuweisung.MA_ID JOIN Rollen ON Rollen.ID = Rollenzuweisung.Rollen_ID 
        WHERE Rollenbezeichnung='management'";
        $mailadresse_res = mysqli_query($conn, $mailadresse_sql);
        while ($mailadresse_row = mysqli_fetch_assoc($mailadresse_res)) {
            $mailadresse = $mailadresse_row['Mailadresse'];
            sendmails($text, $mailadresse, $betreff);
        }
    }

    // Cookie wieder löschen
    setcookie("slidCookie", "", time() - 3600);
}

// bei Klick auf den Zuweisen Button, werden MitarbeiterNummer & Status aktualisiert sowie ein aktueller Zeitstempel hinterlegt
if (isset($_POST['assign_slid']) && isset($_POST['sessionId'])) {
    $slid = $_POST['assign_slid'];
    $sessionId = $_POST['sessionId'];

    $assign_sql = "UPDATE Sprint_Log SET Assignee = '$sessionId', Status='zugewiesen', Zeitstempel_zugewiesen=now() WHERE Sprint_Log.SL_ID = '$slid'";
    mysqli_query($conn, $assign_sql);
}


// Aktualisieren von realen Aufwand
if (isset($_POST['editTodo']) && isset($_POST['realEffort'])) {

    
    for ($i = 0; $i < count($_POST['slid']); $i++) {
        $realEffort = $_POST['realEffort'][$i];
        $slid = $_POST['slid'][$i];

        $updateRealEffort_sql = "UPDATE Sprint_Log SET realerAufwand = '$realEffort' WHERE Sprint_Log.SL_ID = '$slid'";

        mysqli_query($conn, $updateRealEffort_sql);
    }
}

// Aktualisieren von geschätztem Aufwand
if (isset($_POST['editTodo']) && isset($_POST['estimatedEffort'])) {

    for ($i = 0; $i < count($_POST['slid']); $i++) {
        
        $estimatedEffort = $_POST['estimatedEffort'][$i];
        $slid = $_POST['slid'][$i];

        $updateEstimatedEffort_sql = "UPDATE Sprint_Log SET geschaetzterAufwand = '$estimatedEffort' WHERE Sprint_Log.SL_ID = '$slid'";
        mysqli_query($conn, $updateEstimatedEffort_sql);
        $status_sql = "UPDATE Sprint_Log SET Status='inBearbeitung', Zeitstempel_inBearbeitung=now() WHERE Sprint_Log.SL_ID = '$slid'";
        mysqli_query($conn, $status_sql);
    }
}

// Aktualisieren von Testcoverage
if (isset($_POST['editTodo']) && isset($_POST['testcoverage'])) {

    for ($i = 0; $i < count($_POST['slid']); $i++) {

        $testcoverage = $_POST['testcoverage'][$i];
        $slid = $_POST['slid'][$i];

        $updateTestcoverage_sql = "UPDATE Sprint_Log SET Testcoverage = '$testcoverage' WHERE Sprint_Log.SL_ID = '$slid'";

        mysqli_query($conn, $updateTestcoverage_sql);
    }
}

header("Location: ../home.php");
