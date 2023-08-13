<?php

require 'connection.php';

$radniZadaci = [];
$sql = "SELECT rz.ID, rz.Naziv, rz.Opis, rz.Datum, rz.Odradeno, rz.KorisnikID, rz.OcijenaZaposlenikaID, oz.Ocijena, k.Ime, k.Prezime FROM radnizadatak rz LEFT JOIN ocijenazaposlenika oz ON rz.OcijenaID = oz.ID LEFT JOIN korisnik k ON rz.KorisnikID = k.ID";

if ($result = mysqli_query($con, $sql)) {
    $cr = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $radniZadaci[$cr]['ID'] = (int)$row['ID'];
        $radniZadaci[$cr]['Naziv'] = $row['Naziv'];
        $radniZadaci[$cr]['Opis'] = $row['Opis'];
        $radniZadaci[$cr]['Datum'] = $row['Datum'];
        $radniZadaci[$cr]['Odradeno'] = (bool)$row['Odradeno'];
        $radniZadaci[$cr]['KorisnikID'] = (int)$row['KorisnikID'];
        $radniZadaci[$cr]['OcijenaZaposlenikaID'] = (int)$row['OcijenaZaposlenikaID'];
        $radniZadaci[$cr]['Ocijena'] = $row['Ocijena'];
        $radniZadaci[$cr]['Ime'] = $row['Ime'];
        $radniZadaci[$cr]['Prezime'] = $row['Prezime'];
        $cr++;
    }

    echo json_encode(['data' => $radniZadaci]);
} else {
    http_response_code(404);
}

mysqli_close($con);
