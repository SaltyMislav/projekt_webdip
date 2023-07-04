<?php

require 'connection.php';

$dolasciNaPosao = [];
$sql = "SELECT dnp.ID, dnp.Detail, dnp.DatumPromjene, dnp.KorisnikID, k.Ime, k.Prezime FROM dolascinaposao dnp LEFT JOIN korisnik k ON dnp.VrstaPromjeneID = k.ID";

if($result = mysqli_query($con, $sql))
{
    $cr = 0;
    while ($row = mysqli_fetch_assoc($result))
    {
        $dolasciNaPosao[$cr]['ID'] = $row['ID'];
        $dolasciNaPosao[$cr]['Detail'] = $row['Detail'];
        $dolasciNaPosao[$cr]['DatumPromjene'] = $row['DatumPromjene'];
        $dolasciNaPosao[$cr]['KorisnikID'] = $row['KorisnikID'];
        $dolasciNaPosao[$cr]['Ime'] = $row['Ime'];
        $dolasciNaPosao[$cr]['Prezime'] = $row['Prezime'];
        $cr++;
    }

    echo json_encode(['data' => $dolasciNaPosao]);
}
else
{
    http_response_code(404);
}

mysqli_close($con);