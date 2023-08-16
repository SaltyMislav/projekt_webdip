<?php

require_once 'connection.php';
require_once 'dnevnikClass.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje poduzece', Dnevnik::TrenutnoVrijeme($con), 5);

    if (isset($result->data->ID) && !empty($result->data->ID)) {

        Dnevnik::upisiUDnevnik($con, 'Ažuriranje poduzeća', Dnevnik::TrenutnoVrijeme($con), 5);

        $id = filter_var($result->data->ID, FILTER_SANITIZE_NUMBER_INT);
        $naziv = htmlspecialchars($result->data->Naziv);
        $opis = htmlspecialchars($result->data->Opis);
        $vrijemePocetka = htmlspecialchars($result->data->RadnoVrijemeOd);
        $vrijemePocetka = strtotime($vrijemePocetka);
        $vrijemeKraja = htmlspecialchars($result->data->RadnoVrijemeDo);
        $vrijemeKraja = strtotime($vrijemeKraja);

        if (!isset($id) || !isset($naziv) || !isset($opis) || !isset($vrijemePocetka) || !isset($vrijemeKraja)) {
            Dnevnik::upisiUDnevnik($con, 'Nisu svi podaci uneseni', Dnevnik::TrenutnoVrijeme($con), 7);
            trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
        }

        if ($vrijemePocetka > $vrijemeKraja) {
            Dnevnik::upisiUDnevnik($con, 'Vrijeme kraja ne može biti prije vremena početka', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Vrijeme kraja ne može biti prije vremena početka", E_USER_ERROR);
        }

        $vrijemePocetka = date('H:i:s', $vrijemePocetka);
        $vrijemeKraja = date('H:i:s', $vrijemeKraja);

        $sql = "UPDATE poduzece SET Naziv = ?, Opis = ?, RadnoVrijemeOd = ?, RadnoVrijemeDo = ? WHERE ID = ?";

        Dnevnik::upisiUDnevnik($con, 'Upit za ažuriranje poduzeća', Dnevnik::TrenutnoVrijeme($con), 3);

        $stmt = mysqli_prepare($con, $sql); //prepare statement
        mysqli_stmt_bind_param($stmt, "ssssi", $naziv, $opis, $vrijemePocetka, $vrijemeKraja, $id); //bind parameters

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            Dnevnik::upisiUDnevnik($con, 'Uspješno ažuriranje poduzeća', Dnevnik::TrenutnoVrijeme($con), 9);
            $moderatori = $result->data->moderatorCtrl;
            if (isset($moderatori) && !empty($moderatori)) {
                Dnevnik::upisiUDnevnik($con, 'Ažuriranje moderatora', Dnevnik::TrenutnoVrijeme($con), 2);
                unesi_moderatore($moderatori, $id, $con, false);
            } else {
                Dnevnik::upisiUDnevnik($con, 'Brisanje moderatora', Dnevnik::TrenutnoVrijeme($con), 4);
                unesi_moderatore($moderatori, $id, $con, true);
            }

            Dnevnik::upisiUDnevnik($con, 'Uspješno ažuriranje moderatora', Dnevnik::TrenutnoVrijeme($con), 9);
            echo json_encode(['data' => 'Success']);
        } else {
            Dnevnik::upisiUDnevnik($con, 'Neuspješno ažuriranje poduzeća', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Došlo je do pogreške prilikom ažuriranja poduzeća", E_USER_ERROR);
        }
    } else {

        Dnevnik::upisiUDnevnik($con, 'Unos poduzeća', Dnevnik::TrenutnoVrijeme($con), 1);

        $naziv = htmlspecialchars($result->data->Naziv);
        $opis = htmlspecialchars($result->data->Opis);
        $vrijemePocetka = htmlspecialchars($result->data->RadnoVrijemeOd);
        $vrijemePocetka = strtotime($vrijemePocetka);
        $vrijemeKraja = htmlspecialchars($result->data->RadnoVrijemeDo);
        $vrijemeKraja = strtotime($vrijemeKraja);


        if (!isset($naziv) || !isset($opis) || !isset($vrijemePocetka) || !isset($vrijemeKraja)) {
            Dnevnik::upisiUDnevnik($con, 'Nisu svi podaci uneseni', Dnevnik::TrenutnoVrijeme($con), 7);
            trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
        }

        if ($vrijemePocetka > $vrijemeKraja) {
            Dnevnik::upisiUDnevnik($con, 'Vrijeme kraja ne može biti prije vremena početka', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Vrijeme kraja ne može biti prije vremena početka", E_USER_ERROR);
        }

        $vrijemePocetka = date('H:i:s', $vrijemePocetka);
        $vrijemeKraja = date('H:i:s', $vrijemeKraja);

        $sql = "INSERT INTO poduzece (Naziv, Opis, RadnoVrijemeOd, RadnoVrijemeDo) VALUES (?, ?, ?, ?)";

        Dnevnik::upisiUDnevnik($con, 'Upit za unos poduzeća', Dnevnik::TrenutnoVrijeme($con), 3);

        $stmt = mysqli_prepare($con, $sql); //prepare statement
        mysqli_stmt_bind_param($stmt, "ssss", $naziv, $opis, $vrijemePocetka, $vrijemeKraja); //bind parameters
        mysqli_stmt_execute($stmt); //execute query

        $affected_rows = mysqli_stmt_affected_rows($stmt); //get the number of affected rows
        mysqli_stmt_close($stmt);

        if ($affected_rows == 1) {
            Dnevnik::upisiUDnevnik($con, 'Uspješno dodano poduzeće', Dnevnik::TrenutnoVrijeme($con), 9);
            echo json_encode(['data' => 'Success']);
        } else {
            Dnevnik::upisiUDnevnik($con, 'Neuspješno dodavanje poduzeća', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Došlo je do pogreške prilikom dodavanja poduzeća", E_USER_ERROR);
        }
    }
    mysqli_close($con); //close connection
}


function unesi_moderatore($moderatori, $poduzeceID, $con, $empty)
{

    $sql = "SELECT KorisnikID FROM moderatorpoduzeca WHERE PoduzeceID = ?"; //get all moderators from poduzece

    Dnevnik::upisiUDnevnik($con, 'Upit za dohvaćanje moderatora', Dnevnik::TrenutnoVrijeme($con), 3);

    $stmt = mysqli_prepare($con, $sql); //prepare statement
    mysqli_stmt_bind_param($stmt, "i", $poduzeceID); //bind parameters
    mysqli_stmt_execute($stmt); //execute query

    $result = mysqli_stmt_get_result($stmt); //get the result

    if(!empty($result) && $result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $sql1 = "UPDATE korisnik SET UlogaKorisnikaID = 1 WHERE ID = ?"; //update korisnik UlogaKorisnikaID to 1

            Dnevnik::upisiUDnevnik($con, 'Upit za ažuriranje uloge korisnika', Dnevnik::TrenutnoVrijeme($con), 3);

            $stmt1 = mysqli_prepare($con, $sql1); //prepare statement
            mysqli_stmt_bind_param($stmt1, "i", $row['KorisnikID']); //bind parameters
            mysqli_stmt_execute($stmt1); //execute query

            mysqli_stmt_close($stmt1); //close statement
        }
    }

    $sql = "DELETE FROM moderatorpoduzeca WHERE PoduzeceID = ?"; //delete all moderators from poduzece

    Dnevnik::upisiUDnevnik($con, 'Upit za brisanje moderatora', Dnevnik::TrenutnoVrijeme($con), 4);

    $stmt = mysqli_prepare($con, $sql); //prepare statement
    mysqli_stmt_bind_param($stmt, "i", $poduzeceID); //bind parameters
    mysqli_stmt_execute($stmt); //execute query

    mysqli_stmt_close($stmt); //close statement

    if ($empty) {
        return;
    }

    foreach ($moderatori as $moderator) {
        $moderatorID = filter_var($moderator->ID, FILTER_SANITIZE_NUMBER_INT);
        //insert data into table moderatoripoduzeca
        $sql = "INSERT INTO moderatorpoduzeca (KorisnikID, PoduzeceID) VALUES (?, ?)";

        Dnevnik::upisiUDnevnik($con, 'Upit za unos moderatora', Dnevnik::TrenutnoVrijeme($con), 1);

        $stmt = mysqli_prepare($con, $sql); //prepare statement
        mysqli_stmt_bind_param($stmt, "ii", $moderatorID, $poduzeceID); //bind parameters
        mysqli_stmt_execute($stmt); //execute query

        $affected_rows = mysqli_stmt_affected_rows($stmt); //get the number of affected rows
        mysqli_stmt_close($stmt);

        if ($affected_rows != 1) {
            Dnevnik::upisiUDnevnik($con, 'Neuspješno unesen moderator', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Došlo je do pogreške prilikom dodavanja moderatora", E_USER_ERROR);
        }

        //update korisnik UlogaKorisnikaID to 2
        $sql = "UPDATE korisnik SET UlogaKorisnikaID = 2 WHERE ID = ?";

        Dnevnik::upisiUDnevnik($con, 'Upit za ažuriranje uloge korisnika', Dnevnik::TrenutnoVrijeme($con), 3);

        $stmt = mysqli_prepare($con, $sql); //prepare statement
        mysqli_stmt_bind_param($stmt, "i", $moderatorID); //bind parameters
        if (!mysqli_stmt_execute($stmt)){ //execute query
            mysqli_stmt_close($stmt);
            Dnevnik::upisiUDnevnik($con, 'Neuspješno ažuriranje uloge korisnika', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Došlo je do pogreške prilikom ažuriranja uloge korisnika", E_USER_ERROR);
        }

        mysqli_stmt_close($stmt); //close statement

    }
}
