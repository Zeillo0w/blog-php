<?php

$servername = "localhost";
$username = "root";  
$password = "root";
$database = "blog";

//connexion
$conn = new mysqli($servername, $username, $password, $database);

// Verif la connexion
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

?>
