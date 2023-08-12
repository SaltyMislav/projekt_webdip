<?php

require 'connection.php';

//todo update da se koriste prededefinirane vrijednosti

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

    $sql = "SELECT n.ID AS ID, n.Naziv AS Naziv, n.VrijemeKraja AS VrijemeKraja, n.VrijemePocetka AS VrijemePocetka, n.Opis AS Opis, s.ID AS StatusNatjecajaID, s.Naziv AS VrstaStatusa, p.ID AS PoduzeceID, p.Naziv AS NazivPoduzeca
            FROM natjecaj n 
            LEFT JOIN statusnatjecaja s ON s.ID = n.StatusNatjecajaID 
            LEFT JOIN natjecaj_poduzeca np ON np.NatjecajID = n.ID 
            LEFT JOIN poduzece p ON p.ID = np.PoduzeceID";

    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $sql .= " ORDER BY s.ID ASC, n.ID ASC";

    $stmt = mysqli_prepare($con, $sql);

    if ($types != '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $natjecaj[] = [
                'ID' => (int)$row['ID'],
                'Naziv' => $row['Naziv'],
                'VrijemeKraja' => $row['VrijemeKraja'],
                'VrijemePocetka' => $row['VrijemePocetka'],
                'Opis' => $row['Opis'],
                'StatusNatjecajaID' => (int)$row['StatusNatjecajaID'],
                'StatusNatjecajaNaziv' => $row['VrstaStatusa'],
                'PoduzeceID' => (int)$row['PoduzeceID'],
                'PoduzeceNaziv' => $row['NazivPoduzeca']
            ];
        }

        echo json_encode(['data' => $natjecaj]);
    } else {
        http_response_code(404);
    }
} else {
    $natjecaj = [];
    $sql = "SELECT n.ID AS ID, n.Naziv AS Naziv, n.VrijemeKraja AS VrijemeKraja, n.VrijemePocetka AS VrijemePocetka, n.Opis AS Opis, s.ID AS StatusNatjecajaID, s.Naziv AS VrstaStatusa, p.ID AS PoduzeceID, p.Naziv AS NazivPoduzeca
            FROM natjecaj n 
            LEFT JOIN statusnatjecaja s ON s.ID = n.StatusNatjecajaID 
            LEFT JOIN natjecaj_poduzeca np ON np.NatjecajID = n.ID 
            LEFT JOIN poduzece p ON p.ID = np.PoduzeceID 
            ORDER BY s.ID ASC, n.ID ASC";

    if ($result = mysqli_query($con, $sql)) {
        $cr = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $natjecaj[$cr]['ID'] = (int)$row['ID'];
            $natjecaj[$cr]['Naziv'] = $row['Naziv'];
            $natjecaj[$cr]['VrijemeKraja'] = $row['VrijemeKraja'];
            $natjecaj[$cr]['VrijemePocetka'] = $row['VrijemePocetka'];
            $natjecaj[$cr]['Opis'] = $row['Opis'];
            $natjecaj[$cr]['StatusNatjecajaID'] = (int)$row['StatusNatjecajaID'];
            $natjecaj[$cr]['StatusNatjecajaNaziv'] = $row['VrstaStatusa'];
            $natjecaj[$cr]['PoduzeceID'] = (int)$row['PoduzeceID'];
            $natjecaj[$cr]['PoduzeceNaziv'] = $row['NazivPoduzeca'];
            $cr++;
        }

        echo json_encode(['data' => $natjecaj]);
    } else {
        http_response_code(404);
    }
}

mysqli_close($con);
