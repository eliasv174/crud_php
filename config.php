<?php
// config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("datos_conexion.php");

// Helper function to connect safely
function get_db_connection()
{
    global $db_host, $db_usr, $db_pass, $db_nombre, $db_puerto;
    $conn = mysqli_connect($db_host, $db_usr, $db_pass, $db_nombre, $db_puerto);
    if (!$conn) {
        die("Error crítico de base de datos.");
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

// 1. Database Migration (Requisito 0)
// We need to ensure created_at and updated_at exist in empleados and puestos.
function check_db_schema()
{
    $conn = get_db_connection();

    // Check if created_at exists in empleados
    $res = $conn->query("SHOW COLUMNS FROM empleados LIKE 'created_at'");
    if ($res && $res->num_rows === 0) {
        $conn->query("ALTER TABLE empleados 
            ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
            ADD COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    }

    // Check if created_at exists in puestos
    $res2 = $conn->query("SHOW COLUMNS FROM puestos LIKE 'created_at'");
    if ($res2 && $res2->num_rows === 0) {
        $conn->query("ALTER TABLE puestos 
            ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
            ADD COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    }

    $conn->close();
}

// Ensure schema is updated first time this is loaded
check_db_schema();

// 2. Mensajes del Sistema (Requisito 2)
function set_mensaje($mensaje, $tipo = "success")
{
    $_SESSION["mensaje"] = $mensaje;
    $_SESSION["tipo_mensaje"] = $tipo;
}

function mostrar_mensajes()
{
    if (isset($_SESSION["mensaje"])) {
        $mensaje = htmlspecialchars($_SESSION["mensaje"], ENT_QUOTES, "UTF-8");
        $tipo = $_SESSION["tipo_mensaje"] === "success" ? "success" : "danger";
        // Convert internal types (success/danger/warning/info) safely
        if (!in_array($tipo, ['success', 'danger'])) {
            $tipo = "danger"; // Default to danger if not success
        }
        $real_type = htmlspecialchars($_SESSION["tipo_mensaje"], ENT_QUOTES, "UTF-8");

        echo "<div class='alert alert-{$real_type} alert-dismissible fade show' role='alert'>
                {$mensaje}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
        unset($_SESSION["mensaje"]);
        unset($_SESSION["tipo_mensaje"]);
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