<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include("datos_conexion.php");

/*
 * Función para mostrar contenido de forma segura en HTML.
 */
function escapar(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, "UTF-8");
}

/*
 * Abrir una sola conexión para cargar puestos y empleados.
 */
$db_conexion = mysqli_connect(
    $db_host,
    $db_usr,
    $db_pass,
    $db_nombre
);

$db_conexion->set_charset("utf8mb4");

/*
 * Cargar puestos.
 */
$sql_puestos = "SELECT id_puesto, puesto
                FROM puestos
                ORDER BY puesto";

$resultado_puestos = $db_conexion->query($sql_puestos);

/*
 * Cargar empleados.
 */
$sql_empleados = "SELECT
                    e.id_empleado,
                    e.codigo,
                    e.nombres,
                    e.apellidos,
                    e.direccion,
                    e.telefono,
                    e.fecha_nacimiento,
                    e.id_puesto,
                    p.puesto
                  FROM empleados AS e
                  INNER JOIN puestos AS p
                    ON e.id_puesto = p.id_puesto
                  ORDER BY e.id_empleado DESC";

$resultado_empleados = $db_conexion->query($sql_empleados);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Gestión de empleados</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous"
    >

    <style>
        body {
            background-color: #f4f6f9;
        }

        .page-header {
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
        }

        .card {
            border: 0;
            box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.08);
        }

        .table tbody tr {
            cursor: pointer;
        }

        .table tbody tr:hover {
            background-color: #eef4ff;
        }

        .required::after {
            content: " *";
            color: #dc3545;
        }
    </style>
</head>

<body>

<header class="page-header py-4 mb-4">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="h3 mb-1">Gestión de empleados</h1>
                <p class="text-secondary mb-0">
                    Agregue, modifique o elimine registros de empleados.
                </p>
            </div>

            <button
                type="button"
                id="btn_nuevo"
                class="btn btn-primary"
            >
                Nuevo empleado
            </button>
        </div>
    </div>
</header>

<main class="container pb-5">

    <div class="card">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <h2 class="h5 mb-0">Listado de empleados</h2>

                <input
                    type="search"
                    id="txt_buscar"
                    class="form-control"
                    style="max-width: 320px;"
                    placeholder="Buscar empleado..."
                    aria-label="Buscar empleado"
                >
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Código</th>
                            <th scope="col">Nombres</th>
                            <th scope="col">Apellidos</th>
                            <th scope="col">Dirección</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col">Nacimiento</th>
                            <th scope="col">Puesto</th>
                        </tr>
                    </thead>

                    <tbody id="tbl_empleados">
                    <?php if ($resultado_empleados->num_rows > 0): ?>

                        <?php while ($fila = $resultado_empleados->fetch_assoc()): ?>
                            <tr
                                data-id="<?= (int) $fila["id_empleado"] ?>"
                                data-id-puesto="<?= (int) $fila["id_puesto"] ?>"
                                data-codigo="<?= escapar($fila["codigo"]) ?>"
                                data-nombres="<?= escapar($fila["nombres"]) ?>"
                                data-apellidos="<?= escapar($fila["apellidos"]) ?>"
                                data-direccion="<?= escapar($fila["direccion"]) ?>"
                                data-telefono="<?= escapar($fila["telefono"]) ?>"
                                data-nacimiento="<?= escapar($fila["fecha_nacimiento"]) ?>"
                            >
                                <td><?= escapar($fila["codigo"]) ?></td>
                                <td><?= escapar($fila["nombres"]) ?></td>
                                <td><?= escapar($fila["apellidos"]) ?></td>
                                <td><?= escapar($fila["direccion"]) ?></td>
                                <td><?= escapar($fila["telefono"]) ?></td>
                                <td><?= escapar($fila["fecha_nacimiento"]) ?></td>
                                <td><?= escapar($fila["puesto"]) ?></td>
                            </tr>
                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr id="fila_sin_registros">
                            <td colspan="7" class="text-center text-secondary py-4">
                                No hay empleados registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white text-secondary small">
            Seleccione una fila para modificar o eliminar un empleado.
        </div>
    </div>

</main>

