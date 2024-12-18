<?php  

// hier werden die Daten, die der Product Owner aus home.php absendet, weiterverarbeitet

include "../db_conn.php";
include "sendmails.php";

date_default_timezone_set("Europe/Berlin");

// Eintragen eines neuen Scores für die Kommunikationsintensität
if(isset($_POST['CSsubmit'])) {
    $sprintID = $_POST['SprintSelectCS'];
    $score = $_POST['cs'];

    $newScore_sql = "UPDATE Sprint SET Zufriedenheit_Teamwork=$score WHERE Sprint_ID = '$sprintID'";
    mysqli_query($conn, $newScore_sql);

}

// Eintragen eines neuen Scores für den Net Promoter Score
if(isset($_POST['NPSsubmit'])) {
    $sprintID = $_POST['SprintSelectNPS'];
    $score = $_POST['nps'];

    $newScore_sql = "UPDATE Sprint SET NetPromoterScore=$score WHERE Sprint_ID = '$sprintID'";
    mysqli_query($conn, $newScore_sql);

    // anschließend wird überprüft, ob der neue Gesamtwert unter dem Grenzwert von 1 liegt

    $totalNPS_sql = "SELECT COUNT(Sprint_ID) FROM Sprint WHERE NetPromoterScore IS NOT NULL";
    $totalNPS_res = mysqli_query($conn, $totalNPS_sql);
    $totalNPS_row = mysqli_fetch_assoc($totalNPS_res);
    $totalNPS = $totalNPS_row['COUNT(Sprint_ID)'];

    $promoter_sql = "SELECT COUNT(Sprint_ID) FROM Sprint WHERE NetPromoterScore > 8";
    $promoter_res = mysqli_query($conn, $promoter_sql);
    $promoter_row = mysqli_fetch_assoc($promoter_res);
    $promoter = $promoter_row['COUNT(Sprint_ID)'];

    $detraktoren_sql = "SELECT COUNT(Sprint_ID) FROM Sprint WHERE NetPromoterScore < 7";
    $detraktoren_res = mysqli_query($conn, $detraktoren_sql);
    $detraktoren_row = mysqli_fetch_assoc($detraktoren_res);
    $detraktoren = $detraktoren_row['COUNT(Sprint_ID)'];

    $nps_overall = round((($promoter / $totalNPS) * 100) - (($detraktoren / $totalNPS) * 100), 1);

    // bei zu niedrigem Gesamt NPS werden Alertmails an das Management verschickt
    if($nps_overall < 1) {
        $betreff = "Net Promoter Score unter 1";
        $text = "Der Net Promoter Score ist mit dem Rating von Sprint Nummer: " . $sprintID . " auf einen Wert von unter 1 gesunken. \nUm weitere Details zu erhalten melden Sie sich unter https://web06.iis.uni-bamberg.de/WIP/wip21_g2/ in AgileView an.";
        $mailadresse_sql = "SELECT Mailadresse FROM Mitarbeiter JOIN Rollenzuweisung ON 
        Mitarbeiter.Mitarbeiter_ID = Rollenzuweisung.MA_ID JOIN Rollen ON Rollen.ID = Rollenzuweisung.Rollen_ID 
        WHERE Rollenbezeichnung='management'";
        $mailadresse_res = mysqli_query($conn, $mailadresse_sql);
        while($mailadresse_row = mysqli_fetch_assoc($mailadresse_res)) {
            $mailadresse = $mailadresse_row['Mailadresse'];
            sendmails($text, $mailadresse, $betreff);
        }
    }

    // überprüfen, ob der neue Wert des betreuenden Scrum Masters unter dem Grenzwert von 1 liegt

    $betreuer_sql = "SELECT FK_ScrumMaster FROM Sprint WHERE Sprint_ID = '$sprintID'";
    $betreuer_res = mysqli_query($conn, $betreuer_sql);
    $betreuer_row = mysqli_fetch_assoc($betreuer_res);
    $betreuer = $betreuer_row['FK_ScrumMaster'];

    $totalNPS_sql = "SELECT COUNT(Sprint_ID) FROM Sprint WHERE NetPromoterScore IS NOT NULL AND FK_ScrumMaster='$betreuer'";
    $totalNPS_res = mysqli_query($conn, $totalNPS_sql);
    $totalNPS_row = mysqli_fetch_assoc($totalNPS_res);
    $totalNPS = $totalNPS_row['COUNT(Sprint_ID)'];

    $promoter_sql = "SELECT COUNT(Sprint_ID) FROM Sprint WHERE NetPromoterScore > 8 AND FK_ScrumMaster='$betreuer'";
    $promoter_res = mysqli_query($conn, $promoter_sql);
    $promoter_row = mysqli_fetch_assoc($promoter_res);
    $promoter = $promoter_row['COUNT(Sprint_ID)'];

    $detraktoren_sql = "SELECT COUNT(Sprint_ID) FROM Sprint WHERE NetPromoterScore < 7 AND FK_ScrumMaster='$betreuer'";
    $detraktoren_res = mysqli_query($conn, $detraktoren_sql);
    $detraktoren_row = mysqli_fetch_assoc($detraktoren_res);
    $detraktoren = $detraktoren_row['COUNT(Sprint_ID)'];

    $npsSM = round((($promoter / $totalNPS) * 100) - (($detraktoren / $totalNPS) * 100), 1);

    // bei zu niedrigem NPS wird eine Alertmail an den betreuenden Scrum Master verschickt
    if($npsSM < 1) {
        $betreff = "Net Promoter Score unter 1";
        $text = "Der Net Promoter Score ist mit dem Rating von Sprint Nummer: " . $sprintID . " auf einen Wert von unter 1 gesunken. \nUm weitere Details zu erhalten melden Sie sich unter https://web06.iis.uni-bamberg.de/WIP/wip21_g2/ in AgileView an.";
        $mailadresse_sql = "SELECT Mailadresse FROM Mitarbeiter WHERE Mitarbeiter_ID = '$betreuer'";
        $mailadresse_res = mysqli_query($conn, $mailadresse_sql);
        $mailadresse_row = mysqli_fetch_assoc($mailadresse_res);
        $mailadresse = $mailadresse_row['Mailadresse'];
        sendmails($text, $mailadresse, $betreff);
    }
}

