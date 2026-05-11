<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once __DIR__ . '/../controller/AuthController.php';

$mensaje = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $auth = new AuthController();

    $resultado = $auth->registrar(
        $_POST['nombre'],
        $_POST['apellido'],
        $_POST['correo'],
        $_POST['clave'],
        $_POST['confirmarClave']
    );

    $mensaje = $resultado['mensaje'];

    if($resultado['ok']){
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Crear Cuenta</title>

<link rel="stylesheet" href="../assetes/css/creacuenta.css">

</head>

<body>

<div class="container">

<h2>Crear Cuenta</h2>

<form method="POST">

<input type="text" name="nombre" placeholder="Nombre" required>

<input type="text" name="apellido" placeholder="Apellido" required>

<input type="email" name="correo" placeholder="Correo" required>

<input type="password" name="clave" placeholder="Contraseña" required>

<input type="password" name="confirmarClave" placeholder="Confirmar contraseña" required>

<button type="submit">Registrar</button>

</form>

<p class="mensaje"><?= $mensaje ?></p>

<a href="login.php">Ir al Login</a>

</div>

</body>
</html>