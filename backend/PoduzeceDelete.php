<?php

require 'connection.php';

$id = ($_GET['id'] !== null && (int)$_GET['id'] > 0) ? mysqli_real_escape_string($con, (int)$_GET['id']) : false;

if (!$id) {
    trigger_error("Nije zadan ID poduzeća koje treba obrisati", E_USER_ERROR);
}

$sql = "DELETE FROM poduzece WHERE ID = ?";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);

$success = mysqli_stmt_execute($stmt);

if ($success) {
    echo json_encode(['data' => 'Success']);
} else {
    trigger_error("Nije moguće izbrisati poduzeće", E_USER_ERROR);
}

mysqli_stmt_close($stmt);
mysqli_close($con);