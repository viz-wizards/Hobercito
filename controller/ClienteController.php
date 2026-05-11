<?php
session_start();
require_once __DIR__ . '/../model/Cliente.php';

class ClienteController
{
    public function listar(): array
    {
        $clienteModel = new Cliente();
        return $clienteModel->listar();
    }

    public function obtenerPorId(int $idCliente): ?array
    {
        $clienteModel = new Cliente();
        return $clienteModel->obtenerPorId($idCliente);
    }

    public function registrar(array $post): array
    {
        $validacion = $this->validarDatos($post);

        if (!$validacion['ok']) {
            return $validacion;
        }

        try {
            $clienteModel = new Cliente();

            if ($clienteModel->correoExiste($validacion['datos']['correo'])) {
                return [
                    'ok' => false,
                    'mensaje' => 'El correo ya está registrado'
                ];
            }

            $idCliente = $clienteModel->registrar($validacion['datos']);

            if ($idCliente > 0) {
                return [
                    'ok' => true,
                    'mensaje' => 'Cliente registrado correctamente'
                ];
            }

            return [
                'ok' => false,
                'mensaje' => 'No se pudo registrar el cliente'
            ];
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'mensaje' => 'Error al registrar el cliente'
            ];
        }
    }

    public function actualizar(array $post): array
    {
        $idCliente = (int)($post['idCliente'] ?? 0);

        if ($idCliente <= 0) {
            return [
                'ok' => false,
                'mensaje' => 'Cliente no válido'
            ];
        }

        $validacion = $this->validarDatos($post);

        if (!$validacion['ok']) {
            return $validacion;
        }

        try {
            $clienteModel = new Cliente();

            if ($clienteModel->correoExiste($validacion['datos']['correo'], $idCliente)) {
                return [
                    'ok' => false,
                    'mensaje' => 'El correo ya está registrado por otro cliente'
                ];
            }

            $actualizado = $clienteModel->actualizar($idCliente, $validacion['datos']);

            if ($actualizado) {
                return [
                    'ok' => true,
                    'mensaje' => 'Cliente actualizado correctamente'
                ];
            }

            return [
                'ok' => false,
                'mensaje' => 'No se pudo actualizar el cliente'
            ];
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'mensaje' => 'Error al actualizar el cliente'
            ];
        }
    }

    public function eliminar(int $idCliente): array
    {
        if ($idCliente <= 0) {
            return [
                'ok' => false,
                'mensaje' => 'Cliente no válido'
            ];
        }

        try {
            $clienteModel = new Cliente();
            $eliminado = $clienteModel->eliminar($idCliente);

            if ($eliminado) {
                return [
                    'ok' => true,
                    'mensaje' => 'Cliente eliminado correctamente'
                ];
            }

            return [
                'ok' => false,
                'mensaje' => 'No se pudo eliminar el cliente'
            ];
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'mensaje' => 'No se puede eliminar el cliente porque tiene información relacionada'
            ];
        }
    }

    private function validarDatos(array $post): array
    {
        $nombre = trim($post['txtNombre'] ?? '');
        $apellido = trim($post['txtApellido'] ?? '');
        $correo = trim($post['txtCorreo'] ?? '');
        $dni = trim($post['txtDni'] ?? '');
        $telefono = trim($post['txtTelefono'] ?? '');
        $direccion = trim($post['txtDireccion'] ?? '');
        $edad = trim($post['txtEdad'] ?? '');

        if ($nombre === '' || $apellido === '' || $correo === '' || $telefono === '') {
            return [
                'ok' => false,
                'mensaje' => 'Complete los campos obligatorios'
            ];
        }

        if (strlen($nombre) > 30) {
            return [
                'ok' => false,
                'mensaje' => 'El nombre no debe superar los 30 caracteres'
            ];
        }

        if (strlen($apellido) > 50) {
            return [
                'ok' => false,
                'mensaje' => 'El apellido no debe superar los 50 caracteres'
            ];
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return [
                'ok' => false,
                'mensaje' => 'Ingrese un correo válido'
            ];
        }

        if (strlen($correo) > 80) {
            return [
                'ok' => false,
                'mensaje' => 'El correo no debe superar los 80 caracteres'
            ];
        }

        if ($dni !== '' && !preg_match('/^[0-9]{8}$/', $dni)) {
            return [
                'ok' => false,
                'mensaje' => 'El DNI debe tener exactamente 8 números'
            ];
        }

        if (!preg_match('/^[0-9]{7,12}$/', $telefono)) {
            return [
                'ok' => false,
                'mensaje' => 'El teléfono debe tener entre 7 y 12 números'
            ];
        }

        if (strlen($direccion) > 90) {
            return [
                'ok' => false,
                'mensaje' => 'La dirección no debe superar los 90 caracteres'
            ];
        }

        if ($edad !== '') {
            if (!ctype_digit($edad)) {
                return [
                    'ok' => false,
                    'mensaje' => 'La edad debe ser un número válido'
                ];
            }

            $edad = (int)$edad;

            if ($edad < 0 || $edad > 120) {
                return [
                    'ok' => false,
                    'mensaje' => 'Ingrese una edad válida'
                ];
            }
        } else {
            $edad = null;
        }

        return [
            'ok' => true,
            'datos' => [
                'nombre' => $nombre,
                'apellido' => $apellido,
                'correo' => $correo,
                'dni' => $dni !== '' ? $dni : null,
                'telefono' => $telefono,
                'direccion' => $direccion !== '' ? $direccion : null,
                'edad' => $edad
            ]
        ];
    }
}