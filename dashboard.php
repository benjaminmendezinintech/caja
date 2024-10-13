<?php
session_start();
date_default_timezone_set('America/Mexico_City');
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

if ($_SERVER["REQUEST_METHOD"] == "POST" || $user_info['COD_PERMISOS'] != '99') {
    if ($user_info['COD_PERMISOS'] == '99') {
        $id_tercero = $_POST['id_tercero'];
        $tip_tercero = $_POST['tip_tercero'];
    } else {
        $id_tercero = $user_info['ID_TERCERO'];
        $tip_tercero = $user_info['TIP_TERCERO'];
    }

    $movimientos = get_movimientos($id_tercero, $tip_tercero);
    
    $saldo_acumulado = 0;
    foreach ($movimientos as $key => $mov) {
        $imp_retiro = floatval($mov['IMP_RETIRO']);
        $imp_deposito = floatval($mov['IMP_DEPOSITO']);
        
        $saldo_acumulado += $imp_deposito + $imp_retiro;
        $movimientos[$key]['SALDO'] = $saldo_acumulado;
        
        $suma_depositos += $imp_deposito;
        $suma_retiros += $imp_retiro;
        
        $tipo_movimiento = $mov['DESC_MOVIMIENTO'];
        if (!isset($sumatorias_por_tipo[$tipo_movimiento])) {
            $sumatorias_por_tipo[$tipo_movimiento] = 0;
        }
        $sumatorias_por_tipo[$tipo_movimiento] += $imp_deposito + $imp_retiro;
    }
    $saldo_total = $saldo_acumulado;
    
    // Añadir el concepto 'Prestamo por liquidar:' al final de las sumatorias
    $prestamo_por_liquidar = $sumatorias_por_tipo['PRESTAMO:'] ?? 0;
    $sumatorias_por_tipo['Prestamo por liquidar:'] = $prestamo_por_liquidar;
}

function formatDate($date) {
    $dateObj = DateTime::createFromFormat('Y-m-d H:i:s', $date);
    return $dateObj->format('d-M-Y');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Financiero</title>
    <link rel="stylesheet" href="css/body.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<div class="container">
      <div class="logo">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="32.519"
          height="30.7"
          viewBox="0 0 32.519 30.7"
          fill="#363b46"
        >
          <g id="Logo" transform="translate(-90.74 -84.875)">
            <path
              id="B"
              d="M14.378-30.915c-5.124,0-9.292,3.767-9.292,10.228S9.254-10.46,14.378-10.46h1.471c5.124,0,9.292-3.767,9.292-10.228s-4.168-10.228-9.292-10.228H14.378M11.7-33.456h6.819A12.768,12.768,0,0,1,31.29-20.687,12.768,12.768,0,0,1,18.522-7.919H11.7A12.768,12.768,0,0,1-1.065-20.687C-2.4-51.282,4.652-33.456,11.7-33.456Z"
              transform="translate(91.969 123.494)"
            />
          </g>
        </svg>
      </div>
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
            <h4>Bienvenido <?php echo $nombre_completo; ?></h4>

         <!-- Aquí se cargará el estado del ahorro -->
        </div>
</div>

<div class="main-content">
    <div class="status-container">
        <div class="status-box">
            <h4>Estado del Ahorro</h4>
            <p id="ahorro-estado"> $<?php echo number_format($suma_depositos, 2); ?></p> <!-- Aquí se cargará el estado del ahorro -->
        </div>
        <div class="status-box">
            <h4>Estado del Préstamo</h4>
            <p id="prestamo-estado">$<?php echo number_format($suma_depositos, 2); ?></p> <!-- Aquí se cargará el estado del préstamo -->
        </div>
        <div class="status-box">
            <h4>Rendimiento del Interés Anual</h4>
            <p id="interes-estado">Cargando...</p> <!-- Aquí se cargará el rendimiento del interés -->
        </div>
    </div>

    <div class="chart-container">
        <canvas id="ahorro-chart"></canvas> <!-- Aquí irá el gráfico -->
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



