<?php
declare(strict_types=1);

header_remove('X-Powered-By');
header('X-Frame-Options: DENY', true);
header('X-Content-Type-Options: nosniff', true);
header("Content-Security-Policy: default-src 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'none'; form-action 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://code.jquery.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://code.jquery.com; img-src 'self' data: https:; font-src 'self' data: https://cdn.jsdelivr.net; connect-src 'self';", true);
header('Referrer-Policy: strict-origin-when-cross-origin', true);
header('Permissions-Policy: geolocation=(), microphone=(), camera=()', true);

ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/error_handler.php';
register_custom_error_handler();

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once __DIR__ . '/datos_conexion.php';

// Helper function to connect safely
function get_db_connection()
{
    global $db_host, $db_usr, $db_pass, $db_nombre, $db_puerto;
    $conn = mysqli_connect($db_host, $db_usr, $db_pass, $db_nombre, $db_puerto);
    if (!$conn) {
        http_response_code(500);
        trigger_error('Error crítico de base de datos.', E_USER_ERROR);
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

// 1. Database Migration (Requisito 0)
function check_db_schema()
{
    $conn = get_db_connection();

    $res = $conn->query("SHOW COLUMNS FROM empleados LIKE 'created_at'");
    if ($res && $res->num_rows === 0) {
        $conn->query("ALTER TABLE empleados 
            ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
            ADD COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    }

    $res2 = $conn->query("SHOW COLUMNS FROM puestos LIKE 'created_at'");
    if ($res2 && $res2->num_rows === 0) {
        $conn->query("ALTER TABLE puestos 
            ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
            ADD COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    }

    $conn->close();
}

check_db_schema();

// 2. Mensajes del Sistema (Requisito 2)
function set_mensaje($mensaje, $tipo = 'success')
{
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = $tipo;
}

function mostrar_mensajes()
{
    if (isset($_SESSION['mensaje'])) {
        $mensaje = htmlspecialchars((string) $_SESSION['mensaje'], ENT_QUOTES, 'UTF-8');
        $tipo = in_array($_SESSION['tipo_mensaje'] ?? '', ['success', 'danger'], true) ? $_SESSION['tipo_mensaje'] : 'danger';
        $real_type = htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8');

        echo "<div class='alert alert-{$real_type} alert-dismissible fade show' role='alert'>
                {$mensaje}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
        unset($_SESSION['mensaje']);
        unset($_SESSION['tipo_mensaje']);
    }
}

// 3. Protección CSRF (Requisito 3)
function get_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verificar_csrf($token_enviado)
{
    if (empty($_SESSION['csrf_token']) || empty($token_enviado)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token_enviado);
}
?>