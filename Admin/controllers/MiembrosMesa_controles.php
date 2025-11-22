<?php
require_once '../model/conexion.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'listarMiembros':
        $stmt = $conexion->prepare("
            SELECT amm.id_miembro_mesa, u.nombre AS nombre_miembro, u.apellido, m.numero AS mesa_numero, p.nombre AS proceso_nombre, amm.fecha_asignacion, amm.rol_miembro
            FROM asignacion_miembros_mesa amm
            JOIN usuario u ON amm.id_usuario = u.id_usuario
            JOIN mesa_sufragio m ON amm.id_mesa = m.id_mesa
            JOIN proceso_electoral p ON amm.id_proceso = p.id_proceso
            ORDER BY amm.fecha_asignacion DESC
        ");
        $stmt->execute();
        echo json_encode(['success' => true, 'miembros' => $stmt->fetchAll()]);
        break;

    case 'listarMiembrosPorMesa':
        $id_mesa = (int)$_POST['id_mesa'] ?? 0;
        $stmt = $conexion->prepare("
            SELECT amm.id_miembro_mesa, u.nombre AS nombre_miembro, u.apellido, amm.rol_miembro
            FROM asignacion_miembros_mesa amm
            JOIN usuario u ON amm.id_usuario = u.id_usuario
            WHERE amm.id_mesa = ?
            ORDER BY amm.rol_miembro, u.nombre
        ");
        $stmt->execute([$id_mesa]);
        echo json_encode(['success' => true, 'miembros' => $stmt->fetchAll()]);
        break;

    case 'listarMiembrosDisponibles':
        $stmt = $conexion->prepare("
            SELECT u.id_usuario, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo
            FROM usuario u
            JOIN rol r ON u.id_rol = r.id_rol
            WHERE r.nombre = 'Miembro de Mesa' AND u.estado = 1
        ");
        $stmt->execute();
        echo json_encode(['success' => true, 'miembros' => $stmt->fetchAll()]);
        break;

    case 'listarMesas':
        $stmt = $conexion->prepare("
            SELECT id_mesa, numero, ubicacion
            FROM mesa_sufragio
            WHERE activo = 1
        ");
        $stmt->execute();
        echo json_encode(['success' => true, 'mesas' => $stmt->fetchAll()]);
        break;

    case 'listarProcesos':
        $stmt = $conexion->prepare("
            SELECT id_proceso, nombre
            FROM proceso_electoral
            WHERE activo = 1
        ");
        $stmt->execute();
        echo json_encode(['success' => true, 'procesos' => $stmt->fetchAll()]);
        break;

    case 'buscarPorDni':
        $dni = trim($_POST['dni'] ?? '');
        if (empty($dni)) {
            echo json_encode(['success' => false, 'message' => 'DNI es obligatorio']);
            exit;
        }
        $stmt = $conexion->prepare("
            SELECT a.id_alumno AS id_usuario, CONCAT(a.nombre, ' ', a.apellidos) AS nombre
            FROM alumnos a
            WHERE a.dni = ? AND a.activo = 1
        ");
        $stmt->execute([$dni]);
        $alumno = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($alumno) {
            echo json_encode(['success' => true, 'alumno' => $alumno]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Alumno no encontrado']);
        }
        break;

    case 'asignarMiembro':
        $id_usuario = (int)$_POST['id_usuario'];
        $id_mesa = (int)$_POST['id_mesa'];
        $id_proceso = (int)$_POST['id_proceso'];
        $rol_miembro = $_POST['rol_miembro'] ?? '';

        if (!in_array($rol_miembro, ['Presidente', 'Vocal', 'Suplente'])) {
            echo json_encode(['success' => false, 'message' => 'Rol inválido']);
            exit;
        }

        try {
            $stmt = $conexion->prepare("
                INSERT INTO asignacion_miembros_mesa (id_usuario, id_mesa, id_proceso, rol_miembro)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$id_usuario, $id_mesa, $id_proceso, $rol_miembro]);
            echo json_encode(['success' => true, 'message' => 'Miembro asignado correctamente']);
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') { // UNIQUE constraint violation
                echo json_encode(['success' => false, 'message' => 'Este alumno ya está asignado a una mesa en este proceso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al asignar: ' . $e->getMessage()]);
            }
        }
        break;

    case 'eliminarMiembro':
        $id_miembro_mesa = (int)$_POST['id_miembro_mesa'];
        $stmt = $conexion->prepare("DELETE FROM asignacion_miembros_mesa WHERE id_miembro_mesa = ?");
        $stmt->execute([$id_miembro_mesa]);
        echo json_encode(['success' => true, 'message' => 'Asignación eliminada']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
?>