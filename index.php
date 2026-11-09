<?php

/*
 * index.php
 * --------------------------------------------------
 * Tablero principal de la aplicación.
 *
 * Esta página está protegida:
 * solamente pueden entrar usuarios con sesión activa.
 *
 * Aquí el usuario puede:
 *
 * - Ver sus notas.
 * - Crear una nueva nota.
 * - Editar notas.
 * - Eliminar notas.
 * - Consultar el tipo de cambio.
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
 * Proteger la página.
 *
 * Si no existe una sesión activa,
 * protegerPagina() envía al usuario al login.
 */

protegerPagina();


/*
 * Obtener el ID del usuario
 * que actualmente inició sesión.
 */

$usuarioId = usuarioActual();


/*
 * Obtener solamente las notas
 * pertenecientes al usuario actual.
 *
 * Utilizamos prepare() + execute().
 */

$sql = "
    SELECT
        id,
        titulo,
        contenido,
        creado_en,
        actualizado_en

    FROM notas

    WHERE usuario_id = ?

    ORDER BY actualizado_en DESC
";


$stmt = $pdo->prepare($sql);


$stmt->execute([
    $usuarioId
]);


/*
 * Obtener todas las notas encontradas.
 */

$notas = $stmt->fetchAll();


/*
 * Cargar encabezado.
 */

require_once "includes/header.php";


/*
 * Mostrar mensajes flash.
 */

mostrarFlash();

?>


<!-- ENCABEZADO DEL TABLERO -->

<div
    class="d-flex
        justify-content-between
        align-items-center
        flex-wrap
        gap-3
        mb-4">


    <div>

        <h1 class="mb-1">

            Mi tablero

        </h1>


        <p class="text-muted mb-0">

            Gestiona tus notas personales.

        </p>

    </div>


    <!-- Botón para crear una nota -->

    <a
        href="nota_guardar.php"
        class="btn btn-success">

        + Nueva nota

    </a>

</div>


<!-- ============================================= -->
<!-- WIDGET DE TIPO DE CAMBIO                      -->
<!-- ============================================= -->

<div class="card shadow-sm mb-4">


    <div class="card-body">


        <h2 class="h5 mb-3">

            Tipo de cambio

        </h2>


        <!--
            JavaScript colocará aquí
            el resultado de la API.
        -->

        <div id="tipoCambio">


            <div
                class="spinner-border
                    spinner-border-sm"
                role="status">

            </div>


            Consultando tipo de cambio...


        </div>


    </div>


</div>


<!-- ============================================= -->
<!-- LISTA DE NOTAS                                -->
<!-- ============================================= -->

<div class="row">


    <!-- Si el usuario todavía no tiene notas -->

    <?php if (count($notas) === 0): ?>


        <div class="col-12">


            <div class="alert alert-info">

                No tienes notas todavía.

                Presiona

                <strong>
                    Nueva nota
                </strong>

                para crear la primera.

            </div>


        </div>


    <?php endif; ?>


    <!-- Recorrer todas las notas -->

    <?php foreach ($notas as $nota): ?>


        <div
            class="col-12
                col-md-6
                col-lg-4
                mb-4">


            <div
                class="card
                    nota-card
                    h-100
                    shadow-sm">


                <!-- Contenido de la nota -->

                <div class="card-body">


                    <h2 class="h5">


                        <?= htmlspecialchars(
                            $nota["titulo"]
                        ) ?>


                    </h2>


                    <p class="nota-contenido">


                        <?= nl2br(
                            htmlspecialchars(
                                $nota["contenido"]
                            )
                        ) ?>


                    </p>


                    <small class="text-muted">

                        Actualizada:

                        <?= htmlspecialchars(
                            $nota["actualizado_en"]
                        ) ?>

                    </small>


                </div>


                <!-- Botones -->

                <div
                    class="card-footer
                        bg-white
                        border-0
                        pb-3">


                    <!-- BOTÓN EDITAR -->

                    <a
                        href="nota_guardar.php?id=<?= (int) $nota["id"] ?>"
                        class="btn
                            btn-warning
                            btn-sm">

                        Editar

                    </a>


                    <!-- BOTÓN ELIMINAR -->

                    <form
                        method="POST"
                        action="nota_eliminar.php"
                        class="d-inline"
                        onsubmit="
                            return confirm(
                                '¿Está seguro de eliminar esta nota?'
                            );
                        ">


                        <!--
                            El ID de la nota se manda
                            de forma oculta por POST.
                        -->

                        <input
                            type="hidden"
                            name="id"
                            value="<?= (int) $nota["id"] ?>">


                        <button
                            type="submit"
                            class="btn
                                   btn-danger
                                   btn-sm">

                            Eliminar

                        </button>


                    </form>


                </div>


            </div>


        </div>


    <?php endforeach; ?>


</div>


<!-- ============================================= -->
<!-- JAVASCRIPT PARA EL TIPO DE CAMBIO             -->
<!-- ============================================= -->

<script>

/*
 * fetch() consulta nuestro propio endpoint PHP:
 *
 * api/tipocambio.php
 */

fetch("api/tipocambio.php")


    /*
     * Convertir la respuesta a JSON.
     */

    .then(function(response) {

        return response.json();

    })


    /*
     * Procesar los datos recibidos.
     */

    .then(function(data) {


        /*
         * Si nuestro endpoint devuelve
         * un error, detener el proceso.
         */

        if (data.ok !== true) {

            throw new Error(
                data.error
            );

        }


        /*
         * Mostrar USD y EUR en pantalla.
         */

        document.getElementById(
            "tipoCambio"
        ).innerHTML = `


            <div class="row g-3">


                <!-- DÓLAR -->

                <div class="col-md-6">


                    <div class="exchange-box">


                        <span class="exchange-title">

                            Dólar estadounidense

                        </span>


                        <strong>

                            ₡${Number(
                                data.USD
                            ).toFixed(2)}

                        </strong>


                        <small>

                            1 USD

                        </small>


                    </div>


                </div>


                <!-- EURO -->

                <div class="col-md-6">


                    <div class="exchange-box">


                        <span class="exchange-title">

                            Euro

                        </span>


                        <strong>

                            ₡${Number(
                                data.EUR
                            ).toFixed(2)}

                        </strong>


                        <small>

                            1 EUR

                        </small>


                    </div>


                </div>


            </div>


            <!-- Fecha -->

            <p
                class="small
                    text-muted
                    mt-3
                    mb-0">

                Última actualización:

                ${data.fecha}

            </p>

        `;

    })


    /*
     * Si ocurre algún error,
     * mostrar un mensaje.
     */

    .catch(function(error) {


        document.getElementById(
            "tipoCambio"
        ).innerHTML = `


            <div
                class="alert
                    alert-warning
                    mb-0">

                No se pudo obtener
                el tipo de cambio.

            </div>


        `;


        console.error(
            "Error del tipo de cambio:",
            error
        );


    });

</script>


<?php

/*
 * Cargar footer.
 */

require_once "includes/footer.php";

?>