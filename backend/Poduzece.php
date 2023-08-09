<?php

require 'connection.php';

$poduzece = [];
$sql = "SELECT p.ID, p.Naziv, p.Opis, p.RadnoVrijemeOd, p.RadnoVrijemeDo, k.ID AS KorisnikID, k.KorisnickoIme 
        FROM poduzece p
        LEFT JOIN moderatorpoduzeca mp on p.ID = mp.PoduzeceID
        LEFT JOIN korisnik k on k.ID = mp.KorisnikID";

if ($result = mysqli_query($con, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Check if the poduzece already exists in the array
        if (!isset($poduzece[$row['ID']])) {
            // If not, add it with the poduzece details and an empty 'moderatori' array
            $poduzece[$row['ID']] = [
                'ID' => (int)$row['ID'],
                'Naziv' => $row['Naziv'],
                'Opis' => $row['Opis'],
                'RadnoVrijemeOd' => $row['RadnoVrijemeOd'],
                'RadnoVrijemeDo' => $row['RadnoVrijemeDo'],
                'Moderatori' => []
            ];
        }

        // Add the korisnik data into the 'moderatori' subarray of the existing poduzece
        if($row['KorisnikID'] != null && $row['KorisnickoIme'] != null) {
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

mysqli_close($con);
