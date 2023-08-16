<?php

require_once ("./connection.php");
require_once ("./virtualnoVrijemeClass.php");

VirtualnoVrijeme::postaviVrijemeUBazu($con);

mysqli_close($con);