<?php

/*
 * login.php
 * --------------------------------------------------
 * Autenticación de usuarios utilizando sesiones PHP.
 *
 * Solo los usuarios registrados en la base de datos
 * pueden acceder al tablero.
 * --------------------------------------------------
 */

/*
 * session_start() siempre debe ir primero.
 */
session_start();


/*
 * Incluir conexión a la base de datos
 * y funciones reutilizables.
 */
require_once "includes/db.php";
require_once "includes/funciones.php";


/*
 * Si el usuario ya inició sesión,
 * lo enviamos directamente al tablero.
 */
if (isset($_SESSION["usuario_id"])) {

    header("Location: index.php");
    exit;

}


/*
 * Variables iniciales.
 */
$error = "";
$correo = "";


/*
 * Procesar el formulario cuando se envía por POST.
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {


    /*
     * Obtener los datos enviados.
     */
    $correo = trim($_POST["correo"] ?? "");
    $clave = $_POST["clave"] ?? "";


    /*
     * Validar campos vacíos.
     */
    if ($correo === "" || $clave === "") {

        $error = "Debe completar todos los campos.";

    }


    /*
     * Validar formato del correo.
     */
    elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $error = "Ingrese un correo electrónico válido.";

    }


    else {


        /*
         * Buscar al usuario en la base de datos.
         *
         * Se utiliza prepare() + execute()
         * como solicita el proyecto.
         */
        $sql = "
            SELECT
                id,
                nombre,
                correo,
                clave

            FROM usuarios

            WHERE correo = ?

            LIMIT 1
        ";


        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $correo
        ]);


        $usuario = $stmt->fetch();


        /*
         * Verificar si existe el usuario
         * y si la contraseña es correcta.
         */
        if (
            $usuario &&
            verificarClave(
                $clave,
                $usuario["clave"]
            )
        ) {


            /*
             * Regenerar el ID de sesión
             * después de un login correcto.
             */
            session_regenerate_id(true);


            /*
             * Guardar datos del usuario
             * dentro de la sesión.
             */
            $_SESSION["usuario_id"] =
                $usuario["id"];

            $_SESSION["usuario_nombre"] =
                $usuario["nombre"];

            $_SESSION["usuario_correo"] =
                $usuario["correo"];


            /*
             * Crear mensaje flash.
             */
            mensajeFlash(
                "success",
                "Bienvenido al Tablero Personal."
            );


            /*
             * POST → Redirect → GET
             */
            header("Location: index.php");
            exit;

        }


        else {

            $error =
                "Correo o contraseña incorrectos.";

        }

    }

}


/*
 * Cargar encabezado.
 */
require_once "includes/header.php";

?>


<div class="row justify-content-center">

    <div class="col-12 col-md-6 col-lg-5">

        <div class="card shadow-lg">

            <div class="card-body p-4">


                <h1 class="h3 text-center mb-4">

                    Iniciar sesión

                </h1>


                <!-- Mostrar errores -->

                <?php if ($error !== ""): ?>

                    <div class="alert alert-danger">

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>


                <!-- Formulario de login -->

                <form
                    method="POST"
                    action="login.php">


                    <!-- Correo -->

                    <div class="mb-3">

                        <label
                            for="correo"
                            class="form-label">

                            Correo electrónico

                        </label>


                        <input
                            type="email"
                            id="correo"
                            name="correo"
                            class="form-control"
                            value="<?= htmlspecialchars($correo) ?>"
                            required>

                    </div>


                    <!-- Contraseña -->

                    <div class="mb-3">

                        <label
                            for="clave"
                            class="form-label">

                            Contraseña

                        </label>


                        <input
                            type="password"
                            id="clave"
                            name="clave"
                            class="form-control"
                            required>

                    </div>


                    <!-- Botón ingresar -->

                    <button
                        type="submit"
                        class="btn btn-primary w-100">

                        Ingresar

                    </button>


                </form>


                <!-- Usuario de prueba -->

                <div class="text-center mt-4">

                    <small class="text-muted">

                        <strong>
                            Usuario de prueba
                        </strong>

                        <br>

                        Correo:
                        usuario@gmail.com

                        <br>

                        Contraseña:
                        123456

                    </small>

                </div>


            </div>

        </div>

    </div>

</div>


<?php

/*
 * Cargar footer.
 */
require_once "includes/footer.php";

?>