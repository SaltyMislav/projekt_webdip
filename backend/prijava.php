<?php

require_once 'connection.php';
require_once 'virtualnoVrijemeClass.php';
require_once 'dnevnikClass.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje prijava', Dnevnik::TrenutnoVrijeme($con), 5);

    $user = find_user_by_email($result->data->korisnickoIme, $con);

    if ($user === null) {
        Dnevnik::upisiUDnevnik($con, 'Korisnik ne postoji', Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Korisnik ne postoji", E_USER_ERROR);
    }

    if ($user['Active'] == 0) {
        Dnevnik::upisiUDnevnik($con, 'Korisnik nije aktiviran', Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Korisnik nije aktiviran, prijava nije moguća", E_USER_ERROR);
    }

    if ($user['Blokiran'] == 1) {
        Dnevnik::upisiUDnevnik($con, 'Korisnik je blokiran', Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Pogrešna lozinka, korisnik je blokiran, kontaktirajte administratora", E_USER_ERROR);
    }

    if (password_verify($result->data->password, $user['Password'])) {

        Dnevnik::upisiUDnevnik($con, 'Potvrđena lozinka', Dnevnik::TrenutnoVrijeme($con), 9);

        //write login date and time to base
        $currentDateTime = date('Y-m-d H:i:s');

        $pomak = date('Y-m-d H:i:s', strtotime($currentDateTime . VirtualnoVrijeme::procitajVrijeme($con) . 'hours'));

        $sql = "UPDATE korisnik SET DatumZadnjePrijave = ?, NeuspjesnePrijave = 0 WHERE ID = ?";

        Dnevnik::upisiUDnevnik($con, 'Upit za spremanje datuma i vremena prijave', Dnevnik::TrenutnoVrijeme($con), 4);

        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "si", $pomak, $user['ID']);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        session_start();
        $_SESSION['user_ID'] = (int)$user['ID'];
        $_SESSION['uloga'] = (int)$user['UlogaKorisnikaID'];
        $_SESSION['user'] = $user['KorisnickoIme'];
        
        Dnevnik::upisiUDnevnik($con, 'Uspješna prijava', Dnevnik::TrenutnoVrijeme($con), 9);
        echo json_encode(['data' => $_SESSION]);
    } else {

        Dnevnik::upisiUDnevnik($con, 'Pogrešna lozinka', Dnevnik::TrenutnoVrijeme($con), 8);

        if ($user['UlogaKorisnikaID'] == 3) {
            trigger_error("Pogrešna lozinka", E_USER_ERROR);
        }

        $userNeuspjesnePrijave = $user['NeuspjesnePrijave'] + 1;

        if ($user['NeuspjesnePrijave'] == 2) {
            $sql = "UPDATE korisnik SET Blokiran = 1 WHERE ID = ?";

            Dnevnik::upisiUDnevnik($con, 'Upit za blokiranje korisnika', Dnevnik::TrenutnoVrijeme($con), 4);

            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "i", $user['ID']);
            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);

            Dnevnik::upisiUDnevnik($con, 'Korisnik je blokiran', Dnevnik::TrenutnoVrijeme($con), 9);
            trigger_error("Korisnik je blokiran, kontaktirajte administratora", E_USER_ERROR);
        }

        $sql = "UPDATE korisnik SET NeuspjesnePrijave = ? WHERE ID = ?";

        Dnevnik::upisiUDnevnik($con, 'Upit za spremanje broja neuspješnih prijava', Dnevnik::TrenutnoVrijeme($con), 4);

        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $userNeuspjesnePrijave, $user['ID']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        Dnevnik::upisiUDnevnik($con, 'Broj neuspješnih prijava spremljen', Dnevnik::TrenutnoVrijeme($con), 9);

        $brojpreostalihPokusaja = 3 - $userNeuspjesnePrijave;

        if ($brojpreostalihPokusaja != 0){
            Dnevnik::upisiUDnevnik($con, 'Pogrešna lozinka'. $user['ID'] . 'ima pravo na još ' . $brojpreostalihPokusaja . ' pokušaja', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Pogrešna lozinka, imate pravo na još $brojpreostalihPokusaja pokušaja", E_USER_ERROR);
        }

        Dnevnik::upisiUDnevnik($con, 'Pogrešna lozinka'. $user['ID'] . 'korisnik je blokiran', Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Pogrešna lozinka, korisnik je blokiran, kontaktirajte administratora", E_USER_ERROR);
    }

    mysqli_close($con);
}

function find_user_by_email($korisnickoIme, $con) {
    $sql = "SELECT ID, KorisnickoIme, Password, Active, NeuspjesnePrijave, Blokiran, UlogaKorisnikaID FROM korisnik WHERE KorisnickoIme = ?";

    Dnevnik::upisiUDnevnik($con, 'Upit za dohvat korisnika', Dnevnik::TrenutnoVrijeme($con), 3);

    $korisnickoIme = trim($korisnickoIme);
    $korisnickoIme = strip_tags($korisnickoIme);
    $korisnickoIme = htmlspecialchars($korisnickoIme);

    $stmt = mysqli_prepare($con, $sql); //prepare statement
    mysqli_stmt_bind_param($stmt, "s", $korisnickoIme); //bind parameters
    mysqli_stmt_execute($stmt); //execute query

    $result = mysqli_stmt_get_result($stmt); //get the mysqli result
    mysqli_stmt_close($stmt);

    if (mysqli_num_rows($result) == 0) {
        Dnevnik::upisiUDnevnik($con, 'Korisnik ne postoji', Dnevnik::TrenutnoVrijeme($con), 8);
        return null;
    }

    Dnevnik::upisiUDnevnik($con, 'Uspješan upit za dohvat korisnika', Dnevnik::TrenutnoVrijeme($con), 9);

    return mysqli_fetch_assoc($result); //fetch data
}