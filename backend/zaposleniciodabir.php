<?php

require_once 'connection.php';
require_once 'dnevnikClass.php';

$postData = file_get_contents('php://input');
$zaposlenici = [];

if (isset($postData) && !empty($postData)) {
    $request = json_decode($postData);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje zaposleniciodabir', Dnevnik::TrenutnoVrijeme($con), 5);

    $korisnikID = filter_var($request->data->KorisnikID, FILTER_SANITIZE_NUMBER_INT);
    $ulogaID = filter_var($request->data->UlogaID, FILTER_SANITIZE_NUMBER_INT);

    if (!isset($korisnikID) || !isset($ulogaID)) {
        Dnevnik::upisiUDnevnik($con, 'Nisu svi podaci uneseni', Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
    }

    $conditions = [];
    $params = [];
    $types = '';

    if ($ulogaID == 2) {
        $sql = "SELECT k.ID, CONCAT(k.Ime, ' ', k.Prezime) AS ImePrezime, k.PoduzeceID
                FROM korisnik k
                INNER JOIN moderatorpoduzeca mp ON mp.PoduzeceID = k.PoduzeceID";
        if ($korisnikID != '') {
            $conditions[] = "mp.KorisnikID = ?";
            $params[] = $korisnikID;
            $types .= 'i';
        }
    } else if ($ulogaID == 3) {
        $sql = "SELECT k.ID, CONCAT(k.Ime, ' ', k.Prezime) AS ImePrezime, k.PoduzeceID
                FROM korisnik k
                WHERE k.UlogaKorisnikaID = 1 AND k.PoduzeceID IS NOT NULL";
    } else {
        $sql = "SELECT k.ID, CONCAT(k.Ime, ' ', k.Prezime) AS ImePrezime, k.PoduzeceID
                FROM korisnik k";
        if ($korisnikID != '') {
            $conditions[] = "k.ID = ?";
            $params[] = $korisnikID;
            $types .= 'i';
        }
    }

    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
    
    if ($types != '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        $cr= 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $zaposlenici[$cr]['ID'] = (int)$row['ID'];
            $zaposlenici[$cr]['ImePrezime'] = $row['ImePrezime'];
            $zaposlenici[$cr]['PoduzeceID'] = (int)$row['PoduzeceID'];
            $cr++;
        }

        Dnevnik::upisiUDnevnik($con, 'Uspješno dohvaćeni zaposlenici', Dnevnik::TrenutnoVrijeme($con), 9);
        echo json_encode(['data' => $zaposlenici]);
    } else {
        mysqli_stmt_close($stmt);
        Dnevnik::upisiUDnevnik($con, 'Neuspješno dohvaćeni zaposlenici', Dnevnik::TrenutnoVrijeme($con), 8);
        http_response_code(404);
    }
} else {
    Dnevnik::upisiUDnevnik($con, 'Neuspješno dohvaćeni zaposlenici', Dnevnik::TrenutnoVrijeme($con), 8);
    trigger_error("Nije moguće dohvatiti podatke o zaposlenicima!", E_USER_ERROR);
}

mysqli_close($con);
