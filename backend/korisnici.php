<?php

require 'connection.php';

$korisnici = [];
$sql = "SELECT k.ID, k.Ime, k.Prezime, k.KorisnickoIme, k.Email, k.NeuspjesnePrijave, k.Active, k.Blokiran, k.UlogaKorisnikaID, u.Naziv AS UlogaKorisnikaNaziv FROM korisnik k LEFT JOIN ulogakorisnika u ON k.UlogaKorisnikaID = u.ID";

if($result = mysqli_query($con, $sql)) {
    $cr = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $korisnici[$cr]['ID'] = $row['ID'];
        $korisnici[$cr]['Ime'] = $row['Ime'];
        $korisnici[$cr]['Prezime'] = $row['Prezime'];
        $korisnici[$cr]['KorisnickoIme'] = $row['KorisnickoIme'];
        $korisnici[$cr]['Email'] = $row['Email'];
        $korisnici[$cr]['NeuspjesnePrijave'] = $row['NeuspjesnePrijave'];
        $korisnici[$cr]['Active'] = $row['Active'];
        $korisnici[$cr]['Blokiran'] = $row['Blokiran'];
        $korisnici[$cr]['UlogaKorisnikaID'] = $row['UlogaKorisnikaID'];
        $korisnici[$cr]['UlogaKorisnikaNaziv'] = $row['UlogaKorisnikaNaziv'];
        $cr++;
    }

    echo json_encode(['data' => $korisnici]);
} else {
    http_response_code(404);
}

mysqli_close($con);