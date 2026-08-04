<?php
require_once("config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Validar CSRF (Requisito 3)
    $token_enviado = $_POST['csrf_token'] ?? '';
    if (!verificar_csrf($token_enviado)) {
        set_mensaje("La solicitud de seguridad no es válida.", "danger");
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $db_conexion = get_db_connection();

    try {
        /*
         * AGREGAR EMPLEADO
         */
        if (isset($_POST["btn_agregar"])) {
            $codigo = trim($_POST['txt_codigo'] ?? '');
            $nombres = trim($_POST['txt_nombres'] ?? '');
            $apellidos = trim($_POST['txt_apellidos'] ?? '');
            $direccion = trim($_POST['txt_direccion'] ?? '');
            $telefono = trim($_POST['txt_telefono'] ?? '');
            $fn = trim($_POST['txt_fn'] ?? '');
            $id_puesto = (int)($_POST['drop_puesto'] ?? 0);

            // Validaciones (Requisito 1)
            $errores = [];

            if (empty($codigo) || strlen($codigo) < 2 || strlen($codigo) > 10 || !preg_match("/^[A-Za-z0-9-]+$/", $codigo)) {
                $errores[] = "El código ingresado no es válido.";
            }

            if (empty($nombres) || strlen($nombres) < 2 || !preg_match('/^[\p{L} \-\']+$/u', $nombres)) {
                $errores[] = "Los nombres ingresados no son válidos (solo letras).";
            }

            if (empty($apellidos) || strlen($apellidos) < 2 || !preg_match('/^[\p{L} \-\']+$/u', $apellidos)) {
                $errores[] = "Los apellidos ingresados no son válidos (solo letras).";
            }

            if ($id_puesto <= 0) {
                $errores[] = "El puesto seleccionado no es válido.";
            } else {
                $puesto_chk = $db_conexion->prepare("SELECT id_puesto FROM puestos WHERE id_puesto = ?");
                $puesto_chk->bind_param("i", $id_puesto);
                $puesto_chk->execute();
                if ($puesto_chk->get_result()->num_rows === 0) {
                    $errores[] = "El puesto seleccionado no existe.";
                }
                $puesto_chk->close();
            }

            if (empty($fn) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $fn) || strtotime($fn) > time()) {
                $errores[] = "La fecha de nacimiento no puede ser futura ni inválida.";
            }

            if (!empty($telefono) && !preg_match("/^[0-9]{8}$/", $telefono)) {
                $errores[] = "El teléfono debe tener 8 dígitos numéricos.";
            }

            // Validar código duplicado
            $cod_chk = $db_conexion->prepare("SELECT id_empleado FROM empleados WHERE codigo = ?");
            $cod_chk->bind_param("s", $codigo);
            $cod_chk->execute();
            if ($cod_chk->get_result()->num_rows > 0) {
                $errores[] = "El código ingresado ya existe.";
            }
            $cod_chk->close();

            if (count($errores) > 0) {
                set_mensaje($errores[0], "danger");
                header("Location: index.php");
                exit;
            }

            $sql = "INSERT INTO empleados (codigo, nombres, apellidos, direccion, telefono, fecha_nacimiento, id_puesto) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $parametros = $db_conexion->prepare($sql);
            $parametros->bind_param("ssssssi", $codigo, $nombres, $apellidos, $direccion, $telefono, $fn, $id_puesto);
            
            if ($parametros->execute()) {
                set_mensaje("Empleado registrado correctamente.", "success");
            } else {
                set_mensaje("No se pudo completar la operación.", "danger");
            }
            $parametros->close();
        } 
        /*
         * MODIFICAR EMPLEADO
         */
        elseif (isset($_POST["btn_modificar"])) {
            $txt_id = (int)($_POST["txt_id"] ?? 0);
            $codigo = trim($_POST['txt_codigo'] ?? '');
            $nombres = trim($_POST['txt_nombres'] ?? '');
            $apellidos = trim($_POST['txt_apellidos'] ?? '');
            $direccion = trim($_POST['txt_direccion'] ?? '');
            $telefono = trim($_POST['txt_telefono'] ?? '');
            $fn = trim($_POST['txt_fn'] ?? '');
            $id_puesto = (int)($_POST['drop_puesto'] ?? 0);

            if ($txt_id <= 0) {
                set_mensaje("El identificador del empleado no es válido.", "danger");
                header("Location: index.php");
                exit;
            }

            // Validaciones (Requisito 1)
            $errores = [];

            if (empty($codigo) || strlen($codigo) < 2 || strlen($codigo) > 10 || !preg_match("/^[A-Za-z0-9-]+$/", $codigo)) {
                $errores[] = "El código ingresado no es válido.";
            }

            if (empty($nombres) || strlen($nombres) < 2 || !preg_match('/^[\p{L} \-\']+$/u', $nombres)) {
                $errores[] = "Los nombres ingresados no son válidos (solo letras).";
            }

            if (empty($apellidos) || strlen($apellidos) < 2 || !preg_match('/^[\p{L} \-\']+$/u', $apellidos)) {
                $errores[] = "Los apellidos ingresados no son válidos (solo letras).";
            }

            if ($id_puesto <= 0) {
                $errores[] = "El puesto seleccionado no es válido.";
            } else {
                $puesto_chk = $db_conexion->prepare("SELECT id_puesto FROM puestos WHERE id_puesto = ?");
                $puesto_chk->bind_param("i", $id_puesto);
                $puesto_chk->execute();
                if ($puesto_chk->get_result()->num_rows === 0) {
                    $errores[] = "El puesto seleccionado no existe.";
                }
                $puesto_chk->close();
            }

            if (empty($fn) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $fn) || strtotime($fn) > time()) {
                $errores[] = "La fecha de nacimiento no puede ser futura ni inválida.";
            }

            if (!empty($telefono) && !preg_match("/^[0-9]{8}$/", $telefono)) {
                $errores[] = "El teléfono debe tener 8 dígitos numéricos.";
            }

            // Validar código duplicado pero excluyendo sí mismo
            $cod_chk = $db_conexion->prepare("SELECT id_empleado FROM empleados WHERE codigo = ? AND id_empleado <> ?");
            $cod_chk->bind_param("si", $codigo, $txt_id);
            $cod_chk->execute();
            if ($cod_chk->get_result()->num_rows > 0) {
                $errores[] = "El código ingresado ya existe.";
            }
            $cod_chk->close();

            // Validar que empleado exista
            $emp_chk = $db_conexion->prepare("SELECT id_empleado FROM empleados WHERE id_empleado = ?");
            $emp_chk->bind_param("i", $txt_id);
            $emp_chk->execute();
            if ($emp_chk->get_result()->num_rows === 0) {
                $errores[] = "El identificador del empleado no es válido.";
            }
            $emp_chk->close();

            if (count($errores) > 0) {
                set_mensaje($errores[0], "danger");
                header("Location: index.php");
                exit;
            }

            $sql = "UPDATE empleados SET codigo = ?, nombres = ?, apellidos = ?, direccion = ?, telefono = ?, fecha_nacimiento = ?, id_puesto = ? WHERE id_empleado = ?";
            $parametros = $db_conexion->prepare($sql);
            $parametros->bind_param("ssssssii", $codigo, $nombres, $apellidos, $direccion, $telefono, $fn, $id_puesto, $txt_id);
            
            if ($parametros->execute()) {
                set_mensaje("Empleado modificado correctamente.", "success");
            } else {
                set_mensaje("No se pudo completar la operación.", "danger");
            }
            $parametros->close();
        }
        /*
         * ELIMINAR EMPLEADO
         */
        elseif (isset($_POST["btn_eliminar"])) {
            $txt_id = (int)($_POST["txt_id"] ?? 0);
            
            if ($txt_id <= 0) {
                set_mensaje("El identificador del empleado no es válido.", "danger");
                header("Location: index.php");
                exit;
            }

            $emp_chk = $db_conexion->prepare("SELECT id_empleado FROM empleados WHERE id_empleado = ?");
            $emp_chk->bind_param("i", $txt_id);
            $emp_chk->execute();
            if ($emp_chk->get_result()->num_rows === 0) {
                set_mensaje("El identificador del empleado no es válido.", "danger");
                $emp_chk->close();
                header("Location: index.php");
                exit;
            }
            $emp_chk->close();

            $sql = "DELETE FROM empleados WHERE id_empleado = ?";
            $parametros = $db_conexion->prepare($sql);
            $parametros->bind_param("i", $txt_id);
            
            if ($parametros->execute()) {
                set_mensaje("Empleado eliminado correctamente.", "success");
            } else {
                set_mensaje("No se pudo completar la operación.", "danger");
            }
            $parametros->close();
        }

    } catch (Exception $error) {
        set_mensaje("No se pudo completar la operación.", "danger");
        // $error->getMessage() can be logged internally
    } finally {
        $db_conexion->close();
    }

    header("Location: index.php");
    exit;
} else {
    // Si acceden por GET, los enviamos a index.php
    header("Location: index.php");
    exit;
}
?>
