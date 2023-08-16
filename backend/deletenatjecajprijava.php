<?php

require_once ("./connection.php");
require_once ("./virtualnoVrijemeClass.php");
require_once ("./dnevnikclass.php");

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje delete',  Dnevnik::TrenutnoVrijeme($con), 5);

    $ID = filter_var($result->data, FILTER_SANITIZE_NUMBER_INT);

    if (!isset($ID)) {
        Dnevnik::upisiUDnevnik($con, $ID . " - ID prijave nije validan", Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
    }

    provjeraZatvorenogNatjecaja($con, $ID);

    $sql = "DELETE FROM prijavananatjecaj WHERE ID = ?";
    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
    mysqli_stmt_bind_param($stmt, "i", $ID);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        Dnevnik::upisiUDnevnik($con, $ID . " - uspješno obrisana prijava", Dnevnik::TrenutnoVrijeme($con), 9);
        echo json_encode(['data' => 'Uspješno obrisana prijava']);
    } else {
        mysqli_stmt_close($stmt);
        Dnevnik::upisiUDnevnik($con, $ID . " - problem kod brisanja prijave", Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Problem kod brisanja prijave", E_USER_ERROR);
    }
}

function provjeraZatvorenogNatjecaja($con, $ID)
{
    $sql = "SELECT n.ID, n.StatusNatjecajaID FROM natjecaj n
            LEFT JOIN prijavananatjecaj p ON n.ID = p.NatjecajID
            WHERE p.ID = ?";
    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
    mysqli_stmt_bind_param($stmt, "i", $ID);

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        mysqli_stmt_close($stmt);

        if ($row['StatusNatjecajaID'] == 2) {
            Dnevnik::upisiUDnevnik($con, $row['ID'] . " - natječaj je zatvoren, nije moguće obrisati prijavu", Dnevnik::TrenutnoVrijeme($con), 3);
            trigger_error("Natječaj je zatvoren, nije moguće obrisati prijavu", E_USER_ERROR);
        }
    } else {
        Dnevnik::upisiUDnevnik($con, "problem kod provjere zatvorenog natječaja", Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Problem kod provjere zatvorenog natječaja", E_USER_ERROR);
    }
}

mysqli_close($con);
