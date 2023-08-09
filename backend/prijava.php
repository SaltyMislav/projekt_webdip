<?php

require 'connection.php';
require 'virtualnoVrijemeClass.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    $user = find_user_by_email($result->data->korisnickoIme, $con);

    if ($user === null) {
        trigger_error("Korisnik ne postoji", E_USER_ERROR);
    }

    if ($user['Active'] == 0) {
        trigger_error("Korisnik nije aktiviran, prijava nije moguća", E_USER_ERROR);
    }

    if ($user['Blokiran'] == 1) {
        trigger_error("Pogrešna lozinka, korisnik je blokiran, kontaktirajte administratora", E_USER_ERROR);
    }

    if (password_verify($result->data->password, $user['Password'])) {

        //write login date and time to base
        $currentDateTime = date('Y-m-d H:i:s');

        $pomak = date('Y-m-d H:i:s', strtotime($currentDateTime . VirtualnoVrijeme::procitajVrijeme($con) . 'hours'));

        $sql = "UPDATE korisnik SET DatumZadnjePrijave = ?, NeuspjesnePrijave = 0 WHERE ID = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "si", $pomak, $user['ID']);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        session_start();
        $_SESSION['user_ID'] = (int)$user['ID'];
        $_SESSION['uloga'] = (int)$user['UlogaKorisnikaID'];
        $_SESSION['user'] = $user['KorisnickoIme'];
        
        echo json_encode(['data' => $_SESSION]);
    } else {

        if ($user['UlogaKorisnikaID'] == 3) {
            trigger_error("Pogrešna lozinka", E_USER_ERROR);
        }

        $userNeuspjesnePrijave = $user['NeuspjesnePrijave'] + 1;

        if ($user['NeuspjesnePrijave'] == 2) {
            $sql = "UPDATE korisnik SET Blokiran = 1 WHERE ID = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "i", $user['ID']);
            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);

            trigger_error("Korisnik je blokiran, kontaktirajte administratora", E_USER_ERROR);
        }

        $sql = "UPDATE korisnik SET NeuspjesnePrijave = ? WHERE ID = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $userNeuspjesnePrijave, $user['ID']);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        $brojpreostalihPokusaja = 3 - $userNeuspjesnePrijave;

        if ($brojpreostalihPokusaja != 0){
            trigger_error("Pogrešna lozinka, imate pravo na još $brojpreostalihPokusaja pokušaja", E_USER_ERROR);
        }

        trigger_error("Pogrešna lozinka, korisnik je blokiran, kontaktirajte administratora", E_USER_ERROR);
    }

    mysqli_close($con);
}

function find_user_by_email($korisnickoIme, $con) {
    $sql = "SELECT ID, KorisnickoIme, Password, Active, NeuspjesnePrijave, Blokiran, UlogaKorisnikaID FROM korisnik WHERE KorisnickoIme = ?";

    $korisnickoIme = trim($korisnickoIme);
    $korisnickoIme = strip_tags($korisnickoIme);
    $korisnickoIme = htmlspecialchars($korisnickoIme);

    $stmt = mysqli_prepare($con, $sql); //prepare statement
    mysqli_stmt_bind_param($stmt, "s", $korisnickoIme); //bind parameters
    mysqli_stmt_execute($stmt); //execute query

    $result = mysqli_stmt_get_result($stmt); //get the mysqli result

    return mysqli_fetch_assoc($result); //fetch data
}