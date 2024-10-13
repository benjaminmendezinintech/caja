<?php
$servername = "34.29.9.49";
$username = "bmadev";
$password = "d3v1a";
$database = "PRUEBA_DB_CAJA_FINTECH";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>