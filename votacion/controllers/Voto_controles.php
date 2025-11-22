<?php
require_once '../conexion.php'; // Usa PDO para seguridad

header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'buscarPorDni':
            // Buscar alumno por DNI y verificar si votó
            $dni = trim($_POST['dni'] ?? '');
            if (empty($dni) || !preg_match('/^\d{8}$/', $dni)) {
                echo json_encode(['success' => false, 'message' => 'DNI inválido, debe tener 8 dígitos']);
                exit;
            }

            // Buscar alumno y su mesa
            $stmt = $conexion->prepare("
                SELECT a.id_alumno, a.dni, CONCAT(a.nombre, ' ', a.apellidos) AS nombre, a.nivel, 
                       COALESCE(m.numero, 'Sin asignar') AS mesa_numero
                FROM alumnos a 
                LEFT JOIN asignacion_mesa am ON a.id_alumno = am.id_alumno 
                LEFT JOIN mesa_sufragio m ON am.id_mesa = m.id_mesa AND am.id_proceso = 1
                WHERE a.dni = ? AND a.activo = 1
            ");
            $stmt->execute([$dni]);
            $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$alumno) {
                echo json_encode(['success' => false, 'message' => 'DNI no encontrado o alumno inactivo']);
                exit;
            }

            // Validar que la mesa asignada corresponde al nivel del alumno
            if ($alumno['mesa_numero'] !== 'Sin asignar') {
                $prefix = ($alumno['nivel'] === 'Primaria') ? 'PRI-' : 'SEC-';
                if (strpos($alumno['mesa_numero'], $prefix) !== 0) {
                    echo json_encode(['success' => false, 'message' => 'Error: La mesa asignada no corresponde al nivel del alumno']);
                    exit;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No tienes mesa asignada para votar']);
                exit;
            }

            // Verificar si ya votó
            $stmt = $conexion->prepare("SELECT id_voto FROM voto WHERE id_alumno = ? AND id_proceso = 1");
            $stmt->execute([$alumno['id_alumno']]);
            $votado = $stmt->fetch() !== false;

            // Obtener candidatos por nivel
            $stmt = $conexion->prepare("
                SELECT c.id_candidato AS id, CONCAT(a.nombre, ' ', a.apellidos) AS nombre, 
                       c.foto_perfil AS foto_candidata, c.foto_campaña AS foto_campana, c.lema AS campaña
                FROM candidato c 
                JOIN alumnos a ON c.id_alumno = a.id_alumno 
                WHERE c.id_proceso = 1 AND a.nivel = ?
                ORDER BY a.nombre, a.apellidos
            ");
            $stmt->execute([$alumno['nivel']]);
            $candidatos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Ajustar rutas de fotos
            foreach ($candidatos as &$c) {
                $c['foto_candidata'] = $c['foto_candidata'] ? '../Uploads/' . $c['foto_candidata'] : '../Uploads/default.jpg';
                $c['foto_campana'] = $c['foto_campana'] ? '../Uploads/' . $c['foto_campana'] : '../Uploads/default_campana.jpg';
            }

            echo json_encode([
                'success' => true,
                'alumno' => [
                    'dni' => $alumno['dni'],
                    'id_alumno' => $alumno['id_alumno'],
                    'nombre' => $alumno['nombre'],
                    'nivel' => $alumno['nivel'],
                    'mesa' => 'Mesa ' . $alumno['mesa_numero'],
                    'votado' => $votado
                ],
                'candidatos' => $candidatos
            ]);
            break;

        case 'registrarVoto':
            // Registrar voto
            $dni = trim($_POST['dni'] ?? '');
            $candidato_id = $_POST['candidato_id'] ?? null;

            if (empty($dni) || !preg_match('/^\d{8}$/', $dni)) {
                echo json_encode(['success' => false, 'message' => 'DNI inválido, debe tener 8 dígitos']);
                exit;
            }

            if ($candidato_id === 'nulo') {
                $candidato_id = null;
            }

            // Obtener id_alumno
            $stmt = $conexion->prepare("SELECT id_alumno, nivel FROM alumnos WHERE dni = ? AND activo = 1");
            $stmt->execute([$dni]);
            $alumno = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$alumno) {
                echo json_encode(['success' => false, 'message' => 'Alumno no encontrado o inactivo']);
                exit;
            }
            $id_alumno = $alumno['id_alumno'];
            $nivel = $alumno['nivel'];

            // Verificar si ya votó
            $stmt = $conexion->prepare("SELECT id_voto FROM voto WHERE id_alumno = ? AND id_proceso = 1");
            $stmt->execute([$id_alumno]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Ya has votado']);
                exit;
            }

            // Obtener id_mesa y validar prefijo
            $prefix = ($nivel === 'Primaria') ? 'PRI-' : 'SEC-';
            $stmt = $conexion->prepare("
                SELECT am.id_mesa 
                FROM asignacion_mesa am 
                JOIN mesa_sufragio m ON am.id_mesa = m.id_mesa 
                WHERE am.id_alumno = ? AND am.id_proceso = 1 AND m.numero LIKE ?
            ");
            $stmt->execute([$id_alumno, "$prefix%"]);
            $mesa = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$mesa) {
                echo json_encode(['success' => false, 'message' => 'No tienes mesa asignada o la mesa no corresponde a tu nivel']);
                exit;
            }
            $id_mesa = $mesa['id_mesa'];

            // Validar candidato (si no es nulo)
            if ($candidato_id !== null) {
                $stmt = $conexion->prepare("
                    SELECT c.id_candidato 
                    FROM candidato c 
                    JOIN alumnos a ON c.id_alumno = a.id_alumno 
                    WHERE c.id_candidato = ? AND c.id_proceso = 1 AND a.nivel = ?
                ");
                $stmt->execute([$candidato_id, $nivel]);
                if (!$stmt->fetch()) {
                    echo json_encode(['success' => false, 'message' => 'Candidato no válido para tu nivel']);
                    exit;
                }
            }

            // Insertar voto
            $stmt = $conexion->prepare("
                INSERT INTO voto (id_mesa, id_candidato, id_alumno, id_proceso) 
                VALUES (?, ?, ?, 1)
            ");
            $stmt->execute([$id_mesa, $candidato_id, $id_alumno]);

            echo json_encode(['success' => true, 'message' => 'Voto registrado exitosamente']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>