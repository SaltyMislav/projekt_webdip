<?php

require_once ("./connection.php");
require_once ("./virtualnoVrijemeClass.php");
require_once ("./dnevnikclass.php");

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $request = json_decode($postData);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje radnizadatakKorisnikSave', Dnevnik::TrenutnoVrijeme($con), 5);

    $id = filter_var($request->data->ID, FILTER_SANITIZE_NUMBER_INT);
    $opis = mysqli_real_escape_string($con, trim($request->data->Opis));
    $datum = mysqli_real_escape_string($con, trim($request->data->Datum));
    $korisnikID = filter_var($request->data->KorisnikID, FILTER_SANITIZE_NUMBER_INT);

    provjeraKorisnikaNaTomZadatku($con, $korisnikID, $id);
    provjeraDatuma($datum, $con);
    provjeraPrijaveNaPosao($con, $datum, $korisnikID);

    $odradeno = 1;
    $opis .= " - Odrađeno";

    $sql = "UPDATE radnizadatak SET Opis = ?, Datum = ?, Odradeno = ? WHERE ID = ? AND KorisnikID = ?";
    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
    mysqli_stmt_bind_param($stmt, "ssiii", $opis, $datum, $odradeno, $id, $korisnikID);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        Dnevnik::upisiUDnevnik($con, 'Uspješno odrađen zadatak', Dnevnik::TrenutnoVrijeme($con), 9);
        echo json_encode(['data' => 'Uspješno odrađen zadatak']);
    } else {
        mysqli_stmt_close($stmt);
        Dnevnik::upisiUDnevnik($con, 'Neuspješno odrađen zadatak', Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Greška kod prijave zadatka", E_USER_ERROR);
    }
}

function provjeraKorisnikaNaTomZadatku($con, $korisnikID, $id) {

    Dnevnik::upisiUDnevnik($con, 'Pokretanje provjere korisnika na tom zadatku', Dnevnik::TrenutnoVrijeme($con), 7);

    $sql = "SELECT * FROM radnizadatak WHERE ID = ? AND KorisnikID = ?";
    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
    mysqli_stmt_bind_param($stmt, "ii", $id, $korisnikID);

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_array($result);
        mysqli_stmt_close($stmt);

        if ($row) {
            Dnevnik::upisiUDnevnik($con, 'Korisnik je zadužen za taj zadatak', Dnevnik::TrenutnoVrijeme($con), 9);
            return;
        } else {
            Dnevnik::upisiUDnevnik($con, 'Korisnik nije zadužen za taj zadatak', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Korisnik nije zadužen za taj zadatak", E_USER_ERROR);
        }
    } else {
        mysqli_stmt_close($stmt);
        Dnevnik::upisiUDnevnik($con, 'Greška kod izvršavanja upita', Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Greška kod izvršavanja upita", E_USER_ERROR);
    }
}

function provjeraDatuma ($datum, $con) {

    Dnevnik::upisiUDnevnik($con, 'Pokretanje provjere datuma', Dnevnik::TrenutnoVrijeme($con), 7);

    $trenutniDatum = date("Y-m-d");
    $trenutniDatum = strtotime($trenutniDatum . VirtualnoVrijeme::procitajVrijeme($con) . 'hours');
    $trenutniDatum = date("Y-m-d", $trenutniDatum);

    if ($datum !== $trenutniDatum) {
        Dnevnik::upisiUDnevnik($con, 'Zadatak se nemože izvršiti u prošlosti', Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Zadatak se nemože izvršiti u prošlosti", E_USER_ERROR);
    }
}

function provjeraPrijaveNaPosao($con, $datum, $korisnikID) {

    Dnevnik::upisiUDnevnik($con, 'Pokretanje provjere prijave na posao', Dnevnik::TrenutnoVrijeme($con), 7);

    $sql = "SELECT * FROM dolascinaposao WHERE DatumVrijemeDolaska LIKE CONCAT(? ,'%') AND KorisnikID = ?";
    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
    mysqli_stmt_bind_param($stmt, "si", $datum, $korisnikID);

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_array($result);
        mysqli_stmt_close($stmt);

        if ($row) {
            Dnevnik::upisiUDnevnik($con, 'Korisnik je prijavljen na posao za taj datum', Dnevnik::TrenutnoVrijeme($con), 9);
            return;
        } else {
            Dnevnik::upisiUDnevnik($con, 'Korisnik nije prijavljen na posao za taj datum', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Korisnik nije prijavljen na posao za taj datum", E_USER_ERROR);
        }
    } else {
        mysqli_stmt_close($stmt);
        Dnevnik::upisiUDnevnik($con, 'Greška kod izvršavanja upita', Dnevnik::TrenutnoVrijeme($con), 8);
        trigger_error("Greška kod izvršavanja upita", E_USER_ERROR);
    }
}