<div
    class="modal fade"
    id="modal_empleados"
    tabindex="-1"
    aria-labelledby="titulo_modal_empleados"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">

            <form
                id="form_empleados"
                class="needs-validation"
                action="crud_empleado.php"
                method="post"
                novalidate
            >
                <div class="modal-header">
                    <div>
                        <h2
                            class="modal-title fs-5"
                            id="titulo_modal_empleados"
                        >
                            Nuevo empleado
                        </h2>

                        <small class="text-secondary">
                            Los campos marcados con * son obligatorios.
                        </small>
                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"
                    ></button>
                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label for="txt_id" class="form-label">
                                ID
                            </label>

                            <input
                                type="number"
                                name="txt_id"
                                id="txt_id"
                                class="form-control"
                                value="0"
                                readonly
                            >
                        </div>

                        <div class="col-md-4">
                            <label
                                for="txt_codigo"
                                class="form-label required"
                            >
                                Código
                            </label>

                            <input
                                type="text"
                                name="txt_codigo"
                                id="txt_codigo"
                                class="form-control"
                                placeholder="Ejemplo: E001"
                                maxlength="10"
                                pattern="[A-Za-z0-9-]{2,10}"
                                required
                            >

                            <div class="valid-feedback">
                                Código válido.
                            </div>

                            <div class="invalid-feedback">
                                Ingrese un código de 2 a 10 caracteres usando letras, números o guiones.
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label
                                for="txt_nombres"
                                class="form-label required"
                            >
                                Nombres
                            </label>

                            <input
                                type="text"
                                name="txt_nombres"
                                id="txt_nombres"
                                class="form-control"
                                placeholder="Ejemplo: Ana Lucía"
                                maxlength="100"
                                minlength="2"
                                required
                            >

                            <div class="valid-feedback">
                                Nombres válidos.
                            </div>

                            <div class="invalid-feedback">
                                Ingrese los nombres del empleado.
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label
                                for="txt_apellidos"
                                class="form-label required"
                            >
                                Apellidos
                            </label>

                            <input
                                type="text"
                                name="txt_apellidos"
                                id="txt_apellidos"
                                class="form-control"
                                placeholder="Ejemplo: López Pérez"
                                maxlength="100"
                                minlength="2"
                                required
                            >

                            <div class="valid-feedback">
                                Apellidos válidos.
                            </div>

                            <div class="invalid-feedback">
                                Ingrese los apellidos del empleado.
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label
                                for="txt_direccion"
                                class="form-label"
                            >
                                Dirección
                            </label>

                            <input
                                type="text"
                                name="txt_direccion"
                                id="txt_direccion"
                                class="form-control"
                                placeholder="Ejemplo: Zona 1, Ciudad"
                                maxlength="200"
                            >

                            <div class="valid-feedback">
                                Dirección válida.
                            </div>

                            <div class="invalid-feedback">
                                Revise la dirección ingresada.
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label
                                for="txt_telefono"
                                class="form-label"
                            >
                                Teléfono
                            </label>

                            <input
                                type="tel"
                                name="txt_telefono"
                                id="txt_telefono"
                                class="form-control"
                                placeholder="Ejemplo: 55551234"
                                maxlength="8"
                                minlength="8"
                                pattern="[0-9]{8}"
                                inputmode="numeric"
                            >

                            <div class="valid-feedback">
                                Teléfono válido.
                            </div>

                            <div class="invalid-feedback">
                                Ingrese un número de teléfono de 8 dígitos.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label
                                for="txt_fn"
                                class="form-label required"
                            >
                                Fecha de nacimiento
                            </label>

                            <input
                                type="date"
                                name="txt_fn"
                                id="txt_fn"
                                class="form-control"
                                required
                            >

                            <div class="valid-feedback">
                                Fecha válida.
                            </div>

                            <div class="invalid-feedback">
                                Seleccione la fecha de nacimiento.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label
                                for="drop_puesto"
                                class="form-label required"
                            >
                                Puesto
                            </label>

                            <select
                                class="form-select"
                                name="drop_puesto"
                                id="drop_puesto"
                                required
                            >
                                <option value="" selected disabled>
                                    Seleccione un puesto
                                </option>

                                <?php while ($puesto = $resultado_puestos->fetch_assoc()): ?>
                                    <option value="<?= (int) $puesto["id_puesto"] ?>">
                                        <?= escapar($puesto["puesto"]) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>

                            <div class="valid-feedback">
                                Puesto seleccionado.
                            </div>

                            <div class="invalid-feedback">
                                Seleccione un puesto válido.
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        name="btn_eliminar"
                        id="btn_eliminar"
                        class="btn btn-danger d-none"
                        formnovalidate
                    >
                        Eliminar
                    </button>

                    <button
                        type="submit"
                        name="btn_modificar"
                        id="btn_modificar"
                        class="btn btn-success d-none"
                    >
                        Guardar cambios
                    </button>

                    <button
                        type="submit"
                        name="btn_agregar"
                        id="btn_agregar"
                        class="btn btn-primary"
                    >
                        Agregar empleado
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"
></script>

