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

            $mensaje = 'Correo o clave incorrectos';
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

<header></header>
<main>

    <div class="contenedor">

       
        <div class="lado-izquierdo">

            <div class="saludo">
                <h3>Hello!</h3>
                <h1>Good Morning</h1>
            </div>

            <div class="caja">

                <div class="titulo">
                    <h2>Login your account</h2>
                </div>

                <?php if ($mensaje !== ''): ?>
                    <p class="mensaje error">
                        <?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                <?php endif; ?>

                <form action="" method="post" id="frmFormulario">

                    <div class="inputs">
                        <label for="txtUser">Usuario</label>
                        <input 
                            type="text" 
                            name="txtUser" 
                            id="txtUser" 
                            placeholder="Ingrese usuario"
                        >
                    </div>

                    <br>

                    <div class="inputs">
                        <label for="txtPass">Contraseña</label>
                        <input 
                            type="password" 
                            name="txtPass" 
                            id="txtPass" 
                            placeholder="Ingrese clave"
                        >
                    </div>

                    <br>

                    <div class="extra">
                        <a href="#">¿Olvidó su contraseña?</a>
                    </div>

                    <br>

                    <div class="botones">
                        <button type="submit" id="btnPrimary">
                            Ingresar
                        </button>
                    </div>

                    <br>

                    <div class="crear">
                        <a href="#">Crear cuenta</a>
                    </div>

                </form>

            </div>

        </div>

       
        <div class="lado-derecho">

            <div class="contenido-derecho">
                <h1>WELCOME</h1>
            </div>

        </div>

    </div>

</main>
<footer>

</footer>

<script src="../assetes/js/app.js"></script>

</body>
</html>