// Anlegen eines neuen Story Point
if(isset($_POST['submitNewStory'])) {

    $newSP_sql = mysqli_prepare($conn, "INSERT INTO Story_Points (Bezeichnung, Prioritaet, BusinessValue, Status, Zeitstempel_offen, ProductOwner_ID) VALUES (?, ?, ?, 'offen', now(), ?)");
    
    $bezeichnung = $_POST['storyBezeichnung'];
    $prio = $_POST['selectPrio'];
    $value = $_POST['businessValue'];
    $mid = $_POST['pid'];

    mysqli_stmt_bind_param($newSP_sql, "ssii", $bezeichnung, $prio, $value, $mid);
    mysqli_stmt_execute($newSP_sql);
}

// Bearbeiten eines Story Points
if(isset($_POST['editSPsubmit'])) {
    $editSP_sql = mysqli_prepare($conn, "UPDATE Story_Points SET Bezeichnung=?, Prioritaet=?, BusinessValue=? WHERE SP_ID=?");

    $bezeichnung = $_POST['editbezeichnung'];
    $prio = $_POST['editselectPrio'];
    $value = $_POST['editbusinessValue'];
    $spid = $_POST['editSP_ID'];

    mysqli_stmt_bind_param($editSP_sql, "ssii", $bezeichnung, $prio, $value, $spid);
    mysqli_stmt_execute($editSP_sql);
}

// Löschen eines Story Points
if(isset($_POST['deleteSP'])) {
    $spid = $_POST['deleteSP'];

    $deleteSP_sql = "DELETE FROM Story_Points WHERE SP_ID='$spid'";
    mysqli_query($conn, $deleteSP_sql);
}

// Bewerten der Anforderungskorrektheit und der Abnahme eines Story Points
if(isset($_POST['submitRateStory'])) {
    $spid = $_POST['rateSP_ID'];
    $slid = $_POST['rateSL_ID'];
    $sprint_ID = $_POST['Sprint_ID'];
    $correctness = $_POST['rateCorrectness'];
    $abnahme = $_POST['abnahme'];

    $rateSP_sql = "UPDATE Sprint_Log SET Status='geschlossen', Zeitstempel_geschlossen=now(), Anforderungskorrektheit='$correctness', Abnahme='$abnahme' WHERE SL_ID='$slid'";
    mysqli_query($conn, $rateSP_sql);

    if($abnahme == 'Akzeptiert') {
        $closeSP_sql = "UPDATE Story_Points SET Status='geschlossen', Zeitstempel_geschlossen=now() WHERE SP_ID='$spid'";
        mysqli_query($conn, $closeSP_sql);
    } 

    // überprüfen, ob der neue Wert den Grenzwert von unter 5 erreicht hat
    $betreuer_sql = "SELECT FK_ScrumMaster FROM Sprint WHERE Sprint_ID = '$sprint_ID'";
    $betreuer_res = mysqli_query($conn, $betreuer_sql);
    $betreuer_row = mysqli_fetch_assoc($betreuer_res);
    $betreuer = $betreuer_row['FK_ScrumMaster'];

    $correctness_sql = "SELECT AVG(Anforderungskorrektheit) FROM Sprint_Log JOIN Sprint ON Sprint.Sprint_ID = Sprint_Log.Sprint_ID WHERE Sprint.FK_ScrumMaster = '$betreuer'";
    $correctness_res = mysqli_query($conn, $correctness_sql);
    $correctness_row = mysqli_fetch_assoc($correctness_res);
    $correctness = round($correctness_row['AVG(Anforderungskorrektheit)'], 1);

    // bei zu niedrigem Wert wird eine Alertmail an den betreuenden Scrum Master verschickt
    if($correctness < 5) {
        $betreff = "Anforderungskorrektheit unter 5";
        $text = "Der Anforderungskorrektheit ist mit dem Rating von Story Point " . $sprintID . " aus Sprint Nummer: " . $sprintID . " auf einen Wert von unter 5 gesunken. \nUm weitere Details zu erhalten melden Sie sich unter https://web06.iis.uni-bamberg.de/WIP/wip21_g2/ in AgileView an.";
        $mailadresse_sql = "SELECT Mailadresse FROM Mitarbeiter WHERE Mitarbeiter_ID = '$betreuer'";
        $mailadresse_res = mysqli_query($conn, $mailadresse_sql);
        $mailadresse_row = mysqli_fetch_assoc($mailadresse_res);
        $mailadresse = $mailadresse_row['Mailadresse'];
        sendmails($text, $mailadresse, $betreff);
    }

}

header("Location: ../home.php");
