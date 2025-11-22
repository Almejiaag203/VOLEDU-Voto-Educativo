<?php
        session_start(); // Iniciar la sesión
        // Verificar rol - Solo Administrador
        if ($_SESSION['rol'] !== 'Administrador') {
            header("Location: ../login/login.php");
            exit;
        }
        ?>

<?php include_once 'include/header.php'; ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Registro de Usuarios</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Registro de Usuarios</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white d-flex align-items-center">
                    <h6 class="m-0 font-weight-bold">Gestión de Usuarios</h6>
                    <button class="btn btn-warning btn-sm ml-auto" data-toggle="modal" data-target="#usuarioModal">
                        <i class="fas fa-plus mr-1"></i> Registrar Nuevo Usuario
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="usuariosTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los usuarios se cargarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Registrar Usuario -->
<div class="modal fade" id="usuarioModal" tabindex="-1" role="dialog" aria-labelledby="usuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="usuarioModalLabel">Registrar Nuevo Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="usuarioForm" novalidate>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                                <div class="invalid-feedback">El nombre es obligatorio.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellido">Apellido <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="apellido" name="apellido" required>
                                <div class="invalid-feedback">El apellido es obligatorio.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" id="usuario" name="usuario" style="display: none;">
                                <div class="invalid-feedback">El usuario debe ser único si se proporciona.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="invalid-feedback">La contraseña es obligatoria.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_rol">Rol <span class="text-danger">*</span></label>
                                <select class="form-control" id="id_rol" name="id_rol" required>
                                    <option value="">Seleccione un rol</option>
                                </select>
                                <div class="invalid-feedback">Por favor, seleccione un rol.</div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Registrar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Detalles -->
