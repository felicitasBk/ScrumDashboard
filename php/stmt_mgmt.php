<?php

// hier werden die Datengrundlagen, die in home.php für das Management genutzt werden, zusammengestellt

include "../db_conn.php";

if (isset($_SESSION['username']) && isset($_SESSION['id']) && $_SESSION['role'] == 'management') {

    date_default_timezone_set("Europe/Berlin");
    $timestamp = time();

    // Daten für Kachel: Überstunden
    $overtime_sql = "SELECT SUM(Ueberstunden) FROM Mitarbeiter";
    $overtime_res = mysqli_query($conn, $overtime_sql);
    $overtime_row = mysqli_fetch_assoc($overtime_res);
    $overtime = $overtime_row['SUM(Ueberstunden)'];

    // Daten für Kachel: Anforderungskorrektheit
    $correctness_sql = "SELECT AVG(Anforderungskorrektheit) FROM Sprint_Log";
    $correctness_res = mysqli_query($conn, $correctness_sql);
    $correctness_row = mysqli_fetch_assoc($correctness_res);
    $correctness = round($correctness_row['AVG(Anforderungskorrektheit)'], 1);

    // Daten für Kachel: Zufriedenheit der Entwickler
    $devmood_sql = "SELECT AVG(Zufriedenheit_Entwickler) FROM Sprint_Log";
    $devmood_res = mysqli_query($conn, $devmood_sql);
    $devmood_row = mysqli_fetch_assoc($devmood_res);
    $devmood = round($devmood_row['AVG(Zufriedenheit_Entwickler)'], 1);

    // Daten für Kachel: Net Promoter Score
    // NPS = % Promoter – % Detraktoren; kann somit zwischen 100 & -100 liegen
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

    $nps = round((($promoter / $totalNPS) * 100) - (($detraktoren / $totalNPS) * 100), 1);

    // Daten für Kachel: Kommuniktationsintensität
    $communication_sql = "SELECT AVG(Zufriedenheit_Teamwork) FROM Sprint";
    $communication_res = mysqli_query($conn, $communication_sql);
    $communication_row = mysqli_fetch_assoc($communication_res);
    $communication = round($communication_row['AVG(Zufriedenheit_Teamwork)'], 1);

    // Daten für Kachel: Teambewertung
    $devrating_sql = "SELECT AVG(Teambewertung) FROM Sprint";
    $devrating_res = mysqli_query($conn, $devrating_sql);
    $devrating_row = mysqli_fetch_assoc($devrating_res);
    $devrating = round($devrating_row['AVG(Teambewertung)'], 1);

    // Daten für Kachel: Mitarbeiterfluktuation
    // basiert auf Schlüter-Formel: Fluktuationsrate = (Abgänge ÷ (Anfangspersonalbestand + Zugänge)) x 100
    $gesamtBestand_sql = "SELECT COUNT(Mitarbeiter_ID) FROM Mitarbeiter";
    $gesamtBestand_res = mysqli_query($conn, $gesamtBestand_sql);
    $gesamtBestand_row = mysqli_fetch_assoc($gesamtBestand_res);
    $gesamtBestand = $gesamtBestand_row['COUNT(Mitarbeiter_ID)'];
    $abgaenge_sql = "SELECT COUNT(Mitarbeiter_ID) FROM Mitarbeiter WHERE Status='ausgeschieden'";
    $abgaenge_res = mysqli_query($conn, $abgaenge_sql);
    $abgaenge_row = mysqli_fetch_assoc($abgaenge_res);
    $abgaenge = $abgaenge_row['COUNT(Mitarbeiter_ID)'];
    $devfluctuation = ($abgaenge / $gesamtBestand) * 100;
} else {
    header("Location: index.php");
}

function diff_time($differenz)
{
    $tag  = floor($differenz / (3600 * 24));
    $std  = floor($differenz / 3600 % 24);
    $min  = floor($differenz / 60 % 60);

    return array("min" => $min, "std" => $std, "tag" => $tag);
}
