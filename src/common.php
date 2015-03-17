<?php
/**
 * Created by PhpStorm.
 * User: trp
 * Date: 17.03.15
 * Time: 16:16
 */

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