<?php

// hier werden die Daten, die der Administrator aus home.php absendet, weiterverarbeitet

include "../db_conn.php";

date_default_timezone_set("Europe/Berlin");

// Anlegen eines neuen Teams
if(isset($_POST['teamSubmit'])) {
    $team_sql = mysqli_prepare($conn, "INSERT INTO Team (Bezeichnung, Spezialisierung, FK_ScrumMaster) VALUES (?,?,?)");

    $bezeichnung = $_POST['bezeichnung'];
    $spezi = $_POST['spezi'];
    $scrummaster = $_POST['ScrumMasterselect'];

    mysqli_stmt_bind_param($team_sql, "ssi", $bezeichnung, $spezi, $scrummaster);
    mysqli_stmt_execute($team_sql);

    if(isset($_POST['maID'])) {
        $mitglieder = $_POST['maID'];
        
        $teamName_sql = "SELECT Team_ID FROM Team WHERE Bezeichnung='$bezeichnung'";
        $teamName_res = mysqli_query($conn, $teamName_sql);
        $teamName_row = mysqli_fetch_assoc($teamName_res);
        $teamID = $teamName_row['Team_ID'];

        for ($i=0; $i<count($mitglieder); $i++) {
            $mid = $mitglieder[$i];
            $mitglieder_sql = "UPDATE Mitarbeiter SET FK_Team='$teamID' WHERE Mitarbeiter_ID='$mid'";
            mysqli_query($conn, $mitglieder_sql);
        }
    } 

    header("Location: ../teams.php");
}

// Bearbeiten eines Teams
if(isset($_POST['teamEditSubmit'])) { 
    $editTeam_sql = mysqli_prepare($conn, "UPDATE Team SET Bezeichnung=?, Spezialisierung=?, FK_ScrumMaster=? WHERE Team_ID=?");

    $Team_ID = $_POST['Team_ID'];
    $EditTeamBezeichnung = $_POST['EditTeamBezeichnung'];
    $EditTeamSpezi = $_POST['EditTeamSpezi'];
    $EditScrumMasterselect = $_POST['EditScrumMasterselect'];
    
    mysqli_stmt_bind_param($editTeam_sql, "ssii", $EditTeamBezeichnung, $EditTeamSpezi, $EditScrumMasterselect, $Team_ID);
    mysqli_stmt_execute($editTeam_sql); 

    if(isset($_POST['editMaID'])) {
        $mitglieder = $_POST['editMaID'];
    } else {
        $mitglieder = array();
    }
        // Bisherige Team_mitglieder Zuordnung löschen
        $mitglieder_sql = "UPDATE Mitarbeiter SET FK_Team=NULL WHERE FK_Team='$Team_ID'";
        mysqli_query($conn, $mitglieder_sql);

        // neue Team_mitglieder Zuordnung setzen
        for ($i=0; $i<count($mitglieder); $i++) {
            $mid = $mitglieder[$i];
            $mitglieder_sql = "UPDATE Mitarbeiter SET FK_TEAM='$Team_ID' WHERE Mitarbeiter_ID='$mid'";
            mysqli_query($conn, $mitglieder_sql);
        }

    header("Location: ../teams.php");
}

// Löschen eines Teams
if(isset($_POST['deleteTeam'])) { 
    $Team_ID = $_POST['deleteTeam'];
    $deleteTeam_sql = "DELETE FROM Team WHERE Team_ID='$Team_ID'";
    mysqli_query($conn, $deleteTeam_sql); 

    header("Location: ../teams.php");
}

// Anlegen eines neuen Mitarbeiters
if(isset($_POST['maSubmit'])) {
    $ma_sql = mysqli_prepare($conn, "INSERT INTO Mitarbeiter (Name, Passwort, Username, Eintrittsdatum, Mailadresse, Status, Ueberstunden, Product, Fachgebiet, FK_Team) VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?, ?)");

    $username = $_POST['username'];
    $name = $_POST['name'];
    $passwort = md5($_POST['passwort']);
    $email = $_POST['email'];
    $eintritt = $_POST['eintritt'];
    $status = $_POST['statusselect'];
    if(isset($_POST['teamselect'])) {
        $team = $_POST['teamselect'];
    } else {
        $team = NULL;
    }
    if(isset($_POST['product'])) {
    $product = $_POST['product'];
    } else {
        $product = NULL;
    }
    if(isset($_POST['fachgebiet'])) {
        $fachgebiet = $_POST['fachgebiet'];
    } else {
        $fachgebiet = NULL;
    }
    
    mysqli_stmt_bind_param($ma_sql, "ssssssssi", $name, $passwort, $username, $eintritt, $email, $status, $product, $fachgebiet, $team);
    mysqli_stmt_execute($ma_sql); 

    // neue Mitarbeiter-Rollen Zuordnung setzen
    if(isset($_POST['rollen'])) {
        $rollen = $_POST['rollen'];
        
        $maID_sql = "SELECT Mitarbeiter_ID FROM Mitarbeiter WHERE Username='$username'";
        $maID_res = mysqli_query($conn, $maID_sql);
        $maID_row = mysqli_fetch_assoc($maID_res);
        $maID = $maID_row['Mitarbeiter_ID'];

        for ($i=0; $i<count($rollen); $i++) {
            $rolle = $rollen[$i];
            $rollenUpdate_sql = "INSERT INTO Rollenzuweisung (MA_ID, Rollen_ID) VALUES ('$maID', '$rolle')";
            mysqli_query($conn, $rollenUpdate_sql);
        }
    }

    header("Location: ../home.php");
}

