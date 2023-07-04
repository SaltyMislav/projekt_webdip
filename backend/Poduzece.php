<?php

require 'connection.php';

$poduzece = [];
$sql = "SELECT p.ID, p.Naziv, p.Opis, p.RadnoVrijemeOd, p.RadnoVrijemeDo FROM poduzece p";

if($result = mysqli_query($con, $sql))
{
    $cr = 0;
    while ($row = mysqli_fetch_assoc($result))
    {
        $poduzece[$cr]['ID'] = $row['ID'];
        $poduzece[$cr]['Naziv'] = $row['Naziv'];
        $poduzece[$cr]['Opis'] = $row['Opis'];
        $poduzece[$cr]['RadnoVrijemeOd'] = $row['RadnoVrijemeOd'];
        $poduzece[$cr]['RadnoVrijemeDo'] = $row['RadnoVrijemeDo'];
        $cr++;
    }

    echo json_encode(['data' => $poduzece]);
}
else 
{
    http_response_code(404);
}

mysqli_close($con);