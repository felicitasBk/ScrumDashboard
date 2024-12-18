<?Php

session_start();
include "../db_conn.php";

header ('Content-Type: application/json');

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

    //Spi Chart Daten aus der Datenbank holen
    $spiDays_sql = "SELECT Enddatum, Startdatum FROM Sprint WHERE Sprint_ID=$currentSprintID";
    $spiDays_sql = mysqli_query($conn, $spiDays_sql);
    $spiDays_row = mysqli_fetch_assoc($spiDays_sql);
    $spiDays = diff_time(strtotime($spiDays_row['Enddatum']) - strtotime($spiDays_row['Startdatum']));
    $i = $spiDays['tag'];

    $totalSP_sql = "SELECT SUM(Story_Points.BusinessValue) FROM Sprint_Log JOIN Story_Points ON Sprint_Log.StoryPoint_ID = Story_Points.SP_ID WHERE Sprint_Log.Sprint_ID = $currentSprintID";
    $totalSP_res = mysqli_query($conn, $totalSP_sql);
    $totalSP_row = mysqli_fetch_assoc($totalSP_res);
    $totalSP = $totalSP_row['SUM(Story_Points.BusinessValue)'];

    $SPperDay = round($totalSP/$i , 2);


    $spiValue_sql = "SELECT Sprint_Log.Zeitstempel_geschlossen, Story_Points.BusinessValue, Story_Points.Bezeichnung FROM Sprint_Log JOIN Story_Points ON Sprint_Log.StoryPoint_ID = Story_Points.SP_ID WHERE Sprint_Log.Sprint_ID = $currentSprintID AND Sprint_Log.Status = 'geschlossen'  ORDER BY Sprint_Log.Zeitstempel_geschlossen ASC";
    $spiValue_res = mysqli_query($conn, $spiValue_sql);
    $spiValue_row = mysqli_fetch_assoc($spiValue_res);

    $masterSpi_data = array ();
    $value = 0;
    $counter = 0;
    $plannedValue = 0;

    //Array mit Werten füllen
    while($counter<=$i){

        if($spiValue_row != NULL) {     
            $timePassed = diff_time(strtotime($spiValue_row['Zeitstempel_geschlossen']) - strtotime($spiDays_row['Startdatum']));
            if($counter == ($timePassed['tag'])) {
                $currentDay = date('j', strtotime($spiValue_row['Zeitstempel_geschlossen']));
                    while ($spiValue_row != NULL && $currentDay == date('j', strtotime($spiValue_row['Zeitstempel_geschlossen']))) {
                    
                        if (isset($spiValue_row['BusinessValue'])){
                        $value = $value + $spiValue_row['BusinessValue'];
                        }
                        $spiValue_row = mysqli_fetch_assoc($spiValue_res);
                        
                    
                    }
                
            } 
        }


        $masterSpi_data[] = array("Tag" => "".$counter)  + array("Value" => "".$value) + array ( "plannedValue" => "".$plannedValue);
        
        $plannedValue = $plannedValue + $SPperDay;
        $counter++;     
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

    $SPperDay = round($totalSP/$i, 2);

    $doneSP_sql = "SELECT Zeitstempel_geschlossen FROM Sprint_Log WHERE Sprint_ID=$currentSprintID AND Status='geschlossen' ORDER BY Zeitstempel_geschlossen ASC";
    $doneSP_res = mysqli_query($conn, $doneSP_sql);
    $doneSP_row = mysqli_fetch_assoc($doneSP_res);

    $ideal = $totalSP;
    $actual = $totalSP;
    $masterBurndown_data = array ();
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
        $ideal =  $ideal-$SPperDay;

        if ($aktuellerTag == 0){
            $ideal = $totalSP;
        }

        if ($ideal < 0){
            $ideal = 0;
        }

        $masterBurndown_data[] = array("Tag" => "".$aktuellerTag)  + array("ideal" => "".$ideal) +  array("actual" => "".$actual);
        
        $i--;
        $aktuellerTag++;    
    }



    //Daten für den Team diversity Chart des Scrum Masters holen und in ein Array schreiben
    $masterDiversity_sql = "SELECT COUNT(Mitarbeiter.Fachgebiet) AS Anzahl , Mitarbeiter.Fachgebiet FROM Mitarbeiter JOIN Team ON Team.Team_ID = Mitarbeiter.FK_Team WHERE Team.FK_ScrumMaster = $mid AND Mitarbeiter.Mitarbeiter_ID != $mid GROUP BY Mitarbeiter.Fachgebiet";
    $masterDiversityResult = $conn -> query ($masterDiversity_sql);
    $masterDiversityData = array ();
    foreach ($masterDiversityResult as $row  ){
        $masterDiversityData[] = $row;
    }

    // Daten über die Sprint_ID und alle geplanten Story Points für den Velocity Chart des Scrum Masters holen und in ein array schreiben
    $masterSprints_sql = "SELECT COUNT(Sprint_ID) AS Commitment  , Sprint_ID  FROM Sprint_Log GROUP BY Sprint_ID";
    $masterResult = $conn->query($masterSprints_sql);
    $masterSprints_data = array();
    foreach ($masterResult as $row) {
        $masterSprints_data[] = $row;
    }
 
    // Daten über die abgeschlossenen Story Points für den Velocity Chart des Scrum Masters holen und in ein array schreiben
    $masterSprints_sql2 = "SELECT COUNT(Sprint_ID) AS Complete, Sprint_ID  FROM Sprint_Log WHERE Status = 'geschlossen' GROUP BY Sprint_ID";
    $masterResult2 = $conn -> query ($masterSprints_sql2);
    $masterCompletion_data = array();
    foreach ($masterResult2 as $row) {
        $masterCompletion_data[] = $row;
    }
  
    //Daten für den Lead Time Chart des Scrum Masters holen und in ein Array schreiben
    $masterLeadtime_sql = "SELECT Sprint_Log.Zeitstempel_inReview , Sprint_Log.Zeitstempel_zugewiesen, Story_Points.Bezeichnung FROM Sprint_Log JOIN Story_Points ON Sprint_Log.StoryPoint_ID = Story_Points.SP_ID WHERE (Sprint_Log.Status = 'geschlossen' OR Sprint_Log.Status = 'inReview') ORDER BY Zeitstempel_inReview DESC";
    $masterLeadtime_result = $conn -> query ($masterLeadtime_sql);
    $masterLeadtime_data = array ();
    foreach ($masterLeadtime_result as $row){
        $enddatum = strtotime($row['Zeitstempel_inReview']);
        $startdatum = strtotime($row['Zeitstempel_zugewiesen']);
        $leadtime = diff_time2($enddatum - $startdatum);
        $masterLeadtime_data[] = $row + $leadtime;
    }
    } else {
        header("Location: ../index.php");
    }

    // Funktion die die Zeitdifferenz in Tagen berechnet
    function diff_time2($differenz) {  
        $tag  = floor($differenz / (3600*24));
        return array("leadtime"=>$tag);
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
    echo json_encode (array($masterDiversityData, array($masterLeadtime_data,array($masterSprints_data,array($masterCompletion_data, array($masterBurndown_data, array($masterSpi_data)))))));

  
