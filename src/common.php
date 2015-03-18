<?php
/**
 * Created by PhpStorm.
 * User: trp
 * Date: 17.03.15
 * Time: 16:16
 */

// Car//CreditCard Type
const AMEX = 'American Express';
const CARTE_BLANCHE = 'Carte Blanche';
const CHINA_UNION_PAY = 'China UnionPay';
const DINERS_CLUB_INTERNATIONAL = 'Diners Club';
const DISCOVER = 'Discover';//+
const JCB = 'JCB';
const LASER = 'Laser';
const MAESTRO = 'Maestro';
const MASTER_CARD = 'MasterCard';
const SOLO = 'Solo';
const SWITCH_TYPE = 'Switch';
const VISA = 'Visa';
const VISA_ELECTRON = 'Visa Electron';
const UATP = 'UATP';
const UNKNOWN = 'Unknown';

/**
 * @param $fullName
 * @param $paymentID
 * @param $details
 */
function save2DB ($fullName,$paymentID, $details) {
        $ini_array = parse_ini_file(dirname(__DIR__) ."/config/config.ini",true);

        $dbh = new PDO('mysql:host=localhost', $ini_array['MySQL']['user'], $ini_array['MySQL']['pass']);
        $dbh->exec('CREATE DATABASE IF NOT EXISTS '.$ini_array['MySQL']['dbName']);
        $dbh->exec('USE '.$ini_array['MySQL']['dbName']);

        $fileName = dirname(__DIR__) .'/config/table.sql';
        if (file_exists($fileName)) {
            $sql = file_get_contents($fileName);
            $sql = str_replace('%tbName%', $ini_array['MySQL']['tbName'], $sql);
            $dbh->exec($sql);
        }

        $sql = 'INSERT INTO '.$ini_array['MySQL']['tbName']." (FullName, PaymentID, Details) VALUES (\"".$fullName."\",\"".$paymentID."\",\"".$details."\")";
        $dbh->exec($sql);
        $dbh = null;
}

/**
 * @param $number
 * @return string
 */
function cardTypeByNumber($number){
    $type = UNKNOWN;

    if (strlen($number)>0) {
        $firstDigit = (int)$number[0];
        if (strlen($number)>3) $first4Digit = (int) substr($number,0,4);
        if ($firstDigit == 1) {
            $type = UATP;
            return $type;
        }
        if ($firstDigit == 4) {
            $type = VISA;
            if ((!empty($first4Digit)) && (in_array($first4Digit,[4026, 417500, 4405, 4508, 4844, 4913, 4917]))) $type = VISA_ELECTRON;
            if ((!empty($first4Digit)) && (in_array($first4Digit,[4903, 4905, 4911, 4936]))) $type = SWITCH_TYPE;
            return $type;
        }
    }


    if (strlen($number)>1) {
        $first2Digit = (int) substr($number,0,2);


        switch ($first2Digit) {

            case 30:
                $type = CARTE_BLANCHE;
                break;

            case 34:
            case 37:
                $type = AMEX;
                break;

            case 36:
            case 38:
            case 39:
                $type = DINERS_CLUB_INTERNATIONAL;
                break;

            case 35:
                $type = JCB;
                break;

            case 50:
            case 56:
            case 57:
            case 58:
            case 59:
            case 61:
            case 63:
            case 66:
            case 67:
            case 68:
            case 69:
                $type = MAESTRO;
                if ((!empty($first4Digit)) && (in_array($first4Digit,[6334, 6767]))) $type = SOLO;
                if ((!empty($first4Digit)) && (in_array($first4Digit,[6304, 6706, 6771, 6709]))) $type = LASER;
                if ((!empty($first4Digit)) && (in_array($first4Digit,[5641, 6331, 6333, 6759]))) $type = SWITCH_TYPE;
                break;

            case 51:
            case 52:
            case 53:
            case 54:
            case 55:
                $type = MASTER_CARD;
                break;

            case 60:
            case 62:
            case 64:
            case 65:
                $type = DISCOVER;
                break;
            case 88:
                $type = CHINA_UNION_PAY;
                break;


        }
    }
        return $type;
}

