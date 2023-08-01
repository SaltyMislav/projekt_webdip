<?php

require 'connection.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $request = json_decode($postData);

    $korisnickoIme = $request->data->korisnickoIme;

    if (empty($korisnickoIme)) {
        trigger_error("Molimo unesite korisničko ime", E_USER_ERROR);
    }

    mysqli_real_escape_string($con, trim($korisnickoIme));

    $newPassword = random_password();
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $email = get_email($con, $korisnickoIme);

    $sql = "UPDATE korisnik SET Password = ?, Lozinka_upis = ? WHERE KorisnickoIme = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $hashedPassword, $newPassword, $korisnickoIme);
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        slanjeMaila($email, $newPassword);
        http_response_code(200);
    } else {
        http_response_code(404);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
}

function slanjeMaila($email, $newPassword)
{
    $subject = "Promjena lozinke";
    $message = "Vaša nova lozinka je: " . $newPassword;
    $headers = array(
        'From' => 'mznidarec@foi.hr',
        'Content-Type' => 'text/plain; charset=UTF-8' . '\r\n',
    );

    if (mail($email, $subject, $message, $headers)) {
        echo json_encode(['data' => "Success"]);
    } else {
        trigger_error("Greška kod slanja maila, molimo javite se administratoru", E_USER_ERROR);
    };
}

function get_email($con, $korisnickoIme)
{
    $sql = "SELECT Email FROM korisnik WHERE KorisnickoIme = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $korisnickoIme);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $row['Email'];
}


function random_password()
{
    $random_characters = 2;

    $lower_case = implode(range('a', 'z'));
    $upper_case = implode(range('A', 'Z'));
    $numbers = implode(range(0, 9));
    $symbols = '!@#$%^&*()_+~}{[]\:;?><,./-=';

    $lower_case = str_shuffle($lower_case);
    $upper_case = str_shuffle($upper_case);
    $numbers = str_shuffle($numbers);
    $symbols = str_shuffle($symbols);

    $random_password = substr($lower_case, 0, $random_characters);
    $random_password .= substr($upper_case, 0, $random_characters);
    $random_password .= substr($numbers, 0, $random_characters);
    $random_password .= substr($symbols, 0, $random_characters);

    return str_shuffle($random_password);
}
