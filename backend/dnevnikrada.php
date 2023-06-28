<?php

require 'connection.php';

$dnevnikrada = [];
$sql = "SELECT dr.ID, dr.Detail, dr.DatumPromjene, vp.Naziv FROM dnevnikrada dr LEFT JOIN vrstapromjene vp ON dr.VrstaPromjeneID = vp.ID";

if ($result = mysqli_query($con, $sql))
{
    $cr = 0;
    while ($row = mysqli_fetch_assoc($result))
    {
        $dnevnikrada[$cr]['ID'] = $row['ID'];
        $dnevnikrada[$cr]['Detail'] = $row['Detail'];
        $dnevnikrada[$cr]['DatumPromjene'] = $row['DatumPromjene'];
        $dnevnikrada[$cr]['Naziv'] = $row['Naziv'];
        $cr++;
    }
    
    echo json_encode(['data' => $dnevnikrada]);
}
else
{
    http_response_code(404);
}

mysqli_close($con);