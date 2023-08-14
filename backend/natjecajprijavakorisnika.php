<?php

require 'connection.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    $korisnikID = filter_var($result->data->KorisnikID, FILTER_SANITIZE_NUMBER_INT);
    $natjecajID = filter_var($result->data->NatjecajID, FILTER_SANITIZE_NUMBER_INT);
    $slika = mysqli_real_escape_string($con, trim($result->data->Slika));

    if (!isset($korisnikID) || !isset($natjecajID) || !isset($slika)) {
        trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
    }

    provjeraPrijaveKorisnika($korisnikID, $con);

    $sql = "INSERT INTO prijavananatjecaj (KorisnikID, NatjecajID, Slika) VALUES (?, ?, ?)";

    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
    mysqli_stmt_bind_param($stmt, "iis", $korisnikID, $natjecajID, $slika);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['data' => 'Uspješno prijavljeni na natječaj']);
    } else {
        trigger_error("Problem kod prijave na natječaj", E_USER_ERROR);
    }
}

function provjeraPrijaveKorisnika($korisnikID, $con) {
    $sql = "SELECT * FROM prijavananatjecaj WHERE KorisnikID = ?";

    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
    mysqli_stmt_bind_param($stmt, "i", $korisnikID);

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            trigger_error("Korisnik je već prijavljen na natječaj", E_USER_ERROR);
        }
    } else {
        trigger_error("Problem kod provjere prijave korisnika", E_USER_ERROR);
    }

    mysqli_stmt_close($stmt);
}
