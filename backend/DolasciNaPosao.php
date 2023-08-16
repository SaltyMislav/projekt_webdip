<?php

require_once ("./connection.php");
require_once ("./dnevnikclass.php");

Dnevnik::upisiUDnevnik($con, 'Pokretanje dohvatiDolasciNaPosao', Dnevnik::TrenutnoVrijeme($con), 6);

$dolasciNaPosao = [];
$sql = "SELECT dnp.ID, dnp.DatumVrijemeDolaska, dnp.KorisnikID, k.KorisnickoIme FROM dolascinaposao dnp LEFT JOIN korisnik k ON dnp.KorisnikID= k.ID";

Dnevnik::upisiUDnevnik($con, 'Dolasci na posao', Dnevnik::TrenutnoVrijeme($con), 3);

if ($result = mysqli_query($con, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $dolasciNaPosao[] = [
            'ID' => (int)$row['ID'],
            'DatumVrijemeDolaska' => $row['DatumVrijemeDolaska'],
            'KorisnikID' => (int)$row['KorisnikID'],
            'KorisnickoIme' => $row['KorisnickoIme']
        ];
    }

    Dnevnik::upisiUDnevnik($con, 'Uspješan dohvat dolazaka na posao', Dnevnik::TrenutnoVrijeme($con), 9);
    echo json_encode(['data' => $dolasciNaPosao]);
} else {
    Dnevnik::upisiUDnevnik($con, 'Neuspješan dohvat dolazaka na posao', Dnevnik::TrenutnoVrijeme($con), 8);
    http_response_code(404);
}

mysqli_close($con);
