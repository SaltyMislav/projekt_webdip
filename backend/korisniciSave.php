<?php

require 'connection.php';

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
    $request = json_decode($postdata);

    $id = filter_var($request->data->ID, FILTER_SANITIZE_NUMBER_INT);
    $neuspjesnePrijave = filter_var($request->data->NeuspjesnePrijave, FILTER_SANITIZE_NUMBER_INT);

    if((is_bool($request->data->Active) || is_numeric($request->data->Active))&& (is_bool($request->data->Blokiran) || is_numeric($request->data->Blokiran)))
    {
        $blocked = $request->data->Blokiran ? 1 : 0;
        $active = $request->data->Active ? 1 : 0;
    }
    else
    {
        trigger_error("Došlo je do pogreške prilikom ažuriranja korisnika", E_USER_ERROR);
    }

    $sql = "UPDATE korisnik SET NeuspjesnePrijave = ?, Active = ?, Blokiran = ? WHERE ID = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iiii", $neuspjesnePrijave, $active, $blocked, $id);
    mysqli_stmt_execute($stmt);

    $affected_rows = mysqli_stmt_affected_rows($stmt);

    if($affected_rows == 1)
    {
        echo json_encode(['data' => 'Success']);
    }
    else
    {
        trigger_error("Došlo je do pogreške prilikom ažuriranja korisnika", E_USER_ERROR);
    }
}