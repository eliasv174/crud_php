<?php
if (!empty($_POST)) {

    $txt_id = (int) ($_POST["txt_id"] ?? 0);
    $txt_codigo = trim($_POST["txt_codigo"] ?? "");
    $txt_nombres = trim($_POST["txt_nombres"] ?? "");
    $txt_apellidos = trim($_POST["txt_apellidos"] ?? "");
    $txt_direccion = trim($_POST["txt_direccion"] ?? "");
    $txt_telefono = trim($_POST["txt_telefono"] ?? "");
    $txt_fn = trim($_POST["txt_fn"] ?? "");
    $drop_puesto = (int) ($_POST["drop_puesto"] ?? 0);

    include("datos_conexion.php");

    $db_conexion = mysqli_connect(
        $db_host,
        $db_usr,
        $db_pass,
        $db_nombre
    );

    /*
     * Verificar que la conexión se realizó correctamente.
     */
    if (!$db_conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    /*
     * Configurar la conexión para trabajar correctamente
     * con tildes, eñes y otros caracteres UTF-8.
     */
    $db_conexion->set_charset("utf8mb4");

    try {

        /*
         * AGREGAR EMPLEADO
         */
        if (isset($_POST["btn_agregar"])) {

            $sql = "INSERT INTO empleados
                    (
                        codigo,
                        nombres,
                        apellidos,
                        direccion,
                        telefono,
                        fecha_nacimiento,
                        id_puesto
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            $parametros = $db_conexion->prepare($sql);

            $parametros->bind_param(
                "ssssssi",
                $txt_codigo,
                $txt_nombres,
                $txt_apellidos,
                $txt_direccion,
                $txt_telefono,
                $txt_fn,
                $drop_puesto
            );

            $parametros->execute();
        }

        /*
         * MODIFICAR EMPLEADO
         */
        elseif (isset($_POST["btn_modificar"])) {

            $sql = "UPDATE empleados
                    SET codigo = ?,
                        nombres = ?,
                        apellidos = ?,
                        direccion = ?,
                        telefono = ?,
                        fecha_nacimiento = ?,
                        id_puesto = ?
                    WHERE id_empleado = ?";

            $parametros = $db_conexion->prepare($sql);

            $parametros->bind_param(
                "ssssssii",
                $txt_codigo,
                $txt_nombres,
                $txt_apellidos,
                $txt_direccion,
                $txt_telefono,
                $txt_fn,
                $drop_puesto,
                $txt_id
            );

            $parametros->execute();
        }

        /*
         * ELIMINAR EMPLEADO
         */
        elseif (isset($_POST["btn_eliminar"])) {

            $sql = "DELETE FROM empleados
                    WHERE id_empleado = ?";

            $parametros = $db_conexion->prepare($sql);

            $parametros->bind_param(
                "i",
                $txt_id
            );

            $parametros->execute();
        }

        /*
         * Cerrar la consulta preparada si fue creada.
         */
        if (isset($parametros)) {
            $parametros->close();
        }

        $db_conexion->close();

        header("Location: /php_empresa");
        exit;

    } catch (mysqli_sql_exception $error) {

        echo "Ocurrió un error al realizar la operación: "
            . $error->getMessage();

        $db_conexion->close();
    }
}
?>

