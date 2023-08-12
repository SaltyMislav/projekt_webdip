<?php

require 'connection.php';

$postData = file_get_contents("php://input");
$natjecaji = [];

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    $userID = filter_var($result->data->KorisinkID, FILTER_SANITIZE_NUMBER_INT);
    $ulogaID = filter_var($result->data->UlogaID, FILTER_SANITIZE_NUMBER_INT);
    $nazivNatjecaja = mysqli_real_escape_string($con, trim($result->data->NazivNatjecaja));
    $vrijemePocetka = mysqli_real_escape_string($con, trim($result->data->VrijemePocetka));

    if (empty($korisnikID)) {
        trigger_error("Potrebno je dodati podatak koji moderator je tražio podatake", E_USER_ERROR);
    }

    $conditions = [];
    $params = [];
    $types = '';

    if ($nazivNatjecaja != '') {
        $conditions[] = "n.Naziv LIKE ?";
        $params[] = '%' . $nazivNatjecaja . '%';
        $types .= 's';
    }

    if ($vrijemePocetka != '') {
        $conditions[] = "n.VrijemePocetka >= ?";
        $params[] = $vrijemePocetka;
        $types .= 's';
    }

    if ($ulogaID == 2) {
        $sql = "SELECT n.ID AS ID, n.Naziv AS Naziv, n.VrijemeKraja AS VrijemeKraja, n.VrijemePocetka AS VrijemePocetka, n.Opis AS Opis, s.ID AS StatusNatjecajaID, s.Naziv AS VrstaStatusa, p.ID AS PoduzeceID, p.Naziv AS NazivPoduzeca
                FROM natjecaj n 
                INNER JOIN statusnatjecaja s ON s.ID = n.StatusNatjecajaID 
                INNER JOIN natjecaj_poduzeca np ON np.NatjecajID = n.ID 
                INNER JOIN poduzece p ON p.ID = np.PoduzeceID
                INNER JOIN moderatorpoduzeca mp on mp.PoduzeceID = np.PoduzeceID";
        if ($korisnikID != '') {
            $conditions[] = "mp.KorisnikID = ?";
            $params[] = $korisnikID;
            $types .= 'i';
        }
    } else if ($ulogaID == 3) {
        $sql = "SELECT n.ID AS ID, n.Naziv AS Naziv, n.VrijemeKraja AS VrijemeKraja, n.VrijemePocetka AS VrijemePocetka, n.Opis AS Opis, s.ID AS StatusNatjecajaID, s.Naziv AS VrstaStatusa, p.ID AS PoduzeceID, p.Naziv AS NazivPoduzeca
                FROM natjecaj n 
                INNER JOIN statusnatjecaja s ON s.ID = n.StatusNatjecajaID 
                INNER JOIN natjecaj_poduzeca np ON np.NatjecajID = n.ID 
                INNER JOIN poduzece p ON p.ID = np.PoduzeceID";
    }

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
            $natjecaji[] = [
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

        echo json_encode(['data' => $natjecaji]);
    } else {
        http_response_code(404);
    }
}

mysqli_close($con);
