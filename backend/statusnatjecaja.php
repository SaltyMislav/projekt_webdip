<?php

require 'connection.php';

$statusiNatjecaja = [];

$sql = "SELECT ID, Naziv FROM statusnatjecaja";

if ($result = mysqli_query($con, $sql)) {
    $cr = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $statusiNatjecaja[$cr]['ID'] = (int)$row['ID'];
        $statusiNatjecaja[$cr]['Naziv'] = $row['Naziv'];
        $cr++;
    }
    echo json_encode(['data' => $statusiNatjecaja]);
} else {
    http_response_code(404);
}

mysqli_close($con);