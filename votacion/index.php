<?php include 'include/header.php'; ?>
<?php
session_start();
?>

<!-- [ Layout content ] Start -->
<div class="layout-content">
    <!-- Header con reloj -->
    <header class="bg-primary text-white py-2">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Sistema de Votación Escolar</h4>
                <div id="reloj" style="display: inline;"></div>
            </div>
        </div>
    </header>

    <div class="container mt-5">
        <div id="errores-container"></div> <!-- Para mostrar errores via JS -->

        <div id="busqueda-form">
            <div class="card" style="max-width: 400px; margin: 0 auto;">
                <div class="card-body text-center">
                    <h5 class="card-title">Ingresa tu DNI para Votar</h5>
                    <form id="form-buscar-dni">
                        <div class="form-group">
                            <input type="text" id="dni" class="form-control" placeholder="Ingresa tu DNI (8 dígitos)" maxlength="8" required pattern="\d{8}">
                            <div class="invalid-feedback">El DNI debe tener 8 dígitos numéricos.</div>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="feather icon-search"></i> Buscar y Votar</button>
                    </form>
                </div>
            </div>
        </div>

        <div id="datos-alumno" style="display: none;">
            <!-- Se llena via JS -->
        </div>

        <div id="lista-candidatos" style="display: none;">
            <!-- Se llena via JS -->
        </div>

        <div id="votado-exito" style="display: none;">
            <div class="text-center">
                <h2 class="text-success">¡Votación Completada!</h2>
                <p>Gracias por participar en las elecciones escolares.</p>
                <button onclick="regresarInicio()" class="btn btn-primary mt-3">Regresar al Inicio</button>
            </div>
        </div>
    </div>
    <!-- [ content ] End -->
</div>
<!-- [ Layout content ] End -->

