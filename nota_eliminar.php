<?php

/*
 * nota_eliminar.php
 * --------------------------------------------------
 * Este archivo elimina una nota.
 *
 * Requisitos que cumple:
 *
 * - Solo permite eliminar mediante POST.
 * - Verifica que el usuario tenga sesión activa.
 * - Verifica que la nota pertenezca al usuario.
 * - Utiliza prepare() + execute().
 * - Utiliza mensajes flash.
 * - Aplica POST → Redirect → GET.
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
 * el usuario será enviado al login.
 */

protegerPagina();


/*
 * Solo procesamos la eliminación
 * si la solicitud fue enviada por POST.
 */

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    /*
     * Obtener el ID de la nota.
     */

    $id = isset($_POST["id"])
        ? (int) $_POST["id"]
        : 0;


    /*
     * Validar que el ID sea correcto.
     */

    if ($id > 0) {


        /*
         * Eliminar la nota.
         *
         * También verificamos usuario_id
         * para evitar que un usuario pueda
         * eliminar notas de otra persona.
         */

        $sql = "
            DELETE FROM notas

            WHERE id = ?

            AND usuario_id = ?
        ";


        /*
         * Preparar consulta.
         */

        $stmt = $pdo->prepare($sql);


        /*
         * Ejecutar consulta.
         */

        $stmt->execute([

            $id,

            usuarioActual()

        ]);


        /*
         * Verificar si se eliminó una nota.
         */

        if ($stmt->rowCount() > 0) {


            /*
             * Mensaje de éxito.
             */

            mensajeFlash(
                "success",
                "Nota eliminada correctamente."
            );


        } else {


            /*
             * Si no se encontró la nota
             * o pertenece a otro usuario.
             */

            mensajeFlash(
                "danger",
                "La nota no existe o no le pertenece."
            );


        }


    } else {


        /*
         * ID inválido.
         */

        mensajeFlash(
            "danger",
            "No se pudo eliminar la nota."
        );

    }

}


/*
 * POST → Redirect → GET
 *
 * Siempre regresamos al tablero.
 */

header("Location: index.php");

exit;

?>