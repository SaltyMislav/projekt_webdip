<?php

require_once ("./connection.php");
require_once ("./dnevnikclass.php");

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData, true);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje provjera korisnika', Dnevnik::TrenutnoVrijeme($con), 5);

    $username = mysqli_real_escape_string($con, $result['params']['updates'][0]['value']);

    $user = find_user_by_username($username, $con);

    if ($user === null) {
        echo json_encode(['exists' => 'false']);
    } else {
        echo json_encode(['exists' => 'true']);
    }
}


function find_user_by_username($username, $con){

    Dnevnik::upisiUDnevnik($con, 'Pokretanje funkcije za pronalazak korisnika', Dnevnik::TrenutnoVrijeme($con), 5);

    $sql = "SELECT * FROM korisnik WHERE KorisnickoIme = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if ($user) {
        Dnevnik::upisiUDnevnik($con, 'Korisnik pronaden', Dnevnik::TrenutnoVrijeme($con), 9);
        return $user;
    } else {
        Dnevnik::upisiUDnevnik($con, 'Korisnik nije pronaden', Dnevnik::TrenutnoVrijeme($con), 8);
        return null;
    }
}