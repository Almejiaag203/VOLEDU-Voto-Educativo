<?php
ob_start(); // Iniciar buffer de salida para permitir headers después de output
session_start(); // Iniciar sesión general
include "../model/conexion.php";

if (isset($_POST["btningresar"])) {
    $usuario = trim($_POST["usuario"] ?? '');
    $password = trim($_POST["password"] ?? '');

    try {
        if (empty($usuario) || empty($password)) {
            echo "<script>
                Swal.fire({
                    title: 'Error',
                    text: 'Usuario y contraseña son requeridos',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            </script>";
        } else {
            $sql = "SELECT u.id_usuario, u.usuario, u.password, u.nombre, u.apellido, u.estado, r.nombre AS rol 
                    FROM usuario u 
                    INNER JOIN rol r ON u.id_rol = r.id_rol 
                    WHERE u.usuario = :usuario AND u.estado = 1";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultado) {
                if (password_verify($password, $resultado['password'])) {
                    $_SESSION['id_usuario'] = $resultado['id_usuario'];
                    $_SESSION['usuario'] = $resultado['usuario'];
                    $_SESSION['nombre'] = $resultado['nombre'];
                    $_SESSION['apellido'] = $resultado['apellido'];
                    $_SESSION['rol'] = $resultado['rol'];

                    if ($resultado['rol'] === "Administrador") {
                        $_SESSION['admin_usuario'] = true;
                        header("Location: ../admin/index.php");
                        exit;
                    } elseif ($resultado['rol'] === "Miembro de Mesa") {
                        $_SESSION['usuario_autenticado'] = true;
                        header("Location: ../miembro/index.php");
                        exit;
                    } else {
                        echo "<script>
                            Swal.fire({
                                title: 'Error',
                                text: 'Rol no autorizado para este sistema',
                                icon: 'error',
                                confirmButtonText: 'Aceptar'
                            });
                        </script>";
                    }
                } else {
                    echo "<script>
                        Swal.fire({
                            title: 'Error',
                            text: 'Usuario o contraseña incorrectos',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    </script>";
                }
            } else {
                echo "<script>
                    Swal.fire({
                        title: 'Error',
                        text: 'Usuario o contraseña incorrectos',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                </script>";
            }
        }
    } catch (PDOException $e) {
        error_log("Error en login a las " . date('Y-m-d H:i:s') . ": " . $e->getMessage());
        echo "<script>
            Swal.fire({
                title: 'Error',
                text: 'Error al procesar la solicitud: " . addslashes($e->getMessage()) . "',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LOGIN | VOLEDU</title>
  <link rel="icon" type="image/x-icon" href="img/logo.png">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome for icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <!-- MDB UI Kit -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.0/mdb.min.css" rel="stylesheet">
  <style>
    .divider:after,
    .divider:before {
      content: "";
      flex: 1;
      height: 1px;
      background: #eee;
    }
    .h-custom {
      height: calc(100% - 73px);
    }
    @media (max-width: 450px) {
      .h-custom {
        height: 100%;
      }
    }
    .password-container {
      position: relative;
    }
    .password-toggle {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
    }
  </style>
</head>
<body>
  <section class="vh-100">
    <div class="container-fluid h-custom">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-md-9 col-lg-6 col-xl-5">
          <img src="img/logo.png" class="img-fluid" alt="Sample image">
        </div>
        <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
          <form id="loginForm" method="POST" action="login.php">
            <div class="divider d-flex align-items-center my-4">
              <p class="text-center fw-bold mx-3 mb-0">Iniciar Sesion</p>
            </div>

            <!-- User input -->
            <div data-mdb-input-init class="form-outline mb-4">
              <input type="text" id="form3Example3" name="usuario" class="form-control form-control-lg"
                placeholder="Enter your username" />
              <label class="form-label" for="form3Example3">Usuario</label>
            </div>

            <!-- Password input -->
            <div data-mdb-input-init class="form-outline mb-3 password-container">
              <input type="password" id="form3Example4" name="password" class="form-control form-control-lg"
                placeholder="Enter password" />
              <i class="fas fa-eye password-toggle" id="togglePassword"></i>
              <label class="form-label" for="form3Example4">Password</label>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <!-- Checkbox -->
              <div class="form-check mb-0">
                <input class="form-check-input me-2" type="checkbox" value="" id="form2Example3" />
                <label class="form-check-label" for="form2Example3">
                  Remember me
                </label>
              </div>
            </div>

            <div class="text-center text-lg-start mt-4 pt-2">
              <button type="submit" name="btningresar" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
                style="padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-primary">
      <!-- Copyright -->
      <div class="text-white mb-3 mb-md-0" id="copyright">
        Copyright © 2025. TechFusion Data
      </div>
      <!-- Right -->
      <div>
        <a href="https://www.facebook.com/TechFusionData" class="text-white me-4">
          <i class="fab fa-facebook-f"></i>
        </a>
      </div>
      <!-- Right -->
    </div>
  </section>

  <!-- Bootstrap JS and MDB UI Kit JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.0/mdb.min.js"></script>
  <script src="js/copyright.js"></script>
  <script>
    document.getElementById('togglePassword').addEventListener('click', function () {
      const passwordInput = document.getElementById('form3Example4');
      const icon = this;
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    });
  </script>
</body>
</html>