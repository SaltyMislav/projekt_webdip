<?php

require_once 'connection.php';
require_once 'dnevnikClass.php';

$postData = file_get_contents("php://input");
$korisnici = [];

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData, true);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje korisnici', Dnevnik::TrenutnoVrijeme($con), 5);

    $UlogaKorisnikaNaziv = mysqli_real_escape_string($con, trim($result['data']['UlogaKorisnikaNaziv']));
    $Email = mysqli_real_escape_string($con, trim($result['data']['Email']));

    if (!isset($UlogaKorisnikaNaziv) && !isset($Email)) {
        Dnevnik::upisiUDnevnik($con, 'Nisu svi podaci uneseni', Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
    }

    $conditions = [];
    $params = [];
    $types = '';

    if ($UlogaKorisnikaNaziv != '') {
        $conditions[] = "LOWER(u.Naziv) LIKE ?";
        $params[] = '%' . $UlogaKorisnikaNaziv . '%';
        $types .= 's';
    }

    if ($Email != '') {
        $conditions[] = "LOWER(k.Email) LIKE ?";
        $params[] = '%' . $Email . '%';
        $types .= 's';
    }

    $sql = "SELECT k.ID, k.Ime, k.Prezime, k.KorisnickoIme, k.Email, k.NeuspjesnePrijave, k.Active, k.Blokiran, k.UlogaKorisnikaID, u.Naziv AS UlogaKorisnikaNaziv,
            p.ID AS PoduzeceID, p.Naziv AS PoduzeceNaziv
            FROM korisnik k 
            LEFT JOIN ulogakorisnika u ON k.UlogaKorisnikaID = u.ID
            LEFT JOIN moderatorpoduzeca mp ON k.ID = mp.KorisnikID
            LEFT JOIN poduzece p ON p.ID = mp.PoduzeceID";

    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    Dnevnik::upisiUDnevnik($con, 'Upit Korisnici', Dnevnik::TrenutnoVrijeme($con), 3);

    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
    if ($types != '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            if (!isset($korisnici[$row['ID']])) {
                $korisnici[$row['ID']] = [
                    'ID' => (int)$row['ID'],
                    'Ime' => $row['Ime'],
                    'Prezime' => $row['Prezime'],
                    'KorisnickoIme' => $row['KorisnickoIme'],
                    'Email' => $row['Email'],
                    'NeuspjesnePrijave' => (int)$row['NeuspjesnePrijave'],
                    'Active' => (bool)$row['Active'],
                    'Blokiran' => (bool)$row['Blokiran'],
                    'UlogaKorisnikaID' => (int)$row['UlogaKorisnikaID'],
                    'UlogaKorisnikaNaziv' => $row['UlogaKorisnikaNaziv'],
                    'Poduzece' => []
                ];
            }

            if ($row['PoduzeceID'] != null && $row['PoduzeceNaziv'] != null) {
                $korisnici[$row['ID']]['Poduzece'][] = [
                    'ID' => (int)$row['PoduzeceID'],
                    'Naziv' => $row['PoduzeceNaziv']
                ];
            }
        }
        $korisnici = array_values($korisnici);

        mysqli_stmt_close($stmt);

        Dnevnik::upisiUDnevnik($con, 'Uspješan dohvat korisnika', Dnevnik::TrenutnoVrijeme($con), 9);
        echo json_encode(['data' => $korisnici]);
    } else {
        Dnevnik::upisiUDnevnik($con, 'Neuspješan dohvat korisnika', Dnevnik::TrenutnoVrijeme($con), 8);
        http_response_code(404);
    }
} else {
    Dnevnik::upisiUDnevnik($con, 'Nisu postavljeni podaci', Dnevnik::TrenutnoVrijeme($con), 8);
    trigger_error("Nisu postavljeni podaci", E_USER_ERROR);
}
mysqli_close($con);
