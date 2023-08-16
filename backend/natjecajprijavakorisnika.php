<?php

require_once ("./connection.php");
require_once ("./dnevnikclass.php");

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje natjecaj prijava korisnika', Dnevnik::TrenutnoVrijeme($con), 5);

    $korisnikID = filter_var($result->data->KorisnikID, FILTER_SANITIZE_NUMBER_INT);
    $natjecajID = filter_var($result->data->NatjecajID, FILTER_SANITIZE_NUMBER_INT);
    $slika = mysqli_real_escape_string($con, trim($result->data->Slika));

    if (!isset($korisnikID) || !isset($natjecajID) || !isset($slika)) {
        Dnevnik::upisiUDnevnik($con, 'Nisu svi podaci uneseni', Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
    }

    provjeraPrijaveKorisnika($korisnikID, $con);

    $sql = "INSERT INTO prijavananatjecaj (KorisnikID, NatjecajID, Slika) VALUES (?, ?, ?)";

    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
    mysqli_stmt_bind_param($stmt, "iis", $korisnikID, $natjecajID, $slika);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        Dnevnik::upisiUDnevnik($con, 'Uspješna prijava na natječaj', Dnevnik::TrenutnoVrijeme($con), 9);
        echo json_encode(['data' => 'Uspješno prijavljeni na natječaj']);
    } else {
        mysqli_stmt_close($stmt);
        Dnevnik::upisiUDnevnik($con, 'Problem kod prijave na natječaj', Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Problem kod prijave na natječaj", E_USER_ERROR);
    }
}

function provjeraPrijaveKorisnika($korisnikID, $con) {

    Dnevnik::upisiUDnevnik($con, 'Pokretanje provjere prijave korisnika', Dnevnik::TrenutnoVrijeme($con), 3);

    $sql = "SELECT * FROM prijavananatjecaj WHERE KorisnikID = ?";

    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
    mysqli_stmt_bind_param($stmt, "i", $korisnikID);

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        Dnevnik::upisiUDnevnik($con, 'Uspješna provjera prijave korisnika', Dnevnik::TrenutnoVrijeme($con), 9);

        if (mysqli_num_rows($result) > 0) {
            Dnevnik::upisiUDnevnik($con, 'Korisnik je već prijavljen na natječaj', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Korisnik je već prijavljen na natječaj", E_USER_ERROR);
        }
        Dnevnik::upisiUDnevnik($con, 'Korisnik nije prijavljen na natječaj', Dnevnik::TrenutnoVrijeme($con), 9);
    } else {
        Dnevnik::upisiUDnevnik($con, 'Problem kod provjere prijave korisnika', Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Problem kod provjere prijave korisnika", E_USER_ERROR);
    }
}
