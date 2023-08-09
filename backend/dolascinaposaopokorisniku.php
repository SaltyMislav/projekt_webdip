<?php

require 'connection.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $response = json_decode($postData);

    $korisnikID = filter_var($response->data->KorisnikID, FILTER_SANITIZE_NUMBER_INT);

    $sql = "SELECT * FROM dolascinaposao WHERE KorisnikID = ? ORDER BY DatumVrijemeDolaska DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $korisnikID);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $dolasciNaPosao = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $dolasciNaPosao[] = [
            'ID' => (int)$row['ID'],
            'DatumVrijemeDolaska' => $row['DatumVrijemeDolaska'],
            'KorisnikID' => (int)$row['KorisnikID']
        ];
    }

    echo json_encode(['data' => $dolasciNaPosao]);

    mysqli_stmt_close($stmt);
} else {
    trigger_error("Greška kod dohvaćanja dolazaka na posao!", E_USER_ERROR);
}

mysqli_close($con);
