<?php  

// hier werden die Datengrundlagen, die in home.php, teams.php & initiativen.php für den Administrator genutzt werden, zusammengestellt

include "../db_conn.php";

if (isset($_SESSION['username']) && isset($_SESSION['id']) && $_SESSION['role'] == 'administrator') {

    // Zusammenstellen der Nutzertabelle
    $nutzer_sql = "SELECT * FROM Mitarbeiter LEFT JOIN Team on Team.Team_ID = Mitarbeiter.FK_Team ORDER BY Mitarbeiter_ID ASC";
    $nutzer_res = mysqli_query($conn, $nutzer_sql);

    // Zusammenstellen der Teamtabelle
    $teams_sql = "SELECT * FROM Team JOIN Mitarbeiter ON Team.FK_ScrumMaster = Mitarbeiter.Mitarbeiter_ID ORDER BY Team_ID ASC";
    $teams_res = mysqli_query($conn, $teams_sql);

    // Zusammenstellen der Initiativentabelle
    $initiative_sql = "SELECT * FROM Initiative ORDER BY Initiative_ID ASC";
    $initiative_res = mysqli_query($conn, $initiative_sql);


}else{
	header("Location: ../index.php");
}