// Bearbeiten eines Mitarbeiters
if(isset($_POST['maEditSubmit'])) {
    $Mitarbeiter_ID = $_POST['Mitarbeiter_ID'];
    $username = $_POST['username'];
    $name = $_POST['name'];
    $passwort = md5($_POST['passwort']);
    $email = $_POST['email'];
    $eintritt = $_POST['eintritt'];
    $status = $_POST['statusselect'];
    $austritt = $_POST['austritt'];
    $ueberstunden = $_POST['ueberstunden'];
    if(isset($_POST['teamselect'])) {
        $team = $_POST['teamselect'];
    } else {
        $team = NULL;
    }
    if(isset($_POST['product'])) {
    $product = $_POST['product'];
    } else {
        $product = NULL;
    }
    if(isset($_POST['fachgebiet'])) {
        $fachgebiet = $_POST['fachgebiet'];
    } else {
        $fachgebiet = NULL;
    }
    
    $editma_sql = mysqli_prepare($conn, "UPDATE Mitarbeiter SET Name=?, Passwort=?, Username=?, Eintrittsdatum=?, Mailadresse=?, Status=?, Ueberstunden=?, Product=?, Fachgebiet=?, FK_Team=? WHERE Mitarbeiter_ID=?");
    mysqli_stmt_bind_param($editma_sql, "ssssssissii", $name, $passwort, $username, $eintritt, $email, $status, $ueberstunden, $product, $fachgebiet, $team, $Mitarbeiter_ID);
    mysqli_stmt_execute($editma_sql); 

    if(isset($_POST['editRoleID'])) {
        $rollen = $_POST['editRoleID'];

        // Bisherige Mitarbeiter-Rollen Zuordnung löschen
        $deleteRoles_sql = "DELETE FROM Rollenzuweisung WHERE MA_ID='$Mitarbeiter_ID'";
        mysqli_query($conn, $deleteRoles_sql);

        // neue Mitarbeiter-Rollen Zuordnung setzen
        for ($i=0; $i<count($rollen); $i++) {
            $rolle = $rollen[$i];
            $rollenUpdate_sql = "INSERT INTO Rollenzuweisung (MA_ID, Rollen_ID) VALUES ('$Mitarbeiter_ID', '$rolle')";
            mysqli_query($conn, $rollenUpdate_sql);
        }
    } 

    header("Location: ../home.php");
}

// Anlegen einer neuen Initiative
if(isset($_POST['initSubmit'])) {
    $bezeichnung = $_POST['initBezeichnung'];
    $dauer = $_POST['initDauer'];
    $budget = $_POST['initBudget'];
    
    $initiative_sql = mysqli_prepare($conn, "INSERT INTO Initiative (Bezeichnung, Dauer, Budget) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($initiative_sql, "ssi", $bezeichnung, $dauer, $budget);
    mysqli_stmt_execute($initiative_sql); 

    if(isset($_POST['teamID'])) {
        $beteiligte = $_POST['teamID'];
        
        $initID_sql = "SELECT Initiative_ID FROM Initiative WHERE Bezeichnung='$bezeichnung'";
        $initID_res = mysqli_query($conn, $initID_sql);
        $initID_row = mysqli_fetch_assoc($initID_res);
        $initID = $initID_row['Initiative_ID'];
     
        for ($i=0; $i<count($beteiligte); $i++) {
            $teams = $beteiligte[$i];
            $beteiligte_sql = "INSERT INTO team_initiative (Team_ID, Initiative_ID) VALUES ('$teams', '$initID')";
            mysqli_query($conn, $beteiligte_sql);
        }
    }

    header("Location: ../initiativen.php");
}

// Bearbeiten einer Initiative
if(isset($_POST['initEditSubmit'])) {
    $Initiative_ID = $_POST['Initiative_ID'];
    $bezeichnung = $_POST['initEditBezeichnung'];
    $dauer = $_POST['initEditDauer'];
    $budget = $_POST['initEditBudget'];
    
    $editInitiative_sql = mysqli_prepare($conn, "UPDATE Initiative SET Bezeichnung=?, Dauer=?, Budget=? WHERE Initiative_ID=?");
    mysqli_stmt_bind_param($editInitiative_sql, "ssii", $bezeichnung, $dauer, $budget, $Initiative_ID);
    mysqli_stmt_execute($editInitiative_sql); 

    if(isset($_POST['initEditteamID'])) {
        $beteiligte = $_POST['initEditteamID'];
    } else {
        $beteiligte = array();
    }

        // vorherige Verbindungen lösen
        $deleteBeteiligte_sql = "DELETE FROM team_initiative WHERE Initiative_ID='$Initiative_ID'";
        mysqli_query($conn, $deleteBeteiligte_sql); 

        // neue Verbindungen hinzufügen
        for ($i=0; $i<count($beteiligte); $i++) {
            $teams = $beteiligte[$i];
            $beteiligte_sql = "INSERT INTO team_initiative (Team_ID, Initiative_ID) VALUES ('$teams', '$Initiative_ID')";
            mysqli_query($conn, $beteiligte_sql);   

        }

    header("Location: ../initiativen.php");
}

// Löschen einer Initiative
if(isset($_POST['deleteinitID'])) { 
    $Initiative_ID = $_POST['deleteinitID'];
    $deleteInit_sql = "DELETE FROM Initiative WHERE Initiative_ID='$Initiative_ID'";
    mysqli_query($conn, $deleteInit_sql); 

    header("Location: ../initiativen.php");
}
