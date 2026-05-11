<?php
session_start();
require_once __DIR__ . '/../model/ReporteCita.php';

class ReporteCitaController
{
    public function generar(array $get): array
    {
        $filtros = [
            'fecha_desde' => trim($get['fecha_desde'] ?? ''),
            'fecha_hasta' => trim($get['fecha_hasta'] ?? ''),
            'estado' => trim($get['estado'] ?? ''),
            'id_cliente' => (int)($get['id_cliente'] ?? 0)
        ];

        $validacion = $this->validarFiltros($filtros);

        if (!$validacion['ok']) {
            return [
                'ok' => false,
                'mensaje' => $validacion['mensaje'],
                'filtros' => $filtros,
                'citas' => [],
                'resumen' => $this->resumenVacio()
            ];
        }

        $reporteCitaModel = new ReporteCita();
        $citas = $reporteCitaModel->reporte($filtros);

        return [
            'ok' => true,
            'mensaje' => '',
            'filtros' => $filtros,
            'citas' => $citas,
            'resumen' => $this->calcularResumen($citas)
        ];
    }

    private function validarFiltros(array $filtros): array
    {
        if ($filtros['fecha_desde'] !== '' && !$this->fechaValida($filtros['fecha_desde'])) {
            return [
                'ok' => false,
                'mensaje' => 'La fecha desde no es válida'
            ];
        }

        if ($filtros['fecha_hasta'] !== '' && !$this->fechaValida($filtros['fecha_hasta'])) {
            return [
                'ok' => false,
                'mensaje' => 'La fecha hasta no es válida'
            ];
        }

        if ($filtros['fecha_desde'] !== '' && $filtros['fecha_hasta'] !== '') {
            if ($filtros['fecha_desde'] > $filtros['fecha_hasta']) {
                return [
                    'ok' => false,
                    'mensaje' => 'La fecha desde no puede ser mayor que la fecha hasta'
                ];
            }
        }

        $estadosPermitidos = ['', 'Pendiente', 'Confirmada', 'Cancelada', 'Atendida'];

        if (!in_array($filtros['estado'], $estadosPermitidos, true)) {
            return [
                'ok' => false,
                'mensaje' => 'Estado no válido'
            ];
        }

        return [
            'ok' => true,
            'mensaje' => ''
        ];
    }

    private function fechaValida(string $fecha): bool
    {
        $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
        return $fechaObj && $fechaObj->format('Y-m-d') === $fecha;
    }

    private function calcularResumen(array $citas): array
    {
        $resumen = $this->resumenVacio();
        $resumen['total'] = count($citas);

        foreach ($citas as $cita) {
            if ($cita['estado'] === 'Pendiente') {
                $resumen['pendientes']++;
            }

            if ($cita['estado'] === 'Confirmada') {
                $resumen['confirmadas']++;
            }

            if ($cita['estado'] === 'Cancelada') {
                $resumen['canceladas']++;
            }

            if ($cita['estado'] === 'Atendida') {
                $resumen['atendidas']++;
            }
        }

        return $resumen;
    }

    private function resumenVacio(): array
    {
        return [
            'total' => 0,
            'pendientes' => 0,
            'confirmadas' => 0,
            'canceladas' => 0,
            'atendidas' => 0
        ];
    }
}