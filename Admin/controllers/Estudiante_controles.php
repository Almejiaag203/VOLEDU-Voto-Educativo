<?php
include_once '../model/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    try {
        switch ($action) {
            case 'listarEstudiantes':
                // Parámetros de paginación
                $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 20; // Cambiado a 20
                $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
                $offset = ($page - 1) * $limit;

                // Filtro por nivel (opcional)
                $nivel = isset($_POST['nivel']) ? $_POST['nivel'] : null;

                // Construir la cláusula WHERE
                $where = "WHERE activo = 1";
                if ($nivel) {
                    $where .= " AND nivel = :nivel";
                }

                // Contar el total de registros activos con filtro
                $sql_count = "SELECT COUNT(*) as total FROM alumnos $where";
                $stmt = $conexion->prepare($sql_count);
                if ($nivel) {
                    $stmt->bindParam(':nivel', $nivel);
                }
                $stmt->execute();
                $totalRecords = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

                // Obtener los registros de la página actual con filtro
                $sql_select = "
                    SELECT id_alumno, nombre, apellidos, dni, grado, seccion, nivel, activo
                    FROM alumnos
                    $where
                    ORDER BY nombre
                    LIMIT :limit OFFSET :offset
                ";
                $stmt = $conexion->prepare($sql_select);
                if ($nivel) {
                    $stmt->bindParam(':nivel', $nivel);
                }
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();
                $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    'success' => true,
                    'estudiantes' => $estudiantes,
                    'total' => (int)$totalRecords
                ]);
                break;

            case 'obtenerEstudiante':
                $id = $_POST['id'];
                $stmt = $conexion->prepare("
                    SELECT id_alumno, nombre, apellidos, dni, grado, seccion, nivel, activo
                    FROM alumnos
                    WHERE id_alumno = :id AND activo = 1
                ");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($estudiante) {
                    echo json_encode(['success' => true, 'estudiante' => $estudiante]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Estudiante no encontrado o inactivo']);
                }
                break;

            case 'registrarEstudiante':
                $nombre = trim($_POST['nombre']);
                $apellidos = trim($_POST['apellidos']);
                $dni = trim($_POST['dni']);
                $grado = trim($_POST['grado']);
                $seccion = trim($_POST['seccion']);
                $nivel = trim($_POST['nivel']);
                $activo = 1;
                $password = password_hash('default123', PASSWORD_DEFAULT); // Default password
                $id_rol = 2; // Miembro de Mesa
                $fecha_creacion = date('Y-m-d H:i:s');

                // Validate required fields
                if (empty($nombre) || empty($apellidos) || empty($dni) || empty($grado) || empty($seccion) || empty($nivel)) {
                    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
                    exit;
                }

                // Check if DNI already exists
                $stmt = $conexion->prepare("SELECT COUNT(*) FROM alumnos WHERE dni = :dni");
                $stmt->bindParam(':dni', $dni);
                $stmt->execute();
                if ($stmt->fetchColumn() > 0) {
                    echo json_encode(['success' => false, 'message' => 'El DNI ya está registrado']);
                    exit;
                }

                // Start transaction
                $conexion->beginTransaction();

                // Insert student
                $stmt = $conexion->prepare("
                    INSERT INTO alumnos (nombre, apellidos, dni, grado, seccion, nivel, activo)
                    VALUES (:nombre, :apellidos, :dni, :grado, :seccion, :nivel, :activo)
                ");
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':apellidos', $apellidos);
                $stmt->bindParam(':dni', $dni);
                $stmt->bindParam(':grado', $grado);
                $stmt->bindParam(':seccion', $seccion);
                $stmt->bindParam(':nivel', $nivel);
                $stmt->bindParam(':activo', $activo, PDO::PARAM_INT);
                $stmt->execute();
                $id_alumno = $conexion->lastInsertId();

                // Insert user with empty usuario (trigger generates it)
                $stmt = $conexion->prepare("
                    INSERT INTO usuario (id_rol, nombre, apellido, usuario, password, estado, fecha_creacion)
                    VALUES (:id_rol, :nombre, :apellido, '', :password, 1, :fecha_creacion)
                ");
                $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':apellido', $apellidos);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':fecha_creacion', $fecha_creacion);
                $stmt->execute();
                $id_usuario = $conexion->lastInsertId();

                // Fetch generated username
                $stmt = $conexion->prepare("SELECT usuario FROM usuario WHERE id_usuario = :id");
                $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
                $stmt->execute();
                $generatedUsuario = $stmt->fetchColumn();

                $conexion->commit();
                echo json_encode(['success' => true, 'message' => 'Estudiante registrado con éxito', 'usuario' => $generatedUsuario]);
                break;

            case 'actualizarEstudiante':
                $id = $_POST['id_alumno'];
                $nombre = trim($_POST['nombre']);
                $apellidos = trim($_POST['apellidos']);
                $dni = trim($_POST['dni']);
                $grado = trim($_POST['grado']);
                $seccion = trim($_POST['seccion']);
                $nivel = trim($_POST['nivel']);

                // Validate required fields
                if (empty($nombre) || empty($apellidos) || empty($dni) || empty($grado) || empty($seccion) || empty($nivel)) {
                    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
                    exit;
                }

                // Check if DNI already exists (excluding current student)
                $stmt = $conexion->prepare("SELECT COUNT(*) FROM alumnos WHERE dni = :dni AND id_alumno != :id");
                $stmt->bindParam(':dni', $dni);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->fetchColumn() > 0) {
                    echo json_encode(['success' => false, 'message' => 'El DNI ya está registrado']);
                    exit;
                }

                $stmt = $conexion->prepare("
                    UPDATE alumnos
                    SET nombre = :nombre, apellidos = :apellidos, dni = :dni, grado = :grado, seccion = :seccion, nivel = :nivel
                    WHERE id_alumno = :id AND activo = 1
                ");
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':apellidos', $apellidos);
                $stmt->bindParam(':dni', $dni);
                $stmt->bindParam(':grado', $grado);
                $stmt->bindParam(':seccion', $seccion);
                $stmt->bindParam(':nivel', $nivel);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->rowCount() === 0) {
                    echo json_encode(['success' => false, 'message' => 'Estudiante no encontrado o inactivo']);
                    exit;
                }

                echo json_encode(['success' => true, 'message' => 'Estudiante actualizado con éxito']);
                break;

            case 'eliminarEstudiante':
                $id = $_POST['id'];
                // Check for dependencies
                $stmt = $conexion->prepare("
                    SELECT COUNT(*) 
                    FROM asignacion_mesa 
                    WHERE id_alumno = :id
                    UNION ALL
                    SELECT COUNT(*) 
                    FROM candidato 
                    WHERE id_alumno = :id
                ");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $counts = $stmt->fetchAll(PDO::FETCH_COLUMN);
                if (array_sum($counts) > 0) {
                    echo json_encode(['success' => false, 'message' => 'No se puede eliminar el estudiante porque está asociado a mesas o candidaturas']);
                    exit;
                }

                $stmt = $conexion->prepare("UPDATE alumnos SET activo = 0 WHERE id_alumno = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->rowCount() === 0) {
                    echo json_encode(['success' => false, 'message' => 'Estudiante no encontrado']);
                    exit;
                }
                echo json_encode(['success' => true, 'message' => 'Estudiante eliminado con éxito']);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Acción no válida']);
                break;
        }
    } catch (PDOException $e) {
        if (isset($conexion)) $conexion->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
?>