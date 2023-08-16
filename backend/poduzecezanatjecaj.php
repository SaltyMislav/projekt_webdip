<?php

require_once 'connection.php';
require_once 'dnevnikClass.php';

$postData = file_get_contents("php://input");

if(isset($postData) && !empty($postData)){
    $result = json_decode($postData);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje poduzece za natjecaj', Dnevnik::TrenutnoVrijeme($con), 5);

    $ulogaID = filter_var($result->data->UlogaID, FILTER_SANITIZE_NUMBER_INT);
    $korisnikID = filter_var($result->data->KorisnikID, FILTER_SANITIZE_NUMBER_INT);
    $poduzeca = [];

    $conditions = [];
    $params = [];
    $types = '';

    if (empty($ulogaID) || empty($korisnikID)) {
        Dnevnik::upisiUDnevnik($con, 'Nedovoljno podataka za izvršiti upit', Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Nedovoljno podataka za izvršiti upit", E_USER_ERROR);
    }

    if ($ulogaID == 3) {
        $sql = "SELECT p.ID, p.Naziv from poduzece p";
    } else if ($ulogaID == 2) {
        $sql = "SELECT p.ID, p.Naziv from poduzece p
                LEFT JOIN moderatorpoduzeca mp on mp.PoduzeceID = p.ID";
        $conditions[] = "mp.KorisnikID = ?";
        $params[] = $korisnikID;
        $types .= 'i';
    } else if ($ulogaID == 1) {
        $sql = "SELECT p.ID, p.Naziv from poduzece p";
    }

    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $sql .= " ORDER BY p.ID ASC";

    Dnevnik::upisiUDnevnik($con, 'Upit poduzece za natjecaj', Dnevnik::TrenutnoVrijeme($con), 3);

    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));

    if ($types != '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (mysqli_stmt_execute($stmt)) {

        Dnevnik::upisiUDnevnik($con, 'Uspješan upit poduzece za natjecaj', Dnevnik::TrenutnoVrijeme($con), 9);

        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $poduzeca[] = [
                'ID' => (int)$row['ID'],
                'Naziv' => $row['Naziv']
            ];
        }

        Dnevnik::upisiUDnevnik($con, 'Uspješno dohvaćanje poduzece za natjecaj', Dnevnik::TrenutnoVrijeme($con), 9);
        echo json_encode(['data' => $poduzeca]);
    } else {
        Dnevnik::upisiUDnevnik($con, 'Neuspješan upit poduzece za natjecaj', Dnevnik::TrenutnoVrijeme($con), 8);
        http_response_code(404);
    }
}