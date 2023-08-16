<?php

require_once ("./connection.php");
require_once ("./dnevnikclass.php");

$korisnici = array();

Dnevnik::upisiUDnevnik($con, 'Pokretanje korisniciNatjecaj', Dnevnik::TrenutnoVrijeme($con), 6);

$sql = "SELECT ID, Ime, Prezime FROM korisnik WHERE UlogaKorisnikaID = 1";

Dnevnik::upisiUDnevnik($con, 'upit korisniciNatjecaj', Dnevnik::TrenutnoVrijeme($con), 3);

if ($result = mysqli_query($con, $sql)) {
    $cr = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $korisnici[$cr]['ID'] = (int)$row['ID'];
        $korisnici[$cr]['Ime'] = $row['Ime'];
        $korisnici[$cr]['Prezime'] = $row['Prezime'];
        $cr++;
    }

    Dnevnik::upisiUDnevnik($con, 'Uspješan dohvat korisnika natjecaja', Dnevnik::TrenutnoVrijeme($con), 9);
    echo json_encode(['data' => $korisnici]);
} else {
    Dnevnik::upisiUDnevnik($con, 'Neuspješan dohvat korisnika natjecaja', Dnevnik::TrenutnoVrijeme($con), 8);
    http_response_code(404);
}

mysqli_close($con);