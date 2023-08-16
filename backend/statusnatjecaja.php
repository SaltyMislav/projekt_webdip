<?php

require_once 'connection.php';
require_once 'dnevnikClass.php';

Dnevnik::upisiUDnevnik($con, 'Pokretanje statusnatjecaja', Dnevnik::TrenutnoVrijeme($con), 6);

$statusiNatjecaja = [];

$sql = "SELECT ID, Naziv FROM statusnatjecaja";

if ($result = mysqli_query($con, $sql)) {
    $cr = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $statusiNatjecaja[$cr]['ID'] = (int)$row['ID'];
        $statusiNatjecaja[$cr]['Naziv'] = $row['Naziv'];
        $cr++;
    }

    Dnevnik::upisiUDnevnik($con, 'Uspješan dohvat statusa natjecaja', Dnevnik::TrenutnoVrijeme($con), 9);
    echo json_encode(['data' => $statusiNatjecaja]);
} else {
    Dnevnik::upisiUDnevnik($con, 'Neuspješan dohvat statusa natjecaja', Dnevnik::TrenutnoVrijeme($con), 8);
    http_response_code(404);
}

mysqli_close($con);