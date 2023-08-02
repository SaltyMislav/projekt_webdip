<?php

require 'connection.php';

$konfiguracija = [];
$sql = "SELECT ID, Pomak, Stranicenje FROM konfiguracija";

if ($result = mysqli_query($con, $sql)) {
    $cr = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $konfiguracija[$cr]['ID'] = $row['ID'];
        $konfiguracija[$cr]['Pomak'] = $row['Pomak'];
        $konfiguracija[$cr]['Stranicenje'] = $row['Stranicenje'];
        $cr++;
    }

    echo json_encode(['data' => $konfiguracija]);
} else {
    http_response_code(404);
}

mysqli_close($con);