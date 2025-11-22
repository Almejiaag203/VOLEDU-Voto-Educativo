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
        <h4 class="font-weight-bold py-3 mb-0">GESTIÓN DE MESAS DE SUFRAGIO</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item">Mesas de Sufragio</li>
            </ol>
        </div>

        <!-- Pestañas para Primaria y Secundaria -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="primaria-tab" data-toggle="tab" href="#primaria" role="tab" aria-controls="primaria" aria-selected="true">Primaria</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="secundaria-tab" data-toggle="tab" href="#secundaria" role="tab" aria-controls="secundaria" aria-selected="false">Secundaria</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="primaria" role="tabpanel" aria-labelledby="primaria-tab">
                <div class="mb-4">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#generarMesasModal" data-nivel="Primaria">
                        <i class="feather icon-plus"></i> Generar Mesas Primaria
                    </button>
                    <a href="padron_mesas.php?id_proceso=1&nivel=Primaria" class="btn btn-info" target="_blank">
        <i class="feather icon-file-text"></i> Descargar Padrón de Mesas (Primaria)
    </a>
                    <a href="excel.php?id_proceso=1&nivel=Primaria" class="btn btn-success" target="_blank">
                        <i class="feather icon-download"></i> Exportar a Excel (Primaria)
                    </a>
                </div>
                <div class="card">
                    <h6 class="card-header">Lista de Mesas Primaria</h6>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="mesas-table-primaria">
                                <thead>
                                    <tr>
                                        <th>Proceso</th>
                                        <th>Número</th>
                                        <th>Ubicación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-center" id="pagination-primaria"></ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="secundaria" role="tabpanel" aria-labelledby="secundaria-tab">
                <div class="mb-4">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#generarMesasModal" data-nivel="Secundaria">
                        <i class="feather icon-plus"></i> Generar Mesas Secundaria
                    </button>
                    <a href="excel.php?id_proceso=1&nivel=Secundaria" class="btn btn-success" target="_blank">
                        <i class="feather icon-download"></i> Exportar a Excel (Secundaria)
                    </a>
                    <a href="padron_mesas.php?id_proceso=1&nivel=Secundaria" class="btn btn-info" target="_blank">
        <i class="feather icon-file-text"></i> Descargar Padrón de Mesas (Secundaria)
    </a>
                </div>
                <div class="card">
                    <h6 class="card-header">Lista de Mesas Secundaria</h6>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="mesas-table-secundaria">
                                <thead>
                                    <tr>
                                        <th>Proceso</th>
                                        <th>Número</th>
                                        <th>Ubicación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-center" id="pagination-secundaria"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para generar mesas -->
        <div class="modal fade" id="generarMesasModal" tabindex="-1" role="dialog" aria-labelledby="generarMesasModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="generarMesasModalLabel">Generar Nuevas Mesas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="generarMesasForm" novalidate>
                            <input type="hidden" id="nivel" name="nivel">
                            <div class="form-group">
                                <label class="form-label">Proceso Electoral <span class="text-danger">*</span></label>
                                <select class="form-control" id="id_proceso" name="id_proceso" required>
                                    <option value="">Selecciona un proceso</option>
                                </select>
                                <div class="invalid-feedback">El proceso es obligatorio.</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Ubicación Predeterminada <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="Aula A" required>
                                <div class="invalid-feedback">La ubicación es obligatoria.</div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Generar Mesas por Grado</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para editar mesa -->
        <div class="modal fade" id="editarMesaModal" tabindex="-1" role="dialog" aria-labelledby="editarMesaModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarMesaModalLabel">Editar Mesa</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editarMesaForm" novalidate>
                            <input type="hidden" name="id_mesa" id="editar_id_mesa">
                            <div class="form-group">
                                <label class="form-label">Ubicación <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editar_ubicacion" name="ubicacion" required>
                                <div class="invalid-feedback">La ubicación es obligatoria.</div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Actualizar</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para eliminar mesa -->
        <div class="modal fade" id="eliminarMesaModal" tabindex="-1" role="dialog" aria-labelledby="eliminarMesaModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eliminarMesaModalLabel">Confirmar Eliminación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de eliminar la mesa número <strong id="eliminar_numero_mesa"></strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <form id="eliminarMesaForm">
                            <input type="hidden" name="id_mesa" id="eliminar_id_mesa">
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Variables para paginación por nivel
    let currentPagePrimaria = 1;
    let currentPageSecundaria = 1;
    const limit = 20;

    function loadMesas(nivel, page = 1) {
        const tableId = `mesas-table-${nivel.toLowerCase()}`;
        const paginationId = `pagination-${nivel.toLowerCase()}`;
        const tableBody = document.querySelector(`#${tableId} tbody`);
        const currentPageVar = (nivel === 'Primaria') ? 'currentPagePrimaria' : 'currentPageSecundaria';
        window[currentPageVar] = page;

        fetch('controllers/Mesa_controles.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: 'listarMesas',
                page: page,
                limit: limit,
                nivel: nivel
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                tableBody.innerHTML = '';
                data.mesas.forEach(mesa => {
                    tableBody.innerHTML += `
                        <tr>
                            <td>${mesa.proceso_nombre}</td>
                            <td>${mesa.numero}</td>
                            <td>${mesa.ubicacion}</td>
                            <td>
                                <button class="btn btn-sm btn-warning editar-mesa" data-id="${mesa.id_mesa}" data-ubicacion="${mesa.ubicacion}">
                                    <i class="feather icon-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger eliminar-mesa" data-id="${mesa.id_mesa}" data-numero="${mesa.numero}">
                                    <i class="feather icon-trash-2"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                updatePagination(nivel, data.total, page);
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
                text: 'Error al cargar mesas: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
        });
    }

    function updatePagination(nivel, totalRecords, currentPage) {
        const totalPages = Math.ceil(totalRecords / limit);
        const paginationId = `pagination-${nivel.toLowerCase()}`;
        const pagination = document.getElementById(paginationId);
        pagination.innerHTML = '';

        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="javascript:void(0)" aria-label="Previous">Previous</a>`;
        if (currentPage > 1) {
            prevLi.addEventListener('click', () => loadMesas(nivel, currentPage - 1));
        }
        pagination.appendChild(prevLi);

        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        if (startPage > 1) {
            const firstLi = document.createElement('li');
            firstLi.className = 'page-item';
            firstLi.innerHTML = `<a class="page-link" href="javascript:void(0)" data-page="1">1</a>`;
            firstLi.addEventListener('click', () => loadMesas(nivel, 1));
            pagination.appendChild(firstLi);

            if (startPage > 2) {
                const ellipsisLi = document.createElement('li');
                ellipsisLi.className = 'page-item disabled';
                ellipsisLi.innerHTML = '<a class="page-link" href="javascript:void(0)">...</a>';
                pagination.appendChild(ellipsisLi);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === currentPage ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="javascript:void(0)" data-page="${i}">${i}</a>`;
            li.addEventListener('click', () => loadMesas(nivel, i));
            pagination.appendChild(li);
        }

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
            lastLi.addEventListener('click', () => loadMesas(nivel, totalPages));
            pagination.appendChild(lastLi);
        }

        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="javascript:void(0)" aria-label="Next">Next</a>`;
        if (currentPage < totalPages) {
            nextLi.addEventListener('click', () => loadMesas(nivel, currentPage + 1));
        }
        pagination.appendChild(nextLi);
    }

    const generarMesasForm = document.getElementById('generarMesasForm');
    const editarMesaForm = document.getElementById('editarMesaForm');
    const eliminarMesaForm = document.getElementById('eliminarMesaForm');

    function loadProcesos(selectElement) {
        fetch('controllers/Mesa_controles.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: 'listarProcesos'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                selectElement.innerHTML = '<option value="">Selecciona un proceso</option>';
                data.procesos.forEach(proceso => {
                    const option = document.createElement('option');
                    option.value = proceso.id_proceso;
                    option.textContent = proceso.nombre;
                    selectElement.appendChild(option);
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'Error al cargar procesos: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
        });
    }

    // Abrir modal con nivel predefinido desde botón
    document.addEventListener('click', function(e) {
        if (e.target.closest('button[data-toggle="modal"][data-nivel]')) {
            const nivel = e.target.closest('button').getAttribute('data-nivel');
            document.getElementById('nivel').value = nivel;
            document.getElementById('generarMesasModalLabel').textContent = `Generar Nuevas Mesas - ${nivel}`;
        }
    });

    generarMesasForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!generarMesasForm.checkValidity()) {
            e.stopPropagation();
            generarMesasForm.classList.add('was-validated');
            return;
        }
        const formData = new FormData(generarMesasForm);
        formData.append('action', 'generarMesas');
        fetch('controllers/Mesa_controles.php', {
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
                    $('#generarMesasModal').modal('hide');
                    generarMesasForm.reset();
                    generarMesasForm.classList.remove('was-validated');
                    const nivel = document.getElementById('nivel').value;
                    loadMesas(nivel);
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
                text: 'Error al generar las mesas: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.editar-mesa')) {
            e.preventDefault();
            const button = e.target.closest('.editar-mesa');
            const idMesa = button.getAttribute('data-id');
            const ubicacion = button.getAttribute('data-ubicacion');
            document.getElementById('editar_id_mesa').value = idMesa;
            document.getElementById('editar_ubicacion').value = ubicacion;
            $('#editarMesaModal').modal('show');
        }
    });

    editarMesaForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!editarMesaForm.checkValidity()) {
            e.stopPropagation();
            editarMesaForm.classList.add('was-validated');
            return;
        }
        const formData = new FormData(editarMesaForm);
        formData.append('action', 'actualizarMesa');
        fetch('controllers/Mesa_controles.php', {
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
                    $('#editarMesaModal').modal('hide');
                    editarMesaForm.reset();
                    editarMesaForm.classList.remove('was-validated');
                    // Recargar ambas pestañas para seguridad
                    loadMesas('Primaria');
                    loadMesas('Secundaria');
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
                text: 'Error al actualizar la mesa: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.eliminar-mesa')) {
            e.preventDefault();
            const idMesa = e.target.closest('.eliminar-mesa').getAttribute('data-id');
            const numero = e.target.closest('.eliminar-mesa').getAttribute('data-numero');
            document.getElementById('eliminar_id_mesa').value = idMesa;
            document.getElementById('eliminar_numero_mesa').textContent = numero;
            $('#eliminarMesaModal').modal('show');
        }
    });

    eliminarMesaForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(eliminarMesaForm);
        formData.append('action', 'eliminarMesa');
        fetch('controllers/Mesa_controles.php', {
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
                    $('#eliminarMesaModal').modal('hide');
                    // Recargar ambas pestañas
                    loadMesas('Primaria');
                    loadMesas('Secundaria');
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
                text: 'Error al eliminar la mesa: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
        });
    });

    // Cargar procesos y mesas iniciales
    loadProcesos(document.getElementById('id_proceso'));
    loadMesas('Primaria');
    loadMesas('Secundaria');
</script>

<?php include 'include/footer.php'; ?>