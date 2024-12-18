<?Php

include "../db_conn.php";

session_start();
header ('Content-Type: application/json');

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
}else {
	header("Location: ../index.php");
}



    // Burndownchart Daten aus der Datenbank holen und berechnen
    $burndownDays_sql = "SELECT Enddatum, Startdatum FROM Sprint WHERE Sprint_ID=$currentSprintID";
    $burndownDays_res = mysqli_query($conn, $burndownDays_sql);
    $burndownDays_row = mysqli_fetch_assoc($burndownDays_res);
    $burndownDays = diff_time(strtotime($burndownDays_row['Enddatum']) - strtotime($burndownDays_row['Startdatum']));
    $i = $burndownDays['tag'];
   

    $totalSP_sql = "SELECT COUNT(SL_ID) FROM Sprint_Log WHERE Sprint_ID=$currentSprintID";
    $totalSP_res = mysqli_query($conn, $totalSP_sql);
    $totalSP_row = mysqli_fetch_assoc($totalSP_res);
    $totalSP = $totalSP_row['COUNT(SL_ID)'];

    $SPperDay = round($totalSP/$i , 2);

    $doneSP_sql = "SELECT Zeitstempel_geschlossen FROM Sprint_Log WHERE Sprint_ID=$currentSprintID AND Status ='geschlossen'ORDER BY Zeitstempel_geschlossen ASC";
    $doneSP_res = mysqli_query($conn, $doneSP_sql);
    $doneSP_row = mysqli_fetch_assoc($doneSP_res);

    $ideal = $totalSP;
    $actual = $totalSP;
    $devBurndown_data = array ();
    $aktuellerTag = 0;

    // Array mit den Daten füllen
    while($i>=0){
        $count = 0;

        if($doneSP_row != NULL) {
            $timePassed = diff_time(strtotime($doneSP_row['Zeitstempel_geschlossen']) - strtotime($burndownDays_row['Startdatum']));
        
            if($i == ($burndownDays['tag']-$timePassed['tag'])) {
                $currentDay = date('j', strtotime($doneSP_row['Zeitstempel_geschlossen']));
                $count = 1;
                $doneSP_row = mysqli_fetch_assoc($doneSP_res);
                    while ($doneSP_row != NULL && $currentDay == date('j', strtotime($doneSP_row['Zeitstempel_geschlossen']))) {
                        $doneSP_row = mysqli_fetch_assoc($doneSP_res);
                        $count++;
                    }
                
            }
        }

        $actual = $actual - $count;
        if ($aktuellerTag != 0){
        $ideal =  $ideal-$SPperDay;
        }

        $devBurndown_data[] = array("Tag" => "".$aktuellerTag)  + array("ideal" => "".$ideal) +  array("actual" => "".$actual);
        
        $i--;
        $aktuellerTag++;    
    }
    

    // Daten über die Sprint_ID und alle geplanten Story Points für den Velocity Chart des Developers holen und in ein array schreiben
    $devSprints_sql = "SELECT COUNT(Sprint_Log.Sprint_ID) AS Commitment  , Sprint_Log.Sprint_ID  FROM Sprint_Log JOIN Sprint ON Sprint_Log.Sprint_ID = Sprint.Sprint_ID WHERE FK_team=(SELECT FK_Team From Mitarbeiter WHERE Mitarbeiter_ID='$mid') GROUP BY Sprint_Log.Sprint_ID";
    $devResult = $conn->query($devSprints_sql);
    $devSprints_data = array();
    foreach ($devResult as $row) {
        $devSprints_data[] = $row;
    }

    // Daten über die abgeschlossenen Story Points für den Velocity Chart des Developers holen und in ein array schreiben
    $devSprints_sql2 = "SELECT COUNT(Sprint_Log.Sprint_ID) AS Complete  , Sprint_Log.Sprint_ID  FROM Sprint_Log JOIN Sprint ON Sprint_Log.Sprint_ID = Sprint.Sprint_ID WHERE FK_team=(SELECT FK_Team From Mitarbeiter WHERE Mitarbeiter_ID='$mid') AND Sprint_Log.Status = 'geschlossen' GROUP BY Sprint_Log.Sprint_ID";
    $devResult2 = $conn -> query ($devSprints_sql2);
    $devCompletion_data = array();
    foreach ($devResult2 as $row) {
        $devCompletion_data[] = $row;
    }
   

    // Funktion die die Zeitdifferenz in Tagen std. und minuten berechnet
    function diff_time($differenz) {  
        $tag  = floor($differenz / (3600*24));
        $std  = floor($differenz / 3600 % 24);
        $min  = floor($differenz / 60 % 60);
     
        return array("min"=>$min,"std"=>$std,"tag"=>$tag);
     }


    $conn-> close();
    // Daten in Json encoden
    echo json_encode (array($devSprints_data, array($devCompletion_data, array ($devBurndown_data))));

