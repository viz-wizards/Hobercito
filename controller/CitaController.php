<?php
session_start();
require_once __DIR__ . '/../model/Cita.php';
require_once __DIR__ . '/../model/Cliente.php';

class CitaController
{
    public function listar(): array
    {
        $citaModel = new Cita();
        return $citaModel->listar();
    }

    public function obtenerPorId(int $idCita): ?array
    {
        $citaModel = new Cita();
        return $citaModel->obtenerPorId($idCita);
    }

    public function registrar(array $post): array
    {
        $validacion = $this->validarDatos($post);

        if (!$validacion['ok']) {
            return $validacion;
        }

        try {
            $citaModel = new Cita();
            $idCita = $citaModel->registrar($validacion['datos']);

            if ($idCita > 0) {
                return [
                    'ok' => true,
                    'mensaje' => 'Cita registrada correctamente'
                ];
            }

            return [
                'ok' => false,
                'mensaje' => 'No se pudo registrar la cita'
            ];
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'mensaje' => 'Error al registrar la cita'
            ];
        }
    }

    public function actualizar(array $post): array
    {
        $idCita = (int)($post['idCita'] ?? 0);

        if ($idCita <= 0) {
            return [
                'ok' => false,
                'mensaje' => 'Cita no válida'
            ];
        }

        $validacion = $this->validarDatos($post);

        if (!$validacion['ok']) {
            return $validacion;
        }

        try {
            $citaModel = new Cita();
            $actualizado = $citaModel->actualizar($idCita, $validacion['datos']);

            if ($actualizado) {
                return [
                    'ok' => true,
                    'mensaje' => 'Cita actualizada correctamente'
                ];
            }

            return [
                'ok' => false,
                'mensaje' => 'No se pudo actualizar la cita'
            ];
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'mensaje' => 'Error al actualizar la cita'
            ];
        }
    }

    public function eliminar(int $idCita): array
    {
        if ($idCita <= 0) {
            return [
                'ok' => false,
                'mensaje' => 'Cita no válida'
            ];
        }

        try {
            $citaModel = new Cita();
            $eliminado = $citaModel->eliminar($idCita);

            if ($eliminado) {
                return [
                    'ok' => true,
                    'mensaje' => 'Cita eliminada correctamente'
                ];
            }

            return [
                'ok' => false,
                'mensaje' => 'No se pudo eliminar la cita'
            ];
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'mensaje' => 'Error al eliminar la cita'
            ];
        }
    }

    private function validarDatos(array $post): array
    {
        $asunto = trim($post['txtAsunto'] ?? '');
        $detalleCita = trim($post['txtDetalleCita'] ?? '');
        $tipoCita = trim($post['txtTipoCita'] ?? '');
        $lugarCita = trim($post['txtLugarCita'] ?? '');
        $referencia = trim($post['txtReferencia'] ?? '');
        $fecha = trim($post['txtFecha'] ?? '');
        $hora = trim($post['txtHora'] ?? '');
        $estado = trim($post['txtEstado'] ?? '');
        $idCliente = (int)($post['txtCliente'] ?? 0);

        if ($asunto === '' || $lugarCita === '' || $fecha === '' || $hora === '' || $estado === '' || $idCliente <= 0) {
            return [
                'ok' => false,
                'mensaje' => 'Complete los campos obligatorios'
            ];
        }

        if (strlen($asunto) > 90) {
            return [
                'ok' => false,
                'mensaje' => 'El asunto no debe superar los 90 caracteres'
            ];
        }

        if (strlen($detalleCita) > 300) {
            return [
                'ok' => false,
                'mensaje' => 'El detalle no debe superar los 300 caracteres'
            ];
        }

        if (strlen($tipoCita) > 20) {
            return [
                'ok' => false,
                'mensaje' => 'El tipo de cita no debe superar los 20 caracteres'
            ];
        }

        if (strlen($lugarCita) > 100) {
            return [
                'ok' => false,
                'mensaje' => 'El lugar no debe superar los 100 caracteres'
            ];
        }

        if (strlen($referencia) > 200) {
            return [
                'ok' => false,
                'mensaje' => 'La referencia no debe superar los 200 caracteres'
            ];
        }

        $fechaValida = DateTime::createFromFormat('Y-m-d', $fecha);

        if (!$fechaValida || $fechaValida->format('Y-m-d') !== $fecha) {
            return [
                'ok' => false,
                'mensaje' => 'Ingrese una fecha válida'
            ];
        }

        if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $hora)) {
            return [
                'ok' => false,
                'mensaje' => 'Ingrese una hora válida'
            ];
        }

        if (strlen($hora) === 5) {
            $hora .= ':00';
        }

        $estadosPermitidos = ['Pendiente', 'Confirmada', 'Cancelada', 'Atendida'];

        if (!in_array($estado, $estadosPermitidos, true)) {
            return [
                'ok' => false,
                'mensaje' => 'Seleccione un estado válido'
            ];
        }

        $tiposPermitidos = ['', 'Presencial', 'Virtual', 'Telefonica', 'Otro'];

        if (!in_array($tipoCita, $tiposPermitidos, true)) {
            return [
                'ok' => false,
                'mensaje' => 'Seleccione un tipo de cita válido'
            ];
        }

        $clienteModel = new Cliente();
        $cliente = $clienteModel->obtenerPorId($idCliente);

        if (!$cliente) {
            return [
                'ok' => false,
                'mensaje' => 'Seleccione un cliente válido'
            ];
        }

        return [
            'ok' => true,
            'datos' => [
                'asunto' => $asunto,
                'detalle_cita' => $detalleCita !== '' ? $detalleCita : null,
                'tipo_cita' => $tipoCita !== '' ? $tipoCita : null,
                'lugar_cita' => $lugarCita,
                'referencia' => $referencia !== '' ? $referencia : null,
                'fecha' => $fecha,
                'hora' => $hora,
                'estado' => $estado,
                'id_cliente' => $idCliente
            ]
        ];
    }
}