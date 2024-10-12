<?php

include 'includes/scripts.php';
include 'includes/configuration.php';
session_start();
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $CORREO_USUARIO = $_POST['CORREO_USUARIO'];
    $NOM_TERCERO = $_POST['NOM_TERCERO'];
    $APE_PATERNO = $_POST['APE_PATERNO'];
    $APE_MATERNO = $_POST['APE_MATERNO'];
    $TELEFONO_USUARIO = $_POST['TELEFONO_USUARIO'];

    // Generar COD_USUARIO
    $primer_letra_nombre = strtoupper(substr($NOM_TERCERO, 0, 1)); // Primera letra del nombre
    $primer_letra_paterno = strtoupper(substr($APE_PATERNO, 0, 1)); // Primera letra del apellido paterno
    $primer_letra_materno = strtoupper(substr($APE_MATERNO, 0, 1)); // Primera letra del apellido materno

    $COD_USUARIO = $primer_letra_nombre . strtoupper($APE_PATERNO) . $primer_letra_materno;

    // Información adicional
    $TIP_USUARIO = "ACC";
    $COD_PERMISOS = 1;
    $COD_PASS = password_hash($_POST['COD_PASS'], PASSWORD_BCRYPT);
    date_default_timezone_set('America/Mexico_City');
    $FEC_ACTUALIZACION = date('Y-m-d');
    $MCA_INHABILITADO = "N";

    // Obtener fecha
    $year = date("y");
    $month = date("m");
    $day = date("d");

    // Base del ID_TERCERO
    $base_id = $primer_letra_nombre . $primer_letra_paterno . $primer_letra_materno . "-" . $year . $month . $day;

    // Verificar el consecutivo
    $contador = 1;
    $nuevo_id = $base_id . $contador;

    do {
        $sql = "SELECT ID_TERCERO FROM CAT_TERCEROS WHERE ID_TERCERO = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nuevo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $contador++;
            $nuevo_id = $base_id . $contador; // Incrementar el número al final
        }
    } while ($result->num_rows > 0);

    $ID_TERCERO = $nuevo_id; // Este será el nuevo ID que se insertará
    $TIP_TERCERO = 1;

    // Inserción en CAT_USUARIOS
    $sql = "INSERT INTO CAT_USUARIOS (COD_USUARIO, CORREO_USUARIO, TIP_USUARIO, COD_PERMISOS, COD_PASS, FEC_ACTUALIZACION, MCA_INHABILITADO) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $COD_USUARIO, $CORREO_USUARIO, $TIP_USUARIO, $COD_PERMISOS, $COD_PASS, $FEC_ACTUALIZACION, $MCA_INHABILITADO);
    echo 
        '
          <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
          <script>
              Swal.fire({
                  position: "center",
                  icon: "success",
                  title: "Bienvenido",
                  showConfirmButton: false,
                  timer: 5000
              }).then(function() {
                    window.location = "http://34.29.9.49/caja/";
              });
          </script>
          ';
      } else {
          echo "Error: " . $stmt->error;
      }
    $stmt->close();

    // Inserción en CAT_TERCEROS   
    $sql = "INSERT INTO CAT_TERCEROS (ID_TERCERO, TIP_TERCERO, NOM_TERCERO, APE_PATERNO, APE_MATERNO, FEC_ACTUALIZACION, MCA_INHABILITADO, COD_USUARIO) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $ID_TERCERO, $TIP_TERCERO, $NOM_TERCERO, $APE_PATERNO, $APE_MATERNO, $FEC_ACTUALIZACION, $MCA_INHABILITADO, $COD_USUARIO);
    $stmt->execute();
    $stmt->close();
    
    // Inserción en CAT_TER_USUARIOS
    $sql = "INSERT INTO CAT_TER_USUARIO (COD_USUARIO, ID_TERCERO, TIP_TERCERO, FEC_ACTUALIZACION, MCA_INHABILITADO) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $COD_USUARIO, $ID_TERCERO, $TIP_TERCERO, $FEC_ACTUALIZACION, $MCA_INHABILITADO);
    if ($stmt->execute()) {
        echo 
        '
          <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
          <script>
              Swal.fire({
                  position: "center",
                  icon: "success",
                  title: "Bienvenido",
                  showConfirmButton: false,
                  timer: 5000
              }).then(function() {
                    window.location = "http://34.29.9.49/caja/";
              });
          </script>
          ';
      } else {
          echo "Error: " . $stmt->error;
      }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="css/login/style.css">
</head>
<body>
        
    <div class="container">
        <div class="container-form">
        <?php if (isset($_GET['error'])):
            echo '
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Contraseña o correo incorrectos",
                text: "Verifica y vuelve a intentar",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location = "http://34.29.9.49/caja/";
            });
        </script>
        ';
     endif; ?>
            <form class="sign-in" action="login.php"  method="post" class="formulario">
                <h2>Iniciar Sesión</h2>
                <div class="social-networks">
                    <ion-icon name="logo-twitch"></ion-icon>
                    <ion-icon name="logo-twitter"></ion-icon>
                    <ion-icon name="logo-instagram"></ion-icon>
                    <ion-icon name="logo-tiktok"></ion-icon>
                </div>
                <span>Use su correo y contraseña</span>
                <div class="container-input">
                     <ion-icon name="mail-outline"></ion-icon>
                         <input type="text" id="username" name="username" placeholder="Ingresa tu correo o teléfono" required>
                </div>
                <div class="container-input">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" id="password" name="password" placeholder="Ingresa tu conteseña" required>
                </div>
                <a href="#">¿Olvidaste tu contraseña?</a>
                <button class="button">INICIAR SESIÓN</button>
            </form>
        </div>

        <div class="container-form">
            <form method="post" class="sign-up">
                <h2>Registrarse</h2>
                <span>Use su correo electrónico para registrarse</span>
                <div class="container-input">
                    <ion-icon name="person-outline"></ion-icon>
                    <input type="text" name="NOM_TERCERO" placeholder="Ingresa tu nombre" required>
                </div>
                <div class="container-input">
                    <ion-icon name="person-outline"></ion-icon>
                    <input type="text" name="APE_PATERNO" placeholder="Ingresa tu apellido paterno" required>
                </div>
                <div class="container-input">
                    <ion-icon name="person-outline"></ion-icon>
                    <input type="text" name="APE_MATERNO" placeholder="Ingresa tu apellido materno" required>
                </div>
                <div class="container-input">
                    <ion-icon name="mail-outline"></ion-icon>
                    <input type="correo" name="CORREO_USUARIO" placeholder="Ingresa tu correo" required>
                </div>
                <div class="container-input">
                <ion-icon name="call-outline"></ion-icon>
                    <input type="text" name="TELEFONO_USUARIO" placeholder="Ingresa tu numero telefonico" required>
                </div>
                <div class="container-input">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input  type="password" name="COD_PASS" placeholder="Ingresa tu conteseña" required>
                </div>
                <button class="button" >REGISTRARSE</button>
            </form>
        </div>

        <div class="container-welcome">
            <div class="welcome-sign-up welcome">
                <h3>¡Bienvenido!</h3>
                <p>Ingrese sus datos personales para usar las funciones del sitio</p>
                <button class="button" id="btn-sign-up">Registrarse</button>
            </div>
            <div class="welcome-sign-in welcome">
                <h3>¡Hola!</h3>
                <p>Regístrese con sus datos personales para usar las funciones del sitio</p>
                <button class="button" id="btn-sign-in">Iniciar Sesión</button>
            </div>
        </div>

    </div>


    <script src="js/login/script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>