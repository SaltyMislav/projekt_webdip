<?php

class VirtualnoVrijeme
{
    private static function dohvatPomaka() {
        $url = 'http://barka.foi.hr/WebDiP/pomak_vremena/pomak.php?format=json';
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($curl);
        
        if ($response === false) {
            $info = curl_error($curl);
            trigger_error("Greška kod dohvata virtualnog vremena: {$info}", E_USER_ERROR);
        } else {
            $data = json_decode($response, true);
            $pomak = $data['WebDiP']['vrijeme']['pomak']['brojSati'];
        }
        
        curl_close($curl);

        return $pomak;
    }

    public static function postaviVrijemeUBazu($con) {
        //write to base

        $pomak = self::dohvatPomaka();
        $sql = "UPDATE konfiguracija SET Pomak = ? WHERE ID = 1";

        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $pomak);

        $success = mysqli_stmt_execute($stmt);

        if ($success) {
            echo json_encode(['data' => $pomak]);
        } else {
            trigger_error("Nije moguće postaviti virtualno vrijeme u bazu", E_USER_ERROR);
        }
    }

    public static function procitajVrijeme($con) {
        //read from base
        $sql = "SELECT Pomak FROM konfiguracija WHERE ID = 1";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $pomak = mysqli_fetch_assoc($result);
        return $pomak['Pomak'];
    }
}
