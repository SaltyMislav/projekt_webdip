<?php

require 'connection.php';

if (isset($_GET['token']) && isset($_GET['username'])) {
    $token = $_GET['token'];
    $username = $_GET['username'];

    if (!checkifhexadecimal($token)) {
        unset($token);
        trigger_error("Token nije validan", E_USER_ERROR);
    }

    //generate prepared statement
    $sql = "SELECT ID, Token, KorisnickoIme, IstekTokena < NOW() AS Istekao FROM korisnik WHERE KorisnickoIme = ? AND Token = ? AND Active = 0";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $username, $token);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $tokenHash = password_hash($token, PASSWORD_DEFAULT);
        if ((int)$user['Istekao'] === 1) {
            deleteToken($user['ID'], $con);
            trigger_error("Token je istekao, molimo ponovo se registrirajte", E_USER_ERROR);
        }

        if (password_verify($user['Token'], $tokenHash)) {
            $sql = "UPDATE korisnik SET Active = 1 WHERE ID = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $user['ID']);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                http_response_code(201);
            } else {
                trigger_error("Došlo je do pogreške, molim pokušajte ponovo", E_USER_ERROR);
            }
        }
    } else {
        trigger_error("Došlo je do pogreške, molim pokušajte ponovo", E_USER_ERROR);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
}

function checkifhexadecimal($token)
{
    if (ctype_xdigit($token)) {
        return true;
    } else {
        return false;
    }
}

function deleteToken($ID, $con)
{
    $sql = "DELETE FROM korisnik WHERE ID = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $ID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}