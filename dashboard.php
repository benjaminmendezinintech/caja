<?php
// Habilitar la visualización de errores
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);


session_start();
require_once 'includes/functions.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user_info = $_SESSION['user_info'];
$movimientos = [];
$nombre_completo = $user_info['NOM_TERCERO'] . ' ' . $user_info['APE_PATERNO'] . ' ' . $user_info['APE_MATERNO'];
$saldo_total = 0;
$suma_depositos = 0;
$suma_retiros = 0;
$sumatorias_por_tipo = [];
$suma_depositos = 0;

$id_tercero = $user_info['ID_TERCERO'];
$tip_tercero = $user_info['TIP_TERCERO'];
$depositos = deposito($id_tercero, $tip_tercero);

foreach ($depositos as $movimiento) {
    $suma_depositos += floatval($movimiento['IMP_DEPOSITO']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Financiero</title>
    <link rel="shortcut icon" href="img/icono.ico">
    <link rel="stylesheet" href="css/body.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
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
            <h2>Bienvenido </h2>
            <h3><?php echo $nombre_completo; ?></h3>
        </div>
</div>

<div class="main-content">
    <div class="status-container">
        <div class="status-box">
            <h4>Total de tus ahorros</h4>
            <p id="ahorro-estado"> $<?php echo number_format($suma_depositos, 2); ?></p> <!-- Aquí se cargará el estado del ahorro -->
        </div>
        <div class="status-box">
            <h4>Total de tus prestamos</h4>
            <p id="prestamo-estado">$<?php echo number_format($suma_depositos, 2); ?></p> <!-- Aquí se cargará el estado del préstamo -->
        </div>
        <div class="status-box">
            <h4>Rendimiento del Interés Anual</h4>
            <p id="interes-estado">10%</p> <!-- Aquí se cargará el rendimiento del interés -->
        </div>
    </div>

    <div class="chart-container">
        <canvas id="ahorro-chart"></canvas> 
    </div>
</div>

<!---------------------------------------------------------------------------->
   
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('ahorro-chart').getContext('2d');

        // Datos ficticios
        const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo"];
        const ahorrosMensuales = [100, 150, 200, 250, 300];

        // Crear gráfico
        const myChart = new Chart(ctx, {
            type: 'bar', // Tipo de gráfico
            data: {
                labels: meses, // Eje X
                datasets: [{
                    label: 'Ahorro por Mes',
                    data: ahorrosMensuales, // Datos de la gráfica
                    backgroundColor: 'rgba(34, 142, 230, 1)',
                    borderColor: 'rgba(34, 142, 230, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Monto en $'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Meses'
                        }
                    }
                }
            }
        });
    });
</script>

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



