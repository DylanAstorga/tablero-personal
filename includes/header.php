<?php

/*
 * header.php
 * --------------------------------------------------
 * Contiene la apertura del documento HTML,
 * Bootstrap, el CSS personalizado y la barra
 * de navegación.
 * --------------------------------------------------
 */

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Tablero Personal</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- CSS personalizado -->
    <link
        rel="stylesheet"
        href="css/estilos.css">

</head>

<body>


<nav class="navbar navbar-expand-lg navbar-dark bg-primary">

    <div class="container">

        <a
            class="navbar-brand fw-bold"
            href="index.php">

            Tablero Personal

        </a>


        <?php if (isset($_SESSION["usuario_id"])): ?>

            <div class="d-flex align-items-center gap-3">

                <span class="text-white">

                    Hola,
                    <?= htmlspecialchars(
                        $_SESSION["usuario_nombre"]
                    ) ?>

                </span>


                <a
                    href="logout.php"
                    class="btn btn-light btn-sm">

                    Cerrar sesión

                </a>

            </div>

        <?php endif; ?>

    </div>

</nav>


<main class="container py-4">