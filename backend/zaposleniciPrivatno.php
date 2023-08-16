<?php

require_once ("./connection.php");
require_once ("./dnevnikclass.php");

$postData = file_get_contents("php://input");

$zaposlenici = [];

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData, true);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje zaposleniciPrivatno', Dnevnik::TrenutnoVrijeme($con), 5);

    $ime = mysqli_real_escape_string($con, strtolower(trim($result['data']['Ime'])));
    $prezime = mysqli_real_escape_string($con, strtolower(trim($result['data']['Prezime'])));
    $ulogaID = filter_var($result['data']['UlogaID'], FILTER_SANITIZE_NUMBER_INT);
    $korisnikID = filter_var($result['data']['KorisnikID'], FILTER_SANITIZE_NUMBER_INT);

    if (!isset($ulogaID) || !isset($korisnikID) || !isset($ime) || !isset($prezime)) {
        Dnevnik::upisiUDnevnik($con, 'Nisu svi podaci uneseni', Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
    }

    if (empty($korisnikID)) {
        Dnevnik::upisiUDnevnik($con, 'Nije dodan podatak koji moderator je tražio podatake', Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Potrebno je dodati podatak koji moderator je tražio podatake", E_USER_ERROR);
    }

    $conditions = [];
    $params = [];
    $types = '';

    if ($ime != '') {
        $conditions[] = "LOWER(k.Ime) LIKE ?";
        $params[] = '%' . $ime . '%';
        $types .= 's';
    }

    if ($prezime != '') {
        $conditions[] = "LOWER(k.Prezime) LIKE ?";
        $params[] = '%' . $prezime . '%';
        $types .= 's';
    }

    if ($ulogaID == 2) {
        $sql = "SELECT k.ID, k.Ime, k.Prezime, k.BrojOdradenihZadataka, k.BrojNeodradenihZadataka, k.PoduzeceID, p.Naziv AS PoduzeceNaziv
                FROM korisnik k 
                INNER JOIN poduzece p ON k.PoduzeceID = p.ID 
                INNER JOIN moderatorpoduzeca mp ON mp.PoduzeceID = p.ID
                WHERE k.UlogaKorisnikaID = 1";
        if ($korisnikID != '') {
            $conditions[] = "mp.KorisnikID = ?";
            $params[] = $korisnikID;
            $types .= 'i';
        }
    } else if ($ulogaID == 3) {
        $sql = "SELECT k.ID, k.Ime, k.Prezime, k.BrojDolazakaNaPosao, k.BrojOdradenihZadataka, k.BrojNeodradenihZadataka, k.PoduzeceID, p.Naziv AS PoduzeceNaziv
                FROM korisnik k 
                INNER JOIN poduzece p ON k.PoduzeceID = p.ID 
                WHERE k.UlogaKorisnikaID = 1";
    } else {
        trigger_error("Uloga korisnika nema prava na pristup podatacima", E_USER_ERROR);
    }

    if (count($conditions) > 0) {
        $sql .= " AND " . implode(' AND ', $conditions);
    }

    $stmt = mysqli_prepare($con, $sql);

    if ($types != '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            if ($ulogaID == 2) {
                $zaposlenici[] = [
                    'ID' => (int)$row['ID'],
                    'Ime' => $row['Ime'],
                    'Prezime' => $row['Prezime'],
                    'BrojOdradenihZadataka' => (int)$row['BrojOdradenihZadataka'],
                    'BrojNeodradenihZadataka' => (int)$row['BrojNeodradenihZadataka'],
                    'PoduzeceID' => (int)$row['PoduzeceID'],
                    'PoduzeceNaziv' => $row['PoduzeceNaziv']
                ];
            } else {
                $zaposlenici[] = [
                    'ID' => (int)$row['ID'],
                    'Ime' => $row['Ime'],
                    'Prezime' => $row['Prezime'],
                    'BrojDolazakaNaPosao' => (int)$row['BrojDolazakaNaPosao'],
                    'BrojOdradenihZadataka' => (int)$row['BrojOdradenihZadataka'],
                    'BrojNeodradenihZadataka' => (int)$row['BrojNeodradenihZadataka'],
                    'PoduzeceID' => (int)$row['PoduzeceID'],
                    'PoduzeceNaziv' => $row['PoduzeceNaziv']
                ];
            }
        }

        Dnevnik::upisiUDnevnik($con, 'Uspješno dohvaćeni zaposlenici', Dnevnik::TrenutnoVrijeme($con), 9);
        echo json_encode(['data' => $zaposlenici]);
    } else {
        Dnevnik::upisiUDnevnik($con, 'Neuspješno dohvaćeni zaposlenici', Dnevnik::TrenutnoVrijeme($con), 8);
        http_response_code(404);
    }
} else {
    mysqli_stmt_close($stmt);
    Dnevnik::upisiUDnevnik($con, 'Neuspješno dohvaćeni zaposlenici', Dnevnik::TrenutnoVrijeme($con), 8);
    trigger_error('Nije moguće dohvatiti podatke o zaposlenicima!', E_USER_ERROR);
}

mysqli_close($con);
