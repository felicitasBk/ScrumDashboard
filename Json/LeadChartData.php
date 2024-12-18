<?Php

include "../db_conn.php";

header ('Content-Type: application/json');

    //Daten für Forecast Consistency Chart holen und in ein array schreiben 
    $leadForecast_sql = "SELECT geplanteDauerInTagen, Enddatum, Startdatum, Sprint_ID FROM Sprint";
    $leadForecastResult = $conn -> query ($leadForecast_sql);
    $leadForecast_data = array ();

    foreach ($leadForecastResult as $row ){
        //Tatsächliche Dauer berechnen
        $enddatum = strtotime($row['Enddatum']);
        $startdatum = strtotime($row['Startdatum']);
        $realeDauer = diff_time($enddatum - $startdatum);
        $leadForecast_data[] = $row + $realeDauer;
    }

    //Daten für Epic Burndown Chart berechnen
    $estimate_sql = "SELECT COUNT(Story_Points.SP_ID), Sprint.Sprint_ID FROM Story_Points JOIN Sprint WHERE Story_Points.Zeitstempel_offen <= Sprint.Startdatum GROUP BY Sprint.Sprint_ID ";
    $estimate_res = mysqli_query($conn, $estimate_sql);
    $estimate_row = mysqli_fetch_assoc($estimate_res);
    $estimate = $estimate_row ['COUNT(Story_Points.SP_ID)'];

    $maxSprint_sql = "SELECT COUNT(Sprint_ID) FROM Sprint ";
    $max_res = mysqli_query($conn, $maxSprint_sql);
    $max_row = mysqli_fetch_assoc($max_res);
    $maxSprint = $max_row ['COUNT(Sprint_ID)'];

    $sprintNumber_sql = "SELECT * FROM Sprint ";
    $sprintNumber_res = mysqli_query($conn, $sprintNumber_sql);
    $sprintNumber_row = mysqli_fetch_assoc($sprintNumber_res);



    $epicComp_sql = "SELECT COUNT(Sprint_Log.SL_ID) FROM Sprint_Log JOIN Story_Points ON Sprint_Log.StoryPoint_ID = Story_Points.SP_ID WHERE Sprint_Log.Status = 'geschlossen' GROUP BY Sprint_Log.Sprint_ID ORDER BY Sprint_Log.Sprint_ID ASC" ;
    $epicComp_res = mysqli_query($conn, $epicComp_sql);
    $epicComp_row = mysqli_fetch_assoc($epicComp_res);

    $sql = "SELECT SP_ID, Zeitstempel_offen FROM Story_Points";
    $sql_res = mysqli_query ($conn, $sql);
    $sql_row = mysqli_fetch_assoc($sql_res);

    $sql2 = "SELECT Startdatum, Sprint_ID FROM Sprint ORDER BY Sprint_ID ASC";
    $sql2_res = mysqli_query ($conn, $sql2);
    $sql2_row = mysqli_fetch_assoc($sql2_res);

    $epicBurndown_data = array();
    $zähler = 0;
    $aktuellerSprint = 0;


    while($zähler<= $maxSprint){
        $added = 0;
        $completed = 0;

    if ( $zähler == 0){ 
        $epicBurndown_data[] = array ( "current" => "".$estimate) + array ("Sprint" => "Commitment" ) + array("completed" => "0") + array ("added" => "0");
        $zähler++;

    } else {

        if ($epicComp_row != null){
        $completed = $completed - $epicComp_row['COUNT(Sprint_Log.SL_ID)'];
        }

        $estimate =  $estimate + $completed;

        if ($estimate_row != null){
        $backlogCount = $estimate_row ['COUNT(Story_Points.SP_ID)'];
        $estimate_row = mysqli_fetch_assoc($estimate_res);
        }
        if ($estimate_row != null){
        $backlogCountNext = $estimate_row ['COUNT(Story_Points.SP_ID)'];
        $added = $backlogCountNext - $backlogCount ;
        }
        $aktuellerSprint = $sprintNumber_row['Sprint_ID'];
        if ($estimate < 0 ){
            $i = 0;
        $epicBurndown_data[] = array ( "current" => "".$i) + array ("Sprint" => "Sprint ".$aktuellerSprint ) + array("completed" => "".$completed) + array("added" => "".$added);
        } else {
        $epicBurndown_data[] = array ( "current" => "".$estimate) + array ("Sprint" => "Sprint ".$aktuellerSprint ) + array("completed" => "".$completed) + array("added" => "".$added);
        }
        $estimate = $estimate + $added;
        $epicComp_row = mysqli_fetch_assoc($epicComp_res);
        $sql2_row = mysqli_fetch_assoc($sql2_res);
        $sprintNumber_row = mysqli_fetch_assoc($sprintNumber_res);

            $zähler++;
        }
    }

    //Differenz zwischen Start und Enddatum in Tagen berechnen
    function diff_time($differenz) {  
        $tag  = floor($differenz / (3600*24));
        return array("realeDauer"=>$tag);
    }
    
    $conn-> close();
    //Daten in Json encoden
    echo json_encode (array($leadForecast_data, $epicBurndown_data));
