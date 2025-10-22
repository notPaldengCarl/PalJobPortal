<?php
// start output buffering so header() still works if a file emits output early
if (!ob_get_level()) {
    ob_start();
}

// ensure a session is started for all pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// define APPURL once for all scripts
if (!defined('APPURL')) {
    define('APPURL', 'http://localhost/paljob');
}

try {
        
    $host = "localhost";

    $dbname = "paljob";
    
    $user = "root";
    
    $pass = "";
    
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    echo $e->getMessage();
}




