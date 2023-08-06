<?php

require 'connection.php';

$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    $request = json_decode($postdata);

    $id = filter_var($request->data->ID, FILTER_SANITIZE_NUMBER_INT);
    $neuspjesnePrijave = filter_var($request->data->NeuspjesnePrijave, FILTER_SANITIZE_NUMBER_INT);
    $ulogaKorisnika = filter_var($request->data->UlogaKorisnikaID, FILTER_SANITIZE_NUMBER_INT);

    if ((is_bool($request->data->Active) || is_numeric($request->data->Active)) && (is_bool($request->data->Blokiran) || is_numeric($request->data->Blokiran))) {
        $blocked = $request->data->Blokiran ? 1 : 0;
        $active = $request->data->Active ? 1 : 0;
    } else {
        trigger_error("Došlo je do pogreške prilikom ažuriranja korisnika", E_USER_ERROR);
    }

    $sql = "UPDATE korisnik SET UlogaKorisnikaID = ?, NeuspjesnePrijave = ?, Active = ?, Blokiran = ? WHERE ID = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iiiii", $ulogaKorisnika, $neuspjesnePrijave, $active, $blocked, $id);

    if (mysqli_stmt_execute($stmt)) {
        $poduzeca = $request->data->poduzeceCtrl;

        if ($ulogaKorisnika == 2 && (empty($poduzeca) || $poduzeca == null)) {
            trigger_error("Korisnik nemože biti moderator ako nije postavljeno poduzeće", E_USER_ERROR);
        }

        if (isset($poduzeca) && !empty($poduzeca)) {
            unesi_moderatora($poduzeca, $id, $con, false);
        } else {
            unesi_moderatora($poduzeca, $id, $con, true);
        }

        echo json_encode(['data' => 'Success']);
    } else {
        trigger_error("Došlo je do pogreške prilikom ažuriranja korisnika", E_USER_ERROR);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($con);

function unesi_moderatora($poduzeca, $moderatorID, $con, $empty)
{
    $sql = "DELETE FROM moderatorpoduzeca WHERE KorisnikID = ?"; //delete all moderators from poduzece
    $stmt = mysqli_prepare($con, $sql); //prepare statement
    mysqli_stmt_bind_param($stmt, "i", $moderatorID); //bind parameters
    mysqli_stmt_execute($stmt); //execute query

    mysqli_stmt_close($stmt); //close statement

    if ($empty) {
        return;
    }

    foreach ($poduzeca as $poduzece) {
        $poduzeceID = filter_var($poduzece->ID, FILTER_SANITIZE_NUMBER_INT);

        $sql = "INSERT INTO moderatorpoduzeca (KorisnikID, PoduzeceID) VALUES (?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $moderatorID, $poduzeceID);
        mysqli_stmt_execute($stmt);

        $affectedRows = mysqli_stmt_affected_rows($stmt);

        if ($affectedRows != 1) {
            trigger_error("Došlo je do pogreške prilikom unosa moderatora", E_USER_ERROR);
        }
        mysqli_stmt_close($stmt);
    }
}
