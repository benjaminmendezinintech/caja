<?php
// Habilitar la visualización de errores
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
$user_info = $_SESSION['user_info'];
?>

 <!-------- Formulario Registrar Movimiento---------------------->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Financiero</title>
    <link rel="shortcut icon" href="img/icono.ico">
    <link rel="stylesheet" href="css/body.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/26bd214a86.js" crossorigin="anonymous"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
    
form .input-box span.details {
  display: block;
  font-weight: 500;
  margin-bottom: 5px;
}
.user-details .input-box input {
  height: 45px;
  width: 100%;
  outline: none;
  font-size: 16px;
  border-radius: 5px;
  padding-left: 15px;
  border: 1px solid #ccc;
  border-bottom-width: 2px;
  transition: all 0.3s ease;
}

form .gender-details .gender-title {
  font-size: 20px;
  font-weight: 500;
}
form .category {
  display: flex;
  width: 80%;
  margin: 14px 0;
  justify-content: space-between;
}
form .category label {
  display: flex;
  align-items: center;
  cursor: pointer;
}
form .category label .dot {
  height: 18px;
  width: 18px;
  border-radius: 50%;
  margin-right: 10px;
  background: #d9d9d9;
  border: 5px solid transparent;
  transition: all 0.3s ease;
}

form input[type="radio"] {
  display: none;
}
form .button {
  height: 45px;
  margin: 35px 0
}
form .button input {
  height: 100%;
  width: 100%;
  border-radius: 5px;
  border: none;
  color: #fff;
  font-size: 18px;
  font-weight: 500;
  letter-spacing: 1px;
  cursor: pointer;
  transition: all 0.3s ease;
  background: linear-gradient(135deg, #71b7e6, #240fe0);
}
form .button input:hover {
  background: linear-gradient(-135deg, #71b7e6, #240fe0);
}

</style>
    
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
        <!----------Registrar movimiento -->  


         <h4>Registrar Movimiento</h4>
        <form action="insertar_movimientos.php" method="POST">
        <div class="input-box">
            <span class="details">ID Tercero:</span>
                <select name="ID_TERCERO" id="ID_TERCERO" required>
                    <option value="">Seleccione un Tercero</option>
                        <?php
                        // Conexión a la base de datos
                        include 'includes/configuration.php';
                    
                        $sql = "SELECT ID_TERCERO, NOM_TERCERO, APE_PATERNO, APE_MATERNO FROM CAT_TERCEROS"; // Cambia la tabla y campos según tu base de datos
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['ID_TERCERO'] . "'>" . htmlspecialchars($row['NOM_TERCERO'])." " .htmlspecialchars($row['APE_PATERNO']).  " ". htmlspecialchars($row['APE_MATERNO']) . "</option>";
                        }
                        $conn->close();
                        ?>
                </select>
        </div>

        <div class="input-box">
            <span class="details">Tipo de tercero:</span>
                <select name="TIP_TERCERO" id="TIP_TERCERO" required>
                    <option value="1">Accionista</option>
                    <option value="2">Prestuario</option>
                    <!-- Puedes añadir más tipos de movimiento aquí -->
            </select>
        </div>

        <div class="input-box">
            <span class="details">Tipo de Movimiento:</span>
                <select name="COD_MOVIMIENTO" id="COD_MOVIMIENTO" required>
                    <option value="">Seleccione un MOVIMIENTO</option>
                        <?php
                            // Conexión a la base de datos
                            include 'includes/configuration.php';
                            $sql = "SELECT COD_MOVIMIENTO, DESC_MOVIMIENTO FROM CAT_TIP_MOVIMIENTOS"; // Cambia la tabla y campos según tu base de datos
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['COD_MOVIMIENTO'] . "'>" . htmlspecialchars($row['DESC_MOVIMIENTO']) . "</option>";
                            }
                            $conn->close();
                            ?>
                </select>
        </div>

        <div class="input-box">
            <span class="details">Importe de Retiro:</span>
            <input type="number" step="0.01" name="IMP_RETIRO" id="IMP_RETIRO" placeholder="Importe del retiro">
        </div>
        
        <div class="input-box">
            <span class="details">Importe de Depósito:</span>
            <input type="number" step="0.01" name="IMP_DEPOSITO" id="IMP_DEPOSITO" placeholder="Importe del Deposito">
        </div>
    
<script>
document.getElementById('COD_MOVIMIENTO').addEventListener('change', function() {
    var depositoInput = document.getElementById('IMP_DEPOSITO');
    var retiroInput = document.getElementById('IMP_RETIRO');

    // Opciones que inhabilitan el campo IMP_DEPOSITO
    var disableDepositoOptions = ['2', '6']; // Cambia estos valores según tu lógica
    // Opciones que inhabilitan el campo IMP_RETIRO
    var disableRetiroOptions = ['1', '3', '4', '5']; // Cambia estos valores según tu lógica

    // Deshabilitar IMP_DEPOSITO si se selecciona un préstamo
    if (disableDepositoOptions.includes(this.value)) {
        depositoInput.disabled = true;
        depositoInput.value = ''; // Limpia el valor del input inhabilitado
    } else {
        depositoInput.disabled = false; // Habilita el input de depósito
    }

    // Deshabilitar IMP_RETIRO si se selecciona un movimiento específico
    if (disableRetiroOptions.includes(this.value)) {
        retiroInput.disabled = true;
        retiroInput.value = ''; // Limpia el valor del input inhabilitado
    } else {
        retiroInput.disabled = false; // Habilita el input de retiro
    }
});
</script>   

        <div class="input-box">
            <span class="details">Inhabilitado:</span>
                <select name="MCA_INHABILITADO" id="MCA_INHABILITADO" required>
                    <option value="N">NO</option>
                    <option value="Y">SI</option>
                </select>
        </div>
        <div class="button">
          <input type="submit" value="Register">
        </div>
        </form>
    </div>
  </div>

<script>
document.getElementById('COD_MOVIMIENTO').addEventListener('change', function() {
    var depositoInput = document.getElementById('IMP_DEPOSITO');
    var retiroInput = document.getElementById('IMP_RETIRO');

    // Opciones que inhabilitan el campo IMP_DEPOSITO
    var disableDepositoOptions = ['2', '6']; // Asumiendo que estos son los valores de los tipos de movimiento

    // Opciones que inhabilitan el campo IMP_RETIRO
    var disableRetiroOptions = ['1', '3', '4', '5']; // Asumiendo que estos son los valores de los tipos de movimiento

    // Deshabilitar IMP_DEPOSITO si se selecciona un préstamo
    if (disableDepositoOptions.includes(this.value)) {
        depositoInput.disabled = true;
        depositoInput.value = ''; // Limpia el valor del input inhabilitado
    } else {
        depositoInput.disabled = false; // Habilita el input de depósito
    }

    // Deshabilitar IMP_RETIRO si se selecciona un movimiento específico
    if (disableRetiroOptions.includes(this.value)) {
        retiroInput.disabled = true;
        retiroInput.value = ''; // Limpia el valor del input inhabilitado
    } else {
        retiroInput.disabled = false; // Habilita el input de retiro
    }
});
</script>



        </div>
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
