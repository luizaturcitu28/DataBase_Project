<!-- Fisierul care verifica conexiunea la baza de date -->

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "events_db";  

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Conexiunea a esuat: " . $conn->connect_error);
} else {

    if (!$conn->select_db($dbname)) {
        die("Baza de date nu exista sau nu se poate selecta: " . $conn->error);
    }
    //echo "Conexiune reusita la baza de date!";
}
?>