<style>
    #reloj {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-weight: bold;
        color: #fff;
        background: linear-gradient(45deg, #007bff, #0056b3);
        padding: 8px 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    .media img {
        border-radius: 8px;
        width: 100px;
        height: 100px;
        object-fit: cover;
    }
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let datosEstudiante = null;
    let candidatos = [];

    // Reloj
    function actualizarReloj() {
        const fecha = new Date();
        const dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        const diaSemana = dias[fecha.getDay()];
        const dia = fecha.getDate();
        const mes = meses[fecha.getMonth()];
        const ano = fecha.getFullYear();
        let horas = fecha.getHours();
        const minutos = fecha.getMinutes().toString().padStart(2, '0');
        const segundos = fecha.getSeconds().toString().padStart(2, '0');
        const ampm = horas >= 12 ? 'PM' : 'AM';
        horas = horas % 12 || 12;
        horas = horas.toString().padStart(2, '0');
        document.getElementById('reloj').innerText = `${diaSemana}, ${dia} de ${mes} de ${ano}, ${horas}:${minutos}:${segundos} ${ampm}`;
    }
    setInterval(actualizarReloj, 1000);
    actualizarReloj();

    // Mostrar errores
    function mostrarError(mensaje) {
        document.getElementById('errores-container').innerHTML = `<div class="alert alert-danger">${mensaje}</div>`;
        Swal.fire({
            title: 'Error',
            text: mensaje,
            icon: 'error',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#3085d6'
        });
    }

    // Mostrar éxito
    function mostrarExito(mensaje) {
        Swal.fire({
            title: 'Éxito',
            text: mensaje,
            icon: 'success',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#3085d6'
        });
    }

    // Regresar al inicio
    function regresarInicio() {
        window.location.href = 'index.php';
    }

    // Búsqueda por DNI (AJAX)
    document.getElementById('form-buscar-dni').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        const dni = document.getElementById('dni').value.trim();
        if (!dni) return;

        fetch('controllers/Voto_controles.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'buscarPorDni', dni: dni })
        })
        .then(response => response.json())
        .then(data => {
            form.classList.remove('was-validated');
            if (data.success) {
                datosEstudiante = data.alumno;
                candidatos = data.candidatos;
                if (data.alumno.votado) {
                    mostrarError('Este alumno ya ha votado.');
                    document.getElementById('busqueda-form').style.display = 'none';
                    document.getElementById('votado-exito').style.display = 'block';
                } else {
                    mostrarDatosAlumno();
                    mostrarListaCandidatos();
                }
                document.getElementById('busqueda-form').style.display = 'none';
            } else {
                mostrarError(data.message);
            }
        })
        .catch(error => mostrarError('Error al buscar: ' + error.message));
    });

    // Mostrar datos del alumno
    function mostrarDatosAlumno() {
        const container = document.getElementById('datos-alumno');
        container.innerHTML = `
            <div class="card mb-4" style="max-width: 400px; margin: 0 auto;">
                <div class="card-body text-left">
                    <h5 class="card-title">Datos del Alumno</h5>
                    <p><strong>Nombre:</strong> ${datosEstudiante.nombre}</p>
                    <p><strong>Nivel:</strong> ${datosEstudiante.nivel}</p>
                    <p><strong>Mesa de Votación:</strong> ${datosEstudiante.mesa}</p>
                    <p class="text-info">Estás votando en las elecciones de ${datosEstudiante.nivel}</p>
                </div>
            </div>
        `;
        container.style.display = 'block';
    }

    // Mostrar lista de candidatos
    function mostrarListaCandidatos() {
        const container = document.getElementById('lista-candidatos');
        if (candidatos.length === 0) {
            container.innerHTML = `
                <div class="card mt-4" style="max-width: 600px; margin: 0 auto;">
                    <div class="card-body text-left">
                        <h5 class="card-title">Lista de Candidatos (Nivel: ${datosEstudiante.nivel})</h5>
                        <p class="text-warning">No hay candidatos disponibles para este nivel.</p>
                    </div>
                </div>
            `;
        } else {
            let html = `
                <div class="card mt-4" style="max-width: 600px; margin: 0 auto;">
                    <div class="card-body text-left">
                        <h5 class="card-title">Lista de Candidatos (Nivel: ${datosEstudiante.nivel})</h5>
                        <p>Selecciona un candidato para votar en las elecciones de ${datosEstudiante.nivel}.</p>
            `;
            candidatos.forEach(candidato => {
                html += `
                    <div class="media mb-3">
                        <img src="${candidato.foto_candidata}" alt="Foto de ${candidato.nombre}" class="mr-3">
                        <img src="${candidato.foto_campana}" alt="Foto de Campaña" class="ml-3">
                        <div class="media-body">
                            <h6 class="mt-0">${candidato.nombre}</h6>
                            <p class="mb-0"><strong>Campaña:</strong> ${candidato.campaña}</p>
                            <button onclick="votar(${candidato.id})" class="btn btn-primary mt-2"><i class="feather icon-check-square"></i> Votar por este Candidato</button>
                        </div>
                    </div>
                    <hr>
                `;
            });
            html += `
                    <div class="media mb-3 border-top pt-3">
                        <div class="media-body">
                            <h6 class="mt-0">Voto Nulo</h6>
                            <p class="mb-0"><strong>Descripción:</strong> Voto en blanco, no asignado a ningún candidato.</p>
                            <button onclick="votar('nulo')" class="btn btn-secondary mt-2"><i class="feather icon-x-circle"></i> Emitir Voto Nulo</button>
                        </div>
                    </div>
                    <p class="text-muted mt-3">Nota: Solo puedes votar una vez. Una vez votado, no podrás regresar.</p>
                    </div>
                </div>
            `;
            container.innerHTML = html;
        }
        container.style.display = 'block';
    }

    // Votar (AJAX)
    function votar(candidatoId) {
        if (!datosEstudiante) return;

        fetch('controllers/Voto_controles.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'registrarVoto', dni: datosEstudiante.dni, candidato_id: candidatoId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarExito(data.message);
                document.getElementById('lista-candidatos').style.display = 'none';
                document.getElementById('datos-alumno').style.display = 'none';
                document.getElementById('votado-exito').style.display = 'block';
            } else {
                mostrarError(data.message);
            }
        })
        .catch(error => mostrarError('Error al votar: ' + error.message));
    }
</script>

<?php include 'include/footer.php'; ?>