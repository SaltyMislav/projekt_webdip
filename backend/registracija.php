<?php

require 'connection.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    $ime = validate_ime($result);
    $prezime = validate_prezime($result);
    $userName = validate_userName($result);
    $email = validate_email($result);
    $password = $result->data->password;
    $confirmedPassword = $result->data->confirmedPassword;


    //check if user already exists
    provjeraKorisnika($userName, $con);

    //check if passwords match
    if ($result->data->password != $result->data->confirmedPassword) {
        trigger_error("Lozinke se ne podudaraju", E_USER_ERROR);
    }

    //validate password with regex
    if (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/", $password)) {
        trigger_error("Lozinka mora imati najmanje 8 znakova, jedno veliko slovo, jedan broj i jedan specijalni znak", E_USER_ERROR);
    } else {
        $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
    }

    $token = bin2hex(openssl_random_pseudo_bytes(20));

    //token expiry time set to 10 hours
    $expiry = 36000;
    $istekAktivacije = time() + $expiry; //86400 = 1 dan

    $sql = "INSERT INTO korisnik (Ime, Prezime, KorisnickoIme, Email, Password, Lozinka_Upis, DatumRegistracije, Token, IstekTokena, UlogaKorisnikaID) VALUES ('$ime', '$prezime', '$userName', '$email', '$passwordHashed', '$password', NOW(), '$token', '$istekAktivacije', 1)";
    //insert data into database
    if (mysqli_query($con, $sql)) {
        slanjeMaila($email, $token);
        http_response_code(201);
    } else {
        http_response_code(422);
    }
    
    mysqli_close($con);
}


function slanjeMaila($email, $token) {
    $subject = "Registracija";
    $message = "Uspješno ste se registrirali na stranicu! Molimo potvrdite svoj račun klikom na link: http://localhost/aktivacija/?email=$email&token=$token";
    $headers = array(
        'From' => 'mznidarec@foi.hr',
        'Content-Type' => 'text/plain; charset=UTF-8' . '\r\n',
    );
    //send email
    if (mail($email, $subject, $message, $headers))
    {
        echo "Uspješno ste se registrirali na stranicu!";
    }
    else
    {
        echo "Email nije poslan";
    };
}

function validate_ime($result)
{
    if (isset($result->data->ime) && !empty($result->data->ime)) {
        $ime = trim($result->data->ime);
        $ime = strip_tags($ime);
        $ime = htmlspecialchars($ime);
        if (strlen($ime) < 3 || strlen($ime) > 20) {
            trigger_error("Ime nema između 3 i 20 znakova", E_USER_ERROR);
        } else {
            return $ime;
        }
    } else {
        trigger_error("Ime nema između 3 i 20 znakova", E_USER_ERROR);
    }
}

function validate_prezime($result)
{
    if (isset($result->data->prezime) && !empty($result->data->prezime)) {
        $prezime = trim($result->data->prezime);
        $prezime = strip_tags($prezime);
        $prezime = htmlspecialchars($prezime);
        if (strlen($prezime) < 3 || strlen($prezime) > 20) {
            trigger_error("Prezime nema između 3 i 20 znakova", E_USER_ERROR);
        } else {
            return $prezime;
        }
    } else {
        trigger_error("Prezime nema između 3 i 20 znakova", E_USER_ERROR);
    }
}

function validate_userName($result)
{
    if (isset($result->data->userName) && !empty($result->data->userName)) {
        $userName = trim($result->data->userName);
        $userName = strip_tags($userName);
        $userName = htmlspecialchars($userName);
        if (strlen($userName) < 3 || strlen($userName) > 20) {
            trigger_error("Korisničko ime nema između 3 i 20 znakova", E_USER_ERROR);
        } else {
            return $userName;
        }
    } else {
        trigger_error("Korisničko ime nema između 3 i 20 znakova", E_USER_ERROR);
    }
}

//function to validate email
function validate_email($result)
{
    if (isset($result->data->email) && !empty($result->data->email)) {
        $email = trim($result->data->email);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, 274)) //274 is FILTER_VALIDATE_EMAIL
        {
            trigger_error("Email nije ispravan", E_USER_ERROR);
        } else {
            return $email;
        }
    } else {
        trigger_error("Email nije ispravan", E_USER_ERROR);
    }
}


function provjeraKorisnika($userName, $con)
{
    $sqlProvjera = "SELECT * FROM korisnik WHERE KorisnickoIme = '$userName'";
    $provjeraPodataka = mysqli_query($con, $sqlProvjera);
    if (mysqli_num_rows($provjeraPodataka) > 0) {
        trigger_error("Korisnik već postoji", E_USER_ERROR);
    }
}
