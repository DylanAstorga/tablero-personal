<?php

/*
 * funciones.php
 * --------------------------------------------------
 * Contiene funciones reutilizables para diferentes
 * partes de la aplicación.
 * --------------------------------------------------
 */


/*
 * protegerPagina()
 * --------------------------------------------------
 * Comprueba que el usuario haya iniciado sesión.
 * Si no existe sesión activa, lo envía al login.
 * --------------------------------------------------
 */

function protegerPagina()
{
    if (!isset($_SESSION["usuario_id"])) {

        header("Location: login.php");

        exit;
    }
}


/*
 * usuarioActual()
 * --------------------------------------------------
 * Devuelve el ID del usuario que inició sesión.
 * --------------------------------------------------
 */

function usuarioActual()
{
    return $_SESSION["usuario_id"] ?? null;
}


/*
 * mensajeFlash()
 * --------------------------------------------------
 * Guarda un mensaje temporal dentro de la sesión.
 *
 * Tipos comunes:
 * success
 * danger
 * warning
 * info
 * --------------------------------------------------
 */

function mensajeFlash($tipo, $mensaje)
{
    $_SESSION["flash"] = [
        "tipo" => $tipo,
        "mensaje" => $mensaje
    ];
}


/*
 * mostrarFlash()
 * --------------------------------------------------
 * Muestra el mensaje guardado y después lo elimina
 * para que solamente aparezca una vez.
 * --------------------------------------------------
 */

function mostrarFlash()
{
    if (isset($_SESSION["flash"])) {

        $flash = $_SESSION["flash"];

        echo '
        <div class="alert alert-' .
        htmlspecialchars($flash["tipo"]) .
        ' alert-dismissible fade show" role="alert">

            ' . htmlspecialchars($flash["mensaje"]) . '

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>
        ';

        unset($_SESSION["flash"]);
    }
}


/*
 * verificarClave()
 * --------------------------------------------------
 * Compara la contraseña ingresada por el usuario
 * con el hash almacenado en la base de datos.
 *
 * Se usa password_verify(), que trabaja con hashes
 * generados por password_hash() (bcrypt). Es la forma
 * recomendada en PHP para guardar contraseñas, porque
 * cada hash lleva su propia "sal" y es lento de atacar.
 * --------------------------------------------------
 */

function verificarClave($claveIngresada, $claveGuardada)
{
    return password_verify(
        $claveIngresada,
        $claveGuardada
    );
}

?>