<?php

require_once ("./connection.php");
require_once ("./dnevnikclass.php");

$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    $result = json_decode($postdata);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje radni zadatak', Dnevnik::TrenutnoVrijeme($con), 5);

    $naziv = mysqli_real_escape_string($con, trim($result->data->Naziv));
    $opis = mysqli_real_escape_string($con, trim($result->data->Opis));
    $ulogaID = filter_var($result->data->UlogaID, FILTER_VALIDATE_INT);
    $korisnikID = filter_var($result->data->KorisnikID, FILTER_VALIDATE_INT);

    if (!isset($korisnikID) || !isset($ulogaID) || !isset($naziv) || !isset($opis)) {
        Dnevnik::upisiUDnevnik($con, 'Nisu svi podaci uneseni', Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Nisu proslijeđeni svi parametri", E_USER_ERROR);
    }

    $radnizadaci = [];
    $conditions = [];
    $params = [];
    $types = '';

    if ($naziv != '') {
        $conditions[] = 'LOWER(rz.Naziv) LIKE ?';
        $params[] = '%' . $naziv . '%';
        $types .= 's';
    }

    if ($opis != '') {
        $conditions[] = 'LOWER(rz.Opis) LIKE ?';
        $params[] = '%' . $opis . '%';
        $types .= 's';
    }

    if ($ulogaID == 2) {
        $sql = "SELECT rz.ID, rz.Naziv, rz.Opis, rz.Datum, rz.Odradeno, rz.OcijenaZaposlenikaID, 
                rz.KorisnikID, rz.PoduzeceID, oz.Ocijena, CONCAT(k.Ime, ' ', k.Prezime) as ImePrezime, p.Naziv AS PoduzeceNaziv 
                FROM radnizadatak rz 
                LEFT JOIN ocijenazaposlenika oz ON rz.OcijenaZaposlenikaID = oz.ID 
                LEFT JOIN korisnik k ON rz.KorisnikID = k.ID
                LEFT JOIN moderatorpoduzeca mp ON rz.PoduzeceID = mp.PoduzeceID 
                LEFT JOIN poduzece p ON mp.PoduzeceID = p.ID";
        if ($korisnikID != '') {
            $conditions[] = 'mp.KorisnikID = ?';
            $params[] = $korisnikID;
            $types .= 'i';
        }
    } else if ($ulogaID == 3) {
        $sql = "SELECT rz.ID, rz.Naziv, rz.Opis, rz.Datum, rz.Odradeno, rz.OcijenaZaposlenikaID, 
                rz.KorisnikID, rz.PoduzeceID, oz.Ocijena, CONCAT(k.Ime, ' ', k.Prezime) as ImePrezime, p.Naziv AS PoduzeceNaziv 
                FROM radnizadatak rz 
                LEFT JOIN ocijenazaposlenika oz ON rz.OcijenaZaposlenikaID = oz.ID 
                LEFT JOIN korisnik k ON rz.KorisnikID = k.ID
                LEFT JOIN poduzece p ON rz.PoduzeceID = p.ID";
    } else if ($ulogaID == 1) {
        $sql = "SELECT rz.ID, rz.Naziv, rz.Opis, rz.Datum, rz.Odradeno, rz.OcijenaZaposlenikaID, 
                rz.KorisnikID, rz.PoduzeceID, oz.Ocijena, CONCAT(k.Ime, ' ', k.Prezime) as ImePrezime, p.Naziv AS PoduzeceNaziv 
                FROM radnizadatak rz 
                LEFT JOIN ocijenazaposlenika oz ON rz.OcijenaZaposlenikaID = oz.ID 
                LEFT JOIN korisnik k ON rz.KorisnikID = k.ID
                LEFT JOIN poduzece p ON rz.PoduzeceID = p.ID";
        if ($korisnikID != '') {
            $conditions[] = 'rz.KorisnikID = ?';
            $params[] = $korisnikID;
            $types .= 'i';
        }
    }

    if (count($conditions) > 0) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    Dnevnik::upisiUDnevnik($con, 'Upit radni zadatak', Dnevnik::TrenutnoVrijeme($con), 3);

    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));

    if ($types != '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);

        Dnevnik::upisiUDnevnik($con, 'Uspješan upit radni zadatak', Dnevnik::TrenutnoVrijeme($con), 9);

        $cr = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $radnizadaci[$cr]['ID'] = (int)$row['ID'];
            $radnizadaci[$cr]['Naziv'] = $row['Naziv'];
            $radnizadaci[$cr]['Opis'] = $row['Opis'];
            $radnizadaci[$cr]['Datum'] = $row['Datum'];
            $radnizadaci[$cr]['Odradeno'] = (boolean)$row['Odradeno'];
            $radnizadaci[$cr]['OcijenaZaposlenikaID'] = (int)$row['OcijenaZaposlenikaID'];
            $radnizadaci[$cr]['KorisnikID'] = (int)$row['KorisnikID'];
            $radnizadaci[$cr]['PoduzeceID'] = (int)$row['PoduzeceID'];
            $radnizadaci[$cr]['Ocijena'] = (int)$row['Ocijena'];
            $radnizadaci[$cr]['ImePrezime'] = $row['ImePrezime'];
            $radnizadaci[$cr]['PoduzeceNaziv'] = $row['PoduzeceNaziv'];
            $cr++;
        }

        Dnevnik::upisiUDnevnik($con, 'Uspješno dohvaćanje radni zadatak', Dnevnik::TrenutnoVrijeme($con), 9);
        echo json_encode(['data' => $radnizadaci]);
    } else {
        Dnevnik::upisiUDnevnik($con, 'Neuspješan upit radni zadatak', Dnevnik::TrenutnoVrijeme($con), 8);
        http_response_code(404);
    }
} else {
    Dnevnik::upisiUDnevnik($con, 'Nemate pristup stranici', Dnevnik::TrenutnoVrijeme($con), 8);
    trigger_error("Nemate pristup stranici", E_USER_ERROR);
}

mysqli_close($con);
