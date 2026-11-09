# Personal Dashboard (Tablero Personal)

A PHP web application where each user signs in and manages their own personal notes, with a live exchange-rate widget (USD/EUR → CRC).

> **ES:** Aplicación web en PHP donde cada usuario inicia sesión y administra sus notas personales, con un widget de tipo de cambio (USD/EUR → colones).

## Features

- **User authentication** with PHP sessions (login / logout, session ID regenerated after login).
- **Secure password storage** using `password_hash()` / `password_verify()` (bcrypt).
- **Notes CRUD**: create, edit and delete notes. Each user can only see and modify their own notes.
- **SQL injection protection**: every query uses PDO prepared statements.
- **XSS protection**: all user content is escaped with `htmlspecialchars()`.
- **Own REST endpoint** (`api/tipocambio.php`) that calls the ExchangeRate API and returns JSON. Only signed-in users can use it.
- **Flash messages** and the **Post/Redirect/Get** pattern to avoid duplicate form submissions.
- Responsive interface built with **Bootstrap**.

## Tech stack

PHP 8 · MySQL · PDO · JavaScript (Fetch API) · Bootstrap 5 · HTML/CSS

## Project structure

```
tablero-personal/
├── api/tipocambio.php      # REST endpoint (JSON) for the exchange rate
├── css/estilos.css
├── database/tablero_db.sql # Database schema + test user
├── includes/
│   ├── db.php              # PDO connection
│   ├── funciones.php       # Session, auth and flash-message helpers
│   ├── header.php
│   └── footer.php
├── index.php               # Dashboard (protected page)
├── login.php
├── logout.php
├── nota_guardar.php        # Create / edit note
└── nota_eliminar.php       # Delete note (POST only)
```

## How to run it locally

1. Install [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP).
2. Copy the `tablero-personal` folder into `xampp/htdocs/`.
3. Open phpMyAdmin and import `database/tablero_db.sql`.
4. (Optional) Get a free API key at [exchangerate-api.com](https://www.exchangerate-api.com/) and put it in `api/tipocambio.php` instead of `TU_API_KEY`.
5. Go to `http://localhost/tablero-personal/login.php`.

**Test user:** `usuario@gmail.com` / `123456`

## Author

**Dylan Astorga** – Computer Engineering student, Universidad Hispanoamericana (Costa Rica).
Developed for the Programming course at Universidad Hispanoamericana.
