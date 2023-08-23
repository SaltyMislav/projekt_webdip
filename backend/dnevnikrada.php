<?php

require_once("./connection.php");
require_once("./dnevnikclass.php");

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje dnevnika rada', Dnevnik::TrenutnoVrijeme($con), 5);

    $vrstaPromjeneID = filter_var($result->data->vrstaPromjeneID, FILTER_SANITIZE_NUMBER_INT);
    $datumPromjene = mysqli_real_escape_string($con, trim($result->data->datumPromjene));

    if (!isset($vrstaPromjeneID) || !isset($datumPromjene)) {
        Dnevnik::upisiUDnevnik($con, 'Nisu uneseni svi podaci', Dnevnik::TrenutnoVrijeme($con), 7);
        trigger_error("Nisu uneseni svi podaci", E_USER_ERROR);
    }

    $dnevnik = [];
    $conditions = [];
    $params = [];
    $types = '';

    if ($vrstaPromjeneID != 0) {
        $conditions[] = 'vrstaPromjeneID = ?';
        $params[] = $vrstaPromjeneID;
        $types .= 'i';
    }

    if ($datumPromjene != '') {
        $conditions[] = 'datumPromjene = ?';
        $params[] = $datumPromjene;
        $types .= 's';
    }

    $sql = "SELECT dr.ID, dr.Detail, dr.DatumPromjene, dr.VrstaPromjeneID, vp.Naziv 
                FROM dnevnikrada dr 
                LEFT JOIN vrstapromjene vp ON dr.VrstaPromjeneID = vp.ID";

    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    
    $sql .= " ORDER BY dr.DatumPromjene DESC";

    Dnevnik::upisiUDnevnik($con, 'SQL upit za dohvat dnevnika rada', Dnevnik::TrenutnoVrijeme($con), 7);

    $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));

    if ($types != ''){
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);

        Dnevnik::upisiUDnevnik($con, 'Uspješno dohvaćanje dnevnika rada', Dnevnik::TrenutnoVrijeme($con), 7);

        $cr = 0;

        while ($row = mysqli_fetch_assoc($result)) {
            $dnevnik[$cr]['ID'] = $row['ID'];
            $dnevnik[$cr]['Detail'] = $row['Detail'];
            $dnevnik[$cr]['DatumPromjene'] = $row['DatumPromjene'];
            $dnevnik[$cr]['VrstaPromjeneID'] = $row['VrstaPromjeneID'];
            $dnevnik[$cr]['Naziv'] = $row['Naziv'];
            $cr++;
        }

        echo json_encode(['data' => $dnevnik]);
    } else {
        Dnevnik::upisiUDnevnik($con, 'Neuspješno dohvaćanje dnevnika rada', Dnevnik::TrenutnoVrijeme($con), 8);
        http_response_code(404);
    }
} else {
    Dnevnik::upisiUDnevnik($con, 'Nisu uneseni svi podaci', Dnevnik::TrenutnoVrijeme($con), 7);
    trigger_error("Nisu uneseni svi podaci", E_USER_ERROR);
}

mysqli_close($con);
