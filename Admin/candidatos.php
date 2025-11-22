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
        <h4 class="font-weight-bold py-3 mb-0">GESTIÓN DE CANDIDATOS</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item">Candidatos</li>
            </ol>
        </div>

        <div class="mb-4">
            <button class="btn btn-primary" data-toggle="modal" data-target="#agregarCandidatoModal">
                <i class="feather icon-plus"></i> Agregar Candidato
            </button>
        </div>

        <div class="card">
            <h6 class="card-header">
                Lista de Candidatos
                <ul class="nav nav-tabs mt-2" id="candidatosTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="primaria-tab" data-toggle="tab" href="#primaria" role="tab" aria-controls="primaria" aria-selected="true">Primaria</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="secundaria-tab" data-toggle="tab" href="#secundaria" role="tab" aria-controls="secundaria" aria-selected="false">Secundaria</a>
                    </li>
                </ul>
            </h6>
            <div class="card-body">
                <div class="tab-content" id="candidatosTabContent">
                    <div class="tab-pane fade show active" id="primaria" role="tabpanel" aria-labelledby="primaria-tab">
                        <div class="table-responsive">
                            <table class="table table-striped" id="candidatos-table-primaria">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Proceso</th>
                                        <th>Lema</th>
                                        <th>Foto Perfil</th>
                                        <th>Foto Campaña</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="secundaria" role="tabpanel" aria-labelledby="secundaria-tab">
                        <div class="table-responsive">
                            <table class="table table-striped" id="candidatos-table-secundaria">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Proceso</th>
                                        <th>Lema</th>
                                        <th>Foto Perfil</th>
                                        <th>Foto Campaña</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="agregarCandidatoModal" tabindex="-1" role="dialog" aria-labelledby="agregarCandidatoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 10px;">
                    <div class="modal-header bg-primary text-white" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
                        <h5 class="modal-title" id="agregarCandidatoModalLabel">Agregar Nuevo Candidato</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="agregarCandidatoForm" novalidate enctype="multipart/form-data">
                            <input type="hidden" id="id_alumno" name="id_alumno">
                            <div class="form-group">
                                <label class="font-weight-bold">Proceso Electoral <span class="text-danger">*</span></label>
                                <select class="form-control" id="id_proceso" name="id_proceso" required>
                                    <option value="">Selecciona un proceso</option>
                                </select>
                                <div class="invalid-feedback">El proceso es obligatorio.</div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Buscar por DNI <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="buscarDni" name="dni" placeholder="Ingresa DNI" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-secondary" id="buscarBtn"><i class="feather icon-search"></i> Buscar</button>
                                    </div>
                                    <div class="invalid-feedback">El DNI es obligatorio.</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Nombre del Estudiante</label>
                                <input type="text" class="form-control" id="nombreCandidato" readonly>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Lema</label>
                                <input type="text" class="form-control" id="lema" name="lema" placeholder="Ingresa el lema (opcional)">
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Subir Foto de Perfil</label>
                                <input type="file" class="form-control-file" id="foto_perfil" name="foto_perfil" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Subir Foto de Campaña</label>
                                <input type="file" class="form-control-file" id="foto_campaña" name="foto_campaña" accept="image/*">
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

        <div class="modal fade" id="verCandidatoModal" tabindex="-1" role="dialog" aria-labelledby="verCandidatoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-gradient-info text-white">
                        <h5 class="modal-title" id="verCandidatoModalLabel">
                            <i class="feather icon-user mr-2"></i> Detalles del Candidato
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="text-center">
                            <img id="ver_foto_perfil" src="" alt="Foto Perfil" style="width: 100px; height: 100px; margin-bottom: 10px;">
                            <h5 id="verCandidatoTitle" class="font-weight-bold"></h5>
                        </div>
                        <div class="mt-3">
                            <p><strong>Nombre:</strong> <span id="ver_nombre"></span></p>
                            <p><strong>Proceso:</strong> <span id="ver_proceso"></span></p>
                            <p><strong>DNI:</strong> <span id="ver_dni"></span></p>
                            <p><strong>Lema:</strong> <span id="ver_lema"></span></p>
                            <p><strong>Foto de Campaña:</strong></p>
                            <img id="ver_foto_campaña" src="" alt="Foto Campaña" style="width: 100px; height: 100px;">
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

        <div class="modal fade" id="editarCandidatoModal" tabindex="-1" role="dialog" aria-labelledby="editarCandidatoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 10px;">
                    <div class="modal-header bg-info text-white" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
                        <h5 class="modal-title" id="editarCandidatoModalLabel">Editar Candidato</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="editarCandidatoForm" novalidate enctype="multipart/form-data">
                            <input type="hidden" id="editar_id_candidato" name="id_candidato">
                            <input type="hidden" id="editar_id_alumno" name="id_alumno">
                            <div class="form-group">
                                <label class="font-weight-bold">Proceso Electoral <span class="text-danger">*</span></label>
                                <select class="form-control" id="editar_id_proceso" name="id_proceso" required>
                                    <option value="">Selecciona un proceso</option>
                                </select>
                                <div class="invalid-feedback">El proceso es obligatorio.</div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Buscar por DNI <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="editar_buscarDni" name="dni" placeholder="Ingresa DNI" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-secondary" id="editar_buscarBtn"><i class="feather icon-search"></i> Buscar</button>
                                    </div>
                                    <div class="invalid-feedback">El DNI es obligatorio.</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Nombre del Estudiante</label>
                                <input type="text" class="form-control" id="editar_nombreCandidato" readonly>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Lema</label>
                                <input type="text" class="form-control" id="editar_lema" name="lema" placeholder="Ingresa el lema (opcional)">
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Subir Foto de Perfil</label>
                                <input type="file" class="form-control-file" id="editar_foto_perfil" name="foto_perfil" accept="image/*">
                                <img id="editar_foto_perfil_previa" src="" alt="Foto Perfil Previa" style="width: 100px; height: 100px; display: none; margin-top: 10px;">
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Subir Foto de Campaña</label>
                                <input type="file" class="form-control-file" id="editar_foto_campaña" name="foto_campaña" accept="image/*">
                                <img id="editar_foto_campaña_previa" src="" alt="Foto Campaña Previa" style="width: 100px; height: 100px; display: none; margin-top: 10px;">
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

        <div class="modal fade" id="eliminarCandidatoModal" tabindex="-1" role="dialog" aria-labelledby="eliminarCandidatoModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eliminarCandidatoModalLabel">Eliminar Candidato</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas eliminar a <span id="eliminar_nombre_candidato"></span>?</p>
                        <form id="eliminarCandidatoForm">
                            <input type="hidden" name="id" id="eliminar_candidato_id">
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
    const agregarCandidatoForm = document.getElementById('agregarCandidatoForm');
    const editarCandidatoForm = document.getElementById('editarCandidatoForm');
    const eliminarCandidatoForm = document.getElementById('eliminarCandidatoForm');
    const candidatosTableBodyPrimaria = document.querySelector('#candidatos-table-primaria tbody');
    const candidatosTableBodySecundaria = document.querySelector('#candidatos-table-secundaria tbody');
    const idProcesoSelect = document.getElementById('id_proceso');
    const editarIdProcesoSelect = document.getElementById('editar_id_proceso');

    // Corregir z-index de SweetAlert2
    const style = document.createElement('style');
    style.innerHTML = `
        .swal2-zindex {
            z-index: 9999 !important;
        }
    `;
    document.head.appendChild(style);

    function loadProcesos(selectElement, selectedValue = '') {
        fetch('controllers/Candidato_controles.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'listarProcesos' })
        })
        .then(response => response.json())
        .then(data => {
            selectElement.innerHTML = '<option value="">Selecciona un proceso</option>';
            if (data.success && data.procesos.length > 0) {
                data.procesos.forEach(proceso => {
                    const option = document.createElement('option');
                    option.value = proceso.id_proceso;
                    option.textContent = proceso.nombre;
                    if (proceso.id_proceso === selectedValue) {
                        option.selected = true;
                    }
                    selectElement.appendChild(option);
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'No se encontraron procesos activos',
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
                text: 'Error al cargar los procesos: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
                });
            });
    }

    function loadCandidatos(nivel, tableBody) {
        fetch('controllers/Candidato_controles.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'listarCandidatos', nivel: nivel })
        })
        .then(response => response.json())
        .then(data => {
            tableBody.innerHTML = '';
            if (data.success) {
                if (data.candidatos.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No hay candidatos registrados.</td></tr>';
                    return;
                }
                data.candidatos.forEach(candidato => {
                    tableBody.innerHTML += `
                        <tr data-id="${candidato.id_candidato}">
                            <td>${candidato.full_name}</td>
                            <td>${candidato.proceso_nombre}</td>
                            <td>${candidato.lema || 'Sin lema'}</td>
                            <td>${candidato.foto_perfil ? `<img src="../Uploads/${candidato.foto_perfil}" alt="Foto Perfil" style="width: 50px; height: 50px;">` : 'Sin foto'}</td>
                            <td>${candidato.foto_campaña ? `<img src="../Uploads/${candidato.foto_campaña}" alt="Foto Campaña" style="width: 50px; height: 50px;">` : 'Sin foto'}</td>
                            <td>
                                <button class="btn btn-info btn-sm ver-candidato" data-id="${candidato.id_candidato}">
                                    <i class="feather icon-eye"></i>
                                </button>
                                <button class="btn btn-warning btn-sm editar-candidato" data-id="${candidato.id_candidato}">
                                    <i class="feather icon-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm eliminar-candidato" data-id="${candidato.id_candidato}" data-nombre="${candidato.full_name}">
                                    <i class="feather icon-trash-2"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'Error al cargar candidatos',
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
                text: 'Error al cargar los candidatos: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
        });
    }

    // Load candidates for each tab
    loadCandidatos('primaria', candidatosTableBodyPrimaria);
    loadCandidatos('secundaria', candidatosTableBodySecundaria);

    document.getElementById('buscarBtn').addEventListener('click', function() {
        const dni = document.getElementById('buscarDni').value.trim();
        const id_proceso = idProcesoSelect.value;
        if (!dni || !id_proceso) {
            Swal.fire({
                title: 'Error',
                text: !dni ? 'Por favor, ingresa un DNI.' : 'Por favor, selecciona un proceso electoral.',
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
            return;
        }
        fetch('controllers/Candidato_controles.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'buscarPorDni',
                dni: dni,
                id_proceso: id_proceso
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('id_alumno').value = data.alumno.id_alumno;
                document.getElementById('nombreCandidato').value = `${data.alumno.nombre} ${data.alumno.apellidos}`;
            } else {
                document.getElementById('id_alumno').value = '';
                document.getElementById('nombreCandidato').value = '';
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
                text: 'Error al buscar el estudiante: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
        });
    });

    agregarCandidatoForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!agregarCandidatoForm.checkValidity() || !idProcesoSelect.value) {
            e.stopPropagation();
            agregarCandidatoForm.classList.add('was-validated');
            if (!idProcesoSelect.value) {
                Swal.fire({
                    title: 'Error',
                    text: 'Por favor, selecciona un proceso electoral.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#3085d6',
                    customClass: { popup: 'swal2-zindex' }
                });
            }
            return;
        }
        const formData = new FormData(agregarCandidatoForm);
        formData.append('action', 'registrarCandidato');
        fetch('controllers/Candidato_controles.php', {
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
                    $('#agregarCandidatoModal').modal('hide');
                    agregarCandidatoForm.reset();
                    agregarCandidatoForm.classList.remove('was-validated');
                    // Reload both tabs
                    loadCandidatos('primaria', candidatosTableBodyPrimaria);
                    loadCandidatos('secundaria', candidatosTableBodySecundaria);
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
                text: 'Error al registrar el candidato: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.ver-candidato')) {
            e.preventDefault();
            const idCandidato = e.target.closest('.ver-candidato').getAttribute('data-id');
            fetch('controllers/Candidato_controles.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'obtenerCandidato',
                    id: idCandidato
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const candidato = data.candidato;
                    document.getElementById('verCandidatoTitle').textContent = candidato.full_name;
                    document.getElementById('ver_nombre').textContent = candidato.full_name;
                    document.getElementById('ver_proceso').textContent = candidato.proceso_nombre;
                    document.getElementById('ver_dni').textContent = candidato.dni;
                    document.getElementById('ver_lema').textContent = candidato.lema || 'Sin lema';
                    document.getElementById('ver_foto_perfil').src = candidato.foto_perfil ? `../Uploads/${candidato.foto_perfil}` : '../Uploads/default_perfil.jpg';
                    document.getElementById('ver_foto_campaña').src = candidato.foto_campaña ? `../Uploads/${candidato.foto_campaña}` : '../Uploads/default_campaña.jpg';
                    $('#verCandidatoModal').modal('show');
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
                    text: 'Error al obtener el candidato: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#3085d6',
                    customClass: { popup: 'swal2-zindex' }
                });
            });
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.editar-candidato')) {
            e.preventDefault();
            const idCandidato = e.target.closest('.editar-candidato').getAttribute('data-id');
            fetch('controllers/Candidato_controles.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'obtenerCandidato',
                    id: idCandidato
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const candidato = data.candidato;
                    document.getElementById('editar_id_candidato').value = candidato.id_candidato;
                    document.getElementById('editar_id_alumno').value = candidato.id_alumno;
                    document.getElementById('editar_buscarDni').value = candidato.dni;
                    document.getElementById('editar_nombreCandidato').value = `${candidato.alumno_nombre} ${candidato.apellidos}`;
                    document.getElementById('editar_lema').value = candidato.lema || '';
                    document.getElementById('editar_foto_perfil_previa').src = candidato.foto_perfil ? `../Uploads/${candidato.foto_perfil}` : '../Uploads/default_perfil.jpg';
                    document.getElementById('editar_foto_perfil_previa').style.display = candidato.foto_perfil ? 'block' : 'none';
                    document.getElementById('editar_foto_campaña_previa').src = candidato.foto_campaña ? `../Uploads/${candidato.foto_campaña}` : '../Uploads/default_campaña.jpg';
                    document.getElementById('editar_foto_campaña_previa').style.display = candidato.foto_campaña ? 'block' : 'none';
                    loadProcesos(editarIdProcesoSelect, candidato.id_proceso);
                    $('#editarCandidatoModal').modal('show');
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
                    text: 'Error al obtener el candidato: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#3085d6',
                    customClass: { popup: 'swal2-zindex' }
                });
            });
        }
    });

    document.getElementById('editar_buscarBtn').addEventListener('click', function() {
        const dni = document.getElementById('editar_buscarDni').value.trim();
        const id_proceso = editarIdProcesoSelect.value;
        if (!dni || !id_proceso) {
            Swal.fire({
                title: 'Error',
                text: !dni ? 'Por favor, ingresa un DNI.' : 'Por favor, selecciona un proceso electoral.',
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
            return;
        }
        fetch('controllers/Candidato_controles.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'buscarPorDni',
                dni: dni,
                id_proceso: id_proceso
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('editar_id_alumno').value = data.alumno.id_alumno;
                document.getElementById('editar_nombreCandidato').value = `${data.alumno.nombre} ${data.alumno.apellidos}`;
            } else {
                document.getElementById('editar_id_alumno').value = '';
                document.getElementById('editar_nombreCandidato').value = '';
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
                text: 'Error al buscar el estudiante: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
        });
    });

    editarCandidatoForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!editarCandidatoForm.checkValidity() || !editarIdProcesoSelect.value) {
            e.stopPropagation();
            editarCandidatoForm.classList.add('was-validated');
            if (!editarIdProcesoSelect.value) {
                Swal.fire({
                    title: 'Error',
                    text: 'Por favor, selecciona un proceso electoral.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#3085d6',
                    customClass: { popup: 'swal2-zindex' }
                });
            }
            return;
        }
        const formData = new FormData(editarCandidatoForm);
        formData.append('action', 'actualizarCandidato');
        fetch('controllers/Candidato_controles.php', {
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
                    $('#editarCandidatoModal').modal('hide');
                    editarCandidatoForm.reset();
                    editarCandidatoForm.classList.remove('was-validated');
                    document.getElementById('editar_foto_perfil_previa').style.display = 'none';
                    document.getElementById('editar_foto_campaña_previa').style.display = 'none';
                    // Reload both tabs
                    loadCandidatos('primaria', candidatosTableBodyPrimaria);
                    loadCandidatos('secundaria', candidatosTableBodySecundaria);
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
                text: 'Error al actualizar el candidato: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.eliminar-candidato')) {
            e.preventDefault();
            const idCandidato = e.target.closest('.eliminar-candidato').getAttribute('data-id');
            const nombreCandidato = e.target.closest('.eliminar-candidato').getAttribute('data-nombre');
            document.getElementById('eliminar_candidato_id').value = idCandidato;
            document.getElementById('eliminar_nombre_candidato').textContent = nombreCandidato;
            $('#eliminarCandidatoModal').modal('show');
        }
    });

    eliminarCandidatoForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(eliminarCandidatoForm);
        formData.append('action', 'eliminarCandidato');
        fetch('controllers/Candidato_controles.php', {
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
                    $('#eliminarCandidatoModal').modal('hide');
                    // Reload both tabs
                    loadCandidatos('primaria', candidatosTableBodyPrimaria);
                    loadCandidatos('secundaria', candidatosTableBodySecundaria);
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
                text: 'Error al eliminar el candidato: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
        });
    });

    loadProcesos(idProcesoSelect);
});
</script>

<?php include 'include/footer.php'; ?>