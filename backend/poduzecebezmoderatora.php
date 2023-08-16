<?php

require_once ("./connection.php");
require_once ("./dnevnikclass.php");

Dnevnik::upisiUDnevnik($con, 'Pokretanje poduzecebezmoderatora', Dnevnik::TrenutnoVrijeme($con), 6);

$poduzece = [];

$sql = "SELECT p.ID, p.Naziv from poduzece p";

Dnevnik::upisiUDnevnik($con, 'Poduzece bez moderatora', Dnevnik::TrenutnoVrijeme($con), 3);

if ($result = mysqli_query($con, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $poduzece[] = [
            'ID' => (int)$row['ID'],
            'Naziv' => $row['Naziv']
        ];
    }

    Dnevnik::upisiUDnevnik($con, 'Uspješan dohvat poduzeća bez moderatora', Dnevnik::TrenutnoVrijeme($con), 9);
    echo json_encode(['data' => $poduzece]);
} else {
    Dnevnik::upisiUDnevnik($con, 'Neuspješan dohvat poduzeća bez moderatora', Dnevnik::TrenutnoVrijeme($con), 8);
    http_response_code(404);
}

mysqli_close($con);