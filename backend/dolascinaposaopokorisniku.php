<?php

require_once ("./connection.php");
require_once ("./dnevnikclass.php");

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $response = json_decode($postData);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje dohvat dolaska na posao po korisniku', Dnevnik::TrenutnoVrijeme($con), 5);

    $korisnikID = filter_var($response->data->KorisnikID, FILTER_SANITIZE_NUMBER_INT);

    if (!isset($korisnikID)) {
        Dnevnik::upisiUDnevnik($con, 'KorisnikID nije validan', Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
    }

    $sql = "SELECT * FROM dolascinaposao WHERE KorisnikID = ? ORDER BY DatumVrijemeDolaska DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $korisnikID);
    mysqli_stmt_execute($stmt);

    Dnevnik::upisiUDnevnik($con, 'Upit za dohvat dolazaka na posao po korisniku -' . $korisnikID, Dnevnik::TrenutnoVrijeme($con), 3);

    $result = mysqli_stmt_get_result($stmt);
    
    mysqli_stmt_close($stmt);

    $dolasciNaPosao = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $dolasciNaPosao[] = [
            'ID' => (int)$row['ID'],
            'DatumVrijemeDolaska' => $row['DatumVrijemeDolaska'],
            'KorisnikID' => (int)$row['KorisnikID']
        ];
    }

    Dnevnik::upisiUDnevnik($con, 'Uspješan dohvat dolazaka na posao po korisniku -' . $korisnikID, Dnevnik::TrenutnoVrijeme($con), 9);
    echo json_encode(['data' => $dolasciNaPosao]);
} else {
    Dnevnik::upisiUDnevnik($con, 'Neuspješan dohvat dolazaka na posao po korisniku -' . $korisnikID, Dnevnik::TrenutnoVrijeme($con), 8);
    trigger_error("Greška kod dohvaćanja dolazaka na posao!", E_USER_ERROR);
}

mysqli_close($con);
