<?php

require 'connection.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    if(isset($result->data->ID) && !empty($result->data->ID))
    {

        $id = filter_var($result->data->ID, FILTER_SANITIZE_NUMBER_INT);
        $naziv = htmlspecialchars($result->data->Naziv);
        $opis = htmlspecialchars($result->data->Opis);
        $vrijemePocetka = htmlspecialchars($result->data->RadnoVrijemeOd);
        $vrijemePocetka = strtotime($vrijemePocetka);
        $vrijemeKraja = htmlspecialchars($result->data->RadnoVrijemeDo);
        $vrijemeKraja = strtotime($vrijemeKraja);

        if(!isset($id) || !isset($naziv) || !isset($opis) || !isset($vrijemePocetka) || !isset($vrijemeKraja))
        {
            trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
        }

        if ($vrijemePocetka > $vrijemeKraja) {
            trigger_error("Vrijeme kraja ne može biti prije vremena početka", E_USER_ERROR);
        }

        $vrijemePocetka = date('H:i:s', $vrijemePocetka);
        $vrijemeKraja = date('H:i:s', $vrijemeKraja);

        $sql = "UPDATE poduzece SET Naziv = ?, Opis = ?, RadnoVrijemeOd = ?, RadnoVrijemeDo = ? WHERE ID = ?";
        $stmt = mysqli_prepare($con, $sql); //prepare statement
        mysqli_stmt_bind_param($stmt, "ssssi", $naziv, $opis, $vrijemePocetka, $vrijemeKraja, $id); //bind parameters
        mysqli_stmt_execute($stmt); //execute query
        $affected_rows = mysqli_stmt_affected_rows($stmt); //get the number of affected rows

        if ($affected_rows == 1) {
            echo json_encode(['data' => 'Success']);
        } else {
            trigger_error("Došlo je do pogreške prilikom ažuriranja poduzeća", E_USER_ERROR);
        }

        mysqli_stmt_close($stmt); //close statement
    } else {
        $naziv = htmlspecialchars($result->data->Naziv);
        $opis = htmlspecialchars($result->data->Opis);
        $vrijemePocetka = htmlspecialchars($result->data->RadnoVrijemeOd);
        $vrijemePocetka = strtotime($vrijemePocetka);
        $vrijemeKraja = htmlspecialchars($result->data->RadnoVrijemeDo);
        $vrijemeKraja = strtotime($vrijemeKraja);

        
        if(!isset($naziv) || !isset($opis) || !isset($vrijemePocetka) || !isset($vrijemeKraja))
        {
            trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
        }

        if ($vrijemePocetka > $vrijemeKraja) {
            trigger_error("Vrijeme kraja ne može biti prije vremena početka", E_USER_ERROR);
        }

        $vrijemePocetka = date('H:i:s', $vrijemePocetka);
        $vrijemeKraja = date('H:i:s', $vrijemeKraja);

        $sql = "INSERT INTO poduzece (Naziv, Opis, RadnoVrijemeOd, RadnoVrijemeDo) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql); //prepare statement
        mysqli_stmt_bind_param($stmt, "ssss", $naziv, $opis, $vrijemePocetka, $vrijemeKraja); //bind parameters
        mysqli_stmt_execute($stmt); //execute query

        $affected_rows = mysqli_stmt_affected_rows($stmt); //get the number of affected rows

        if ($affected_rows == 1) {
            echo json_encode(['data' => 'Success']);
        } else {
            trigger_error("Došlo je do pogreške prilikom dodavanja poduzeća", E_USER_ERROR);
        }

        mysqli_stmt_close($stmt); //close statement
    }
    mysqli_close($con); //close connection
}