<?php
require_once '../model/conexion.php';

header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'listarProcesos':
            $stmt = $conexion->prepare("SELECT id_proceso, nombre FROM proceso_electoral WHERE activo = 1 ORDER BY nombre");
            $stmt->execute();
            $procesos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'procesos' => $procesos]);
            break;

        case 'listarMesas':
            $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 20;
            $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
            $offset = ($page - 1) * $limit;
            $nivel = isset($_POST['nivel']) ? $_POST['nivel'] : null;

            $prefix = ($nivel === 'Secundaria') ? 'SEC-' : 'PRI-';
            $where = "WHERE m.activo = 1";
            if ($nivel) {
                $where .= " AND m.numero LIKE '$prefix%'";
            }

            $sql_count = "SELECT COUNT(*) as total FROM mesa_sufragio m JOIN proceso_electoral p ON m.id_proceso = p.id_proceso $where";
            $stmt = $conexion->prepare($sql_count);
            $stmt->execute();
            $totalRecords = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $sql_select = "
                SELECT m.id_mesa, m.numero, m.ubicacion, m.activo, p.nombre AS proceso_nombre, p.id_proceso
                FROM mesa_sufragio m
                JOIN proceso_electoral p ON m.id_proceso = p.id_proceso
                $where
                ORDER BY p.nombre, m.numero
                LIMIT :limit OFFSET :offset
            ";
            $stmt = $conexion->prepare($sql_select);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($mesas as &$mesa) {
                $mesa['nivel'] = strpos($mesa['numero'], 'SEC-') === 0 ? 'Secundaria' : 'Primaria';
            }

            echo json_encode([
                'success' => true,
                'mesas' => $mesas,
                'total' => (int)$totalRecords
            ]);
            break;

        case 'generarMesas':
            $id_proceso = $_POST['id_proceso'] ?? '';
            $ubicacion = trim($_POST['ubicacion'] ?? 'Aula Predeterminada');
            $nivel = trim($_POST['nivel'] ?? '');

            if (empty($id_proceso) || empty($ubicacion) || empty($nivel)) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
                exit;
            }

            $stmt = $conexion->prepare("SELECT id_proceso FROM proceso_electoral WHERE id_proceso = ?");
            $stmt->execute([$id_proceso]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Proceso electoral no encontrado']);
                exit;
            }

            // Definir grados esperados por nivel
            $grados_esperados = ($nivel === 'Secundaria') 
                ? ['PRIMERO', 'SEGUNDO', 'TERCERO', 'CUARTO', 'QUINTO']
                : ['PRIMERO', 'SEGUNDO', 'TERCERO', 'CUARTO', 'QUINTO', 'SEXTO'];

            // Obtener grados existentes en la base de datos
            $stmt = $conexion->prepare("SELECT DISTINCT grado FROM alumnos WHERE nivel = ? AND activo = 1 ORDER BY FIELD(grado, 'PRIMERO', 'SEGUNDO', 'TERCERO', 'CUARTO', 'QUINTO', 'SEXTO')");
            $stmt->execute([strtolower($nivel)]);
            $grados = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Filtrar solo los grados válidos
            $grados = array_intersect($grados_esperados, $grados);
            if (empty($grados)) {
                echo json_encode(['success' => false, 'message' => 'No hay grados válidos definidos en este nivel']);
                exit;
            }

            $prefix = ($nivel === 'Secundaria') ? 'SEC-' : 'PRI-';

            // Limpiar mesas existentes del nivel para evitar duplicados
            $stmt = $conexion->prepare("DELETE FROM asignacion_mesa WHERE id_proceso = ? AND id_alumno IN (SELECT id_alumno FROM alumnos WHERE nivel = ?)");
            $stmt->execute([$id_proceso, strtolower($nivel)]);
            $stmt = $conexion->prepare("DELETE FROM mesa_sufragio WHERE id_proceso = ? AND numero LIKE '$prefix%'");
            $stmt->execute([$id_proceso]);

            // Crear una mesa por grado, empezando desde 001
            $mesas_creadas = [];
            foreach ($grados_esperados as $index => $grado) {  // Cambiado para usar grados_esperados directamente
                if (in_array($grado, $grados)) {  // Solo crear si el grado existe
                    $numero = $prefix . sprintf('%03d', $index + 1);
                    $stmt = $conexion->prepare("
                        INSERT INTO mesa_sufragio (id_proceso, numero, ubicacion, nivel, activo)
                        VALUES (?, ?, ?, ?, 1)
                    ");
                    $stmt->execute([$id_proceso, $numero, $ubicacion, $nivel]);
                    $id_mesa = $conexion->lastInsertId();
                    $mesas_creadas[$grado] = $id_mesa;
                }
            }

            // Asignar alumnos por grado a su mesa correspondiente
            foreach ($grados as $grado) {
                $id_mesa = $mesas_creadas[$grado];
                $stmt_alumnos = $conexion->prepare("
                    SELECT id_alumno 
                    FROM alumnos 
                    WHERE nivel = ? AND grado = ? AND activo = 1 
                    ORDER BY apellidos, nombre
                ");
                $stmt_alumnos->execute([strtolower($nivel), $grado]);
                $alumnos = $stmt_alumnos->fetchAll(PDO::FETCH_COLUMN);

                foreach ($alumnos as $id_alumno) {
                    $stmt = $conexion->prepare("
                        INSERT INTO asignacion_mesa (id_mesa, id_alumno, id_proceso) 
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([$id_mesa, $id_alumno, $id_proceso]);
                }
            }

            echo json_encode([
                'success' => true, 
                'message' => 'Mesas generadas por grado (' . count($grados) . ' mesas) y alumnos asignados correctamente'
            ]);
            break;

        case 'actualizarMesa':
            $id_mesa = $_POST['id_mesa'] ?? '';
            $ubicacion = trim($_POST['ubicacion'] ?? '');

            if (empty($id_mesa) || empty($ubicacion)) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
                exit;
            }

            $stmt = $conexion->prepare("UPDATE mesa_sufragio SET ubicacion = ? WHERE id_mesa = ?");
            $stmt->execute([$ubicacion, $id_mesa]);

            echo json_encode(['success' => true, 'message' => 'Mesa actualizada correctamente']);
            break;

        case 'eliminarMesa':
            $id_mesa = $_POST['id_mesa'] ?? '';

            if (empty($id_mesa)) {
                echo json_encode(['success' => false, 'message' => 'ID de mesa no proporcionado']);
                exit;
            }

            $stmt = $conexion->prepare("SELECT id_alumno FROM asignacion_mesa WHERE id_mesa = ?");
            $stmt->execute([$id_mesa]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'No se puede eliminar la mesa porque tiene alumnos asignados']);
                exit;
            }

            $stmt = $conexion->prepare("DELETE FROM mesa_sufragio WHERE id_mesa = ?");
            $stmt->execute([$id_mesa]);

            echo json_encode(['success' => true, 'message' => 'Mesa eliminada correctamente']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>