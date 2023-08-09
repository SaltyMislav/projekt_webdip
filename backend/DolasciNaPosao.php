<?php

require 'connection.php';

$dolasciNaPosao = [];
$sql = "SELECT dnp.ID, dnp.DatumVrijemeDolaska, dnp.KorisnikID, k.KorisnickoIme FROM dolascinaposao dnp LEFT JOIN korisnik k ON dnp.KorisnikID= k.ID";

if ($result = mysqli_query($con, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $dolasciNaPosao[] = [
            'ID' => (int)$row['ID'],
            'DatumVrijemeDolaska' => $row['DatumVrijemeDolaska'],
            'KorisnikID' => (int)$row['KorisnikID'],
            'KorisnickoIme' => $row['KorisnickoIme']
        ];
    }

    echo json_encode(['data' => $dolasciNaPosao]);
} else {
    http_response_code(404);
}

mysqli_close($con);
