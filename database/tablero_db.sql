/*
    tablero_db.sql
    --------------------------------------------------
    Crea la base de datos del Tablero Personal.

    Contiene:
    - Tabla usuarios
    - Tabla notas
    - Relación entre usuario y notas
    - Usuario de prueba
    --------------------------------------------------
*/

DROP DATABASE IF EXISTS tablero_db;

CREATE DATABASE tablero_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE tablero_db;


/*
    Tabla de usuarios.
*/

CREATE TABLE usuarios (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL,

    correo VARCHAR(150) NOT NULL UNIQUE,

    clave VARCHAR(255) NOT NULL,

    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);


/*
    Tabla de notas.
*/

CREATE TABLE notas (

    id INT AUTO_INCREMENT PRIMARY KEY,

    usuario_id INT NOT NULL,

    titulo VARCHAR(200) NOT NULL,

    contenido TEXT NOT NULL,

    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    actualizado_en TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_notas_usuario

        FOREIGN KEY (usuario_id)

        REFERENCES usuarios(id)

        ON DELETE CASCADE

);


/*
    Usuario de prueba.

    Correo:
    usuario@gmail.com

    Contraseña:
    123456

    La contraseña se almacena como hash bcrypt,
    generado con password_hash('123456', PASSWORD_DEFAULT).
*/

INSERT INTO usuarios
(
    nombre,
    correo,
    clave
)
VALUES
(
    'Usuario Prueba',
    'usuario@gmail.com',
    '$2y$10$TrfuUamNgwEiO.EkHe2.JuswnwplAvttZZbsC.CxI.vRt0DeRMZDK'
);