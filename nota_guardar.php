<?php

/*
 * nota_guardar.php
 * --------------------------------------------------
 * Este archivo permite:
 *
 * 1. Crear nuevas notas.
 * 2. Editar notas existentes.
 *
 * Todas las notas quedan asociadas al usuario
 * que actualmente tiene la sesión iniciada.
 *
 * Para guardar cambios se utiliza POST.
 * Después se aplica:
 *
 * POST → Redirect → GET
 * --------------------------------------------------
 */


/*
 * session_start() debe ir primero.
 */

session_start();


/*
 * Incluir conexión y funciones.
 */

require_once "includes/db.php";
require_once "includes/funciones.php";


/*
 * Proteger esta página.
 *
 * Si el usuario no inició sesión,
 * será enviado al login.
 */

protegerPagina();


/*
 * Obtener ID del usuario actual.
 */

$usuarioId = usuarioActual();


/*
 * Variables iniciales del formulario.
 */

$id = 0;

$titulo = "";

$contenido = "";

$error = "";

$esEdicion = false;


/*
 * ==================================================
 * GET
 * ==================================================
 *
 * Cuando entramos mediante GET:
 *
 * nota_guardar.php
 *
 * significa crear una nota.
 *
 * Si entramos:
 *
 * nota_guardar.php?id=5
 *
 * significa editar la nota número 5.
 * ==================================================
 */

if ($_SERVER["REQUEST_METHOD"] === "GET") {


    /*
     * Obtener ID si existe.
     */

    $id = isset($_GET["id"])
        ? (int) $_GET["id"]
        : 0;


    /*
     * Si tenemos ID,
     * estamos editando.
     */

    if ($id > 0) {


        /*
         * Buscar la nota.
         *
         * También verificamos que pertenezca
         * al usuario que inició sesión.
         */

        $sql = "
            SELECT
                id,
                titulo,
                contenido

            FROM notas

            WHERE id = ?

            AND usuario_id = ?

            LIMIT 1
        ";


        $stmt = $pdo->prepare($sql);


        $stmt->execute([
            $id,
            $usuarioId
        ]);


        $nota = $stmt->fetch();


        /*
         * Si la nota no existe
         * o pertenece a otro usuario.
         */

        if (!$nota) {


            mensajeFlash(
                "danger",
                "La nota no existe o no le pertenece."
            );


            header(
                "Location: index.php"
            );


            exit;

        }


        /*
         * Colocar los datos existentes
         * dentro del formulario.
         */

        $titulo =
            $nota["titulo"];


        $contenido =
            $nota["contenido"];


        $esEdicion =
            true;

    }

}


