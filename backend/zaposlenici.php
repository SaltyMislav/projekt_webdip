<?php

require 'connection.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $request = json_decode($postData);

    $prezime = mysqli_real_escape_string($con, trim($request->data));
    $prezime = strtolower($prezime);

    $zaposlenici = [];

    $sql = "SELECT k.ID, k.Ime, k.Prezime, k.PoduzeceID, p.Naziv AS PoduzeceNaziv
            FROM korisnik k 
            INNER JOIN poduzece p ON k.PoduzeceID = p.ID 
            WHERE k.UlogaKorisnikaID = 1
            AND
            LOWER(k.Prezime) LIKE '%" . $prezime . "%'";

    if ($result = mysqli_query($con, $sql)) {
        $cr = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $zaposlenici[$cr]['ID'] = (int)$row['ID'];
            $zaposlenici[$cr]['Ime'] = $row['Ime'];
            $zaposlenici[$cr]['Prezime'] = $row['Prezime'];
            $zaposlenici[$cr]['PoduzeceID'] = (int)$row['PoduzeceID'];
            $zaposlenici[$cr]['PoduzeceNaziv'] = $row['PoduzeceNaziv'];
            $cr++;
        }
        echo json_encode(['data' => $zaposlenici]);
    } else {
        http_response_code(404);
    }
} else {
    $zaposlenici = [];
    $sql = "SELECT k.ID, k.Ime, k.Prezime, k.PoduzeceID, p.Naziv AS PoduzeceNaziv
            FROM korisnik k 
            INNER JOIN poduzece p ON k.PoduzeceID = p.ID 
            WHERE k.UlogaKorisnikaID = 1";

    if ($result = mysqli_query($con, $sql)) {
        $cr = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $zaposlenici[$cr]['ID'] = (int)$row['ID'];
            $zaposlenici[$cr]['Ime'] = $row['Ime'];
            $zaposlenici[$cr]['Prezime'] = $row['Prezime'];
            $zaposlenici[$cr]['PoduzeceID'] = (int)$row['PoduzeceID'];
            $zaposlenici[$cr]['PoduzeceNaziv'] = $row['PoduzeceNaziv'];
            $cr++;
        }
        echo json_encode(['data' => $zaposlenici]);
    } else {
        http_response_code(404);
    }
}

mysqli_close($con);
