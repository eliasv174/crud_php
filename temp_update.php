<?php
include("c:/dev/crud_php/php_empresa/datos_conexion.php");

$db_conexion = mysqli_connect($db_host, $db_usr, $db_pass, $db_nombre);

if (!$db_conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

$queries = [
    "ALTER TABLE empleados ADD COLUMN IF NOT EXISTS created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;",
    "ALTER TABLE empleados ADD COLUMN IF NOT EXISTS updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;",
    "ALTER TABLE puestos ADD COLUMN IF NOT EXISTS created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;",
    "ALTER TABLE puestos ADD COLUMN IF NOT EXISTS updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;"
];

foreach ($queries as $sql) {
    try {
        $db_conexion->query($sql);
        echo "Ejecutado: $sql\n";
    } catch (Exception $e) {
        echo "Error en $sql: " . $e->getMessage() . "\n";
    }
}

$db_conexion->close();
echo "Fin.\n";
?>
