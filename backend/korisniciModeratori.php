<?php

require 'connection.php';

$korisnici = array();

$sql = "SELECT ID, KorisnickoIme FROM korisnik WHERE UlogaKorisnikaID = 1 OR UlogaKorisnikaID = 2";

if ($result = mysqli_query($con, $sql)) {
    $cr = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $korisnici[$cr]['ID'] = $row['ID'];
        $korisnici[$cr]['KorisnickoIme'] = $row['KorisnickoIme'];
        $cr++;
    }

    echo json_encode(['data' => $korisnici]);
} else {
    http_response_code(404);
}

mysqli_close($con);