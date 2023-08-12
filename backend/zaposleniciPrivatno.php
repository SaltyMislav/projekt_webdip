<?php

require 'connection.php';

$postData = file_get_contents("php://input");

$zaposlenici = [];

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData, true);

    $ime = mysqli_real_escape_string($con, strtolower(trim($result['data']['Ime'])));
    $prezime = mysqli_real_escape_string($con, strtolower(trim($result['data']['Prezime'])));
    $ulogaID = filter_var($result['data']['UlogaID'], FILTER_SANITIZE_NUMBER_INT);
    $korisnikID = filter_var($result['data']['KorisnikID'], FILTER_SANITIZE_NUMBER_INT);

    if (empty($korisnikID)) {
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
        echo json_encode(['data' => $zaposlenici]);
    } else {
        http_response_code(404);
    }
} else {
    $sql = "SELECT k.ID, k.Ime, k.Prezime, k.BrojDolazakaNaPosao, k.BrojOdradenihZadataka, k.BrojNeodradenihZadataka, k.PoduzeceID, p.Naziv AS PoduzeceNaziv
                FROM korisnik k 
                INNER JOIN poduzece p ON k.PoduzeceID = p.ID 
                WHERE k.UlogaKorisnikaID = 1";

    if ($result = mysqli_query($con, $sql)) {
        $cr = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $zaposlenici[$cr]['ID'] = (int)$row['ID'];
            $zaposlenici[$cr]['Ime'] = $row['Ime'];
            $zaposlenici[$cr]['Prezime'] = $row['Prezime'];
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
}

mysqli_close($con);
