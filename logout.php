<?php

/*
 * logout.php
 * --------------------------------------------------
 * Este archivo cierra la sesión del usuario.
 *
 * Cumple con los 3 pasos requeridos:
 *
 * 1. Vaciar $_SESSION.
 * 2. Eliminar la cookie de sesión.
 * 3. Destruir la sesión.
 * --------------------------------------------------
 */


/*
 * session_start() debe ir primero.
 */

session_start();


/*
 * PASO 1
 * Vaciar todas las variables de sesión.
 */

$_SESSION = [];


/*
 * PASO 2
 * Eliminar la cookie de sesión.
 */

if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );

}


/*
 * PASO 3
 * Destruir completamente la sesión.
 */

session_destroy();


/*
 * Redirigir al login.
 */

header("Location: login.php");

exit;

?>