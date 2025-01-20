<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

    define("SERVER", "localhost");
    define("DB", "mybigcompany");
    define("USER", "FREDERIC");
    define("PASSWORD", "Kylian250510?");
    $db = new PDO('mysql:host='.SERVER.';dbname='.DB, USER, PASSWORD);
?>