<div class="modal fade" id="viewUsuarioModal" tabindex="-1" role="dialog" aria-labelledby="viewUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-gradient-info text-white">
                <h5 class="modal-title" id="viewUsuarioModalLabel">
                    <i class="fas fa-user mr-2"></i> Detalles del Usuario
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center">
                    <i class="fas fa-user fa-4x mb-3 text-primary"></i>
                    <h5 id="viewUsuarioTitle" class="font-weight-bold"></h5>
                </div>
                <div class="mt-3">
                    <p><strong>Nombre:</strong> <span id="view_nombre"></span></p>
                    <p><strong>Apellido:</strong> <span id="view_apellido"></span></p>
                    <p><strong>Usuario:</strong> <span id="view_usuario"></span></p>
                    <p><strong>Rol:</strong> <span id="view_rol"></span></p>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="editUsuarioModal" tabindex="-1" role="dialog" aria-labelledby="editUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="editUsuarioModalLabel">Editar Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editUsuarioForm" novalidate>
                    <input type="hidden" id="edit_id_usuario" name="id_usuario">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nombre">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                                <div class="invalid-feedback">El nombre es obligatorio.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_apellido">Apellido <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_apellido" name="apellido" required>
                                <div class="invalid-feedback">El apellido es obligatorio.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_usuario">Usuario <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_usuario" name="usuario" required>
                                <div class="invalid-feedback">El usuario es obligatorio y debe ser único.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_password">Contraseña (dejar en blanco para no cambiar)</label>
                                <input type="password" class="form-control" id="edit_password" name="password">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_id_rol">Rol <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_id_rol" name="id_rol" required>
                                    <option value="">Seleccione un rol</option>
                                </select>
                                <div class="invalid-feedback">Por favor, seleccione un rol.</div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-info">Actualizar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const usuarioForm = document.getElementById('usuarioForm');
        const editUsuarioForm = document.getElementById('editUsuarioForm');
        const idRolSelect = document.getElementById('id_rol');
        const editIdRolSelect = document.getElementById('edit_id_rol');
        const usuariosTableBody = document.querySelector('#usuariosTable tbody');

        // Cargar roles para los modales
        fetch('controllers/Usuario_controles.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: 'listarRoles'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    idRolSelect.innerHTML = '<option value="">Seleccione un rol</option>';
                    editIdRolSelect.innerHTML = '<option value="">Seleccione un rol</option>';
                    data.roles.forEach(rol => {
                        idRolSelect.innerHTML += `<option value="${rol.id_rol}">${rol.nombre}</option>`;
                        editIdRolSelect.innerHTML += `<option value="${rol.id_rol}">${rol.nombre}</option>`;
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#3085d6',
                        customClass: {
                            popup: 'swal2-zindex'
                        }
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'Error al cargar los roles: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#3085d6',
                    customClass: {
                        popup: 'swal2-zindex'
                    }
                });
            });

        // Cargar usuarios en la tabla
        function loadUsuarios() {
            fetch('controllers/Usuario_controles.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'listarUsuarios'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        usuariosTableBody.innerHTML = '';
                        if (data.usuarios.length === 0) {
                            usuariosTableBody.innerHTML = '<tr><td colspan="5" class="text-center">No hay usuarios registrados.</td></tr>';
                            return;
                        }
                        data.usuarios.forEach(usuario => {
                            usuariosTableBody.innerHTML += `
                                <tr data-id="${usuario.id_usuario}">
                                    <td>${usuario.nombre}</td>
                                    <td>${usuario.apellido}</td>
                                    <td>${usuario.usuario}</td>
                                    <td>${usuario.rol}</td>
                                    <td>
                                        <a href="#" class="btn btn-info btn-sm view-usuario" data-id="${usuario.id_usuario}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-warning btn-sm edit-usuario" data-id="${usuario.id_usuario}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-danger btn-sm delete-usuario" data-id="${usuario.id_usuario}">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#3085d6',
                            customClass: {
                                popup: 'swal2-zindex'
                            }
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al cargar los usuarios: ' + error.message,
                        icon: 'error',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#3085d6',
                        customClass: {
                            popup: 'swal2-zindex'
                        }
                    });
                });
        }

        // Validación y registro de usuario
        usuarioForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!usuarioForm.checkValidity()) {
                e.stopPropagation();
                usuarioForm.classList.add('was-validated');
                return;
            }

            const formData = new FormData(usuarioForm);
            formData.append('action', 'registrarUsuario');

            fetch('controllers/Usuario_controles.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Éxito',
                            text: data.message + (data.usuario ? ` Usuario generado: ${data.usuario}` : ''),
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#3085d6',
                            customClass: {
                                popup: 'swal2-zindex'
                            }
                        }).then(() => {
                            $('#usuarioModal').modal('hide');
                            usuarioForm.reset();
                            usuarioForm.classList.remove('was-validated');
                            loadUsuarios();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#3085d6',
                            customClass: {
                                popup: 'swal2-zindex'
                            }
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al registrar el usuario: ' + error.message,
                        icon: 'error',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#3085d6',
                        customClass: {
                            popup: 'swal2-zindex'
                        }
                    });
                });
        });

        // Validación y edición de usuario
        editUsuarioForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!editUsuarioForm.checkValidity()) {
                e.stopPropagation();
                editUsuarioForm.classList.add('was-validated');
                return;
            }

            const formData = new FormData(editUsuarioForm);
            formData.append('action', 'actualizarUsuario');

            fetch('controllers/Usuario_controles.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Éxito',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#3085d6',
                            customClass: {
                                popup: 'swal2-zindex'
                            }
                        }).then(() => {
                            $('#editUsuarioModal').modal('hide');
                            editUsuarioForm.reset();
                            editUsuarioForm.classList.remove('was-validated');
                            loadUsuarios();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#3085d6',
                            customClass: {
                                popup: 'swal2-zindex'
                            }
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al actualizar el usuario: ' + error.message,
                        icon: 'error',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#3085d6',
                        customClass: {
                            popup: 'swal2-zindex'
                        }
                    });
                });
        });

        // Ver detalles de un usuario
        document.addEventListener('click', function(e) {
            if (e.target.closest('.view-usuario')) {
                e.preventDefault();
                const idUsuario = e.target.closest('.view-usuario').getAttribute('data-id');
                fetch('controllers/Usuario_controles.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            action: 'obtenerUsuario',
                            id: idUsuario
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const usuario = data.usuario;
                            document.getElementById('viewUsuarioTitle').textContent = `${usuario.nombre} ${usuario.apellido}`;
                            document.getElementById('view_nombre').textContent = usuario.nombre;
                            document.getElementById('view_apellido').textContent = usuario.apellido;
                            document.getElementById('view_usuario').textContent = usuario.usuario;
                            document.getElementById('view_rol').textContent = usuario.rol;
                            $('#viewUsuarioModal').modal('show');
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'Aceptar',
                                confirmButtonColor: '#3085d6',
                                customClass: {
                                    popup: 'swal2-zindex'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error al obtener el usuario: ' + error.message,
                            icon: 'error',
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#3085d6',
                            customClass: {
                                popup: 'swal2-zindex'
                            }
                        });
                    });
            }
        });

        // Editar usuario
        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-usuario')) {
                e.preventDefault();
                const idUsuario = e.target.closest('.edit-usuario').getAttribute('data-id');
                fetch('controllers/Usuario_controles.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            action: 'obtenerUsuario',
                            id: idUsuario
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const usuario = data.usuario;
                            document.getElementById('edit_id_usuario').value = usuario.id_usuario;
                            document.getElementById('edit_nombre').value = usuario.nombre;
                            document.getElementById('edit_apellido').value = usuario.apellido;
                            document.getElementById('edit_usuario').value = usuario.usuario;
                            document.getElementById('edit_id_rol').value = usuario.id_rol || '';
                            document.getElementById('edit_password').value = '';
                            $('#editUsuarioModal').modal('show');
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'Aceptar',
                                confirmButtonColor: '#3085d6',
                                customClass: {
                                    popup: 'swal2-zindex'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error al obtener el usuario: ' + error.message,
                            icon: 'error',
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#3085d6',
                            customClass: {
                                popup: 'swal2-zindex'
                            }
                        });
                    });
            }
        });

        // Eliminar usuario
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-usuario')) {
                e.preventDefault();
                const idUsuario = e.target.closest('.delete-usuario').getAttribute('data-id');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'No podrás revertir esta acción.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        popup: 'swal2-zindex'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('controllers/Usuario_controles.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: new URLSearchParams({
                                    action: 'eliminarUsuario',
                                    id: idUsuario
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Eliminado',
                                        text: data.message,
                                        icon: 'success',
                                        confirmButtonText: 'Aceptar',
                                        confirmButtonColor: '#3085d6',
                                        customClass: {
                                            popup: 'swal2-zindex'
                                        }
                                    }).then(() => {
                                        loadUsuarios();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: data.message,
                                        icon: 'error',
                                        confirmButtonText: 'Aceptar',
                                        confirmButtonColor: '#3085d6',
                                        customClass: {
                                            popup: 'swal2-zindex'
                                        }
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Error al eliminar el usuario: ' + error.message,
                                    icon: 'error',
                                    confirmButtonText: 'Aceptar',
                                    confirmButtonColor: '#3085d6',
                                    customClass: {
                                        popup: 'swal2-zindex'
                                    }
                                });
                            });
                    }
                });
            }
        });

        // Agregar clase CSS para SweetAlert2 z-index
        const style = document.createElement('style');
        style.innerHTML = `
            .swal2-zindex {
                z-index: 9999 !important;
            }
        `;
        document.head.appendChild(style);

        // Cargar usuarios al iniciar
        loadUsuarios();
    });
</script>

<?php include_once 'include/footer.php'; ?>