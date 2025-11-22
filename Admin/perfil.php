<?php
session_start(); // Iniciar la sesión
// Verificar rol - Solo Administrador
if ($_SESSION['rol'] !== 'Administrador') {
    header("Location: ../login/login.php");
    exit;
}
?>

<?php include 'include/header.php'; ?>

<?php
// Incluir la conexión a la base de datos
require_once 'model/conexion.php';

// Obtener datos del usuario logueado
$id_usuario = $_SESSION['id_usuario'];
$sql = "SELECT nombre, apellidos, rol FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Manejar el cambio de contraseña
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_contrasena'])) {
    $contrasena_actual = $_POST['contrasena_actual'] ?? '';
    $nueva_contrasena = $_POST['nueva_contrasena'] ?? '';
    $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';

    if (!empty($contrasena_actual) && !empty($nueva_contrasena) && !empty($confirmar_contrasena)) {
        // Verificar contraseña actual
        $sql = "SELECT contrasena FROM usuarios WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();
        $stmt->close();

        if (password_verify($contrasena_actual, $usuario['contrasena'])) {
            if ($nueva_contrasena === $confirmar_contrasena) {
                // Hash de la nueva contraseña
                $nueva_contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET contrasena = ? WHERE id_usuario = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $nueva_contrasena_hash, $id_usuario);
                if ($stmt->execute()) {
                    $msg = '<div class="alert alert-success">Contraseña cambiada exitosamente.</div>';
                } else {
                    $msg = '<div class="alert alert-danger">Error al cambiar la contraseña.</div>';
                }
                $stmt->close();
            } else {
                $msg = '<div class="alert alert-danger">La nueva contraseña y la confirmación no coinciden.</div>';
            }
        } else {
            $msg = '<div class="alert alert-danger">La contraseña actual es incorrecta.</div>';
        }
    } else {
        $msg = '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
    }
}
?>

<!-- [ Layout content ] Start -->
<div class="layout-content">
    <!-- [ content ] Start -->
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">GESTIÓN DE ESTUDIANTES</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item">Estudiantes</li>
            </ol>
        </div>

        <!-- Perfil del usuario -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Mi Perfil</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($user['nombre']); ?></p>
                        <p><strong>Apellidos:</strong> <?php echo htmlspecialchars($user['apellidos']); ?></p>
                        <p><strong>Rol:</strong> <?php echo htmlspecialchars($user['rol']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cambio de contraseña -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Cambiar Contraseña</h5>
            </div>
            <div class="card-body">
                <?php echo $msg; ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="contrasena_actual">Contraseña Actual</label>
                        <input type="password" name="contrasena_actual" id="contrasena_actual" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="nueva_contrasena">Nueva Contraseña</label>
                        <input type="password" name="nueva_contrasena" id="nueva_contrasena" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmar_contrasena">Confirmar Nueva Contraseña</label>
                        <input type="password" name="confirmar_contrasena" id="confirmar_contrasena" class="form-control" required>
                    </div>
                    <button type="submit" name="cambiar_contrasena" class="btn btn-primary">Cambiar Contraseña</button>
                </form>
            </div>
        </div>
    </div>
    <!-- [ content ] End -->
</div>
<!-- [ Layout content ] End -->

<?php include 'include/footer.php'; ?>
