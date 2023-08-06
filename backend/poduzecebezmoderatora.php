<?php

require 'connection.php';

$poduzece = [];

$sql = "SELECT p.ID, p.Naziv from poduzece p";

if ($result = mysqli_query($con, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $poduzece[] = [
            'ID' => $row['ID'],
            'Naziv' => $row['Naziv']
        ];
    }
    echo json_encode(['data' => $poduzece]);
} else {
    http_response_code(404);
}

mysqli_close($con);