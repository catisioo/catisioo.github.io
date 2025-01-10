<?php
$host = 'localhost';
$db = 'ziedu_veikals';
$user = 'root';
$password = '';
$port = 3307; //manuāli nomainīts port no 3306 uz 3307 jo xampp nevar palaist uz 3306


$conn = new mysqli($host, $user, $password, $db, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>