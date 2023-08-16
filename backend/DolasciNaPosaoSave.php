<?php

require_once ("./connection.php");
require_once ("./virtualnoVrijemeClass.php");
require_once ("./dnevnikclass.php");

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {

    $request = json_decode($postData);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje spremanja dolaska na posao po korisniku -' . $korisnikID, Dnevnik::TrenutnoVrijeme($con), 5);

    $korisnikID = filter_var($request->data->KorisnikID, FILTER_SANITIZE_NUMBER_INT);
    $datumVrijemeDolaska = date('Y-m-d H:i:s', strtotime($request->data->DatumVrijemeDolaska));
    $datumDolaska = date('Y-m-d', strtotime($request->data->DatumVrijemeDolaska));

    if (!isset($korisnikID) || !isset($datumVrijemeDolaska) || !isset($datumDolaska)) {
        Dnevnik::upisiUDnevnik($con, 'KorisnikID ili datum dolaska nisu validni', Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
    }

    $sql = "SELECT * FROM dolascinaposao WHERE KorisnikID = ? AND DatumVrijemeDolaska LIKE CONCAT(? ,'%')";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'is', $korisnikID, $datumDolaska);
    mysqli_stmt_execute($stmt);

    $stmt_rows = mysqli_stmt_get_result($stmt);

    mysqli_stmt_close($stmt);

    if (mysqli_num_rows($stmt_rows) > 0) {
        Dnevnik::upisiUDnevnik($con, 'Korisnik je već evidentiran na poslu', Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Korisnik je već evidentiran na poslu!", E_USER_ERROR);
    }

    $sql = "INSERT INTO dolascinaposao (DatumVrijemeDolaska, KorisnikID) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'si', $datumVrijemeDolaska, $korisnikID);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);

        Dnevnik::upisiUDnevnik($con, 'Uspješan insert dolaska na posao za korisnika -' . $korisnikID, Dnevnik::TrenutnoVrijeme($con), 9);

        $sql = "UPDATE korisnik SET BrojDolazakaNaPosao = BrojDolazakaNaPosao + 1 WHERE ID = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $korisnikID);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            Dnevnik::upisiUDnevnik($con, 'Neuspješan update broja dolazaka na posao za korisnika -' . $korisnikID, Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Greška kod spremanja dolaska na posao!", E_USER_ERROR);
        }
        Dnevnik::upisiUDnevnik($con, 'Uspješan update broja dolazaka na posao za korisnika -' . $korisnikID, Dnevnik::TrenutnoVrijeme($con), 9);
        echo json_encode(['data' => 'Success!']);
    } else {
        Dnevnik::upisiUDnevnik($con, 'Neuspješan insert dolaska na posao za korisnika -' . $korisnikID, Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Greška kod spremanja dolaska na posao!", E_USER_ERROR);
    }

    mysqli_close($con);
}
