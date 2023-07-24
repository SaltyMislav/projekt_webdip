<?php

require 'connection.php';

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

    if (password_verify($result->data->password, $user['Password'])) {
        session_start();
        $_SESSION['user_ID'] = $user['ID'];
        $_SESSION['uloga'] = $user['UlogaKorisnikaID'];
        $_SESSION['user'] = $user['KorisnickoIme'];
        echo json_encode(['data' => $_SESSION]);
    } else {
        trigger_error("Pogrešna lozinka", E_USER_ERROR);
    }
}

function find_user_by_email($korisnickoIme, $con) {
    $sql = "SELECT ID, KorisnickoIme, Password, Active, UlogaKorisnikaID FROM korisnik WHERE KorisnickoIme = ?";

    
    $korisnickoIme = trim($korisnickoIme);
    $korisnickoIme = strip_tags($korisnickoIme);
    $korisnickoIme = htmlspecialchars($korisnickoIme);

    $stmt = mysqli_prepare($con, $sql); //prepare statement
    mysqli_stmt_bind_param($stmt, "s", $korisnickoIme); //bind parameters
    mysqli_stmt_execute($stmt); //execute query

    $result = mysqli_stmt_get_result($stmt); //get the mysqli result

    return mysqli_fetch_assoc($result); //fetch data
}