<?php

require 'connection.php';

$zaposlenici = [];

$sql = "SELECT k.ID, k.Ime, k.Prezime, k.KorisnickoIme, k.BrojDolazakaNaPosao, k.BrojOdradenihZadataka, k.BrojNeodradenihZadataka, k.PoduzeceID, p.Naziv AS PoduzeceNaziv
            FROM korisnik k 
            INNER JOIN poduzece p ON k.PoduzeceID = p.ID 
            WHERE k.UlogaKorisnikaID = 1";

if ($result = mysqli_query($con, $sql)) {
    $cr = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $zaposlenici[$cr]['ID'] = (int)$row['ID'];
        $zaposlenici[$cr]['Ime'] = $row['Ime'];
        $zaposlenici[$cr]['Prezime'] = $row['Prezime'];
        $zaposlenici[$cr]['KorisnickoIme'] = $row['KorisnickoIme'];
        $zaposlenici[$cr]['BrojDolazakaNaPosao'] = (int)$row['BrojDolazakaNaPosao'];
        $zaposlenici[$cr]['BrojOdradenihZadataka'] = (int)$row['BrojOdradenihZadataka'];
        $zaposlenici[$cr]['BrojNeodradenihZadataka'] = (int)$row['BrojNeodradenihZadataka'];
        $zaposlenici[$cr]['PoduzeceID'] = (int)$row['PoduzeceID'];
        $zaposlenici[$cr]['PoduzeceNaziv'] = $row['PoduzeceNaziv'];
        $cr++;
    }
    echo json_encode(['data' => $zaposlenici]);
} else {
    http_response_code(404);
}
mysqli_close($con);
