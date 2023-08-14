<?php

require 'connection.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    $ime = mysqli_real_escape_string($con, trim($result->data->Ime));
    $prezime = mysqli_real_escape_string($con, trim($result->data->Prezime));
    $natjecajID = filter_var($result->data->NatjecajID, FILTER_SANITIZE_NUMBER_INT);

    if (!isset($ime) || !isset($prezime) || !isset($natjecajID)) {
        trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
    }

    $korisnici = [];
    $conditions = [];
    $params = [];
    $types = '';

    if ($ime != '') {
        $conditions[] = 'k.Ime LIKE ?';
        $params[] = '%' . $ime . '%';
        $types .= 's';
    }

    if ($prezime != '') {
        $conditions[] = 'k.Prezime LIKE ?';
        $params[] = '%' . $prezime . '%';
        $types .= 's';
    }

    if ($natjecajID != '') {
        $conditions[] = 'pnn.NatjecajID = ?';
        $params[] = $natjecajID;
        $types .= 'i';
    }

    $sql = "SELECT pnn.ID, k.Ime, k.Prezime, pnn.Slika 
            FROM prijavananatjecaj pnn
            LEFT JOIN korisnik k ON pnn.KorisnikID = k.ID";

    if (count($conditions) > 0) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY pnn.ID DESC';

    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));

    if ($types != '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        $cr = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $base64image = base64_encode($row['Slika']);
            $korisnici[$cr]['ID'] = (int)$row['ID'];
            $korisnici[$cr]['Ime'] = $row['Ime'];
            $korisnici[$cr]['Prezime'] = $row['Prezime'];
            $korisnici[$cr]['Slika'] = $base64image;
            $cr++;
        }
        echo json_encode(['data' => $korisnici]);
    } else {
        trigger_error("Problem kod dohvaćanja prijavljenih korisnika", E_USER_ERROR);
    }
}
