<?php

require_once 'connection.php';
require_once 'dnevnikClass.php';

$korisnici = array();

Dnevnik::upisiUDnevnik($con, 'Pokretanje korisniciModeratori', Dnevnik::TrenutnoVrijeme($con), 6);

$sql = "SELECT ID, KorisnickoIme FROM korisnik WHERE UlogaKorisnikaID = 1 OR UlogaKorisnikaID = 2";

Dnevnik::upisiUDnevnik($con, 'upit korisnicimoderatori', Dnevnik::TrenutnoVrijeme($con), 3);

if ($result = mysqli_query($con, $sql)) {
    $cr = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $korisnici[$cr]['ID'] = (int)$row['ID'];
        $korisnici[$cr]['KorisnickoIme'] = $row['KorisnickoIme'];
        $cr++;
    }

    Dnevnik::upisiUDnevnik($con, 'Uspješan dohvat korisnika moderatora', Dnevnik::TrenutnoVrijeme($con), 9);
    echo json_encode(['data' => $korisnici]);
} else {
    Dnevnik::upisiUDnevnik($con, 'Neuspješan dohvat korisnika moderatora', Dnevnik::TrenutnoVrijeme($con), 8);
    http_response_code(404);
}

mysqli_close($con);