<?php
try {
    // Establecer la conexión con PDO
    $conexion = new PDO(
        "mysql:host=localhost;dbname=votacion_escolar;charset=utf8", // DSN (Data Source Name)
        "root",                                                  // Usuario
        "",                                                      // Contraseña (vacía en tu caso)
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,         // Modo de error: lanzar excepciones
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,    // Modo de fetch por defecto: asociativo
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"     // Asegurar codificación UTF-8
        )
    );

    // Configurar la zona horaria
    date_default_timezone_set("America/Lima");

} catch (PDOException $e) {
    // Manejar errores de conexión
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>