<script>
    "use strict";

    const modalElemento = document.getElementById("modal_empleados");
    const modalEmpleados = new bootstrap.Modal(modalElemento);

    const formulario = document.getElementById("form_empleados");
    const tituloModal = document.getElementById("titulo_modal_empleados");

    const btnNuevo = document.getElementById("btn_nuevo");
    const btnAgregar = document.getElementById("btn_agregar");
    const btnModificar = document.getElementById("btn_modificar");
    const btnEliminar = document.getElementById("btn_eliminar");

    const tablaEmpleados = document.getElementById("tbl_empleados");
    const campoBuscar = document.getElementById("txt_buscar");

    function prepararNuevoEmpleado() {
        formulario.reset();
        formulario.classList.remove("was-validated");

        document.getElementById("txt_id").value = 0;

        tituloModal.textContent = "Nuevo empleado";

        btnAgregar.classList.remove("d-none");
        btnModificar.classList.add("d-none");
        btnEliminar.classList.add("d-none");

        modalEmpleados.show();
    }

    function prepararEdicionEmpleado(fila) {
        formulario.classList.remove("was-validated");

        document.getElementById("txt_id").value = fila.dataset.id;
        document.getElementById("txt_codigo").value = fila.dataset.codigo;
        document.getElementById("txt_nombres").value = fila.dataset.nombres;
        document.getElementById("txt_apellidos").value = fila.dataset.apellidos;
        document.getElementById("txt_direccion").value = fila.dataset.direccion;
        document.getElementById("txt_telefono").value = fila.dataset.telefono;
        document.getElementById("txt_fn").value = fila.dataset.nacimiento;
        document.getElementById("drop_puesto").value = fila.dataset.idPuesto;

        tituloModal.textContent = "Editar empleado";

        btnAgregar.classList.add("d-none");
        btnModificar.classList.remove("d-none");
        btnEliminar.classList.remove("d-none");

        modalEmpleados.show();
    }

    btnNuevo.addEventListener("click", prepararNuevoEmpleado);

    tablaEmpleados.addEventListener("click", function (evento) {
        const fila = evento.target.closest("tr[data-id]");

        if (fila) {
            prepararEdicionEmpleado(fila);
        }
    });

    formulario.addEventListener("submit", function (evento) {
        const botonPresionado = evento.submitter;

        /*
         * La eliminación solamente necesita un ID válido.
         * Los demás campos no se validan para esta operación.
         */
        if (
            botonPresionado &&
            botonPresionado.name === "btn_eliminar"
        ) {
            return;
        }

        if (!formulario.checkValidity()) {
            evento.preventDefault();
            evento.stopPropagation();
        }

        formulario.classList.add("was-validated");
    });

    btnEliminar.addEventListener("click", function (evento) {
        const confirmar = window.confirm(
            "¿Está seguro de que desea eliminar este empleado?"
        );

        if (!confirmar) {
            evento.preventDefault();
        }
    });

    campoBuscar.addEventListener("input", function () {
        const texto = campoBuscar.value.toLowerCase().trim();
        const filas = tablaEmpleados.querySelectorAll("tr[data-id]");

        filas.forEach(function (fila) {
            const contenido = fila.textContent.toLowerCase();

            fila.classList.toggle(
                "d-none",
                !contenido.includes(texto)
            );
        });
    });

    modalElemento.addEventListener("shown.bs.modal", function () {
        document.getElementById("txt_codigo").focus();
    });
</script>

</body>
</html>
<?php
$resultado_puestos->free();
$resultado_empleados->free();
$db_conexion->close();
?>