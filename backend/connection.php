<?php

// db credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'WebDiP2022x057');
define('DB_PASS', 'admin_iZ8g');
define('DB_NAME', 'WebDiP2022x057');

// Connect with the database.
function connect()
{
  $connect = mysqli_connect(DB_HOST ,DB_USER ,DB_PASS ,DB_NAME);

  if ($connect->connect_error) {
    die("Failed to connect:" . mysqli_connect_error());
  }

  mysqli_set_charset($connect, "utf8");

  return $connect;
}

$con = connect();

function customError($errno, $errstr) {
  $error_response = array(
    'error' => array(
      'errHeader' => 'Greška!',
      'errno' => $errno,
      'errstr' => $errstr
    )
  );

  http_response_code(500);
  echo json_encode($error_response, JSON_UNESCAPED_UNICODE);
  die();
}

set_error_handler("customError", E_USER_ERROR);