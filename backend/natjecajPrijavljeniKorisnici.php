<?php

require_once 'connection.php';
require_once 'dnevnikClass.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje natjecaj prijava korisnika', Dnevnik::TrenutnoVrijeme($con), 5);

    $ime = mysqli_real_escape_string($con, trim($result->data->Ime));
    $prezime = mysqli_real_escape_string($con, trim($result->data->Prezime));
    $natjecajID = filter_var($result->data->NatjecajID, FILTER_SANITIZE_NUMBER_INT);

    if (!isset($ime) || !isset($prezime) || !isset($natjecajID)) {
        Dnevnik::upisiUDnevnik($con, 'Nisu svi podaci uneseni', Dnevnik::TrenutnoVrijeme($con), 7);
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

    $sql = "SELECT pnn.ID, k.Ime, k.Prezime, pnn.Slika, pnn.KorisnikID
            FROM prijavananatjecaj pnn
            LEFT JOIN korisnik k ON pnn.KorisnikID = k.ID";

    if (count($conditions) > 0) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY pnn.ID DESC';

    Dnevnik::upisiUDnevnik($con, 'Upit natjecaj prijava korisnika', Dnevnik::TrenutnoVrijeme($con), 3);

    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));

    if ($types != '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (mysqli_stmt_execute($stmt)) {
        Dnevnik::upisiUDnevnik($con, 'Uspješan upit natjecaj prijava korisnika', Dnevnik::TrenutnoVrijeme($con), 9);
        $result = mysqli_stmt_get_result($stmt);

        $cr = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $korisnici[$cr]['ID'] = (int)$row['ID'];
            $korisnici[$cr]['Ime'] = $row['Ime'];
            $korisnici[$cr]['Prezime'] = $row['Prezime'];
            $korisnici[$cr]['Slika'] = $row['Slika'];
            $korisnici[$cr]['KorisnikID'] = (int)$row['KorisnikID'];
            $cr++;
        }

        Dnevnik::upisiUDnevnik($con, 'Uspješan dohvat prijavljenih korisnika', Dnevnik::TrenutnoVrijeme($con), 9);
        echo json_encode(['data' => $korisnici]);
    } else {
        Dnevnik::upisiUDnevnik($con, 'Problem kod dohvaćanja prijavljenih korisnika', Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Problem kod dohvaćanja prijavljenih korisnika", E_USER_ERROR);
    }
}
