<?php
include_once '../model/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    try {
        switch ($action) {
            case 'listarRoles':
                $stmt = $conexion->prepare("SELECT id_rol, nombre FROM rol WHERE nombre IN ('Administrador', 'Miembro de Mesa')");
                $stmt->execute();
                $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'roles' => $roles]);
                break;

            case 'listarUsuarios':
                $stmt = $conexion->prepare("
                    SELECT u.id_usuario, u.nombre, u.apellido, u.usuario, r.nombre as rol, u.id_rol
                    FROM usuario u
                    JOIN rol r ON u.id_rol = r.id_rol
                    WHERE u.estado = 1
                    ORDER BY u.nombre
                ");
                $stmt->execute();
                $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'usuarios' => $usuarios]);
                break;

            case 'obtenerUsuario':
                $id = $_POST['id'];
                $stmt = $conexion->prepare("
                    SELECT u.id_usuario, u.nombre, u.apellido, u.usuario, r.nombre as rol, u.id_rol
                    FROM usuario u
                    JOIN rol r ON u.id_rol = r.id_rol
                    WHERE u.id_usuario = :id AND u.estado = 1
                ");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($usuario) {
                    echo json_encode(['success' => true, 'usuario' => $usuario]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado o inactivo']);
                }
                break;

            case 'registrarUsuario':
                $nombre = trim($_POST['nombre']);
                $apellido = trim($_POST['apellido']);
                $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
                $password = !empty($_POST['password']) ? password_hash(trim($_POST['password']), PASSWORD_DEFAULT) : password_hash('default123', PASSWORD_DEFAULT);
                $id_rol = $_POST['id_rol'];
                $fecha_creacion = date('Y-m-d H:i:s');

                // Validate required fields
                if (empty($nombre) || empty($apellido) || empty($id_rol)) {
                    echo json_encode(['success' => false, 'message' => 'Nombre, apellido y rol son obligatorios']);
                    exit;
                }

                // If usuario is provided, check for uniqueness
                if (!empty($usuario)) {
                    $stmt = $conexion->prepare("SELECT COUNT(*) FROM usuario WHERE usuario = :usuario");
                    $stmt->bindParam(':usuario', $usuario);
                    $stmt->execute();
                    if ($stmt->fetchColumn() > 0) {
                        echo json_encode(['success' => false, 'message' => 'El usuario ya existe']);
                        exit;
                    }
                }

                $stmt = $conexion->prepare("
                    INSERT INTO usuario (id_rol, nombre, apellido, usuario, password, estado, fecha_creacion)
                    VALUES (:id_rol, :nombre, :apellido, :usuario, :password, 1, :fecha_creacion)
                ");
                $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':apellido', $apellido);
                $stmt->bindParam(':usuario', $usuario);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':fecha_creacion', $fecha_creacion);
                $stmt->execute();

                // Fetch the generated username
                $lastId = $conexion->lastInsertId();
                $stmt = $conexion->prepare("SELECT usuario FROM usuario WHERE id_usuario = :id");
                $stmt->bindParam(':id', $lastId, PDO::PARAM_INT);
                $stmt->execute();
                $generatedUsuario = $stmt->fetchColumn();

                echo json_encode(['success' => true, 'message' => 'Usuario registrado con éxito', 'usuario' => $generatedUsuario]);
                break;

            case 'actualizarUsuario':
                $id = $_POST['id_usuario'];
                $nombre = trim($_POST['nombre']);
                $apellido = trim($_POST['apellido']);
                $usuario = trim($_POST['usuario']);
                $id_rol = $_POST['id_rol'];
                $password = !empty($_POST['password']) ? password_hash(trim($_POST['password']), PASSWORD_DEFAULT) : null;

                // Validate required fields
                if (empty($nombre) || empty($apellido) || empty($usuario) || empty($id_rol)) {
                    echo json_encode(['success' => false, 'message' => 'Nombre, apellido, usuario y rol son obligatorios']);
                    exit;
                }

                // Check if usuario already exists (excluding current user)
                $stmt = $conexion->prepare("SELECT COUNT(*) FROM usuario WHERE usuario = :usuario AND id_usuario != :id");
                $stmt->bindParam(':usuario', $usuario);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->fetchColumn() > 0) {
                    echo json_encode(['success' => false, 'message' => 'El usuario ya existe']);
                    exit;
                }

                if ($password) {
                    $stmt = $conexion->prepare("
                        UPDATE usuario
                        SET id_rol = :id_rol, nombre = :nombre, apellido = :apellido, usuario = :usuario, password = :password
                        WHERE id_usuario = :id AND estado = 1
                    ");
                    $stmt->bindParam(':password', $password);
                } else {
                    $stmt = $conexion->prepare("
                        UPDATE usuario
                        SET id_rol = :id_rol, nombre = :nombre, apellido = :apellido, usuario = :usuario
                        WHERE id_usuario = :id AND estado = 1
                    ");
                }
                $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':apellido', $apellido);
                $stmt->bindParam(':usuario', $usuario);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->rowCount() === 0) {
                    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado o inactivo']);
                    exit;
                }

                echo json_encode(['success' => true, 'message' => 'Usuario actualizado con éxito']);
                break;

            case 'eliminarUsuario':
                $id = $_POST['id'];
                $stmt = $conexion->prepare("UPDATE usuario SET estado = 0 WHERE id_usuario = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->rowCount() === 0) {
                    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
                    exit;
                }
                echo json_encode(['success' => true, 'message' => 'Usuario eliminado con éxito']);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Acción no válida']);
                break;
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
?>