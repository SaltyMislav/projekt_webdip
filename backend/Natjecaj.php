<?php

require 'connection.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    $datumOd = mysqli_real_escape_string($con, trim($result->data->fromDate));
    $datumDo = mysqli_real_escape_string($con, trim($result->data->toDate));

    $natjecaj = [];

    $sql = "SELECT n.ID AS ID, n.Naziv AS Naziv, n.VrijemeKraja AS VrijemeKraja, n.VrijemePocetka AS VrijemePocetka, n.Opis AS Opis, s.ID AS StatusNatjecajaID, s.Naziv AS VrstaStatusa, p.ID AS PoduzeceID, p.Naziv AS NazivPoduzeca
            FROM natjecaj n 
            LEFT JOIN statusnatjecaja s ON s.ID = n.StatusNatjecajaID 
            LEFT JOIN natjecaj_poduzeca np ON np.NatjecajID = n.ID 
            LEFT JOIN poduzece p ON p.ID = np.PoduzeceID 
            WHERE n.VrijemePocetka >= '" . $datumOd . "' AND n.VrijemeKraja <= '" . $datumDo . "'
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
