<?php

require_once __DIR__ . '/../config/Database.php';

class Cita
{
    private PDO $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->conectar();
    }

    public function listar(): array
    {
        $sql = "SELECT 
                    ci.*,
                    c.nombre,
                    c.apellido,
                    c.correo,
                    CONCAT(c.nombre, ' ', c.apellido) AS cliente
                FROM cita ci
                INNER JOIN cliente c ON c.id_cliente = ci.id_cliente
                ORDER BY ci.fecha DESC, ci.hora DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $idCita): ?array
    {
        $sql = "SELECT * FROM cita WHERE id_cita = :id_cita LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id_cita' => $idCita
        ]);

        $cita = $stmt->fetch(PDO::FETCH_ASSOC);

        return $cita ?: null;
    }

    public function registrar(array $datos): int
    {
        $sql = "INSERT INTO cita (
                    asunto,
                    detalle_cita,
                    tipo_cita,
                    lugar_cita,
                    referencia,
                    fecha,
                    hora,
                    estado,
                    id_cliente
                ) VALUES (
                    :asunto,
                    :detalle_cita,
                    :tipo_cita,
                    :lugar_cita,
                    :referencia,
                    :fecha,
                    :hora,
                    :estado,
                    :id_cliente
                )";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'asunto' => $datos['asunto'],
            'detalle_cita' => $datos['detalle_cita'],
            'tipo_cita' => $datos['tipo_cita'],
            'lugar_cita' => $datos['lugar_cita'],
            'referencia' => $datos['referencia'],
            'fecha' => $datos['fecha'],
            'hora' => $datos['hora'],
            'estado' => $datos['estado'],
            'id_cliente' => $datos['id_cliente']
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function actualizar(int $idCita, array $datos): bool
    {
        $sql = "UPDATE cita SET
                    asunto = :asunto,
                    detalle_cita = :detalle_cita,
                    tipo_cita = :tipo_cita,
                    lugar_cita = :lugar_cita,
                    referencia = :referencia,
                    fecha = :fecha,
                    hora = :hora,
                    estado = :estado,
                    id_cliente = :id_cliente
                WHERE id_cita = :id_cita";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'asunto' => $datos['asunto'],
            'detalle_cita' => $datos['detalle_cita'],
            'tipo_cita' => $datos['tipo_cita'],
            'lugar_cita' => $datos['lugar_cita'],
            'referencia' => $datos['referencia'],
            'fecha' => $datos['fecha'],
            'hora' => $datos['hora'],
            'estado' => $datos['estado'],
            'id_cliente' => $datos['id_cliente'],
            'id_cita' => $idCita
        ]);
    }

    public function eliminar(int $idCita): bool
    {
        $sql = "DELETE FROM cita WHERE id_cita = :id_cita";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id_cita' => $idCita
        ]);
    }

    //Para hacer repostes de citas
    public function reporte(array $filtros): array
    {
        $sql = "SELECT 
                    ci.*,
                    c.nombre,
                    c.apellido,
                    c.correo,
                    c.telefono,
                    CONCAT(c.nombre, ' ', c.apellido) AS cliente
                FROM cita ci
                INNER JOIN cliente c ON c.id_cliente = ci.id_cliente
                WHERE 1 = 1";

        $params = [];

        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND ci.fecha >= :fecha_desde";
            $params['fecha_desde'] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND ci.fecha <= :fecha_hasta";
            $params['fecha_hasta'] = $filtros['fecha_hasta'];
        }

        if (!empty($filtros['estado'])) {
            $sql .= " AND ci.estado = :estado";
            $params['estado'] = $filtros['estado'];
        }

        if (!empty($filtros['id_cliente'])) {
            $sql .= " AND ci.id_cliente = :id_cliente";
            $params['id_cliente'] = $filtros['id_cliente'];
        }

        $sql .= " ORDER BY ci.fecha ASC, ci.hora ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}