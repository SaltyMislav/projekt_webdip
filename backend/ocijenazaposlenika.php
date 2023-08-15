<?php

require 'connection.php';
    
$ocijene = [];
$sql = "SELECT ID, Ocijena FROM ocijenazaposlenika";

if($result = mysqli_query($con,$sql))
{
  $cr = 0;
  while($row = mysqli_fetch_assoc($result))
  {
    $ocijene[$cr]['ID'] = (int)$row['ID'];
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