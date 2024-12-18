<?php

// hier werden die Datengrundlagen, die in home.php für den Product Owner genutzt werden, zusammengestellt

include "db_conn.php";

if (isset($_SESSION['username']) && isset($_SESSION['id']) && $_SESSION['role'] == 'productowner') {

   $mid = $_SESSION['id'];

   // Daten für Kachel: Anforderungskorrektheit
   $correctness_sql = "SELECT AVG(Anforderungskorrektheit) FROM Sprint_Log";
   $correctness_res = mysqli_query($conn, $correctness_sql);
   $correctness_row = mysqli_fetch_assoc($correctness_res);
   $correctness = round($correctness_row['AVG(Anforderungskorrektheit)'], 1);

   // Daten für Kachel: Net Promoter Score
   // NPS = % Promoter – % Detraktoren; kann somit zwischen 100 & -100 liegen
   $totalNPS_sql = "SELECT COUNT(Sprint_ID) FROM Sprint WHERE NetPromoterScore IS NOT NULL AND Sprint_ID IN (SELECT Sprint_ID 
   FROM Sprint_Log JOIN Story_Points ON Story_Points.SP_ID = Sprint_Log.StoryPoint_ID WHERE Story_Points.ProductOwner_ID='$mid')";
   $totalNPS_res = mysqli_query($conn, $totalNPS_sql);
   $totalNPS_row = mysqli_fetch_assoc($totalNPS_res);
   $totalNPS = $totalNPS_row['COUNT(Sprint_ID)'];

   $promoter_sql = "SELECT COUNT(Sprint_ID) FROM Sprint WHERE NetPromoterScore > 8 AND Sprint_ID IN (SELECT Sprint_ID 
   FROM Sprint_Log JOIN Story_Points ON Story_Points.SP_ID = Sprint_Log.StoryPoint_ID WHERE Story_Points.ProductOwner_ID='$mid')";
   $promoter_res = mysqli_query($conn, $promoter_sql);
   $promoter_row = mysqli_fetch_assoc($promoter_res);
   $promoter = $promoter_row['COUNT(Sprint_ID)'];

   $detraktoren_sql = "SELECT COUNT(Sprint_ID) FROM Sprint WHERE NetPromoterScore < 7 AND Sprint_ID IN (SELECT Sprint_ID 
   FROM Sprint_Log JOIN Story_Points ON Story_Points.SP_ID = Sprint_Log.StoryPoint_ID WHERE Story_Points.ProductOwner_ID='$mid')";
   $detraktoren_res = mysqli_query($conn, $detraktoren_sql);
   $detraktoren_row = mysqli_fetch_assoc($detraktoren_res);
   $detraktoren = $detraktoren_row['COUNT(Sprint_ID)'];

   if ($totalNPS != 0){
      $nps = round((($promoter / $totalNPS) * 100) - (($detraktoren / $totalNPS) * 100), 1);
   } else {
      $nps = 0;
   }
   // Daten für Kachel: Kommuniktationsintensität
   $communication_sql = "SELECT AVG(Zufriedenheit_Teamwork) FROM Sprint";
   $communication_res = mysqli_query($conn, $communication_sql);
   $communication_row = mysqli_fetch_assoc($communication_res);
   $communication = round($communication_row['AVG(Zufriedenheit_Teamwork)'], 1);

   // Daten für Tabelle: Sprints
   $sprints_sql = "SELECT Story_Points.Bezeichnung, Story_Points.SP_ID, Sprint_Log.Sprint_ID, Sprint_Log.SL_ID, Sprint_Log.Status, Sprint_Log.Anforderungskorrektheit, 
   Mitarbeiter.Name, Sprint_Log.Abnahme FROM Sprint_Log JOIN Story_Points ON Sprint_Log.StoryPoint_ID = Story_Points.SP_ID LEFT JOIN 
   Mitarbeiter ON Sprint_Log.Assignee = Mitarbeiter.Mitarbeiter_ID WHERE Story_Points.SP_ID IN (SELECT Story_Points.SP_ID FROM Story_Points JOIN Mitarbeiter ON Story_Points.ProductOwner_ID = Mitarbeiter.Mitarbeiter_ID WHERE 
   Mitarbeiter.Mitarbeiter_ID='$mid')";
   $sprints_res = mysqli_query($conn, $sprints_sql);

   // Daten für Tabelle: Product Backlog
   $productBacklog_sql = "SELECT Story_Points.SP_ID, Story_Points.Bezeichnung, Story_Points.BusinessValue, Story_Points.Prioritaet, 
   Story_Points.Status FROM Story_Points JOIN Mitarbeiter ON Story_Points.ProductOwner_ID = Mitarbeiter.Mitarbeiter_ID WHERE 
   Mitarbeiter.Mitarbeiter_ID='$mid'";
   $productBacklog_res = mysqli_query($conn, $productBacklog_sql);
} else {
   header("Location: ../index.php");
}
