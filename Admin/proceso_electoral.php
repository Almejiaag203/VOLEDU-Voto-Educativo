<?php
        session_start(); // Iniciar la sesión
        // Verificar rol - Solo Administrador
        if ($_SESSION['rol'] !== 'Administrador') {
            header("Location: ../login/login.php");
            exit;
        }
        ?>

<?php include 'include/header.php'; ?>

<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">GESTIÓN DE PROCESOS ELECTORALES</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item">Procesos Electorales</li>
            </ol>
        </div>

        <div class="mb-4">
            <button class="btn btn-primary" data-toggle="modal" data-target="#agregarProcesoModal">
                <i class="feather icon-plus"></i> Agregar Proceso Electoral
            </button>
        </div>

        <div class="card">
            <h6 class="card-header">Lista de Procesos Electorales</h6>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="procesos-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Activo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="agregarProcesoModal" tabindex="-1" role="dialog" aria-labelledby="agregarProcesoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 10px;">
                    <div class="modal-header bg-primary text-white" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
                        <h5 class="modal-title" id="agregarProcesoModalLabel">Agregar Nuevo Proceso Electoral</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="agregarProcesoForm" novalidate>
                            <div class="form-group">
                                <label class="font-weight-bold">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingresa el nombre del proceso" required>
                                <div class="invalid-feedback">El nombre es obligatorio.</div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Fecha de Inicio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                                <div class="invalid-feedback">La fecha de inicio es obligatoria.</div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Fecha de Fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                                <div class="invalid-feedback">La fecha de fin es obligatoria.</div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Activo</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="activo" name="activo">
                                    <label class="form-check-label" for="activo">Activar proceso</label>
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

        <div class="modal fade" id="verProcesoModal" tabindex="-1" role="dialog" aria-labelledby="verProcesoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-gradient-info text-white">
                        <h5 class="modal-title" id="verProcesoModalLabel">
                            <i class="feather icon-info mr-2"></i> Detalles del Proceso Electoral
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mt-3">
                            <p><strong>ID:</strong> <span id="ver_id_proceso"></span></p>
                            <p><strong>Nombre:</strong> <span id="ver_nombre"></span></p>
                            <p><strong>Fecha de Inicio:</strong> <span id="ver_fecha_inicio"></span></p>
                            <p><strong>Fecha de Fin:</strong> <span id="ver_fecha_fin"></span></p>
                            <p><strong>Activo:</strong> <span id="ver_activo"></span></p>
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

        <div class="modal fade" id="editarProcesoModal" tabindex="-1" role="dialog" aria-labelledby="editarProcesoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 10px;">
                    <div class="modal-header bg-info text-white" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
                        <h5 class="modal-title" id="editarProcesoModalLabel">Editar Proceso Electoral</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="editarProcesoForm" novalidate>
                            <input type="hidden" id="editar_id_proceso" name="id_proceso">
                            <div class="form-group">
                                <label class="font-weight-bold">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editar_nombre" name="nombre" placeholder="Ingresa el nombre del proceso" required>
                                <div class="invalid-feedback">El nombre es obligatorio.</div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Fecha de Inicio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="editar_fecha_inicio" name="fecha_inicio" required>
                                <div class="invalid-feedback">La fecha de inicio es obligatoria.</div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Fecha de Fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="editar_fecha_fin" name="fecha_fin" required>
                                <div class="invalid-feedback">La fecha de fin es obligatoria.</div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Activo</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="editar_activo" name="activo">
                                    <label class="form-check-label" for="editar_activo">Activar proceso</label>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-info"><i class="feather icon-save"></i> Actualizar</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="eliminarProcesoModal" tabindex="-1" role="dialog" aria-labelledby="eliminarProcesoModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eliminarProcesoModalLabel">Eliminar Proceso Electoral</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas eliminar el proceso <span id="eliminar_nombre_proceso"></span>?</p>
                        <form id="eliminarProcesoForm">
                            <input type="hidden" name="id" id="eliminar_proceso_id">
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-danger"><i class="feather icon-trash-2"></i> Eliminar</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const agregarProcesoForm = document.getElementById('agregarProcesoForm');
    const editarProcesoForm = document.getElementById('editarProcesoForm');
    const eliminarProcesoForm = document.getElementById('eliminarProcesoForm');
    const procesosTableBody = document.querySelector('#procesos-table tbody');

    // Corregir z-index de SweetAlert2
    const style = document.createElement('style');
    style.innerHTML = `
        .swal2-zindex {
            z-index: 9999 !important;
        }
    `;
    document.head.appendChild(style);

    function loadProcesos() {
        fetch('controllers/ProcesoElectoral_controles.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'listarProcesos' })
        })
        .then(response => response.json())
        .then(data => {
            procesosTableBody.innerHTML = '';
            if (data.success) {
                if (data.procesos.length === 0) {
                    procesosTableBody.innerHTML = '<tr><td colspan="6" class="text-center">No hay procesos electorales registrados.</td></tr>';
                    return;
                }
                data.procesos.forEach(proceso => {
                    procesosTableBody.innerHTML += `
                        <tr data-id="${proceso.id_proceso}">
                            <td>${proceso.id_proceso}</td>
                            <td>${proceso.nombre}</td>
                            <td>${proceso.fecha_inicio}</td>
                            <td>${proceso.fecha_fin}</td>
                            <td>${proceso.activo ? 'Sí' : 'No'}</td>
                            <td>
                                <button class="btn btn-info btn-sm ver-proceso" data-id="${proceso.id_proceso}">
                                    <i class="feather icon-eye"></i>
                                </button>
                                <button class="btn btn-warning btn-sm editar-proceso" data-id="${proceso.id_proceso}">
                                    <i class="feather icon-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm eliminar-proceso" data-id="${proceso.id_proceso}" data-nombre="${proceso.nombre}">
                                    <i class="feather icon-trash-2"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'Error al cargar procesos electorales',
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#3085d6',
                    customClass: { popup: 'swal2-zindex' }
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'Error al cargar los procesos electorales: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
        });
    }

    agregarProcesoForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!agregarProcesoForm.checkValidity()) {
            e.stopPropagation();
            agregarProcesoForm.classList.add('was-validated');
            return;
        }
        const formData = new FormData(agregarProcesoForm);
        formData.append('action', 'registrarProceso');
        fetch('controllers/ProcesoElectoral_controles.php', {
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
                    customClass: { popup: 'swal2-zindex' }
                }).then(() => {
                    $('#agregarProcesoModal').modal('hide');
                    agregarProcesoForm.reset();
                    agregarProcesoForm.classList.remove('was-validated');
                    loadProcesos();
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#3085d6',
                    customClass: { popup: 'swal2-zindex' }
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'Error al registrar el proceso: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.ver-proceso')) {
            e.preventDefault();
            const idProceso = e.target.closest('.ver-proceso').getAttribute('data-id');
            fetch('controllers/ProcesoElectoral_controles.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'obtenerProceso',
                    id: idProceso
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const proceso = data.proceso;
                    document.getElementById('ver_id_proceso').textContent = proceso.id_proceso;
                    document.getElementById('ver_nombre').textContent = proceso.nombre;
                    document.getElementById('ver_fecha_inicio').textContent = proceso.fecha_inicio;
                    document.getElementById('ver_fecha_fin').textContent = proceso.fecha_fin;
                    document.getElementById('ver_activo').textContent = proceso.activo ? 'Sí' : 'No';
                    $('#verProcesoModal').modal('show');
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#3085d6',
                        customClass: { popup: 'swal2-zindex' }
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'Error al obtener el proceso: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#3085d6',
                    customClass: { popup: 'swal2-zindex' }
                });
            });
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.editar-proceso')) {
            e.preventDefault();
            const idProceso = e.target.closest('.editar-proceso').getAttribute('data-id');
            fetch('controllers/ProcesoElectoral_controles.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'obtenerProceso',
                    id: idProceso
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const proceso = data.proceso;
                    document.getElementById('editar_id_proceso').value = proceso.id_proceso;
                    document.getElementById('editar_nombre').value = proceso.nombre;
                    document.getElementById('editar_fecha_inicio').value = proceso.fecha_inicio;
                    document.getElementById('editar_fecha_fin').value = proceso.fecha_fin;
                    document.getElementById('editar_activo').checked = proceso.activo == 1;
                    $('#editarProcesoModal').modal('show');
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#3085d6',
                        customClass: { popup: 'swal2-zindex' }
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'Error al obtener el proceso: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#3085d6',
                    customClass: { popup: 'swal2-zindex' }
                });
            });
        }
    });

    editarProcesoForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!editarProcesoForm.checkValidity()) {
            e.stopPropagation();
            editarProcesoForm.classList.add('was-validated');
            return;
        }
        const formData = new FormData(editarProcesoForm);
        formData.append('action', 'actualizarProceso');
        fetch('controllers/ProcesoElectoral_controles.php', {
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
                    customClass: { popup: 'swal2-zindex' }
                }).then(() => {
                    $('#editarProcesoModal').modal('hide');
                    editarProcesoForm.reset();
                    editarProcesoForm.classList.remove('was-validated');
                    loadProcesos();
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#3085d6',
                    customClass: { popup: 'swal2-zindex' }
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'Error al actualizar el proceso: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.eliminar-proceso')) {
            e.preventDefault();
            const idProceso = e.target.closest('.eliminar-proceso').getAttribute('data-id');
            const nombreProceso = e.target.closest('.eliminar-proceso').getAttribute('data-nombre');
            document.getElementById('eliminar_proceso_id').value = idProceso;
            document.getElementById('eliminar_nombre_proceso').textContent = nombreProceso;
            $('#eliminarProcesoModal').modal('show');
        }
    });

    eliminarProcesoForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(eliminarProcesoForm);
        formData.append('action', 'eliminarProceso');
        fetch('controllers/ProcesoElectoral_controles.php', {
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
                    customClass: { popup: 'swal2-zindex' }
                }).then(() => {
                    $('#eliminarProcesoModal').modal('hide');
                    loadProcesos();
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#3085d6',
                    customClass: { popup: 'swal2-zindex' }
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'Error al eliminar el proceso: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
        });
    });

    loadProcesos();
});
</script>

<?php include 'include/footer.php'; ?>