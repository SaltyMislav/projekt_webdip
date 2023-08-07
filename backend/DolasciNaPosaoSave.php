<?php

require 'connection.php';
require 'virtualnoVrijemeClass.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {

    $request = json_decode($postData);

    $korisnikID = filter_var($request->data->KorisnikID, FILTER_SANITIZE_NUMBER_INT);
    $datumVrijemeDolaska = date('Y-m-d H:i:s', strtotime($request->data->DatumVrijemeDolaska));
    $datumDolaska = date('Y-m-d', strtotime($request->data->DatumVrijemeDolaska));

    if ($korisnikID != 1){
        trigger_error("Samo korisnici smiju evidentirati dolazak na posao!", E_USER_ERROR);
    }

    $sql = "SELECT * FROM dolascinaposao WHERE KorisnikID = ? AND DatumVrijemeDolaska LIKE CONCAT(? ,'%')";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'is', $korisnikID, $datumDolaska);
    mysqli_stmt_execute($stmt);

    $stmt_rows = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($stmt_rows) > 0) {
        trigger_error("Korisnik je već evidentiran na poslu!", E_USER_ERROR);
    }

    $sql = "INSERT INTO dolascinaposao (DatumVrijemeDolaska, KorisnikID) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'si', $datumVrijemeDolaska, $korisnikID);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);

        $sql = "UPDATE korisnik SET BrojDolazakaNaPosao = BrojDolazakaNaPosao + 1 WHERE ID = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $korisnikID);
        if (!mysqli_stmt_execute($stmt)) {
            trigger_error("Greška kod spremanja dolaska na posao!", E_USER_ERROR);
        }

        echo json_encode(['data' => 'Success!']);
    } else {
        trigger_error("Greška kod spremanja dolaska na posao!", E_USER_ERROR);
    }

    mysqli_close($con);
}
