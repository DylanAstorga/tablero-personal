<?php

/*
 * db.php
 * --------------------------------------------------
 * Este archivo realiza la conexión con MySQL
 * utilizando PDO.
 * --------------------------------------------------
 */

$host = "localhost";
$dbname = "tablero_db";
$usuarioBD = "root";
$claveBD = "";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $usuarioBD,
        $claveBD
    );

    /*
     * Mostrar errores como excepciones.
     */
    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    /*
     * Los resultados se devolverán
     * como arreglos asociativos.
     */
    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

    /*
     * Usar consultas preparadas reales.
     */
    $pdo->setAttribute(
        PDO::ATTR_EMULATE_PREPARES,
        false
    );

} catch (PDOException $e) {

    die("Error al conectar con la base de datos.");

}

?>