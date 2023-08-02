<?php

require 'connection.php';

$postData = file_get_contents("php://input");

if(isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    $stranicenje = $result->data->stranicenje;

    mysqli_real_escape_string($con, (int)$stranicenje);

    $sql = "UPDATE konfiguracija SET Stranicenje = ? WHERE ID = 1";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $stranicenje);

    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        echo json_encode(['data' => 'Success']);
    } else {
        trigger_error("Nije moguće postaviti straničenje", E_USER_ERROR);
    }
}