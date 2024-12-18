<?Php

session_start();
include "../db_conn.php";

header ('Content-Type: application/json');

    if (isset($_SESSION['username']) && isset($_SESSION['id']) && $_SESSION['role'] == 'productowner') {

        date_default_timezone_set("Europe/Berlin");
        $timestamp = time();
        $mid = $_SESSION['id'];

        // ist der Product Owner aktuell an einem offenen Sprint beteiligt, wird dieser als Basis für die anderen Werte genommen, ansonsten wird der zuletzt bearbeitete Sprint ausgewählt
        $latest_Sprint = "SELECT MAX(Sprint_ID) FROM Sprint WHERE Sprint_ID IN (SELECT Sprint_ID 
        FROM Sprint_Log JOIN Story_Points ON Story_Points.SP_ID = Sprint_Log.StoryPoint_ID WHERE Story_Points.ProductOwner_ID='$mid')";
        $latestSprint_res = mysqli_query($conn, $latest_Sprint);
        $latestSprint_row = mysqli_fetch_assoc($latestSprint_res);
        $latestSprint = $latestSprint_row['MAX(Sprint_ID)'];

        $currentSprintID_sql = "SELECT Sprint_ID FROM Sprint WHERE Sprint_ID IN (SELECT Sprint_ID 
        FROM Sprint_Log JOIN Story_Points ON Story_Points.SP_ID = Sprint_Log.StoryPoint_ID WHERE Story_Points.ProductOwner_ID='$mid') AND 
        Abgeschlossen='offen'";
        $currentSprintID_res = mysqli_query($conn, $currentSprintID_sql);
        if (mysqli_num_rows($currentSprintID_res) != 0) {
            $currentSprintID_row = mysqli_fetch_assoc($currentSprintID_res);
            $currentSprintID = $currentSprintID_row['Sprint_ID'];
        } else {
            $currentSprintID = $latestSprint;
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

    $SPperDay = round($totalSP/$i, 2);


    $spiValue_sql = "SELECT Sprint_Log.Zeitstempel_geschlossen, Story_Points.BusinessValue, Story_Points.Bezeichnung FROM Sprint_Log JOIN Story_Points ON Sprint_Log.StoryPoint_ID = Story_Points.SP_ID WHERE Sprint_Log.Sprint_ID = $currentSprintID AND Sprint_Log.Status = 'geschlossen'  ORDER BY Sprint_Log.Zeitstempel_geschlossen ASC";
    $spiValue_res = mysqli_query($conn, $spiValue_sql);
    $spiValue_row = mysqli_fetch_assoc($spiValue_res);

    $ownerSpi_data = array ();
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


        $ownerSpi_data[] = array("Tag" => "".$counter)  + array("Value" => "".$value) + array( "PlannedValue" => "".$plannedValue);
        
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

    $doneSP_sql = "SELECT Zeitstempel_geschlossen FROM Sprint_Log WHERE Sprint_ID=$currentSprintID AND  Status='geschlossen' ORDER BY Zeitstempel_geschlossen ASC";
    $doneSP_res = mysqli_query($conn, $doneSP_sql);
    $doneSP_row = mysqli_fetch_assoc($doneSP_res);

    $ideal = $totalSP;
    $actual = $totalSP;
    $ownerBurndown_data = array ();
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

        $ownerBurndown_data[] = array("Tag" => "".$aktuellerTag)  + array("ideal" => "".$ideal) +  array("actual" => "".$actual);
        
        $i--;
        $aktuellerTag++;    
    }
    
   

    // Daten über die Sprint_ID und alle geplanten Story Points für den Velocity Chart des Product Owners holen und in ein array schreiben
    $ownerSprints_sql = "SELECT COUNT(Sprint_ID) AS Commitment  , Sprint_ID  FROM Sprint_Log GROUP BY Sprint_ID";
    $ownerResult = $conn->query($ownerSprints_sql);
    $ownerSprints_data = array();
    foreach ($ownerResult as $row) {
        $ownerSprints_data[] = $row;
    }

    // Daten über die abgeschlossenen Story Points für den Velocity Chart des Product Owners holen und in ein array schreiben
    $ownerSprints_sql2 = "SELECT COUNT(Sprint_ID) AS Complete, Sprint_ID  FROM Sprint_Log WHERE Status = 'geschlossen' GROUP BY Sprint_ID";
    $ownerResult2 = $conn -> query ($ownerSprints_sql2);
    $ownerCompletion_data = array();
    foreach ($ownerResult2 as $row) {
        $ownerCompletion_data[] = $row;
    }

     //Daten für den Lead Time Chart des Product Owner holen und in ein Array schreiben
     $ownerLeadtime_sql = "SELECT Sprint_Log.Zeitstempel_inReview , Sprint_Log.Zeitstempel_zugewiesen, Story_Points.Bezeichnung FROM Sprint_Log JOIN Story_Points ON Sprint_Log.StoryPoint_ID = Story_Points.SP_ID WHERE (Sprint_Log.Status = 'geschlossen' OR Sprint_Log.Status = 'inReview') ORDER BY Zeitstempel_inReview DESC";
     $ownerLeadtime_result = $conn -> query ($ownerLeadtime_sql);
     $ownerLeadtime_data = array ();
     foreach ($ownerLeadtime_result as $row){
         $enddatum = strtotime($row['Zeitstempel_inReview']);
         $startdatum = strtotime($row['Zeitstempel_zugewiesen']);
         $leadtime = diff_time2($enddatum - $startdatum);
         $ownerLeadtime_data[] = $row + $leadtime;
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
    echo json_encode (array ($ownerSprints_data,array($ownerCompletion_data, array($ownerLeadtime_data, array ($ownerBurndown_data, array($ownerSpi_data))))));
  
    
?>