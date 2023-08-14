<?php

require 'connection.php';

$korisnici = array();

$sql = "SELECT ID, Ime, Prezime FROM korisnik WHERE UlogaKorisnikaID = 1";

if ($result = mysqli_query($con, $sql)) {
    $cr = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $korisnici[$cr]['ID'] = (int)$row['ID'];
        $korisnici[$cr]['Ime'] = $row['Ime'];
        $korisnici[$cr]['Prezime'] = $row['Prezime'];
        $cr++;
    }

    echo json_encode(['data' => $korisnici]);
} else {
    http_response_code(404);
}

mysqli_close($con);