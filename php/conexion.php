<?php
// Cargar variables de entorno desde .env si existe
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env as $key => $value) {
        putenv("$key=$value");
    }
}

// Usar variables de entorno o valores por defecto
$host = getenv('DB_HOST') ?: 'localhost';
$usuario = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$base_datos = getenv('DB_NAME') ?: 'prime';

$connection = new mysqli($host, $usuario, $password, $base_datos);

if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
}
?>
