<?php
// Habilitar la visualización de errores
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// Datos de conexión a la base de datos

require_once 'includes/configuration.php';
// Crear conexión
include 'includes/scripts.php';

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
$user_info = $_SESSION['user_info'];
// Recibir datos del formulario
$ID_TERCERO = $_POST['ID_TERCERO'];
$TIP_TERCERO = $_POST['TIP_TERCERO'];
date_default_timezone_set('America/Mexico_City');
$FEC_REGISTRO = date('Y-m-d H:i:s');
$COD_MOVIMIENTO = $_POST['COD_MOVIMIENTO'];
$IMP_RETIRO = $_POST['IMP_RETIRO'] ?? NULL; // Si no se envía, se establece como NULL
$IMP_DEPOSITO = $_POST['IMP_DEPOSITO'] ?? NULL;
date_default_timezone_set('America/Mexico_City');
$FEC_ACTUALIZACION = date('Y-m-d H:i:s');
$MCA_INHABILITADO = $_POST['MCA_INHABILITADO'];
$COD_USUARIO = $_SESSION['user'];
// Preparar la consulta SQL
$stmt = $conn->prepare("INSERT INTO HIS_MOVIMIENTOS (ID_TERCERO, TIP_TERCERO, FEC_REGISTRO, COD_MOVIMIENTO, IMP_RETIRO, IMP_DEPOSITO, FEC_ACTUALIZACION, MCA_INHABILITADO, COD_USUARIO) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iissddsss", $ID_TERCERO, $TIP_TERCERO, $FEC_REGISTRO, $COD_MOVIMIENTO, $IMP_RETIRO, $IMP_DEPOSITO, $FEC_ACTUALIZACION, $MCA_INHABILITADO, $COD_USUARIO);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo '
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      Swal.fire({
  position: "center",
  icon: "success",
  title: "Movimiento cargado",
  showConfirmButton: false,
  timer: 1300
  }).then(function() {
    window.location = "movimientos.php";
});
    </script>
    ';
} else {
    
    echo "Error al insertar el registro: " . $stmt->error;
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>