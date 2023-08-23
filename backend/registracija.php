<?php

require_once ("./connection.php");
require_once ("./virtualnoVrijemeClass.php");
require_once ("./dnevnikclass.php");

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje registracije', Dnevnik::TrenutnoVrijeme($con), 5);

    $ime = validate_ime($result, $con);
    $prezime = validate_prezime($result, $con);
    $userName = validate_userName($result, $con);
    $email = validate_email($result, $con);

    Dnevnik::upisiUDnevnik($con, 'Validacija uspješna', Dnevnik::TrenutnoVrijeme($con), 9);

    $password = $result->data->password;
    $confirmedPassword = $result->data->confirmedPassword;

    $captcha = $result->data->captcha;

    if (!$captcha) {
        Dnevnik::upisiUDnevnik($con, 'Captcha nije ispunjena', Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Captcha nije ispunjena", E_USER_ERROR);
    }

    $secretKey = '6Lc6JFknAAAAAEY00jpvfLEX5SuM3kd-ekLyJ7LW';
    $url = "https://www.google.com/recaptcha/api/siteverify?secret=". urlencode($secretKey). "&response=" . urlencode($captcha);

    $response = file_get_contents($url);
    $responseKeys = json_decode($response, true);

    if (!$responseKeys["success"]) {
        Dnevnik::upisiUDnevnik($con, 'Captcha nije dobra', Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Captcha nije dobra, probajte ponovo", E_USER_ERROR);
    }


    //check if user already exists
    provjeraKorisnika($userName, $con);

    Dnevnik::upisiUDnevnik($con, 'Korisnik ne postoji', Dnevnik::TrenutnoVrijeme($con), 9);

    //check if passwords match
    if ($result->data->password != $result->data->confirmedPassword) {
        Dnevnik::upisiUDnevnik($con, 'Lozinke se ne podudaraju', Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Lozinke se ne podudaraju", E_USER_ERROR);
    }

    //validate password with regex
    if (!preg_match("/((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W]).{8,64})$/", $password)) {

        Dnevnik::upisiUDnevnik($con, 'Lozinka nije ispravna', Dnevnik::TrenutnoVrijeme($con), 8);

        trigger_error("Lozinka mora imati najmanje 8 znakova, jedno veliko slovo, jedan broj i jedan specijalni znak", E_USER_ERROR);
    } else {
        $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
    }

    $token = bin2hex(openssl_random_pseudo_bytes(20));

    //token expiry time set to 10 hours
    $currentDateTime = date('Y-m-d H:i:s');

    $pomak = date('Y-m-d H:i:s', strtotime($currentDateTime . VirtualnoVrijeme::procitajVrijeme($con) .'hours'));  

    // Add 10 hours to the current date and time
    $istekAktivacije = date('Y-m-d H:i:s', strtotime($pomak . ' +10 hours'));

    $sql = "INSERT INTO korisnik (Ime, Prezime, KorisnickoIme, Email, Password, Lozinka_Upis, DatumRegistracije, Token, IstekTokena, UlogaKorisnikaID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    //generate prepared statement
    $stmt = mysqli_prepare($con, $sql);
    //bind parameters
    $ulogaKorisnikaID = 1;
    mysqli_stmt_bind_param($stmt, "sssssssssi", $ime, $prezime, $userName, $email, $passwordHashed, $password, $pomak, $token, $istekAktivacije, $ulogaKorisnikaID);
    mysqli_stmt_execute($stmt); //execute query

    //insert data into database
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        Dnevnik::upisiUDnevnik($con, 'Uspješna registracija', Dnevnik::TrenutnoVrijeme($con), 9);
        slanjeMaila($userName, $email, $token, $con);
        http_response_code(201);
    } else {
        mysqli_stmt_close($stmt);
        Dnevnik::upisiUDnevnik($con, 'Neuspješna registracija', Dnevnik::TrenutnoVrijeme($con), 8);
        http_response_code(422);
    }
    mysqli_close($con);
}


function slanjeMaila($userName, $email, $token, $con) {
    $subject = "Registracija";

    $query_string = http_build_query(array(
        'userName' => $userName,
        'token' => $token
    ));

    $message = "Uspješno ste se registrirali na stranicu! Molimo potvrdite svoj račun klikom na link: http://barka.foi.hr/WebDiP/2022_projekti/WebDiP2022x057/aktivacija?$query_string";
    $headers = array(
        'From' => 'mznidarec@foi.hr' . '\r\n',
        'MIME-Version' => '1.0' . '\r\n',
        'Content-Type' => 'text/plain; charset=UTF-8' . '\r\n',
    );
    //send email
    if (mail($email, $subject, $message, $headers))
    {
        Dnevnik::upisiUDnevnik($con, 'Uspješno slanje maila', Dnevnik::TrenutnoVrijeme($con), 9);
        echo json_encode(['data' => "Uspješno ste se registrirali na stranicu!"]);
    }
    else
    {
        Dnevnik::upisiUDnevnik($con, 'Neuspješno slanje maila', Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Greška kod slanja maila, molimo javite se administratoru radi aktivacije", E_USER_ERROR);
    };
}

function validate_ime($result, $con)
{
    Dnevnik::upisiUDnevnik($con, 'Pokretanje validacije imena', Dnevnik::TrenutnoVrijeme($con), 7);

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

function validate_prezime($result, $con)
{
    Dnevnik::upisiUDnevnik($con, 'Pokretanje validacije prezimena', Dnevnik::TrenutnoVrijeme($con), 7);

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

function validate_userName($result, $con)
{
    Dnevnik::upisiUDnevnik($con, 'Pokretanje validacije korisničkog imena', Dnevnik::TrenutnoVrijeme($con), 7);

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
function validate_email($result, $con)
{
    Dnevnik::upisiUDnevnik($con, 'Pokretanje validacije emaila', Dnevnik::TrenutnoVrijeme($con), 7);
    if (isset($result->data->email) && !empty($result->data->email)) {
        $email = trim($result->data->email);
        if (!filter_var($email, FILTER_SANITIZE_EMAIL)) //274 is FILTER_VALIDATE_EMAIL
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

    Dnevnik::upisiUDnevnik($con, 'Pokretanje provjere korisnika', Dnevnik::TrenutnoVrijeme($con), 7);

    $sql = "SELECT * FROM korisnik WHERE KorisnickoIme = ?"; //sql query
    $stmt = mysqli_prepare($con, $sql); //prepare statement
    mysqli_stmt_bind_param($stmt, "s", $userName); //bind parameters
    mysqli_stmt_execute($stmt); //execute query

    $stmt_rows = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($stmt_rows) > 0) {
        Dnevnik::upisiUDnevnik($con, 'Korisnik već postoji', Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Korisnik već postoji", E_USER_ERROR);
    }

    mysqli_stmt_close($stmt); //close statement
}
