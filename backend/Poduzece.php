<?php

require 'connection.php';

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData, true);

    $naziv = mysqli_real_escape_string($con, trim($result['data']['Naziv']));
    $opis = mysqli_real_escape_string($con, trim($result['data']['Opis']));

    $poduzece = [];

    $sql = "SELECT p.ID, p.Naziv, p.Opis, p.RadnoVrijemeOd, p.RadnoVrijemeDo, k.ID as KorisnikID, k.KorisnickoIme 
            FROM poduzece p
            LEFT JOIN moderatorpoduzeca mp on p.ID = mp.PoduzeceID
            LEFT JOIN korisnik k on k.ID = mp.KorisnikID
            WHERE LOWER(p.Naziv) LIKE '%" . $naziv . "%'
            AND LOWER(p.Opis) LIKE '%" . $opis . "%'";

    if ($result = mysqli_query($con, $sql)) {
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
    $poduzece = [];
    $sql = "SELECT p.ID, p.Naziv, p.Opis, p.RadnoVrijemeOd, p.RadnoVrijemeDo, k.ID AS KorisnikID, k.KorisnickoIme 
            FROM poduzece p
            LEFT JOIN moderatorpoduzeca mp on p.ID = mp.PoduzeceID
            LEFT JOIN korisnik k on k.ID = mp.KorisnikID";

    if ($result = mysqli_query($con, $sql)) {
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
}
mysqli_close($con);
