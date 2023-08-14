<?php

require 'connection.php';

$konfiguracija = [];
$sql = "SELECT ID, Pomak, Stranicenje, ImgSize FROM konfiguracija";

if ($result = mysqli_query($con, $sql)) {
    $cr = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $konfiguracija[$cr]['ID'] = (int)$row['ID'];
        $konfiguracija[$cr]['Pomak'] = (int)$row['Pomak'];
        $konfiguracija[$cr]['Stranicenje'] = (int)$row['Stranicenje'];
        $konfiguracija[$cr]['ImgSize'] = (int)$row['ImgSize'];
        $cr++;
    }

    echo json_encode(['data' => $konfiguracija]);
} else {
    http_response_code(404);
}

mysqli_close($con);