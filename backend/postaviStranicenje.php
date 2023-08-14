<?php

require 'connection.php';

$postData = file_get_contents("php://input");

if(isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    $stranicenje = filter_var($result->data->Stranicenje, FILTER_SANITIZE_NUMBER_INT);
    $imgSize = filter_var($result->data->ImgSize, FILTER_SANITIZE_NUMBER_INT);

    $sql = "UPDATE konfiguracija SET Stranicenje = ?, ImgSize = ? WHERE ID = 1";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $stranicenje, $imgSize);

    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        echo json_encode(['data' => 'Success']);
    } else {
        trigger_error("Nije moguće postaviti straničenje", E_USER_ERROR);
    }
}