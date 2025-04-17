<?php
// Definir contraseña para acceder (cambiar en producción)
$acceso_password = 'prime2025';

// Verificar contraseña
$authorized = false;
if (isset($_POST['password']) && $_POST['password'] === $acceso_password) {
    $authorized = true;
} elseif (isset($_GET['password']) && $_GET['password'] === $acceso_password) {
    $authorized = true;
}

// Función para comprobar elementos
function check_item($check, $label, $success_msg = 'OK', $error_msg = 'Error') {
    echo '<tr>';
    echo '<td>' . $label . '</td>';
    if ($check) {
        echo '<td class="success">' . $success_msg . '</td>';
    } else {
        echo '<td class="error">' . $error_msg . '</td>';
    }
    echo '</tr>';
}

// Capturar errores para extensiones
function check_extension($ext) {
    return extension_loaded($ext);
}

// Verificar conexión a la base de datos
function check_database() {
    try {
        $host = getenv('DB_HOST') ?: 'localhost';
        $usuario = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: '';
        $base_datos = getenv('DB_NAME') ?: 'prime';

        $connection = new mysqli($host, $usuario, $password, $base_datos);

        if ($connection->connect_error) {
            return ['success' => false, 'message' => $connection->connect_error];
        }

        // Verificar tablas
        $result = $connection->query("SHOW TABLES");
        $tables = [];
        while ($row = $result->fetch_array()) {
            $tables[] = $row[0];
        }

        return [
            'success' => true,
            'message' => 'Conectado a ' . $base_datos,
            'details' => 'Tablas: ' . implode(', ', $tables)
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Verificar directorios
function check_directory($dir, $check_writable = true) {
    if (!file_exists($dir)) {
        return ['success' => false, 'message' => 'No existe'];
    }

    if ($check_writable && !is_writable($dir)) {
        return ['success' => false, 'message' => 'Sin permisos de escritura'];
    }

    return ['success' => true, 'message' => 'OK', 'details' => 'Permisos: ' . substr(sprintf('%o', fileperms($dir)), -4)];
}

// Comprobar variables de entorno
function get_env_var($name, $default = '') {
    $value = getenv($name) ?: $default;
    if (empty($value)) {
        return ['success' => false, 'message' => 'No definido o vacío'];
    }
    if ($name == 'DB_PASSWORD' && !empty($value)) {
        return ['success' => true, 'message' => 'Configurado', 'details' => '********'];
    }
    return ['success' => true, 'message' => 'Configurado', 'details' => $value];
}

// Verificar versión PHP
function check_php_version($min_version = '7.4.0') {
    if (version_compare(PHP_VERSION, $min_version, '>=')) {
        return ['success' => true, 'message' => PHP_VERSION];
    } else {
        return ['success' => false, 'message' => PHP_VERSION, 'details' => 'Se requiere PHP ' . $min_version . ' o superior'];
    }
}

// Obtener todas las variables de entorno
function get_all_env_vars() {
    $env_vars = [];
    foreach ($_ENV as $key => $value) {
        if (!in_array($key, ['DB_PASSWORD', 'MYSQL_PASSWORD']) && !str_contains(strtolower($key), 'secret')) {
            $env_vars[$key] = $value;
        } else {
            $env_vars[$key] = '********';
        }
    }
    return $env_vars;
}

// Verificar módulos de Apache
function check_apache_module($module) {
    if (function_exists('apache_get_modules')) {
        return in_array($module, apache_get_modules());
    }
    return false; // No podemos verificar
}

// Si está autorizado, realizar las comprobaciones
if ($authorized) {
    $php_version = check_php_version('7.4.0');
    $db_result = check_database();
    $uploads_dir = check_directory(__DIR__ . '/uploads', true);
    $admin_uploads_dir = check_directory(__DIR__ . '/admin/uploads', true);
    $logs_dir = check_directory(__DIR__ . '/logs', true);

    $env_db_host = get_env_var('DB_HOST', 'localhost');
    $env_db_user = get_env_var('DB_USER', 'root');
    $env_db_password = get_env_var('DB_PASSWORD');
    $env_db_name = get_env_var('DB_NAME', 'prime');

    $all_env_vars = get_all_env_vars();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Diagnóstico - Prime Instinct</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .details {
            font-size: 0.8em;
            color: #666;
            margin-top: 5px;
        }
        form {
            margin: 20px 0;
        }
        input[type="password"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
            width: 250px;
        }
        button {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Diagnóstico de Prime Instinct</h1>

        <?php if (!$authorized): ?>
            <p>Por favor ingresa la contraseña para acceder a la herramienta de diagnóstico.</p>
            <form method="post" action="">
                <input type="password" name="password" placeholder="Contraseña">
                <button type="submit">Verificar</button>
            </form>
        <?php else: ?>

            <div class="section">
                <h2>Información del Sistema</h2>
                <table>
                    <tr>
                        <th>Componente</th>
                        <th>Estado</th>
                    </tr>
                    <tr>
                        <td>Versión de PHP</td>
                        <td class="<?php echo $php_version['success'] ? 'success' : 'error'; ?>">
                            <?php echo $php_version['message']; ?>
                            <?php if (isset($php_version['details'])): ?>
                                <div class="details"><?php echo $php_version['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php check_item(check_extension('mysqli'), 'Extensión MySQLi'); ?>
                    <?php check_item(check_extension('pdo_mysql'), 'Extensión PDO MySQL'); ?>
                    <?php check_item(check_extension('gd'), 'Extensión GD (imágenes)'); ?>
                    <?php check_item(check_extension('zip'), 'Extensión ZIP'); ?>
                    <?php check_item(check_apache_module('mod_rewrite'), 'Apache mod_rewrite'); ?>
                </table>
            </div>

            <div class="section">
                <h2>Base de Datos</h2>
                <table>
                    <tr>
                        <th>Componente</th>
                        <th>Estado</th>
                    </tr>
                    <tr>
                        <td>Conexión a la Base de Datos</td>
                        <td class="<?php echo $db_result['success'] ? 'success' : 'error'; ?>">
                            <?php echo $db_result['message']; ?>
                            <?php if (isset($db_result['details'])): ?>
                                <div class="details"><?php echo $db_result['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <h2>Variables de Entorno</h2>
                <table>
                    <tr>
                        <th>Variable</th>
                        <th>Estado</th>
                    </tr>
                    <tr>
                        <td>DB_HOST</td>
                        <td class="<?php echo $env_db_host['success'] ? 'success' : 'error'; ?>">
                            <?php echo $env_db_host['message']; ?>
                            <?php if (isset($env_db_host['details'])): ?>
                                <div class="details"><?php echo $env_db_host['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>DB_USER</td>
                        <td class="<?php echo $env_db_user['success'] ? 'success' : 'error'; ?>">
                            <?php echo $env_db_user['message']; ?>
                            <?php if (isset($env_db_user['details'])): ?>
                                <div class="details"><?php echo $env_db_user['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>DB_PASSWORD</td>
                        <td class="<?php echo $env_db_password['success'] ? 'success' : 'error'; ?>">
                            <?php echo $env_db_password['message']; ?>
                            <?php if (isset($env_db_password['details'])): ?>
                                <div class="details"><?php echo $env_db_password['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>DB_NAME</td>
                        <td class="<?php echo $env_db_name['success'] ? 'success' : 'error'; ?>">
                            <?php echo $env_db_name['message']; ?>
                            <?php if (isset($env_db_name['details'])): ?>
                                <div class="details"><?php echo $env_db_name['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <h2>Directorios</h2>
                <table>
                    <tr>
                        <th>Directorio</th>
                        <th>Estado</th>
                    </tr>
                    <tr>
                        <td>Directorio de uploads</td>
                        <td class="<?php echo $uploads_dir['success'] ? 'success' : 'error'; ?>">
                            <?php echo $uploads_dir['message']; ?>
                            <?php if (isset($uploads_dir['details'])): ?>
                                <div class="details"><?php echo $uploads_dir['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Directorio admin/uploads</td>
                        <td class="<?php echo $admin_uploads_dir['success'] ? 'success' : 'error'; ?>">
                            <?php echo $admin_uploads_dir['message']; ?>
                            <?php if (isset($admin_uploads_dir['details'])): ?>
                                <div class="details"><?php echo $admin_uploads_dir['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Directorio de logs</td>
                        <td class="<?php echo $logs_dir['success'] ? 'success' : 'error'; ?>">
                            <?php echo $logs_dir['message']; ?>
                            <?php if (isset($logs_dir['details'])): ?>
                                <div class="details"><?php echo $logs_dir['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <h2>Todas las Variables de Entorno</h2>
                <button onclick="toggleEnvVars()">Mostrar/Ocultar Variables</button>
                <div id="envVarsContainer" class="hidden">
                    <table>
                        <tr>
                            <th>Nombre</th>
                            <th>Valor</th>
                        </tr>
                        <?php foreach ($all_env_vars as $key => $value): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($key); ?></td>
                            <td><?php echo htmlspecialchars($value); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <p><small>Este reporte fue generado el <?php echo date('Y-m-d H:i:s'); ?></small></p>

            <script>
                function toggleEnvVars() {
                    const container = document.getElementById('envVarsContainer');
                    if (container.classList.contains('hidden')) {
                        container.classList.remove('hidden');
                    } else {
                        container.classList.add('hidden');
                    }
                }
            </script>
        <?php endif; ?>
    </div>
</body>
</html>
