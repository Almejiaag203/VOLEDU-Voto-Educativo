<?php
require_once '../model/conexion.php';

header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'getResultadosPorNivel':
            $nivel = $_POST['nivel'] ?? ''; // '', 'primaria', 'secundaria'

            // Consulta base con LEFT JOIN para incluir nulos
            $sql = "
                SELECT 
                    COALESCE(CONCAT(a.nombre, ' ', a.apellidos), 'Voto Nulo') AS nombre,
                    c.foto_perfil AS foto, c.foto_campaña AS logo,
                    COUNT(v.id_voto) AS votos
                FROM voto v
                LEFT JOIN candidato c ON v.id_candidato = c.id_candidato
                LEFT JOIN alumnos a ON c.id_alumno = a.id_alumno
                LEFT JOIN asignacion_mesa am ON v.id_alumno = am.id_alumno
                LEFT JOIN mesa_sufragio m ON am.id_mesa = m.id_mesa
                LEFT JOIN proceso_electoral p ON v.id_proceso = p.id_proceso AND m.id_proceso = p.id_proceso
                WHERE p.activo = 1
            ";
            $params = [];
            if ($nivel !== '') {
                $sql .= " AND a.nivel = ?"; // Filtrar por nivel del votante
                $params[] = $nivel;
            }
            $sql .= " GROUP BY v.id_candidato"; // Agrupa por candidato o NULL para nulos
            $sql .= " ORDER BY votos DESC"; // Ordenar por votos descendentes para ranking

            $stmt = $conexion->prepare($sql);
            $stmt->execute($params);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calcular total de votos
            $total_votos = 0;
            foreach ($resultados as $row) {
                $total_votos += (int)$row['votos'];
            }

            // Calcular porcentajes y preparar datos
            $candidatos = [];
            foreach ($resultados as $row) {
                $porcentaje = $total_votos > 0 ? round(($row['votos'] / $total_votos) * 100, 2) : 0;
                $candidatos[] = [
                    'nombre' => $row['nombre'],
                    'foto' => $row['foto'] ?: null,
                    'logo' => $row['logo'] ?: null,
                    'votos' => (int)$row['votos'],
                    'porcentaje' => $porcentaje
                ];
            }

            echo json_encode([
                'success' => true,
                'candidatos' => $candidatos,
                'total_votos' => $total_votos
            ]);
            break;

        case 'getResultadosPorMesa':
            // Fetch results per mesa and candidate, including nulos
            $stmt = $conexion->prepare("
                SELECT 
                    m.id_mesa, m.numero, m.ubicacion, p.nombre AS proceso_nombre,
                    COALESCE(CONCAT(a.nombre, ' ', a.apellidos), 'Voto Nulo') AS candidato_nombre,
                    c.foto_perfil AS foto, c.foto_campaña AS logo, COUNT(v.id_voto) AS votos
                FROM mesa_sufragio m
                JOIN proceso_electoral p ON m.id_proceso = p.id_proceso
                LEFT JOIN voto v ON m.id_mesa = v.id_mesa
                LEFT JOIN candidato c ON v.id_candidato = c.id_candidato
                LEFT JOIN alumnos a ON c.id_alumno = a.id_alumno
                WHERE p.activo = 1
                GROUP BY m.id_mesa, COALESCE(v.id_candidato, 0), c.id_candidato
                ORDER BY m.numero, votos DESC
            ");
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Organize results by mesa
            $mesas = [];

            foreach ($resultados as $row) {
                $mesa_id = $row['id_mesa'];
                if (!isset($mesas[$mesa_id])) {
                    $mesas[$mesa_id] = [
                        'numero' => $row['numero'],
                        'ubicacion' => $row['ubicacion'],
                        'proceso_nombre' => $row['proceso_nombre'],
                        'total_votos' => 0,
                        'candidatos' => []
                    ];
                }

                $votos = (int)$row['votos'];
                $mesas[$mesa_id]['candidatos'][] = [
                    'nombre' => $row['candidato_nombre'],
                    'foto' => $row['foto'] ?: null,
                    'logo' => $row['logo'] ?: null,
                    'votos' => $votos
                ];
                $mesas[$mesa_id]['total_votos'] += $votos;
            }

            // Calculate percentages for each mesa
            foreach ($mesas as &$mesa) {
                foreach ($mesa['candidatos'] as &$candidato) {
                    $candidato['porcentaje'] = $mesa['total_votos'] > 0 
                        ? round(($candidato['votos'] / $mesa['total_votos']) * 100, 2) 
                        : 0;
                }
            }

            echo json_encode([
                'success' => true,
                'mesas' => array_values($mesas)
            ]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>