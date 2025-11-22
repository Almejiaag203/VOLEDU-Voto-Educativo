<?php
session_start(); // Iniciar la sesión
// Verificar rol - Solo Administrador
if ($_SESSION['rol'] !== 'Administrador') {
    header("Location: ../login/login.php");
    exit;
}
?>

<?php include 'include/header.php'; ?>

<!-- [ Layout content ] Start -->
<div class="layout-content">
    <!-- [ content ] Start -->
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">GESTIÓN DE ESTUDIANTES - PRIMARIA</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item">Estudiantes Primaria</li>
            </ol>
        </div>

        <!-- Botón para abrir el modal de registrar estudiante -->
        <div class="mb-4">
            <button class="btn btn-primary" data-toggle="modal" data-target="#registrarEstudianteModal">
                <i class="feather icon-plus"></i> Registrar Estudiante Primaria
            </button>
        </div>

        <!-- Tabla para mostrar estudiantes -->
        <div class="card">
            <h6 class="card-header">Lista de Estudiantes Primaria</h6>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="estudiantes-table">
                        <thead>
                            <tr>
                                <th>Nombre y Apellido</th>
                                <th>Grado</th>
                                <th>Sección</th>
                                <th>Nivel</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los estudiantes se cargarán dinámicamente -->
                        </tbody>
                    </table>
                </div>
                <!-- Paginación elegante -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center" id="pagination">
                        <li class="page-item" id="prev-page">
                            <a class="page-link" href="javascript:void(0)" aria-label="Previous">Previous</a>
                        </li>
                        <!-- Los números se generarán dinámicamente -->
                        <li class="page-item active"><a class="page-link" href="javascript:void(0)" data-page="1">1</a></li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="2">2</a></li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="3">3</a></li>
                        <li class="page-item" id="next-page">
                            <a class="page-link" href="javascript:void(0)" aria-label="Next">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Modal para registrar estudiante -->
        <div class="modal fade" id="registrarEstudianteModal" tabindex="-1" role="dialog" aria-labelledby="registrarEstudianteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 10px;">
                    <div class="modal-header bg-primary text-white" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
                        <h5 class="modal-title" id="registrarEstudianteModalLabel">Registrar Nuevo Estudiante Primaria</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="registrarEstudianteForm" novalidate>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre" class="font-weight-bold">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                                        <div class="invalid-feedback">El nombre es obligatorio.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="apellidos" class="font-weight-bold">Apellidos <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                                        <div class="invalid-feedback">Los apellidos son obligatorios.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dni" class="font-weight-bold">DNI <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="dni" name="dni" required>
                                        <div class="invalid-feedback">El DNI es obligatorio.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="grado" class="font-weight-bold">Grado <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="grado" name="grado" required>
                                        <div class="invalid-feedback">El grado es obligatorio.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="seccion" class="font-weight-bold">Sección <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="seccion" name="seccion" required>
                                        <div class="invalid-feedback">La sección es obligatoria.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nivel" class="font-weight-bold">Nivel <span class="text-danger">*</span></label>
                                        <select class="form-control" id="nivel" name="nivel" required>
                                            <option value="Primaria" selected>Primaria</option>
                                        </select>
                                        <div class="invalid-feedback">El nivel es obligatorio.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary"><i class="feather icon-save"></i> Registrar</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para ver detalles de estudiante -->
        <div class="modal fade" id="verEstudianteModal" tabindex="-1" role="dialog" aria-labelledby="verEstudianteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-gradient-info text-white">
                        <h5 class="modal-title" id="verEstudianteModalLabel">
                            <i class="feather icon-user mr-2"></i> Detalles del Estudiante
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="text-center">
                            <i class="feather icon-user fa-4x mb-3 text-primary"></i>
                            <h5 id="verEstudianteTitle" class="font-weight-bold"></h5>
                        </div>
                        <div class="mt-3">
                            <p><strong>Nombre:</strong> <span id="ver_nombre"></span></p>
                            <p><strong>Apellidos:</strong> <span id="ver_apellidos"></span></p>
                            <p><strong>DNI:</strong> <span id="ver_dni"></span></p>
                            <p><strong>Grado:</strong> <span id="ver_grado"></span></p>
                            <p><strong>Sección:</strong> <span id="ver_seccion"></span></p>
                            <p><strong>Nivel:</strong> <span id="ver_nivel"></span></p>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                            <i class="feather icon-x mr-1"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para editar estudiante -->
        <div class="modal fade" id="editarEstudianteModal" tabindex="-1" role="dialog" aria-labelledby="editarEstudianteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 10px;">
                    <div class="modal-header bg-info text-white" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
                        <h5 class="modal-title" id="editarEstudianteModalLabel">Editar Estudiante Primaria</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="editarEstudianteForm" novalidate>
                            <input type="hidden" id="editar_id_alumno" name="id_alumno">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editar_nombre" class="font-weight-bold">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editar_nombre" name="nombre" required>
                                        <div class="invalid-feedback">El nombre es obligatorio.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editar_apellidos" class="font-weight-bold">Apellidos <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editar_apellidos" name="apellidos" required>
                                        <div class="invalid-feedback">Los apellidos son obligatorios.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editar_dni" class="font-weight-bold">DNI <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editar_dni" name="dni" required>
                                        <div class="invalid-feedback">El DNI es obligatorio.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editar_grado" class="font-weight-bold">Grado <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editar_grado" name="grado" required>
                                        <div class="invalid-feedback">El grado es obligatorio.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editar_seccion" class="font-weight-bold">Sección <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editar_seccion" name="seccion" required>
                                        <div class="invalid-feedback">La sección es obligatoria.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editar_nivel" class="font-weight-bold">Nivel <span class="text-danger">*</span></label>
                                        <select class="form-control" id="editar_nivel" name="nivel" required>
                                            <option value="Primaria" selected>Primaria</option>
                                        </select>
                                        <div class="invalid-feedback">El nivel es obligatorio.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary"><i class="feather icon-save"></i> Actualizar</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para eliminar estudiante -->
        <div class="modal fade" id="eliminarEstudianteModal" tabindex="-1" role="dialog" aria-labelledby="eliminarEstudianteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-gradient-danger text-white">
                        <h5 class="modal-title" id="eliminarEstudianteModalLabel">
                            <i class="feather icon-trash-2 mr-2"></i> Confirmar Eliminación
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <i class="feather icon-alert-triangle fa-3x text-danger mb-3"></i>
                        <p class="mb-0">¿Estás seguro de eliminar al estudiante <strong id="eliminar_nombre_estudiante"></strong>?</p>
                        <small class="text-muted">Esta acción no se puede deshacer.</small>
                    </div>
                    <div class="modal-footer bg-light border-0 justify-content-center">
                        <form id="eliminarEstudianteForm">
                            <input type="hidden" id="eliminar_estudiante_id" name="id">
                            <button type="submit" class="btn btn-danger mr-2">
                                <i class="feather icon-trash-2 mr-1"></i> Eliminar
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                                <i class="feather icon-x mr-1"></i> Cancelar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- [ content ] End -->
</div>
<!-- [ Layout content ] End -->

