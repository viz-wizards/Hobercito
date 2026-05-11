<?php
session_start();
AuthController::verificarSesion();
require_once __DIR__ . '/../model/Usuario.php';

class AuthController
{
    public function login(string $correo, string $clave): bool
    {
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->login($correo, $clave);

        if ($usuario) {
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['usuario'] = $usuario['nombre'] ?: $usuario['correo'];
            $_SESSION['correo'] = $usuario['correo'];
            return true;
        }

        return false;
    }

    public function registrar(
        string $nombre,
        string $apellido,
        string $correo,
        string $clave,
        string $confirmarClave
    ): array {

        if ($nombre === '' || $apellido === '' || $correo === '' || $clave === '' || $confirmarClave === '') {
            return [
                'ok' => false,
                'mensaje' => 'Complete todos los campos'
            ];
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return [
                'ok' => false,
                'mensaje' => 'Ingrese un correo válido'
            ];
        }

        if (strlen($clave) < 4) {
            return [
                'ok' => false,
                'mensaje' => 'La contraseña debe tener mínimo 4 caracteres'
            ];
        }

        if ($clave !== $confirmarClave) {
            return [
                'ok' => false,
                'mensaje' => 'Las contraseñas no coinciden'
            ];
        }

        $usuarioModel = new Usuario();

        if ($usuarioModel->correoExiste($correo)) {
            return [
                'ok' => false,
                'mensaje' => 'El correo ya está registrado'
            ];
        }

        $idUsuario = $usuarioModel->registrar($nombre, $apellido, $correo, $clave);

        if ($idUsuario > 0) {
            return [
                'ok' => true,
                'mensaje' => 'Usuario registrado correctamente'
            ];
        }

        return [
            'ok' => false,
            'mensaje' => 'No se pudo registrar el usuario'
        ];
    }

    public static function verificarSesion(): void
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: login.php');
            exit;
        }
    }

     public static function logout(): void
    {
        session_unset();
        session_destroy();

        header('Location: login.php');
        exit;
    }
}
