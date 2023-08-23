<?php

require_once("./connection.php");
require_once("./dnevnikclass.php");

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $request = json_decode($postData);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje zaposlenici', Dnevnik::TrenutnoVrijeme($con), 5);

    $prezime = mysqli_real_escape_string($con, trim($request->data->Prezime));
    $prezime = strtolower($prezime);

    if (!isset($prezime)) {
        Dnevnik::upisiUDnevnik($con, 'Nisu svi podaci uneseni', Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
    }

    $zaposlenici = [];
    $conditions = [];
    $params = [];
    $types = '';

    $sql = "SELECT k.ID, k.Ime, k.Prezime, k.PoduzeceID, p.Naziv AS PoduzeceNaziv
            FROM korisnik k 
            INNER JOIN poduzece p ON p.ID = k.PoduzeceID
            WHERE k.UlogaKorisnikaID = 1";

    if ($prezime != '') {
        $conditions[] = "LOWER(k.Prezime) LIKE ?";
        $params[] = '%' . $prezime . '%';
        $types .= 's';
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

        Dnevnik::upisiUDnevnik($con, 'Uspješno dohvaćeni zaposlenici', Dnevnik::TrenutnoVrijeme($con), 9);

        while ($row = mysqli_fetch_assoc($result)) {
            $zaposlenici[] = [
                'ID' => (int)$row['ID'],
                'Ime' => $row['Ime'],
                'Prezime' => $row['Prezime'],
                'PoduzeceID' => (int)$row['PoduzeceID'],
                'PoduzeceNaziv' => $row['PoduzeceNaziv']
            ];
        }
        echo json_encode(['data' => $zaposlenici]);
    } else {
        Dnevnik::upisiUDnevnik($con, 'Neuspješno dohvaćeni zaposlenici', Dnevnik::TrenutnoVrijeme($con), 8);
        http_response_code(404);
    }
} else {
    Dnevnik::upisiUDnevnik($con, 'Neuspješno dohvaćeni zaposlenici', Dnevnik::TrenutnoVrijeme($con), 8);
    trigger_error('Nije moguće dohvatiti podatke o zaposlenicima!', E_USER_ERROR);
}

mysqli_close($con);
