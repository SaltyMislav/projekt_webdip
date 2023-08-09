<?php

require 'connection.php';
    
$ocijene = [];
$sql = "SELECT id, Ocijena FROM ocijenazaposlenika";

if($result = mysqli_query($con,$sql))
{
  $cr = 0;
  while($row = mysqli_fetch_assoc($result))
  {
    $ocijene[$cr]['id'] = (int)$row['id'];
    $ocijene[$cr]['Ocijena'] = $row['Ocijena'];
    $cr++;
  }
    
  echo json_encode(['data'=>$ocijene]);
}
else
{
  http_response_code(404);
}

mysqli_close($con);