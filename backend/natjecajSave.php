<?php

require_once ("./connection.php");
require_once ("./virtualnoVrijemeClass.php");
require_once ("./dnevnikclass.php");

$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    $result = json_decode($postData);

    Dnevnik::upisiUDnevnik($con, 'Pokretanje natjecaj save', Dnevnik::TrenutnoVrijeme($con), 5);

    if (isset($result->data->ID) && !empty($result->data->ID)) {

        Dnevnik::upisiUDnevnik($con, 'Uređivanje natječaja', Dnevnik::TrenutnoVrijeme($con), 5);

        $id = filter_var($result->data->ID, FILTER_SANITIZE_NUMBER_INT);
        $naziv = mysqli_real_escape_string($con, trim($result->data->Naziv));
        $opis = mysqli_real_escape_string($con, trim($result->data->Opis));
        $status = filter_var($result->data->StatusNatjecajaID, FILTER_SANITIZE_NUMBER_INT);
        $poduzece = filter_var($result->data->PoduzeceID, FILTER_SANITIZE_NUMBER_INT);
        $vrijemePocetka = mysqli_real_escape_string($con, trim($result->data->VrijemePocetka));
        $vrijemeKraja = mysqli_real_escape_string($con, trim($result->data->VrijemeKraja));

        if (!isset($id) || !isset($naziv) || !isset($opis) || !isset($vrijemePocetka) || !isset($vrijemeKraja)) {
            Dnevnik::upisiUDnevnik($con, 'Nisu svi podaci uneseni', Dnevnik::TrenutnoVrijeme($con), 7);
            trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
        }

        $vrijemePocetka = strtotime($vrijemePocetka);
        $vrijemeKraja = strtotime($vrijemeKraja);

        if ($vrijemePocetka > $vrijemeKraja) {
            Dnevnik::upisiUDnevnik($con, 'Vrijeme početka ne može biti veće od vremena kraja', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Vrijeme početka ne može biti veće od vremena kraja", E_USER_ERROR);
        }

        $difference = $vrijemeKraja - $vrijemePocetka;
        $tedDays = 10 * 24 * 60 * 60;

        if ($difference < $tedDays) {
            Dnevnik::upisiUDnevnik($con, 'Natječaj mora trajati točno 10 dana', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Natječaj mora trajati točno 10 dana", E_USER_ERROR);
        }

        $trenutnoVrijeme = date('Y-m-d H:i:s');
        $trenutnoVrijeme = strtotime($trenutnoVrijeme . VirtualnoVrijeme::procitajVrijeme($con) . 'hours');

        if ($status != 2) {
            if ($trenutnoVrijeme >= $vrijemeKraja)
                $status = 2;
            updateZaposlenika($con, $id, $poduzece, $status);
        } else {
            updateZaposlenika($con, $id, $poduzece, $status);
        }

        $vrijemePocetka = date('Y-m-d H:i:s', $vrijemePocetka);
        $vrijemeKraja = date('Y-m-d H:i:s', $vrijemeKraja);

        $sql = "UPDATE natjecaj SET Naziv = ?, Opis = ?, VrijemePocetka = ?, VrijemeKraja = ?, StatusNatjecajaID = ?, PoduzeceID = ? WHERE ID = ?";

        Dnevnik::upisiUDnevnik($con, 'Upit za uređivanje natječaja', Dnevnik::TrenutnoVrijeme($con), 2);

        $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
        mysqli_stmt_bind_param($stmt, 'ssssiii', $naziv, $opis, $vrijemePocetka, $vrijemeKraja, $status, $poduzece, $id);

        if (mysqli_stmt_execute($stmt)) {
            Dnevnik::upisiUDnevnik($con, 'Uspješno uređen natječaj', Dnevnik::TrenutnoVrijeme($con), 9);
            echo json_encode(['data' => 'Uspješno uređen natječaj']);
        } else {
            Dnevnik::upisiUDnevnik($con, 'Problem kod uređivanja natječaja', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Problem kod uređivanja natječaja", E_USER_ERROR);
        }
    } else {

        Dnevnik::upisiUDnevnik($con, 'Dodavanje natječaja', Dnevnik::TrenutnoVrijeme($con), 1);

        $naziv = mysqli_real_escape_string($con, trim($result->data->Naziv));
        $opis = mysqli_real_escape_string($con, trim($result->data->Opis));
        $status = filter_var($result->data->StatusNatjecajaID, FILTER_SANITIZE_NUMBER_INT);
        $poduzece = filter_var($result->data->PoduzeceID, FILTER_SANITIZE_NUMBER_INT);
        $vrijemePocetka = mysqli_real_escape_string($con, trim($result->data->VrijemePocetka));
        $vrijemeKraja = mysqli_real_escape_string($con, trim($result->data->VrijemeKraja));

        if (!isset($naziv) || !isset($opis) || !isset($vrijemePocetka) || !isset($vrijemeKraja)) {
            Dnevnik::upisiUDnevnik($con, 'Nisu svi podaci uneseni', Dnevnik::TrenutnoVrijeme($con), 7);
            trigger_error("Nisu svi podaci uneseni", E_USER_ERROR);
        }

        $vrijemePocetka = strtotime($vrijemePocetka);
        $vrijemeKraja = strtotime($vrijemeKraja);

        if ($vrijemePocetka > $vrijemeKraja) {
            Dnevnik::upisiUDnevnik($con, 'Vrijeme početka ne može biti veće od vremena kraja', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Vrijeme početka ne može biti veće od vremena kraja", E_USER_ERROR);
        }

        $difference = $vrijemeKraja - $vrijemePocetka;
        $tedDays = 10 * 24 * 60 * 60;

        if ($difference < $tedDays) {
            Dnevnik::upisiUDnevnik($con, 'Natječaj mora trajati točno 10 dana', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Natječaj mora trajati točno 10 dana", E_USER_ERROR);
        }

        $vrijemePocetka = date('Y-m-d H:i:s', $vrijemePocetka);
        $vrijemeKraja = date('Y-m-d H:i:s', $vrijemeKraja);

        $sql = "INSERT INTO natjecaj (Naziv, Opis, VrijemePocetka, VrijemeKraja, StatusNatjecajaID, PoduzeceID) VALUES (?, ?, ?, ?, ?, ?)";

        Dnevnik::upisiUDnevnik($con, 'Upit za dodavanje natječaja', Dnevnik::TrenutnoVrijeme($con), 1);

        $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
        mysqli_stmt_bind_param($stmt, 'ssssii', $naziv, $opis, $vrijemePocetka, $vrijemeKraja, $status, $poduzece);

        if (mysqli_stmt_execute($stmt)) {
            Dnevnik::upisiUDnevnik($con, 'Uspješno dodan natječaj', Dnevnik::TrenutnoVrijeme($con), 9);
            echo json_encode(['data' => 'Uspješno dodan natječaj']);
        } else {
            Dnevnik::upisiUDnevnik($con, 'Problem kod dodavanja natječaja', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Problem kod dodavanja natječaja", E_USER_ERROR);
        }
    }
}

function updateZaposlenika($con, $id, $poduzece, $status)
{

    Dnevnik::upisiUDnevnik($con, 'Pokretanje updatea zaposlenika', Dnevnik::TrenutnoVrijeme($con), 2);

    if ($status == 2) {
        $sql = "SELECT KorisnikID FROM prijavananatjecaj WHERE NatjecajID = ?";
        Dnevnik::upisiUDnevnik($con, 'Upit za dohvaćanje korisnika', Dnevnik::TrenutnoVrijeme($con), 3);
        $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {
            Dnevnik::upisiUDnevnik($con, 'Uspješan upit za dohvaćanje korisnika', Dnevnik::TrenutnoVrijeme($con), 9);
            $result = mysqli_stmt_get_result($stmt);

            while ($row = mysqli_fetch_assoc($result)) {
                $korisnikID = $row['KorisnikID'];
                $sql = "UPDATE korisnik SET PoduzeceID = ? WHERE ID = ?";

                Dnevnik::upisiUDnevnik($con, 'Postavljanje poduzeca' . $poduzece . 'na zaposleniku' . $korisnikID, Dnevnik::TrenutnoVrijeme($con), 2);

                $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
                mysqli_stmt_bind_param($stmt, 'ii', $poduzece, $korisnikID);

                if (!mysqli_stmt_execute($stmt)) {
                    Dnevnik::upisiUDnevnik($con, 'Problem kod ažuriranja broja prijavljenih natječaja', Dnevnik::TrenutnoVrijeme($con), 8);
                    trigger_error("Problem kod ažuriranja broja prijavljenih natječaja", E_USER_ERROR);
                }
            }
        } else {
            trigger_error("Problem kod dohvaćanja korisnika", E_USER_ERROR);
        }
    } else if ($status == 1) {
        Dnevnik::upisiUDnevnik($con, 'Dohvacanje korisnika na prijavananatejecaj', Dnevnik::TrenutnoVrijeme($con), 3);
        $sql = "SELECT KorisnikID FROM prijavananatjecaj WHERE NatjecajID = ?";
        $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {

            $result = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);

            Dnevnik::upisiUDnevnik($con, 'Uspješan upit za dohvaćanje korisnika', Dnevnik::TrenutnoVrijeme($con), 9);

            while ($row = mysqli_fetch_assoc($result)) {
                $korisnikID = $row['KorisnikID'];
                $sql = "UPDATE korisnik SET PoduzeceID = NULL WHERE ID = ?";

                Dnevnik::upisiUDnevnik($con, 'Postavljanje poduzeca na zaposleniku', Dnevnik::TrenutnoVrijeme($con), 2);

                $stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
                mysqli_stmt_bind_param($stmt, 'i', $korisnikID);

                if (!mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    Dnevnik::upisiUDnevnik($con, 'Problem kod ažuriranja broja prijavljenih natječaja', Dnevnik::TrenutnoVrijeme($con), 8);
                    trigger_error("Problem kod ažuriranja broja prijavljenih natječaja", E_USER_ERROR);
                }
                mysqli_stmt_close($stmt);
            }

            Dnevnik::upisiUDnevnik($con, 'Uspješno ažuriranje broj prijavljenih natječaja', Dnevnik::TrenutnoVrijeme($con), 2);
        } else {
            Dnevnik::upisiUDnevnik($con, 'Problem kod dohvaćanja korisnika', Dnevnik::TrenutnoVrijeme($con), 8);
            trigger_error("Problem kod dohvaćanja korisnika", E_USER_ERROR);
        }
    }
}

mysqli_close($con);
