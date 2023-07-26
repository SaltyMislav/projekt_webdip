<?php

require 'connection.php';

$moderatori = [];
$sql = "SELECT m.ID, m.PoduzeceID, m.KorisnikID, k.KorisnickoIme, p.Naziv AS PoduzeceNaziv FROM moderatorpoduzeca m LEFT JOIN korisnik k ON k.ID = m.KorisnikID LEFT JOIN poduzece p ON p.ID = m.PoduzeceID";

if ($result = mysqli_query($con, $sql)) {
    $cr = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $moderatori[$cr]['ID'] = $row['ID'];
        $moderatori[$cr]['PoduzeceID'] = $row['PoduzeceID'];
        $moderatori[$cr]['KorisnikID'] = $row['KorisnikID'];
        $moderatori[$cr]['KorisnickoIme'] = $row['KorisnickoIme'];
        $moderatori[$cr]['PoduzeceNaziv'] = $row['PoduzeceNaziv'];
        $cr++;
    }

    echo json_encode(['data' => $moderatori]);
} else {
    http_response_code(404);
}

mysqli_close($con);