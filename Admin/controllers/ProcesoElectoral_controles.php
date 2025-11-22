<?php
require_once '../model/conexion.php';

header('Content-Type: application/json');

try {
    // Get the action from POST
    $action = $_POST['action'] ?? '';

    // Handle different actions
    switch ($action) {
        case 'listarProcesos':
            // Fetch all electoral processes
            $stmt = $conexion->prepare("
                SELECT id_proceso, nombre, fecha_inicio, fecha_fin, activo
                FROM proceso_electoral
                ORDER BY fecha_inicio DESC
            ");
            $stmt->execute();
            $procesos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'procesos' => $procesos]);
            break;

        case 'registrarProceso':
            // Add a new electoral process
            $nombre = trim($_POST['nombre'] ?? '');
            $fecha_inicio = $_POST['fecha_inicio'] ?? '';
            $fecha_fin = $_POST['fecha_fin'] ?? '';
            $activo = isset($_POST['activo']) ? 1 : 0;

            // Validate required fields
            if (empty($nombre) || empty($fecha_inicio) || empty($fecha_fin)) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos obligatorios deben estar completos']);
                exit;
            }

            // Validate date format and logic
            $inicio = DateTime::createFromFormat('Y-m-d', $fecha_inicio);
            $fin = DateTime::createFromFormat('Y-m-d', $fecha_fin);
            if (!$inicio || !$fin || $inicio >= $fin) {
                echo json_encode(['success' => false, 'message' => 'Fechas inválidas. La fecha de inicio debe ser anterior a la fecha de fin.']);
                exit;
            }

            // Check if process name already exists
            $stmt = $conexion->prepare("SELECT id_proceso FROM proceso_electoral WHERE nombre = ?");
            $stmt->execute([$nombre]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'El nombre del proceso ya está registrado']);
                exit;
            }

            // Insert process into database
            $stmt = $conexion->prepare("
                INSERT INTO proceso_electoral (nombre, fecha_inicio, fecha_fin, activo)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$nombre, $fecha_inicio, $fecha_fin, $activo]);

            echo json_encode(['success' => true, 'message' => 'Proceso electoral registrado correctamente']);
            break;

        case 'obtenerProceso':
            // Fetch process details
            $id_proceso = $_POST['id'] ?? '';

            if (empty($id_proceso)) {
                echo json_encode(['success' => false, 'message' => 'ID de proceso no proporcionado']);
                exit;
            }

            $stmt = $conexion->prepare("
                SELECT id_proceso, nombre, fecha_inicio, fecha_fin, activo
                FROM proceso_electoral
                WHERE id_proceso = ?
            ");
            $stmt->execute([$id_proceso]);
            $proceso = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($proceso) {
                echo json_encode(['success' => true, 'proceso' => $proceso]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Proceso no encontrado']);
            }
            break;

        case 'actualizarProceso':
            // Update process details
            $id_proceso = $_POST['id_proceso'] ?? '';
            $nombre = trim($_POST['nombre'] ?? '');
            $fecha_inicio = $_POST['fecha_inicio'] ?? '';
            $fecha_fin = $_POST['fecha_fin'] ?? '';
            $activo = isset($_POST['activo']) ? 1 : 0;

            if (empty($id_proceso) || empty($nombre) || empty($fecha_inicio) || empty($fecha_fin)) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos obligatorios deben estar completos']);
                exit;
            }

            // Validate process exists
            $stmt = $conexion->prepare("SELECT id_proceso FROM proceso_electoral WHERE id_proceso = ?");
            $stmt->execute([$id_proceso]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Proceso no encontrado']);
                exit;
            }

            // Validate date format and logic
            $inicio = DateTime::createFromFormat('Y-m-d', $fecha_inicio);
            $fin = DateTime::createFromFormat('Y-m-d', $fecha_fin);
            if (!$inicio || !$fin || $inicio >= $fin) {
                echo json_encode(['success' => false, 'message' => 'Fechas inválidas. La fecha de inicio debe ser anterior a la fecha de fin.']);
                exit;
            }

            // Check if process name already exists (excluding current process)
            $stmt = $conexion->prepare("SELECT id_proceso FROM proceso_electoral WHERE nombre = ? AND id_proceso != ?");
            $stmt->execute([$nombre, $id_proceso]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'El nombre del proceso ya está registrado']);
                exit;
            }

            // Update process in database
            $stmt = $conexion->prepare("
                UPDATE proceso_electoral
                SET nombre = ?, fecha_inicio = ?, fecha_fin = ?, activo = ?
                WHERE id_proceso = ?
            ");
            $stmt->execute([$nombre, $fecha_inicio, $fecha_fin, $activo, $id_proceso]);

            echo json_encode(['success' => true, 'message' => 'Proceso electoral actualizado correctamente']);
            break;

        case 'eliminarProceso':
            // Delete process
            $id_proceso = $_POST['id'] ?? '';

            if (empty($id_proceso)) {
                echo json_encode(['success' => false, 'message' => 'ID de proceso no proporcionado']);
                exit;
            }

            // Check if process has associated candidates
            $stmt = $conexion->prepare("SELECT id_candidato FROM candidato WHERE id_proceso = ?");
            $stmt->execute([$id_proceso]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'No se puede eliminar el proceso porque tiene candidatos asociados']);
                exit;
            }

            // Delete process from database
            $stmt = $conexion->prepare("DELETE FROM proceso_electoral WHERE id_proceso = ?");
            $stmt->execute([$id_proceso]);

            echo json_encode(['success' => true, 'message' => 'Proceso electoral eliminado correctamente']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>