/*
 * ==================================================
 * POST
 * ==================================================
 *
 * Cuando el formulario se envía,
 * aquí creamos o actualizamos la nota.
 * ==================================================
 */

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    /*
     * Obtener ID.
     *
     * Si no existe ID = crear.
     * Si existe ID = editar.
     */

    $id = isset($_POST["id"])
        ? (int) $_POST["id"]
        : 0;


    /*
     * Obtener título.
     */

    $titulo = trim(
        $_POST["titulo"] ?? ""
    );


    /*
     * Obtener contenido.
     */

    $contenido = trim(
        $_POST["contenido"] ?? ""
    );


    /*
     * Determinar si estamos editando.
     */

    $esEdicion =
        $id > 0;


    /*
     * ----------------------------------------------
     * VALIDACIONES
     * ----------------------------------------------
     */

    if (
        $titulo === ""
        ||
        $contenido === ""
    ) {


        $error =
            "El título y el contenido son obligatorios.";


    }


    elseif (
        strlen($titulo) > 200
    ) {


        $error =
            "El título no puede superar los 200 caracteres.";


    }


    /*
     * ----------------------------------------------
     * EDITAR NOTA
     * ----------------------------------------------
     */

    elseif ($id > 0) {


        /*
         * Primero verificamos que la nota
         * realmente pertenezca al usuario.
         */

        $sqlVerificar = "
            SELECT id

            FROM notas

            WHERE id = ?

            AND usuario_id = ?

            LIMIT 1
        ";


        $stmtVerificar =
            $pdo->prepare(
                $sqlVerificar
            );


        $stmtVerificar->execute([
            $id,
            $usuarioId
        ]);


        $notaExiste =
            $stmtVerificar->fetch();


        /*
         * Si no existe o pertenece
         * a otro usuario.
         */

        if (!$notaExiste) {


            mensajeFlash(
                "danger",
                "La nota no existe o no le pertenece."
            );


            header(
                "Location: index.php"
            );


            exit;

        }


        /*
         * Actualizar nota.
         */

        $sql = "
            UPDATE notas

            SET
                titulo = ?,

                contenido = ?,

                actualizado_en =
                    CURRENT_TIMESTAMP

            WHERE id = ?

            AND usuario_id = ?
        ";


        $stmt =
            $pdo->prepare(
                $sql
            );


        $stmt->execute([

            $titulo,

            $contenido,

            $id,

            $usuarioId

        ]);


        /*
         * Guardar mensaje flash.
         */

        mensajeFlash(
            "success",
            "Nota actualizada correctamente."
        );


        /*
         * POST → Redirect → GET
         */

        header(
            "Location: index.php"
        );


        exit;

    }


    /*
     * ----------------------------------------------
     * CREAR NOTA
     * ----------------------------------------------
     */

    else {


        /*
         * Insertar nueva nota.
         *
         * Se asocia automáticamente
         * con el usuario actual.
         */

        $sql = "
            INSERT INTO notas
            (
                usuario_id,
                titulo,
                contenido
            )

            VALUES
            (
                ?,
                ?,
                ?
            )
        ";


        $stmt =
            $pdo->prepare(
                $sql
            );


        $stmt->execute([

            $usuarioId,

            $titulo,

            $contenido

        ]);


        /*
         * Crear mensaje flash.
         */

        mensajeFlash(
            "success",
            "Nota creada correctamente."
        );


        /*
         * POST → Redirect → GET
         */

        header(
            "Location: index.php"
        );


        exit;

    }

}


/*
 * Mostrar encabezado.
 */

require_once "includes/header.php";

?>


<!-- ================================================= -->
<!-- FORMULARIO DE NOTA                                -->
<!-- ================================================= -->


<div class="row justify-content-center">


    <div class="col-12 col-lg-8">


        <div class="card shadow-sm">


            <div class="card-body p-4">


                <!--
                    Cambiar el título dependiendo
                    de si estamos creando o editando.
                -->


                <h1 class="h3 mb-4">


                    <?php if ($esEdicion): ?>


                        Editar nota


                    <?php else: ?>


                        Nueva nota


                    <?php endif; ?>


                </h1>


                <!-- Mostrar errores -->


                <?php if ($error !== ""): ?>


                    <div class="alert alert-danger">


                        <?= htmlspecialchars(
                            $error
                        ) ?>


                    </div>


                <?php endif; ?>


                <!-- FORMULARIO -->


                <form
                    method="POST"
                    action="nota_guardar.php">


                    <!--
                        Si estamos editando,
                        enviar el ID de forma oculta.
                    -->


                    <?php if ($esEdicion): ?>


                        <input
                            type="hidden"
                            name="id"
                            value="<?= (int) $id ?>">


                    <?php endif; ?>


                    <!-- TÍTULO -->


                    <div class="mb-3">


                        <label
                            for="titulo"
                            class="form-label">

                            Título

                        </label>


                        <input
                            type="text"
                            id="titulo"
                            name="titulo"
                            class="form-control"
                            maxlength="200"
                            value="<?= htmlspecialchars(
                                $titulo
                            ) ?>"
                            required>


                    </div>


                    <!-- CONTENIDO -->


                    <div class="mb-3">


                        <label
                            for="contenido"
                            class="form-label">

                            Contenido

                        </label>


                        <textarea
                            id="contenido"
                            name="contenido"
                            class="form-control"
                            rows="8"
                            required><?= htmlspecialchars(
                                $contenido
                            ) ?></textarea>


                    </div>


                    <!-- BOTÓN GUARDAR -->


                    <button
                        type="submit"
                        class="btn btn-primary">


                        <?php if ($esEdicion): ?>


                            Actualizar nota


                        <?php else: ?>


                            Guardar nota


                        <?php endif; ?>


                    </button>


                    <!-- BOTÓN CANCELAR -->


                    <a
                        href="index.php"
                        class="btn btn-secondary">

                        Cancelar

                    </a>


                </form>


            </div>


        </div>


    </div>


</div>


<?php

/*
 * Mostrar footer.
 */

require_once "includes/footer.php";

?>