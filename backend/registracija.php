<?php

require 'connection.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData))
{
    $result = json_decode($postData);

    if (validate_ime($result) && validate_prezime($result) && validate_userName($result) || validate_email($result))
    {  
        echo json_encode(['data' => 'Success']);
    }
}


function validate_ime($result) {
    if (isset($result->data->ime) && !empty($result->data->ime))
    {
        $ime = trim($result->data->ime);
        $ime = filter_var($ime, 513);

        if (strlen($ime) < 3 || strlen($ime) > 20)
        {
            trigger_error("Ime nema između 3 i 20 znakova", E_USER_ERROR);
        }
        else
        {
            return $ime;
        }
    }
    else
    {
        trigger_error("Ime nema između 3 i 20 znakova", E_USER_ERROR);
    }
}

function validate_prezime($result) {
    if (isset($result->data->prezime) && !empty($result->data->prezime))
    {
        $prezime = trim($result->data->prezime);
        $prezime = filter_var($prezime, 513);
        if (strlen($prezime) < 3 || strlen($prezime) > 20)
        {
            trigger_error("Prezime nema između 3 i 20 znakova", E_USER_ERROR);
        }
        else
        {
            return $prezime;
        }
    }
    else
    {
        trigger_error("Prezime nema između 3 i 20 znakova", E_USER_ERROR);
    }
}

function validate_userName($result) {
    if (isset($result->data->userName) && !empty($result->data->userName))
    {
        $userName = trim($result->data->userName);
        $userName = filter_var($userName, 513);
        if (strlen($userName) < 3 || strlen($userName) > 20)
        {
            trigger_error("Korisničko ime nema između 3 i 20 znakova", E_USER_ERROR);
        }
        else
        {
            return $userName;
        }
    }
    else
    {
        trigger_error("Korisničko ime nema između 3 i 20 znakova", E_USER_ERROR);
    }
}

//function to validate email
function validate_email($result) {
    if (isset($result->data->email) && !empty($result->data->email))
    {
        $email = trim($result->data->email);
        $email = filter_var($email, 513);
        if (!filter_var($email, 274))
        {
            trigger_error("Email nije ispravan", E_USER_ERROR);
        }
        else
        {
            return $email;
        }
    }
    else
    {
        trigger_error("Email nije ispravan", E_USER_ERROR);
    }
}
