<?php

// db credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'WebDiP2022x057');
define('DB_PASS', 'admin_iZ8g');
define('DB_NAME', 'webdip2022x057');

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