<?php

require 'connection.php';

$postData = file_get_contents('php://input');

if (isset($postData) && !empty($postData)) {
    $request = json_decode($postData);

    if (isset($request->data->ID) && !empty($request->data->ID)) {
        $id = filter_var($request->data->ID, FILTER_VALIDATE_INT);
        $naziv = mysqli_real_escape_string($con, trim($request->data->Naziv));
        $datum = mysqli_real_escape_string($con, trim($request->data->Datum));
        $opis = mysqli_real_escape_string($con, trim($request->data->Opis));
        $ocijenaZaposlenikaID = filter_var($request->data->OcijenaZaposlenikaID, FILTER_VALIDATE_INT);
        $korisnikID = filter_var($request->data->KorisnikID, FILTER_VALIDATE_INT);
        $poduzeceID = filter_var($request->data->PoduzeceID, FILTER_VALIDATE_INT);

        if (!isset($naziv) || !isset($datum) || !isset($opis) || !isset($ocijenaZaposlenikaID) || !isset($korisnikID) || !isset($poduzeceID)) {
            trigger_error("Krivo uređen request", E_USER_ERROR);
        }

        $sql = "UPDATE radnizadatak SET Naziv = ?, Opis = ?, Datum = ?, OcijenaZaposlenikaID = ?, KorisnikID = ?, PoduzeceID = ? WHERE ID = ? LIMIT 1";
        $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
        mysqli_stmt_bind_param($stmt, "sssiiii", $naziv, $opis, $datum, $ocijenaZaposlenikaID, $korisnikID, $poduzeceID, $id);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['data' => 'Uspješno uređen zadatak.']);
        } else {
            trigger_error("Problem kod uređivanja zadatka", E_USER_ERROR);
        }
    } else {
        $naziv = mysqli_real_escape_string($con, trim($request->data->Naziv));
        $datum = mysqli_real_escape_string($con, trim($request->data->Datum));
        $opis = mysqli_real_escape_string($con, trim($request->data->Opis));
        $ocijenaZaposlenikaID = 1;
        $korisnikID = filter_var($request->data->KorisnikID, FILTER_VALIDATE_INT);
        $poduzeceID = filter_var($request->data->PoduzeceID, FILTER_VALIDATE_INT);

        if ((is_bool($request->data->Odradeno) || is_numeric($request->data->Odradeno))) {
            $odradeno = $request->data->Odradeno ? 1 : 0;
        } else {
            trigger_error("Krivo uređen request", E_USER_ERROR);
        }

        if (!isset($naziv) || !isset($datum) || !isset($opis) || !isset($ocijenaZaposlenikaID) || !isset($korisnikID) || !isset($poduzeceID)) {
            trigger_error("Krivo uređen request", E_USER_ERROR);
        }

        $sql = "INSERT INTO radnizadatak (Naziv, Opis, Datum, Odradeno, OcijenaZaposlenikaID, KorisnikID, PoduzeceID) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
        mysqli_stmt_bind_param($stmt, "sssiiii", $naziv, $opis, $datum, $odradeno, $ocijenaZaposlenikaID, $korisnikID, $poduzeceID);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['data' => 'Uspješno dodan zadatak.']);
        } else {
            trigger_error("Problem kod dodavanja zadatka", E_USER_ERROR);
        }
    }
}

mysqli_close($con);