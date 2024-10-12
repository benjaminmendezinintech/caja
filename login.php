<?php
session_start();
require_once 'includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $user = validate_user($username, $password);
    if ($user) {
        $_SESSION['user'] = $user['COD_USUARIO']; // Guarda COD_USUARIO
        $_SESSION['user_info'] = get_user_info($user['COD_USUARIO']); // Guarda información adicional
        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: index.php?error=1");
        exit();
    }
}
?>