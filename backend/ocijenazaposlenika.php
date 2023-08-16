<?php

require_once ("./connection.php");
require_once ("./dnevnikclass.php");

Dnevnik::upisiUDnevnik($con, 'Pokretanje dohvatiOcijeneZaposlenika', Dnevnik::TrenutnoVrijeme($con), 6);

$ocijene = [];
$sql = "SELECT ID, Ocijena FROM ocijenazaposlenika";

Dnevnik::upisiUDnevnik($con, 'Ocijene zaposlenika', Dnevnik::TrenutnoVrijeme($con), 3);

if ($result = mysqli_query($con, $sql)) {
  $cr = 0;
  while ($row = mysqli_fetch_assoc($result)) {
    $ocijene[$cr]['ID'] = (int)$row['ID'];
    $ocijene[$cr]['Ocijena'] = $row['Ocijena'];
    $cr++;
  }

  Dnevnik::upisiUDnevnik($con, 'Uspješan dohvat ocijena zaposlenika', Dnevnik::TrenutnoVrijeme($con), 9);
  echo json_encode(['data' => $ocijene]);
} else {
  Dnevnik::upisiUDnevnik($con, 'Neuspješan dohvat ocijena zaposlenika', Dnevnik::TrenutnoVrijeme($con), 8);
  http_response_code(404);
}

mysqli_close($con);
