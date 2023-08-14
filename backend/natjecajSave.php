<?php

require 'connection.php';
require 'virtualnoVrijemeClass.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    if (isset($result->data->ID) && !empty($result->data->ID)) {
        $id = filter_var($result->data->ID, FILTER_SANITIZE_NUMBER_INT);
        $naziv = mysqli_real_escape_string($con, trim($result->data->Naziv));
        $opis = mysqli_real_escape_string($con, trim($result->data->Opis));
        $status = filter_var($result->data->StatusNatjecajaID, FILTER_SANITIZE_NUMBER_INT);
        $poduzece = filter_var($result->data->PoduzeceID, FILTER_SANITIZE_NUMBER_INT);
        $vrijemePocetka = mysqli_real_escape_string($con, trim($result->data->VrijemePocetka));
        $vrijemeKraja = mysqli_real_escape_string($con, trim($result->data->VrijemeKraja));

        if (!isset($id) || !isset($naziv) || !isset($opis) || !isset($vrijemePocetka) || !isset($vrijemeKraja)) {
            trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
        }

        $vrijemePocetka = strtotime($vrijemePocetka);
        $vrijemeKraja = strtotime($vrijemeKraja);

        if ($vrijemePocetka > $vrijemeKraja) {
            trigger_error("Vrijeme početka ne može biti veće od vremena kraja", E_USER_ERROR);
        }

        $difference = $vrijemeKraja - $vrijemePocetka;
        $tedDays = 10 * 24 * 60 * 60;

        if ($difference < $tedDays) {
            trigger_error("Natječaj mora trajati točno 10 dana", E_USER_ERROR);
        }

        $trenutnoVrijeme = date('Y-m-d H:i:s');
        $trenutnoVrijeme = strtotime($trenutnoVrijeme. VirtualnoVrijeme::procitajVrijeme($con) . 'hours');

        if ($status != 2){
            if ($trenutnoVrijeme >= $vrijemeKraja)
                $status = 2;
            updateZaposlenika($con, $id, $poduzece, $status);
        } else {
            if ($trenutnoVrijeme < $vrijemeKraja)
                $status = 1;
            updateZaposlenika($con, $id, $poduzece, $status);
        }

        $vrijemePocetka = date('Y-m-d H:i:s', $vrijemePocetka);
        $vrijemeKraja = date('Y-m-d H:i:s', $vrijemeKraja);

        $sql = "UPDATE natjecaj SET Naziv = ?, Opis = ?, VrijemePocetka = ?, VrijemeKraja = ?, StatusNatjecajaID = ?, PoduzeceID = ? WHERE ID = ?";
        $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
        mysqli_stmt_bind_param($stmt, 'ssssiii', $naziv, $opis, $vrijemePocetka, $vrijemeKraja, $status, $poduzece, $id);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['data' => 'Uspješno uređen natječaj']);
        } else {
            trigger_error("Problem kod uređivanja natječaja", E_USER_ERROR);
        }
    } else {
        $naziv = mysqli_real_escape_string($con, trim($result->data->Naziv));
        $opis = mysqli_real_escape_string($con, trim($result->data->Opis));
        $status = filter_var($result->data->StatusNatjecajaID, FILTER_SANITIZE_NUMBER_INT);
        $poduzece = filter_var($result->data->PoduzeceID, FILTER_SANITIZE_NUMBER_INT);
        $vrijemePocetka = mysqli_real_escape_string($con, trim($result->data->VrijemePocetka));
        $vrijemeKraja = mysqli_real_escape_string($con, trim($result->data->VrijemeKraja));

        if (!isset($naziv) || !isset($opis) || !isset($vrijemePocetka) || !isset($vrijemeKraja)) {
            trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
        }

        $vrijemePocetka = strtotime($vrijemePocetka);
        $vrijemeKraja = strtotime($vrijemeKraja);

        if ($vrijemePocetka > $vrijemeKraja) {
            trigger_error("Vrijeme početka ne može biti veće od vremena kraja", E_USER_ERROR);
        }

        $difference = $vrijemeKraja - $vrijemePocetka;
        $tedDays = 10 * 24 * 60 * 60;

        if ($difference < $tedDays) {
            trigger_error("Natječaj mora trajati točno 10 dana", E_USER_ERROR);
        }

        $vrijemePocetka = date('Y-m-d H:i:s', $vrijemePocetka);
        $vrijemeKraja = date('Y-m-d H:i:s', $vrijemeKraja);

        $sql = "INSERT INTO natjecaj (Naziv, Opis, VrijemePocetka, VrijemeKraja, StatusNatjecajaID, PoduzeceID) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
        mysqli_stmt_bind_param($stmt, 'ssssii', $naziv, $opis, $vrijemePocetka, $vrijemeKraja, $status, $poduzece);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['data' => 'Uspješno dodan natječaj']);
        } else {
            trigger_error("Problem kod dodavanja natječaja", E_USER_ERROR);
        }
    }
}

function updateZaposlenika($con, $id, $poduzece, $status) {
    if($status == 2) {
        $sql = "SELECT KorisnikID FROM prijavananatjecaj WHERE NatjecajID = ?";
        $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
        mysqli_stmt_bind_param($stmt, 'i', $id);
    
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
    
            while ($row = mysqli_fetch_assoc($result)) {
                $korisnikID = $row['KorisnikID'];
                $sql = "UPDATE korisnik SET PoduzeceID = ? WHERE ID = ?";
                $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
                mysqli_stmt_bind_param($stmt, 'ii', $poduzece, $korisnikID);
    
                if (!mysqli_stmt_execute($stmt)) {
                    trigger_error("Problem kod ažuriranja broja prijavljenih natječaja", E_USER_ERROR);
                }
            }
        } else {
            trigger_error("Problem kod dohvaćanja korisnika", E_USER_ERROR);
        }
    } else if ($status == 1) {
        $sql = "SELECT KorisnikID FROM prijavananatjecaj WHERE NatjecajID = ?";
        $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
        mysqli_stmt_bind_param($stmt, 'i', $id);
    
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
    
            while ($row = mysqli_fetch_assoc($result)) {
                $korisnikID = $row['KorisnikID'];
                $sql = "UPDATE korisnik SET PoduzeceID = NULL WHERE ID = ?";
                $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
                mysqli_stmt_bind_param($stmt, 'i', $korisnikID);
    
                if (!mysqli_stmt_execute($stmt)) {
                    trigger_error("Problem kod ažuriranja broja prijavljenih natječaja", E_USER_ERROR);
                }
            }
        } else {
            trigger_error("Problem kod dohvaćanja korisnika", E_USER_ERROR);
        }
    }
}

mysqli_stmt_close($stmt);
mysqli_close($con);
