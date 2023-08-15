<?php

require 'connection.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    $datumOd = mysqli_real_escape_string($con, trim($result->data->fromDate));
    $datumDo = mysqli_real_escape_string($con, trim($result->data->toDate));

    $natjecaj = [];
    $conditions = [];
    $params = [];
    $types = '';

    if ($datumOd != '') {
        $conditions[] = "n.VrijemePocetka >= ?";
        $params[] = $datumOd;
        $types .= 's';
    }

    if ($datumDo != '') {
        $conditions[] = "n.VrijemePocetka <= ?";
        $params[] = $datumDo;
        $types .= 's';
    }

    $sql = "SELECT n.ID AS ID, n.Naziv AS Naziv, 
                n.VrijemeKraja AS VrijemeKraja, n.VrijemePocetka AS VrijemePocetka, 
                n.Opis AS Opis, s.ID AS StatusNatjecajaID, s.Naziv AS VrstaStatusa, p.ID AS PoduzeceID, p.Naziv AS NazivPoduzeca,
                pn.KorisnikID AS KorisnikID, pn.Slika AS SlikaKorisnika, k.Ime AS PrijavljeniIme, k.Prezime AS PrijavljeniPrezime
            FROM natjecaj n 
            LEFT JOIN prijavananatjecaj pn ON pn.NatjecajID = n.ID
            LEFT JOIN korisnik k on k.ID = pn.KorisnikID
            LEFT JOIN statusnatjecaja s ON s.ID = n.StatusNatjecajaID 
            LEFT JOIN poduzece p ON p.ID = n.PoduzeceID";

    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $sql .= " ORDER BY s.ID ASC, n.ID ASC";

    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));

    if ($types != '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            if (!isset($natjecaj[$row['ID']])) {
                $natjecaj[$row['ID']] = [
                    'ID' => (int)$row['ID'],
                    'Naziv' => $row['Naziv'],
                    'VrijemeKraja' => $row['VrijemeKraja'],
                    'VrijemePocetka' => $row['VrijemePocetka'],
                    'Opis' => $row['Opis'],
                    'StatusNatjecajaID' => (int)$row['StatusNatjecajaID'],
                    'StatusNatjecajaNaziv' => $row['VrstaStatusa'],
                    'PoduzeceID' => (int)$row['PoduzeceID'],
                    'PoduzeceNaziv' => $row['NazivPoduzeca'],
                    'Prijavljeni' => []
                ];
            }

            if ($row['KorisnikID'] != null) {
                $natjecaj[$row['ID']]['Prijavljeni'][] = [
                    'KorisnikID' => (int)$row['KorisnikID'],
                    'Ime' => $row['PrijavljeniIme'],
                    'Prezime' => $row['PrijavljeniPrezime'],
                    'Slika' => $row['SlikaKorisnika']
                ];
            }
        }

        $natjecaj = array_values($natjecaj);

        echo json_encode(['data' => $natjecaj]);
    } else {
        http_response_code(404);
    }
} else {
    trigger_error("Nemate pristup stranici!", E_USER_ERROR);
}

mysqli_close($con);
