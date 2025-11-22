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
        <h4 class="font-weight-bold py-3 mb-0">RESULTADOS DE VOTACIONES</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item">Resultados</li>
            </ol>
        </div>

        <!-- Botones para descargar PDF y Excel -->
        <div class="mb-4">
            <a href="generar_pdf_resultados.php" class="btn btn-primary mr-2" target="_blank"><i class="feather icon-download"></i> Descargar PDF</a>
            <a href="generar_excel_resultados.php" class="btn btn-success"><i class="feather icon-download"></i> Descargar Excel (por Nivel)</a>
        </div>

        <!-- Pestañas para General, Primaria, Secundaria y Por Mesas -->
        <div class="card">
            <h6 class="card-header">
                Resultados por Nivel y Mesas
                <ul class="nav nav-tabs mt-2" id="resultadosTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="primaria-tab" data-toggle="tab" href="#primaria" role="tab" aria-controls="primaria" aria-selected="false">Primaria</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="secundaria-tab" data-toggle="tab" href="#secundaria" role="tab" aria-controls="secundaria" aria-selected="false">Secundaria</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="mesas-tab" data-toggle="tab" href="#mesas" role="tab" aria-controls="mesas" aria-selected="false">Por Mesas</a>
                    </li>
                </ul>
            </h6>
            <div class="card-body">
                <div class="tab-content" id="resultadosTabContent">
                    <!-- General -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <canvas id="general-chart" height="200"></canvas>
                        <div class="mt-3" id="general-ranking"></div>
                        <div class="card-footer text-muted mt-3" id="general-total">Total Votos: 0 (incluye nulos)</div>
                    </div>
                    <!-- Primaria -->
                    <div class="tab-pane fade" id="primaria" role="tabpanel" aria-labelledby="primaria-tab">
                        <canvas id="primaria-chart" height="200"></canvas>
                        <div class="mt-3" id="primaria-ranking"></div>
                        <div class="card-footer text-muted mt-3" id="primaria-total">Total Votos: 0 (incluye nulos)</div>
                    </div>
                    <!-- Secundaria -->
                    <div class="tab-pane fade" id="secundaria" role="tabpanel" aria-labelledby="secundaria-tab">
                        <canvas id="secundaria-chart" height="200"></canvas>
                        <div class="mt-3" id="secundaria-ranking"></div>
                        <div class="card-footer text-muted mt-3" id="secundaria-total">Total Votos: 0 (incluye nulos)</div>
                    </div>
                    <!-- Por Mesas -->
                    <div class="tab-pane fade" id="mesas" role="tabpanel" aria-labelledby="mesas-tab">
                        <div class="row" id="mesas-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Corregir z-index de SweetAlert2
    const style = document.createElement('style');
    style.innerHTML = `
        .swal2-zindex {
            z-index: 9999 !important;
        }
    `;
    document.head.appendChild(style);

    let charts = {}; // Almacenar charts para actualizar

    // Función para renderizar ranking
    function renderRanking(containerId, data, nivel) {
        const container = document.getElementById(containerId);
        container.innerHTML = '';
        data.forEach((c, index) => {
            const bgClass = index === 0 ? 'bg-success text-white' : index === 1 ? 'bg-warning text-dark' : index === 2 ? 'bg-info text-white' : '';
            const position = index === 0 ? '🥇 Ganador' : index === 1 ? '🥈 2do Lugar' : index === 2 ? '🥉 3er Lugar' : `${index + 1}° Lugar`;
            const foto = c.foto ? `<img src="../Uploads/${c.foto}" alt="${c.nombre}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; margin-right: 10px;">` : '';
            const logo = c.logo ? `<img src="../Uploads/${c.logo}" alt="Logo ${c.nombre}" style="width: 50px; height: 50px; object-fit: contain; margin-right: 10px;">` : '';
            container.innerHTML += `
                <div class="d-flex align-items-center mb-2 p-2 rounded ${bgClass}">
                    <strong>${position}</strong>
                    ${foto}${logo}
                    <div class="flex-grow-1">
                        <strong>${c.nombre}</strong><br>
                        ${c.porcentaje}% (${c.votos} votos)
                    </div>
                </div>
            `;
        });
    }

    // Función para renderizar chart
    function renderChart(chartId, data, title) {
        const ctx = document.getElementById(chartId).getContext('2d');
        if (charts[chartId]) {
            charts[chartId].destroy();
        }
        charts[chartId] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(c => c.nombre),
                datasets: [{
                    label: 'Porcentaje de Votos',
                    data: data.map(c => c.porcentaje),
                    backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#dc3545', '#6c757d'], // Verde para 1er, amarillo 2do, etc.
                    borderColor: ['#218838', '#e0a800', '#138496', '#c82333', '#545b62'],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        title: { display: true, text: 'Porcentaje (%)' }
                    }
                },
                plugins: {
                    title: { display: true, text: title },
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.raw}% (${context.dataset.data[context.dataIndex].votos} votos)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Load results for a specific level
    function loadResultadosPorNivel(nivel) {
        fetch('controllers/Resultados_controles.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'getResultadosPorNivel', nivel: nivel })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const chartId = nivel === '' ? 'general-chart' : `${nivel}-chart`;
                const rankingId = nivel === '' ? 'general-ranking' : `${nivel}-ranking`;
                const totalId = nivel === '' ? 'general-total' : `${nivel}-total`;
                const title = nivel === '' ? 'Resultados Generales' : `Resultados ${nivel.charAt(0).toUpperCase() + nivel.slice(1)}`;

                renderChart(chartId, data.candidatos, title);
                renderRanking(rankingId, data.candidatos, nivel);
                document.getElementById(totalId).textContent = `Total Votos: ${data.total_votos} (incluye nulos)`;
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'Error al cargar resultados',
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
                text: 'Error al cargar los resultados: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
        });
    }

    // Load results by mesas
    function loadResultadosPorMesas() {
        fetch('controllers/Resultados_controles.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'getResultadosPorMesa' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const container = document.getElementById('mesas-container');
                container.innerHTML = '';

                data.mesas.forEach((mesa, index) => {
                    const col = document.createElement('div');
                    col.className = 'col-md-6 mb-4';
                    col.innerHTML = `
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Mesa ${mesa.numero} - ${mesa.ubicacion}</h5>
                                <canvas id="mesa-chart-${index}" height="150"></canvas>
                                <div class="mt-3" id="mesa-ranking-${index}"></div>
                            </div>
                            <div class="card-footer text-muted">
                                Total Votos: ${mesa.total_votos} (incluye nulos)
                            </div>
                        </div>
                    `;
                    container.appendChild(col);

                    // Render mini chart for mesa
                    const ctx = document.getElementById(`mesa-chart-${index}`).getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: mesa.candidatos.map(c => c.nombre),
                            datasets: [{
                                label: 'Porcentaje de Votos',
                                data: mesa.candidatos.map(c => c.porcentaje),
                                backgroundColor: ['#007bff', '#28a745', '#dc3545', '#ffc107', '#6f42c1'],
                                borderColor: ['#0056b3', '#218838', '#c82333', '#e0a800', '#5a2d91'],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    max: 100,
                                    title: { display: true, text: 'Porcentaje (%)' }
                                }
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return `${context.raw}% (${context.dataset.data[context.dataIndex].votos} votos)`;
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Render ranking for mesa
                    const rankingContainer = document.getElementById(`mesa-ranking-${index}`);
                    mesa.candidatos.forEach((c, cIndex) => {
                        const bgClass = cIndex === 0 ? 'bg-success text-white' : cIndex === 1 ? 'bg-warning text-dark' : cIndex === 2 ? 'bg-info text-white' : '';
                        const position = cIndex === 0 ? '🥇' : cIndex === 1 ? '🥈' : cIndex === 2 ? '🥉' : `${cIndex + 1}`;
                        const foto = c.foto ? `<img src="../Uploads/${c.foto}" alt="${c.nombre}" style="width: 30px; height: 30px; object-fit: cover; border-radius: 50%; margin-right: 5px;">` : '';
                        const logo = c.logo ? `<img src="../Uploads/${c.logo}" alt="Logo ${c.nombre}" style="width: 30px; height: 30px; object-fit: contain; margin-right: 5px;">` : '';
                        rankingContainer.innerHTML += `
                            <div class="d-flex align-items-center mb-1 p-1 rounded ${bgClass}">
                                <strong>${position}</strong> ${foto}${logo}<strong>${c.nombre}</strong> - ${c.porcentaje}% (${c.votos} votos)
                            </div>
                        `;
                    });
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'Error al cargar resultados por mesas',
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
                text: 'Error al cargar los resultados por mesas: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                customClass: { popup: 'swal2-zindex' }
            });
        });
    }

    // Load all on page load
    loadResultadosPorNivel(''); // General
    loadResultadosPorNivel('primaria');
    loadResultadosPorNivel('secundaria');
    loadResultadosPorMesas(); // Por Mesas
});
</script>

<?php include 'include/footer.php'; ?>