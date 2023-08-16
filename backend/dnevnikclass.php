<?php

require_once ("./virtualnoVrijemeClass.php");
class Dnevnik
{
    public static function upisiUDnevnik($con, $detail, $datumVrijeme, $vrstaPromjene) {
        $sql = "INSERT INTO dnevnikrada (Detail, DatumPromjene, VrstaPromjeneID) VALUES (?, ?, ?)";
        $stmt3 = mysqli_prepare($con, $sql) or die(mysqli_error($con));
        mysqli_stmt_bind_param($stmt3, "ssi", $detail, $datumVrijeme, $vrstaPromjene);
        if (mysqli_stmt_execute($stmt3)) {
            mysqli_stmt_close($stmt3);
            return;
        } else {
            trigger_error("Greška kod upisa u dnevnik", E_USER_ERROR);
        }
        mysqli_stmt_close($stmt3);
    }

    public static function TrenutnoVrijeme($con)
    {
        $vrijeme = date('Y-m-d H:i:s');
        return date('Y-m-d H:i:s', strtotime($vrijeme . VirtualnoVrijeme::procitajVrijeme($con) . 'hours'));
    }
}