<?php
require_once '../model/conexion.php';

header('Content-Type: application/json');

try {
    // Function to generate a unique filename
    function generateUniqueFileName($fileName) {
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $uniqueName = $baseName . '_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        return $uniqueName;
    }

    // Upload directory
    $uploadDir = '../../Uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Get the action from POST
    $action = $_POST['action'] ?? '';

    // Handle different actions
    switch ($action) {
        case 'listarProcesos':
            // Fetch active electoral processes
            $stmt = $conexion->prepare("SELECT id_proceso, nombre FROM proceso_electoral WHERE activo = 1");
            $stmt->execute();
            $procesos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'procesos' => $procesos]);
            break;

        case 'listarCandidatos':
            // Fetch all candidates with their details, filtered by nivel if provided
            $nivel = $_POST['nivel'] ?? '';
            $sql = "
                SELECT c.id_candidato, c.id_alumno, c.id_proceso, c.foto_perfil, c.foto_campaña, c.lema, 
                       a.nombre, a.apellidos, a.dni, p.nombre AS proceso_nombre,
                       CONCAT(a.nombre, ' ', a.apellidos) AS full_name
                FROM candidato c
                JOIN alumnos a ON c.id_alumno = a.id_alumno
                JOIN proceso_electoral p ON c.id_proceso = p.id_proceso
            ";
            $params = [];
            if (!empty($nivel)) {
                $sql .= " WHERE a.nivel = ?";
                $params[] = $nivel;
            }
            $stmt = $conexion->prepare($sql);
            $stmt->execute($params);
            $candidatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'candidatos' => $candidatos]);
            break;

        case 'buscarPorDni':
            // Search for a student by DNI and check if they are already a candidate for the process
            $dni = $_POST['dni'] ?? '';
            $id_proceso = $_POST['id_proceso'] ?? '';

            if (empty($dni) || empty($id_proceso)) {
                echo json_encode(['success' => false, 'message' => 'DNI y proceso electoral son obligatorios']);
                exit;
            }

            // Check if student exists
            $stmt = $conexion->prepare("SELECT id_alumno, nombre, apellidos FROM alumnos WHERE dni = ? AND activo = 1");
            $stmt->execute([$dni]);
            $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$alumno) {
                echo json_encode(['success' => false, 'message' => 'Estudiante no encontrado o no está activo']);
                exit;
            }

            // Check if student is already a candidate for this process
            $stmt = $conexion->prepare("SELECT id_candidato FROM candidato WHERE id_alumno = ? AND id_proceso = ?");
            $stmt->execute([$alumno['id_alumno'], $id_proceso]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'El estudiante ya está registrado como candidato en este proceso']);
                exit;
            }

            echo json_encode(['success' => true, 'alumno' => $alumno]);
            break;

        case 'registrarCandidato':
            // Add a new candidate
            $id_alumno = $_POST['id_alumno'] ?? '';
            $id_proceso = $_POST['id_proceso'] ?? '';
            $lema = trim($_POST['lema'] ?? '');

            // Validate required fields
            if (empty($id_alumno) || empty($id_proceso) || empty($_FILES['foto_perfil']['name']) || empty($_FILES['foto_campaña']['name'])) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos obligatorios deben estar completos']);
                exit;
            }

            // Validate student
            $stmt = $conexion->prepare("SELECT id_alumno FROM alumnos WHERE id_alumno = ? AND activo = 1");
            $stmt->execute([$id_alumno]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'El estudiante no existe o no está activo']);
                exit;
            }

            // Validate electoral process
            $stmt = $conexion->prepare("SELECT id_proceso FROM proceso_electoral WHERE id_proceso = ? AND activo = 1");
            $stmt->execute([$id_proceso]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'El proceso electoral no existe o no está activo']);
                exit;
            }

            // Validate if student is already a candidate for this process
            $stmt = $conexion->prepare("SELECT id_candidato FROM candidato WHERE id_alumno = ? AND id_proceso = ?");
            $stmt->execute([$id_alumno, $id_proceso]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'El estudiante ya está registrado como candidato en este proceso']);
                exit;
            }

            // Process profile photo
            $foto_perfil = $_FILES['foto_perfil'];
            $foto_perfil_name = generateUniqueFileName($foto_perfil['name']);
            $foto_perfil_path = $uploadDir . $foto_perfil_name;
            if (!move_uploaded_file($foto_perfil['tmp_name'], $foto_perfil_path)) {
                echo json_encode(['success' => false, 'message' => 'Error al subir la foto de perfil']);
                exit;
            }

            // Process campaign photo
            $foto_campaña = $_FILES['foto_campaña'];
            $foto_campaña_name = generateUniqueFileName($foto_campaña['name']);
            $foto_campaña_path = $uploadDir . $foto_campaña_name;
            if (!move_uploaded_file($foto_campaña['tmp_name'], $foto_campaña_path)) {
                // Clean up uploaded profile photo if campaign photo fails
                if (file_exists($foto_perfil_path)) {
                    unlink($foto_perfil_path);
                }
                echo json_encode(['success' => false, 'message' => 'Error al subir la foto de campaña']);
                exit;
            }

            // Insert candidate into database
            $stmt = $conexion->prepare("
                INSERT INTO candidato (id_alumno, id_proceso, foto_perfil, foto_campaña, lema)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$id_alumno, $id_proceso, $foto_perfil_name, $foto_campaña_name, $lema]);

            echo json_encode(['success' => true, 'message' => 'Candidato registrado correctamente']);
            break;

        case 'obtenerCandidato':
            // Fetch candidate details
            $id_candidato = $_POST['id'] ?? '';

            if (empty($id_candidato)) {
                echo json_encode(['success' => false, 'message' => 'ID de candidato no proporcionado']);
                exit;
            }

            $stmt = $conexion->prepare("
                SELECT c.id_candidato, c.id_alumno, c.id_proceso, c.foto_perfil, c.foto_campaña, c.lema, 
                       a.nombre AS alumno_nombre, a.apellidos, a.dni, p.nombre AS proceso_nombre,
                       CONCAT(a.nombre, ' ', a.apellidos) AS full_name
                FROM candidato c
                JOIN alumnos a ON c.id_alumno = a.id_alumno
                JOIN proceso_electoral p ON c.id_proceso = p.id_proceso
                WHERE c.id_candidato = ?
            ");
            $stmt->execute([$id_candidato]);
            $candidato = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($candidato) {
                echo json_encode(['success' => true, 'candidato' => $candidato]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Candidato no encontrado']);
            }
            break;

        case 'actualizarCandidato':
            // Update candidate details
            $id_candidato = $_POST['id_candidato'] ?? '';
            $id_alumno = $_POST['id_alumno'] ?? '';
            $id_proceso = $_POST['id_proceso'] ?? '';
            $lema = trim($_POST['lema'] ?? '');

            if (empty($id_candidato) || empty($id_alumno) || empty($id_proceso)) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos obligatorios deben estar completos']);
                exit;
            }

            // Validate candidate
            $stmt = $conexion->prepare("SELECT foto_perfil, foto_campaña FROM candidato WHERE id_candidato = ?");
            $stmt->execute([$id_candidato]);
            $candidato = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$candidato) {
                echo json_encode(['success' => false, 'message' => 'Candidato no encontrado']);
                exit;
            }

            // Validate student
            $stmt = $conexion->prepare("SELECT id_alumno FROM alumnos WHERE id_alumno = ? AND activo = 1");
            $stmt->execute([$id_alumno]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'El estudiante no existe o no está activo']);
                exit;
            }

            // Validate electoral process
            $stmt = $conexion->prepare("SELECT id_proceso FROM proceso_electoral WHERE id_proceso = ? AND activo = 1");
            $stmt->execute([$id_proceso]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'El proceso electoral no existe o no está activo']);
                exit;
            }

            // Check if another candidate exists for this student and process (excluding current candidate)
            $stmt = $conexion->prepare("
                SELECT id_candidato FROM candidato 
                WHERE id_alumno = ? AND id_proceso = ? AND id_candidato != ?
            ");
            $stmt->execute([$id_alumno, $id_proceso, $id_candidato]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'El estudiante ya está registrado como candidato en este proceso']);
                exit;
            }

            // Prepare data for update
            $foto_perfil_path = $candidato['foto_perfil'];
            $foto_campaña_path = $candidato['foto_campaña'];

            // Process new profile photo if uploaded
            if (!empty($_FILES['foto_perfil']['name'])) {
                // Delete old profile photo
                if ($foto_perfil_path && file_exists($uploadDir . $foto_perfil_path)) {
                    unlink($uploadDir . $foto_perfil_path);
                }
                $foto_perfil = $_FILES['foto_perfil'];
                $foto_perfil_name = generateUniqueFileName($foto_perfil['name']);
                $foto_perfil_path = $foto_perfil_name;
                if (!move_uploaded_file($foto_perfil['tmp_name'], $uploadDir . $foto_perfil_path)) {
                    echo json_encode(['success' => false, 'message' => 'Error al subir la nueva foto de perfil']);
                    exit;
                }
            }

            // Process new campaign photo if uploaded
            if (!empty($_FILES['foto_campaña']['name'])) {
                // Delete old campaign photo
                if ($foto_campaña_path && file_exists($uploadDir . $foto_campaña_path)) {
                    unlink($uploadDir . $foto_campaña_path);
                }
                $foto_campaña = $_FILES['foto_campaña'];
                $foto_campaña_name = generateUniqueFileName($foto_campaña['name']);
                $foto_campaña_path = $foto_campaña_name;
                if (!move_uploaded_file($foto_campaña['tmp_name'], $uploadDir . $foto_campaña_path)) {
                    // Clean up new profile photo if campaign photo fails
                    if (isset($foto_perfil_name) && file_exists($uploadDir . $foto_perfil_name)) {
                        unlink($uploadDir . $foto_perfil_name);
                    }
                    echo json_encode(['success' => false, 'message' => 'Error al subir la nueva foto de campaña']);
                    exit;
                }
            }

            // Update candidate in database
            $stmt = $conexion->prepare("
                UPDATE candidato 
                SET id_alumno = ?, id_proceso = ?, foto_perfil = ?, foto_campaña = ?, lema = ?
                WHERE id_candidato = ?
            ");
            $stmt->execute([$id_alumno, $id_proceso, $foto_perfil_path, $foto_campaña_path, $lema, $id_candidato]);

            echo json_encode(['success' => true, 'message' => 'Candidato actualizado correctamente']);
            break;

        case 'eliminarCandidato':
            // Delete candidate
            $id_candidato = $_POST['id'] ?? '';

            if (empty($id_candidato)) {
                echo json_encode(['success' => false, 'message' => 'ID de candidato no proporcionado']);
                exit;
            }

            // Fetch candidate to delete images
            $stmt = $conexion->prepare("SELECT foto_perfil, foto_campaña FROM candidato WHERE id_candidato = ?");
            $stmt->execute([$id_candidato]);
            $candidato = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$candidato) {
                echo json_encode(['success' => false, 'message' => 'Candidato no encontrado']);
                exit;
            }

            // Delete images from server
            if ($candidato['foto_perfil'] && file_exists($uploadDir . $candidato['foto_perfil'])) {
                unlink($uploadDir . $candidato['foto_perfil']);
            }
            if ($candidato['foto_campaña'] && file_exists($uploadDir . $candidato['foto_campaña'])) {
                unlink($uploadDir . $candidato['foto_campaña']);
            }

            // Delete candidate from database
            $stmt = $conexion->prepare("DELETE FROM candidato WHERE id_candidato = ?");
            $stmt->execute([$id_candidato]);

            echo json_encode(['success' => true, 'message' => 'Candidato eliminado correctamente']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>