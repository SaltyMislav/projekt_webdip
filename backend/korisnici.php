<?php

require 'connection.php';

$korisnici = [];
$sql = "SELECT k.ID, k.Ime, k.Prezime, k.KorisnickoIme, k.Email, k.NeuspjesnePrijave, k.Active, k.Blokiran, k.UlogaKorisnikaID, u.Naziv AS UlogaKorisnikaNaziv,
        p.ID AS PoduzeceID, p.Naziv AS PoduzeceNaziv
        FROM korisnik k 
        LEFT JOIN ulogakorisnika u ON k.UlogaKorisnikaID = u.ID
        LEFT JOIN moderatorpoduzeca mp ON k.ID = mp.KorisnikID
        LEFT JOIN poduzece p ON p.ID = mp.PoduzeceID";

if($result = mysqli_query($con, $sql)) {
    while($row = mysqli_fetch_assoc($result)){
        if(!isset($korisnici[$row['ID']])){
            $korisnici[$row['ID']] = [
                'ID' => $row['ID'],
                'Ime' => $row['Ime'],
                'Prezime' => $row['Prezime'],
                'KorisnickoIme' => $row['KorisnickoIme'],
                'Email' => $row['Email'],
                'NeuspjesnePrijave' => $row['NeuspjesnePrijave'],
                'Active' => $row['Active'],
                'Blokiran' => $row['Blokiran'],
                'UlogaKorisnikaID' => $row['UlogaKorisnikaID'],
                'UlogaKorisnikaNaziv' => $row['UlogaKorisnikaNaziv'],
                'Poduzece' => []
            ];
        }

        if($row['PoduzeceID'] != null && $row['PoduzeceNaziv'] != null) {
            $korisnici[$row['ID']]['Poduzece'][] = [
                'ID' => $row['PoduzeceID'],
                'Naziv' => $row['PoduzeceNaziv']
            ];
        }
    }
    $korisnici = array_values($korisnici);

    echo json_encode(['data' => $korisnici]);
} else {
    http_response_code(404);
}

mysqli_close($con);