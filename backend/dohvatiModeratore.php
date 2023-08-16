<?php

require_once ("./connection.php");
require_once ("./dnevnikclass.php");

Dnevnik::upisiUDnevnik($con, 'Pokretanje dohvatiModeratore', Dnevnik::TrenutnoVrijeme($con), 6);

$moderatori = [];
$sql = "SELECT m.ID, m.PoduzeceID, m.KorisnikID, k.KorisnickoIme, p.Naziv AS PoduzeceNaziv FROM moderatorpoduzeca m LEFT JOIN korisnik k ON k.ID = m.KorisnikID LEFT JOIN poduzece p ON p.ID = m.PoduzeceID";

Dnevnik::upisiUDnevnik($con, 'Moderatori', Dnevnik::TrenutnoVrijeme($con), 3);

if ($result = mysqli_query($con, $sql)) {
    $cr = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $moderatori[$cr]['ID'] = (int)$row['ID'];
        $moderatori[$cr]['PoduzeceID'] = (int)$row['PoduzeceID'];
        $moderatori[$cr]['KorisnikID'] = (int)$row['KorisnikID'];
        $moderatori[$cr]['KorisnickoIme'] = $row['KorisnickoIme'];
        $moderatori[$cr]['PoduzeceNaziv'] = $row['PoduzeceNaziv'];
        $cr++;
    }

    Dnevnik::upisiUDnevnik($con, 'Uspješan dohvat moderatora', Dnevnik::TrenutnoVrijeme($con), 9);
    echo json_encode(['data' => $moderatori]);
} else {
    Dnevnik::upisiUDnevnik($con, 'Neuspješan dohvat moderatora', Dnevnik::TrenutnoVrijeme($con), 8);
    http_response_code(404);
}

mysqli_close($con);