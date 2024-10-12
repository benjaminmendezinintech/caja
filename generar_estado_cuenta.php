<?php
session_start();
require_once 'includes/functions.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_tercero = $_POST['id_tercero'];
    $tip_tercero = $_POST['tip_tercero'];
    $pdf_file = generar_estado_cuenta_pdf($id_tercero, $tip_tercero);
    header("Content-type: application/pdf");
    header("Content-Disposition: inline; filename=$pdf_file");
    readfile($pdf_file);
    unlink($pdf_file); // Eliminar el archivo temporal
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Estado de Cuenta - Sistema Financiero</title>
</head>
<body>
    <h2>Generar Estado de Cuenta</h2>
    <form method="post">
        <label for="id_tercero">ID Tercero:</label>
        <input type="text" id="id_tercero" name="id_tercero" required><br><br>
        <label for="tip_tercero">Tipo de Tercero:</label>
        <input type="text" id="tip_tercero" name="tip_tercero" required><br><br>
        <input type="submit" value="Generar PDF">
    </form>
    <br>
    <a href="dashboard.php">Volver al Dashboard</a>
</body>
</html>
