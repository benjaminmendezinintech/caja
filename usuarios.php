<?php
// Habilitar la visualización de errores
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

session_start();
require_once 'includes/configuration.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
$user_info = $_SESSION['user_info'];
$ID_TERCERO = ['ID_TERCERO'];
$NOM_TERCERO = ['NOM_TERCERO'];
$APE_MATERNO = ['APE_MATERNO'];
$APE_PATERNO = ['APE_PATERNO'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Sistema Financiero</title>
    <link rel="shortcut icon" href="img/icono.ico">
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/table.css">
    <link rel="stylesheet" href="css/styless.css">
    <link rel="stylesheet" href="css/botton.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/26bd214a86.js" crossorigin="anonymous"></script>
    <style>
        /* Alternar el color de fondo de las filas */
        .table tbody tr:nth-child(odd) {
            background-color: #e3f2fd; /* Azul claro para filas impares */
        }

        .table tbody tr:nth-child(even) {
            background-color: #ffffff; /* Blanco para filas pares */
        }

        .table tbody tr td {
            color: #000; /* Color de texto negro */
            padding: 8px; /* Espaciado de las celdas */
            text-align: center; /* Centrar el texto */
            border: 1px solid #f1f1f1; /* Borde de las celdas */
        }
        button {
            border: 2px solid;
            border-radius: 9px;
            cursor: pointer;
            margin: 0 10px;
            padding: 1rem 3rem;
            font-weight: bold;
            font-size: 1rem;
        }
            
        thead {
        position: sticky;
        top: 0;     
        }


    </style>
    <script src="https://kit.fontawesome.com/26bd214a86.js" crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
      <ul class="link-items">
      <li class="link-item">
          <a href="dashboard.php" class="link">
            <ion-icon name="cube-outline"></ion-icon>
            <span style="--i: 2">Dashboard</span>
          </a>
        </li>
        <li class="link-item">
          <a href="consulta_movimientos.php" class="link">
          <ion-icon name="swap-horizontal-outline"></ion-icon>
          <span style="--i: 5">Mis movimientos</span>
          </a>
        </li>
        <?php if ($user_info['COD_PERMISOS'] == '99'): ?>
        <li class="link-item">
          <a href="usuarios.php" class="link">
          <ion-icon name="people-outline"></ion-icon>
            <span style="--i: 2">Usuarios</span>
          </a>
        </li>
        <?php endif; ?>
        <li class="link-item">
          <a href="#" class="link">
            <ion-icon class="noti-icon" name="notifications-outline"></ion-icon>
            <span style="--i: 4">notifications</span>
            <span class="num-noti">4</span>
          </a>
        </li>
        <?php if ($user_info['COD_PERMISOS'] == '99'): ?>
        <li class="link-item">
          <a href="movimientos.php" class="link">
          <ion-icon name="calculator-outline"></ion-icon>
            <span style="--i: 6">Registrar Movs</span>
          </a>
        </li>
        <?php endif; ?>
        <li class="link-item dark-mode">
          <a href="#" class="link">
            <ion-icon name="moon-outline"></ion-icon>
            <span style="--i: 8">dark mode</span>
          </a>
        </li>
        <li class="link-item">
          <a href="#" class="link">
            <img src="user.png" alt="" />
            <span style="--i: 9">
              <h4> <?php echo $user_info['NOM_TERCERO']; ?></h4>
              <P> <?php echo $user_info['APE_PATERNO']; ?></p>
            </span>
          </a>
        </li>
        <li class="link-item">
          <a href="logout.php" class="link">
          <ion-icon name="log-out-outline"></ion-icon>
            <span style="--i: 9">
              <h4>Salir</h4>
            </span>
          </a>
        </li>
      </ul>
    </div>
<!---------------------------------------------------------------------------->
<div class="main-content">
    <div class="status-container">
    <div class="status-box">     
    <?php if ($user_info['COD_PERMISOS'] == '99'): ?>
<?php
$sql = "SELECT ID_TERCERO, NOM_TERCERO, APE_PATERNO, APE_MATERNO FROM CAT_TERCEROS ORDER BY ID_TERCERO ASC";
$result = $conn->query($sql);

// Verificar si hay resultados y mostrarlos
if ($result->num_rows > 0) {
?>
 <div class="main-content">
    <div class="status-container">
        <div class="status-box">
            <h1>LISTADO DE USUARIOS</h1>
        </div>
</div>

<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>ID_TERCERO</th>
                <th>NOMBRE</th>
                <th>APELLIDO PATERNO</th>
                <th>APELLIDO MATERNO</th>
                <th>EDITAR</th>
                <th>ELIMINAR</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td data-label="ID_TERCERO"><?php echo $row['ID_TERCERO']; ?></td>
                    <td data-label="NOMBRE"><?php echo $row['NOM_TERCERO']; ?></td>
                    <td data-label="APELLIDO PATERNO"><?php echo $row['APE_PATERNO']; ?></td>
                    <td data-label="APELLIDO MATERNO"><?php echo $row['APE_MATERNO']; ?></td>
                    <td data-label="">
                        <a href="reporte.php"><button class="btn_3"><i class="fa-regular fa-pen-to-square"></i></button></a>
                    </td>
                    <td data-label="">
                        <a href="reporte.php"><button class="btn_2"><i class="fa-solid fa-user-minus"></i></button></a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
} else {
    echo "No se encontraron usuarios.";
}
// Cerrar la conexión
$conn->close();
?>
<?php endif; ?>
        </div>
</div>

<!---------------------------------------------------------------------------->
   
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
<script
      type="module"
      src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"
    ></script>
    <script
      nomodule
      src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"
    ></script>
    <script src="nav.js"></script>

</body>
</html>



