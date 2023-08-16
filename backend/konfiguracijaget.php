<?php

require_once ("./connection.php");
require_once ("./dnevnikclass.php");

Dnevnik::upisiUDnevnik($con, 'Pokretanje konfiguracijaget', Dnevnik::TrenutnoVrijeme($con), 6);

$konfiguracija = [];
$sql = "SELECT ID, Pomak, Stranicenje, ImgSize FROM konfiguracija";

Dnevnik::upisiUDnevnik($con, 'Konfiguracije', Dnevnik::TrenutnoVrijeme($con), 6);

if ($result = mysqli_query($con, $sql)) {
    $cr = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $konfiguracija[$cr]['ID'] = (int)$row['ID'];
        $konfiguracija[$cr]['Pomak'] = (int)$row['Pomak'];
        $konfiguracija[$cr]['Stranicenje'] = (int)$row['Stranicenje'];
        $konfiguracija[$cr]['ImgSize'] = (int)$row['ImgSize'];
        $cr++;
    }

    Dnevnik::upisiUDnevnik($con, 'Uspješan dohvat konfiguracije', Dnevnik::TrenutnoVrijeme($con), 9);
    echo json_encode(['data' => $konfiguracija]);
} else {
    Dnevnik::upisiUDnevnik($con, 'Neuspješan dohvat konfiguracije', Dnevnik::TrenutnoVrijeme($con), 8);
    http_response_code(404);
}

mysqli_close($con);