<?php

require 'connection.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData, true);

    $naziv = mysqli_real_escape_string($con, trim($result['data']['Naziv']));
    $opis = mysqli_real_escape_string($con, trim($result['data']['Opis']));

    $poduzece = [];
    $conditions = [];
    $params = [];
    $types = '';

    if ($naziv != '') {
        $conditions[] = "LOWER(p.Naziv) LIKE ?";
        $params[] = '%' . $naziv . '%';
        $types .= 's';
    }

    if ($opis != '') {
        $conditions[] = "LOWER(p.Opis) LIKE ?";
        $params[] = '%' . $opis . '%';
        $types .= 's';
    }

    $sql = "SELECT p.ID, p.Naziv, p.Opis, p.RadnoVrijemeOd, p.RadnoVrijemeDo, k.ID as KorisnikID, k.KorisnickoIme 
            FROM poduzece p
            LEFT JOIN moderatorpoduzeca mp on p.ID = mp.PoduzeceID
            LEFT JOIN korisnik k on k.ID = mp.KorisnikID";

    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $sql .= " ORDER BY p.ID ASC";

    $stmt = mysqli_prepare($con, $sql);
    if ($types != '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            if (!isset($poduzece[$row['ID']])) {
                $poduzece[$row['ID']] = [
                    'ID' => (int)$row['ID'],
                    'Naziv' => $row['Naziv'],
                    'Opis' => $row['Opis'],
                    'RadnoVrijemeOd' => $row['RadnoVrijemeOd'],
                    'RadnoVrijemeDo' => $row['RadnoVrijemeDo'],
                    'Moderatori' => []
                ];
            }

            if ($row['KorisnikID'] != null && $row['KorisnickoIme'] != null) {
                $poduzece[$row['ID']]['Moderatori'][] = [
                    'ID' => (int)$row['KorisnikID'],
                    'KorisnickoIme' => $row['KorisnickoIme']
                ];
            }
        }
        $poduzece = array_values($poduzece);

        echo json_encode(['data' => $poduzece]);
    } else {
        http_response_code(404);
    }
} else {
    trigger_error("Nemate pristup stranici!", E_USER_ERROR);
}
mysqli_close($con);
