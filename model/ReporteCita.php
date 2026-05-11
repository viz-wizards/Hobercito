<?php

require_once __DIR__ . '/../config/Database.php';

class ReporteCita{


    private PDO $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->conectar();
    }

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

?>