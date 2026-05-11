<?php
session_start();

require_once __DIR__ . '/../controller/AuthController.php';

$mensaje = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $correo = trim($_POST['txtUser'] ?? '');
    $clave  = trim($_POST['txtPass'] ?? '');

    if ($correo === '' || $clave === '') {

        $mensaje = 'Complete todos los campos';

    } else {

        $auth = new AuthController();

        if ($auth->login($correo, $clave)) {

            header('Location: home.php');
            exit;

        } else {

            $mensaje = 'LOGIN ERROR';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="author" content="BC TECH">
    <meta name="robots" content="index,follow">
    <meta name="description" content="Agenda básica">
    <meta name="keywords" content="agenda, login, sistema">

    <link rel="icon" href="../imagenes/icono.ico">
    <link rel="stylesheet" href="../assetes/css/style.css">

    <title>Login</title>

</head>

<body>

<main>

<div class="contenedor">

    <!-- LADO IZQUIERDO -->
    <div class="lado-izquierdo">

        <div class="saludo">
            <h3>Hello!</h3>
            <h1>Good Morning</h1>
        </div>

        <div class="caja">

            <div class="titulo">
                <h2>Login your account</h2>
            </div>

            <!-- MENSAJE ERROR -->
            <?php if ($mensaje !== ''): ?>
                <p class="mensaje error">
                    <?php echo htmlspecialchars($mensaje); ?>
                </p>
            <?php endif; ?>

            <form method="post">

                <div class="inputs">
                    <label>Usuario</label>
                    <input
                        type="text"
                        name="txtUser"
                        placeholder="Ingrese correo"
                        required
                    >
                </div>

                <br>

                <div class="inputs">
                    <label>Contraseña</label>
                    <input
                        type="password"
                        name="txtPass"
                        placeholder="Ingrese clave"
                        required
                    >
                </div>

                <br>

                <div class="extra">
                    <a href="olvido_contra.php">¿Olvidó su contraseña?</a>
                </div>

                <br>

                <div class="botones">
                    <button type="submit" id="btnPrimary">
                        Ingresar
                    </button>
                </div>

                <br>

                <div class="crear">
                    <a href="crearcuenta.php">Crear cuenta</a>
                </div>

            </form>

        </div>

    </div>

    <!-- LADO DERECHO -->
    <div class="lado-derecho">
        <div class="contenido-derecho">
            <h1>WELCOME</h1>
        </div>
    </div>

</div>

</main>

<script src="../assetes/js/app.js"></script>

</body>
</html>