<script>
    // Variables para paginación
    let currentPage = 1;
    const limit = 20; // Manteniendo 20 registros por página

    function loadEstudiantes(page = 1) {
        currentPage = page;
        fetch('controllers/Estudiante_controles.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: 'listarEstudiantes',
                page: page,
                limit: limit,
                nivel: 'Primaria'  // Filtro por nivel Primaria
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tableBody = document.querySelector('#estudiantes-table tbody');
                tableBody.innerHTML = '';
                data.estudiantes.forEach(estudiante => {
                    tableBody.innerHTML += `
                        <tr>
                            <td>${estudiante.nombre} ${estudiante.apellidos}</td>
                            <td>${estudiante.grado}</td>
                            <td>${estudiante.seccion}</td>
                            <td>${estudiante.nivel}</td>
                            <td>
                                <button class="btn btn-sm btn-info ver-estudiante" data-id="${estudiante.id_alumno}">
                                    <i class="feather icon-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning editar-estudiante" data-id="${estudiante.id_alumno}">
                                    <i class="feather icon-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger eliminar-estudiante" data-id="${estudiante.id_alumno}" data-nombre="${estudiante.nombre} ${estudiante.apellidos}">
                                    <i class="feather icon-trash-2"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });

                // Generar paginación elegante
                updatePagination(data.total, page);
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
                text: 'Error al cargar estudiantes: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: {
                    popup: 'swal2-zindex'
                }
            });
        });
    }

    function updatePagination(totalRecords, currentPage) {
        const totalPages = Math.ceil(totalRecords / limit);
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = ''; // Limpiar paginación existente

        // Botón "Previous"
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.id = 'prev-page';
        prevLi.innerHTML = `<a class="page-link" href="javascript:void(0)" aria-label="Previous">Previous</a>`;
        if (currentPage > 1) {
            prevLi.addEventListener('click', () => loadEstudiantes(currentPage - 1));
        }
        pagination.appendChild(prevLi);

        // Calcular rango de páginas visibles (máximo 5 números)
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        // Mostrar "..." si hay páginas antes
        if (startPage > 1) {
            const firstLi = document.createElement('li');
            firstLi.className = 'page-item';
            firstLi.innerHTML = `<a class="page-link" href="javascript:void(0)" data-page="1">1</a>`;
            firstLi.addEventListener('click', () => loadEstudiantes(1));
            pagination.appendChild(firstLi);

            if (startPage > 2) {
                const ellipsisLi = document.createElement('li');
                ellipsisLi.className = 'page-item disabled';
                ellipsisLi.innerHTML = '<a class="page-link" href="javascript:void(0)">...</a>';
                pagination.appendChild(ellipsisLi);
            }
        }

        // Generar números de página
        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === currentPage ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="javascript:void(0)" data-page="${i}">${i}</a>`;
            li.addEventListener('click', () => loadEstudiantes(i));
            pagination.appendChild(li);
        }

        // Mostrar "..." si hay páginas después
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const ellipsisLi = document.createElement('li');
                ellipsisLi.className = 'page-item disabled';
                ellipsisLi.innerHTML = '<a class="page-link" href="javascript:void(0)">...</a>';
                pagination.appendChild(ellipsisLi);
            }

            const lastLi = document.createElement('li');
            lastLi.className = 'page-item';
            lastLi.innerHTML = `<a class="page-link" href="javascript:void(0)" data-page="${totalPages}">${totalPages}</a>`;
            lastLi.addEventListener('click', () => loadEstudiantes(totalPages));
            pagination.appendChild(lastLi);
        }

        // Botón "Next"
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.id = 'next-page';
        nextLi.innerHTML = `<a class="page-link" href="javascript:void(0)" aria-label="Next">Next</a>`;
        if (currentPage < totalPages) {
            nextLi.addEventListener('click', () => loadEstudiantes(currentPage + 1));
        }
        pagination.appendChild(nextLi);
    }

    const registrarEstudianteForm = document.getElementById('registrarEstudianteForm');
    registrarEstudianteForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (registrarEstudianteForm.checkValidity() === false) {
            e.stopPropagation();
            registrarEstudianteForm.classList.add('was-validated');
        } else {
            const formData = new FormData(registrarEstudianteForm);
            formData.append('action', 'registrarEstudiante');

            fetch('controllers/Estudiante_controles.php', {
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
                            $('#registrarEstudianteModal').modal('hide');
                            registrarEstudianteForm.reset();
                            registrarEstudianteForm.classList.remove('was-validated');
                            loadEstudiantes(currentPage); // Mantener la página actual
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
                        text: 'Error al registrar el estudiante: ' + error.message,
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

    const editarEstudianteForm = document.getElementById('editarEstudianteForm');
    editarEstudianteForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (editarEstudianteForm.checkValidity() === false) {
            e.stopPropagation();
            editarEstudianteForm.classList.add('was-validated');
        } else {
            const formData = new FormData(editarEstudianteForm);
            formData.append('action', 'actualizarEstudiante');

            fetch('controllers/Estudiante_controles.php', {
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
                            $('#editarEstudianteModal').modal('hide');
                            editarEstudianteForm.reset();
                            editarEstudianteForm.classList.remove('was-validated');
                            loadEstudiantes(currentPage); // Mantener la página actual
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
                        text: 'Error al actualizar el estudiante: ' + error.message,
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

    // Ver detalles de un estudiante
    document.addEventListener('click', function(e) {
        if (e.target.closest('.ver-estudiante')) {
            e.preventDefault();
            const idEstudiante = e.target.closest('.ver-estudiante').getAttribute('data-id');
            fetch('controllers/Estudiante_controles.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'obtenerEstudiante',
                        id: idEstudiante
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const estudiante = data.estudiante;
                        document.getElementById('verEstudianteTitle').textContent = `${estudiante.nombre} ${estudiante.apellidos}`;
                        document.getElementById('ver_nombre').textContent = estudiante.nombre;
                        document.getElementById('ver_apellidos').textContent = estudiante.apellidos;
                        document.getElementById('ver_dni').textContent = estudiante.dni;
                        document.getElementById('ver_grado').textContent = estudiante.grado;
                        document.getElementById('ver_seccion').textContent = estudiante.seccion;
                        document.getElementById('ver_nivel').textContent = estudiante.nivel || 'No especificado';
                        $('#verEstudianteModal').modal('show');
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
                        text: 'Error al obtener el estudiante: ' + error.message,
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

    // Editar estudiante
    document.addEventListener('click', function(e) {
        if (e.target.closest('.editar-estudiante')) {
            e.preventDefault();
            const idEstudiante = e.target.closest('.editar-estudiante').getAttribute('data-id');
            fetch('controllers/Estudiante_controles.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'obtenerEstudiante',
                        id: idEstudiante
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const estudiante = data.estudiante;
                        document.getElementById('editar_id_alumno').value = estudiante.id_alumno;
                        document.getElementById('editar_nombre').value = estudiante.nombre;
                        document.getElementById('editar_apellidos').value = estudiante.apellidos;
                        document.getElementById('editar_dni').value = estudiante.dni;
                        document.getElementById('editar_grado').value = estudiante.grado;
                        document.getElementById('editar_seccion').value = estudiante.seccion;
                        document.getElementById('editar_nivel').value = estudiante.nivel;
                        $('#editarEstudianteModal').modal('show');
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
                        text: 'Error al obtener el estudiante: ' + error.message,
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

    // Eliminar estudiante
    document.addEventListener('click', function(e) {
        if (e.target.closest('.eliminar-estudiante')) {
            e.preventDefault();
            const idEstudiante = e.target.closest('.eliminar-estudiante').getAttribute('data-id');
            const nombreEstudiante = e.target.closest('.eliminar-estudiante').getAttribute('data-nombre');
            document.getElementById('eliminar_estudiante_id').value = idEstudiante;
            document.getElementById('eliminar_nombre_estudiante').textContent = nombreEstudiante;
            $('#eliminarEstudianteModal').modal('show');
        }
    });

    eliminarEstudianteForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(eliminarEstudianteForm);
        formData.append('action', 'eliminarEstudiante');

        fetch('controllers/Estudiante_controles.php', {
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
                        $('#eliminarEstudianteModal').modal('hide');
                        loadEstudiantes(currentPage); // Mantener la página actual
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
                    text: 'Error al eliminar el estudiante: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#3085d6',
                    customClass: {
                        popup: 'swal2-zindex'
                    }
                });
            });
    });

    // Agregar clase CSS para SweetAlert2 z-index
    const style = document.createElement('style');
    style.innerHTML = `
        .swal2-zindex {
            z-index: 9999 !important;
        }
    `;
    document.head.appendChild(style);

    // Cargar estudiantes al iniciar
    loadEstudiantes();
</script>

<?php include 'include/footer.php'; ?>