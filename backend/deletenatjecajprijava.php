<?php

require 'connection.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    $ID = filter_var($result->data, FILTER_SANITIZE_NUMBER_INT);

    if (!isset($ID)) {
        trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
    }

    provjeraZatvorenogNatjecaja($con, $ID);

    $sql = "DELETE FROM prijavananatjecaj WHERE ID = ?";
    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
    mysqli_stmt_bind_param($stmt, "i", $ID);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['data' => 'Uspješno obrisana prijava']);
    } else {
        trigger_error("Problem kod brisanja prijave", E_USER_ERROR);
    }
}

function provjeraZatvorenogNatjecaja($con, $ID) {
    $sql = "SELECT n.StatusNatjecajaID FROM natjecaj n
            LEFT JOIN prijavananatjecaj p ON n.ID = p.NatjecajID
            WHERE p.ID = ?";
    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
    mysqli_stmt_bind_param($stmt, "i", $ID);

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row['StatusNatjecajaID'] == 2) {
            trigger_error("Natječaj je zatvoren, nije moguće obrisati prijavu", E_USER_ERROR);
        }
    } else {
        trigger_error("Problem kod provjere zatvorenog natječaja", E_USER_ERROR);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($con);