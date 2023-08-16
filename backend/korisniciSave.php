<?php

require_once 'connection.php';
require_once 'dnevnikClass.php';

$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    $request = json_decode($postdata);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje korisniciSave', Dnevnik::TrenutnoVrijeme($con), 5);

    $id = filter_var($request->data->ID, FILTER_SANITIZE_NUMBER_INT);
    $neuspjesnePrijave = filter_var($request->data->NeuspjesnePrijave, FILTER_SANITIZE_NUMBER_INT);
    $ulogaKorisnika = filter_var($request->data->UlogaKorisnikaID, FILTER_SANITIZE_NUMBER_INT);

    if (!isset($id) || !isset($neuspjesnePrijave) || !isset($ulogaKorisnika)) {
        Dnevnik::upisiUDnevnik($con, 'Nisu svi podaci uneseni', Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
    }

    if ((is_bool($request->data->Active) || is_numeric($request->data->Active)) && (is_bool($request->data->Blokiran) || is_numeric($request->data->Blokiran))) {
        $blocked = $request->data->Blokiran ? 1 : 0;
        $active = $request->data->Active ? 1 : 0;
    } else {
        Dnevnik::upisiUDnevnik($con, 'Aktivan ili Blokiran nisu validni', Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Došlo je do pogreške prilikom ažuriranja korisnika", E_USER_ERROR);
    }

    $sql = "UPDATE korisnik SET UlogaKorisnikaID = ?, NeuspjesnePrijave = ?, Active = ?, Blokiran = ? WHERE ID = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iiiii", $ulogaKorisnika, $neuspjesnePrijave, $active, $blocked, $id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        $poduzeca = $request->data->poduzeceCtrl;

        if ($ulogaKorisnika == 2 && (empty($poduzeca) || $poduzeca == null)) {
            Dnevnik::upisiUDnevnik($con, 'Korisnik nemože biti moderator ako nije postavljeno poduzeće', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Korisnik nemože biti moderator ako nije postavljeno poduzeće", E_USER_ERROR);
        }

        if (isset($poduzeca) && !empty($poduzeca)) {
            unesi_moderatora($poduzeca, $id, $con, false);
        } else {
            unesi_moderatora($poduzeca, $id, $con, true);
        }

        Dnevnik::upisiUDnevnik($con, 'Uspješno ažuriran korisnik', Dnevnik::TrenutnoVrijeme($con), 9);
        echo json_encode(['data' => 'Success']);
    } else {
        Dnevnik::upisiUDnevnik($con, 'Neuspješno ažuriranje korisnika', Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Došlo je do pogreške prilikom ažuriranja korisnika", E_USER_ERROR);
    }
}

mysqli_close($con);

function unesi_moderatora($poduzeca, $moderatorID, $con, $empty)
{

    Dnevnik::upisiUDnevnik($con, 'Pokretanje unosa moderatora', Dnevnik::TrenutnoVrijeme($con), 1);
    $sql = "DELETE FROM moderatorpoduzeca WHERE KorisnikID = ?"; //delete all moderators from poduzece
    $stmt = mysqli_prepare($con, $sql); //prepare statement
    mysqli_stmt_bind_param($stmt, "i", $moderatorID); //bind parameters
    mysqli_stmt_execute($stmt); //execute query

    if (mysqli_stmt_close($stmt)) { //close statement
        Dnevnik::upisiUDnevnik($con, 'Uspješno brisanje moderatora', Dnevnik::TrenutnoVrijeme($con), 9);
    } else {
        Dnevnik::upisiUDnevnik($con, 'Neuspješno brisanje moderatora', Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Došlo je do pogreške prilikom brisanja moderatora", E_USER_ERROR);
    }

    if ($empty) {
        return;
    }

    foreach ($poduzeca as $poduzece) {
        $poduzeceID = filter_var($poduzece->ID, FILTER_SANITIZE_NUMBER_INT);

        $sql = "INSERT INTO moderatorpoduzeca (KorisnikID, PoduzeceID) VALUES (?, ?)";
        Dnevnik::upisiUDnevnik($con, 'Upit za unos moderatora', Dnevnik::TrenutnoVrijeme($con), 1);
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $moderatorID, $poduzeceID);
        mysqli_stmt_execute($stmt);

        $affectedRows = mysqli_stmt_affected_rows($stmt);

        mysqli_stmt_close($stmt);

        if ($affectedRows != 1) {
            Dnevnik::upisiUDnevnik($con, 'Neuspješno unesen moderator', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Došlo je do pogreške prilikom unosa moderatora", E_USER_ERROR);
        }
        Dnevnik::upisiUDnevnik($con, 'Uspješno unesen moderator', Dnevnik::TrenutnoVrijeme($con), 9);
        mysqli_stmt_close($stmt);
    }
}
