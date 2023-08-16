<?php

require_once ("./connection.php");
require_once ("./dnevnikclass.php");

$postData = file_get_contents("php://input");

if(isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje postavi stranicenje i veličinu slike', Dnevnik::TrenutnoVrijeme($con), 5);

    $stranicenje = filter_var($result->data->Stranicenje, FILTER_SANITIZE_NUMBER_INT);
    $imgSize = filter_var($result->data->ImgSize, FILTER_SANITIZE_NUMBER_INT);

    if (!isset($stranicenje) || !isset($imgSize)) {
        Dnevnik::upisiUDnevnik($con, 'Nisu svi podaci uneseni', Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
    }

    $sql = "UPDATE konfiguracija SET Stranicenje = ?, ImgSize = ? WHERE ID = 1";

    Dnevnik::upisiUDnevnik($con, 'Upit za postavljanje straničenja i veličine slike', Dnevnik::TrenutnoVrijeme($con), 1);

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $stranicenje, $imgSize);

    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($success) {
        Dnevnik::upisiUDnevnik($con, 'Uspješno postavljeno straničenje i veličina slike', Dnevnik::TrenutnoVrijeme($con), 9);
        echo json_encode(['data' => 'Success']);
    } else {
        Dnevnik::upisiUDnevnik($con, 'Neuspješno postavljeno straničenje i veličina slike', Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Nije moguće postaviti straničenje", E_USER_ERROR);
    }
}