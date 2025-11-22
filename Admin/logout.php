<?php
session_start(); // Iniciar la sesión para poder destruirla
session_destroy(); // Destruir todas las variables de sesión
header("Location: ../login/login.php"); // Redirigir a la página de login
exit; // Asegurar que no se ejecute más código
?>