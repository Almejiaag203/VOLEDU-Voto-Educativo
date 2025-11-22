<?php
session_start(); // Iniciar la sesión
// Verificar rol - Solo Administrador
if ($_SESSION['rol'] !== 'Administrador') {
    header("Location: ../login/login.php");
    exit;
}

// Incluir conexión a la base de datos
include 'model/conexion.php'; // Ruta correcta según tu estructura

// Consultas para obtener datos del dashboard
// Total de alumnos activos
$total_alumnos_query = "SELECT COUNT(*) as total FROM alumnos WHERE activo = 1";
$stmt = $conexion->prepare($total_alumnos_query);
$stmt->execute();
$total_alumnos = $stmt->fetch()['total'];

// Total de candidatos en el proceso activo
$proceso_activo_id = 1; // Cambia por una consulta dinámica si es necesario
$total_candidatos_query = "SELECT COUNT(*) as total FROM candidato WHERE id_proceso = ?";
$stmt = $conexion->prepare($total_candidatos_query);
$stmt->execute([$proceso_activo_id]);
$total_candidatos = $stmt->fetch()['total'];

// Total de mesas de sufragio activas
$total_mesas_query = "SELECT COUNT(*) as total FROM mesa_sufragio WHERE id_proceso = ? AND activo = 1";
$stmt = $conexion->prepare($total_mesas_query);
$stmt->execute([$proceso_activo_id]);
$total_mesas = $stmt->fetch()['total'];

// Total de votos emitidos
$total_votos_query = "SELECT COUNT(*) as total FROM voto WHERE id_proceso = ?";
$stmt = $conexion->prepare($total_votos_query);
$stmt->execute([$proceso_activo_id]);
$total_votos = $stmt->fetch()['total'];

// Porcentaje de participación (votos / alumnos * 100)
$porcentaje_participacion = ($total_alumnos > 0) ? round(($total_votos / $total_alumnos) * 100, 2) : 0;

// Votos por candidato (para tabla de estadísticas)
$votos_por_candidato_query = "
    SELECT c.id_candidato, a.nombre, a.apellidos, COUNT(v.id_voto) as votos
    FROM candidato c
    JOIN alumnos a ON c.id_alumno = a.id_alumno
    LEFT JOIN voto v ON c.id_candidato = v.id_candidato AND v.id_proceso = ?
    WHERE c.id_proceso = ?
    GROUP BY c.id_candidato
";
$stmt = $conexion->prepare($votos_por_candidato_query);
$stmt->execute([$proceso_activo_id, $proceso_activo_id]);
$votos_por_candidato_result = $stmt->fetchAll();

// Datos para gráficos pequeños
// Alumnos activos (usar conteo estático ya que no hay fecha_registro)
$alumnos_data = [
    ['mes' => 'Ene', 'valor' => $total_alumnos],
    ['mes' => 'Feb', 'valor' => $total_alumnos],
    ['mes' => 'Mar', 'valor' => $total_alumnos],
    ['mes' => 'Abr', 'valor' => $total_alumnos]
];

// Votos emitidos por mes (intentar usar datos reales)
$votos_por_mes_query = "
    SELECT DATE_FORMAT(fecha_voto, '%b') as mes, COUNT(*) as valor
    FROM voto
    WHERE id_proceso = ?
    GROUP BY DATE_FORMAT(fecha_voto, '%b')
    ORDER BY fecha_voto
    LIMIT 4
";
try {
    $stmt = $conexion->prepare($votos_por_mes_query);
    $stmt->execute([$proceso_activo_id]);
    $votos_data = $stmt->fetchAll();
} catch (PDOException $e) {
    // Si la consulta falla (por ejemplo, si fecha_voto no existe), usar respaldo estático
    $votos_data = [
        ['mes' => 'Ene', 'valor' => 0],
        ['mes' => 'Feb', 'valor' => 0],
        ['mes' => 'Mar', 'valor' => 0],
        ['mes' => 'Abr', 'valor' => $total_votos]
    ];
}

// Últimos votos (para tabla)
$ultimos_votos_query = "
    SELECT v.fecha_voto, a.nombre as alumno_nombre, a.apellidos as alumno_apellidos, c.nombre as candidato_nombre, c.apellidos as candidato_apellidos, m.numero as mesa
    FROM voto v
    JOIN alumnos a ON v.id_alumno = a.id_alumno
    JOIN alumnos c ON v.id_candidato = (SELECT id_alumno FROM candidato WHERE id_candidato = v.id_candidato)
    JOIN mesa_sufragio m ON v.id_mesa = m.id_mesa
    WHERE v.id_proceso = ?
    ORDER BY v.fecha_voto DESC
    LIMIT 10
";
try {
    $stmt = $conexion->prepare($ultimos_votos_query);
    $stmt->execute([$proceso_activo_id]);
    $ultimos_votos_result = $stmt->fetchAll();
} catch (PDOException $e) {
    $ultimos_votos_result = []; // En caso de error, usar array vacío
}

// Procesos electorales (para mostrar estado)
$procesos_query = "SELECT * FROM proceso_electoral ORDER BY id_proceso DESC LIMIT 5";
$stmt = $conexion->prepare($procesos_query);
$stmt->execute();
$procesos_result = $stmt->fetchAll();

?>

<?php include 'include/header.php'; ?>

<!-- [ content ] Start -->
<div class="container-fluid flex-grow-1 container-p-y">
    <h4 class="font-weight-bold py-3 mb-0">Dashboard de Votación Escolar</h4>
    <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </div>

    <!-- Primera fila: Estadísticas principales -->
    <div class="row">
        <!-- Comentado: Sección de Estadísticas de Votación (gráfico principal) -->
        <!--
        <div class="col-lg-7">
            <div class="card mb-4 shadow-sm">
                <div class="card-header with-elements">
                    <h6 class="card-header-title mb-0">Estadísticas de Votación</h6>
                    <div class="card-header-elements ml-auto">
                        <label class="text m-0">
                            <span class="text-light text-tiny font-weight-semibold align-middle">MOSTRAR GRÁFICOS</span>
                            <span class="switcher switcher-sm d-inline-block align-middle mr-0 ml-2">
                                <input type="checkbox" class="switcher-input" checked>
                                <span class="switcher-indicator"><span class="switcher-yes"></span><span class="switcher-no"></span></span>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div id="statistics-chart-1" style="height:270px"></div>
                    <script>
                        const votosData = <?php
                            $data = [];
                            foreach ($votos_por_candidato_result as $row) {
                                $data[] = [
                                    'candidato' => $row['nombre'] . ' ' . $row['apellidos'],
                                    'votos' => $row['votos']
                                ];
                            }
                            echo json_encode($data);
                        ?>;
                        console.log('Datos para el gráfico principal:', votosData);

                        if (votosData.length > 0) {
                            am4core.ready(function() {
                                am4core.useTheme(am4themes_animated);
                                const chart = am4core.create("statistics-chart-1", am4charts.PieChart);
                                chart.data = votosData;

                                const pieSeries = chart.series.push(new am4charts.PieSeries());
                                pieSeries.dataFields.value = "votos";
                                pieSeries.dataFields.category = "candidato";
                                pieSeries.slices.template.stroke = am4core.color("#fff");
                                pieSeries.slices.template.strokeWidth = 2;
                                pieSeries.slices.template.strokeOpacity = 1;

                                pieSeries.colors.list = [
                                    am4core.color("#FF6384"),
                                    am4core.color("#36A2EB"),
                                    am4core.color("#FFCE56"),
                                    am4core.color("#4BC0C0"),
                                    am4core.color("#9966FF")
                                ];

                                chart.legend = new am4charts.Legend();
                                chart.legend.position = "top";

                                const title = chart.titles.create();
                                title.text = "Votos por Candidato";
                                title.fontSize = 18;
                                title.marginBottom = 20;

                                pieSeries.slices.template.events.on("ready", function(ev) {
                                    ev.target.animate({ property: "scale", from: 0, to: 1 }, 500);
                                });
                            });
                        } else {
                            document.getElementById('statistics-chart-1').style.display = 'none';
                            const cardBody = document.querySelector('.card-body');
                            cardBody.innerHTML += '<p class="text-muted">No hay datos de votos disponibles para mostrar el gráfico.</p>';
                        }
                    </script>
                </div>
            </div>
        </div>
        -->
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-6 col-lg-6 mb-4">
                    <div class="card bg-pattern-2-dark shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="lnr lnr-users display-4 text-primary"></div>
                                <div class="ml-3 text-center">
                                    <div class="text-muted small">Alumnos Activos</div>
                                    <div class="text-large"><?php echo $total_alumnos; ?></div>
                                </div>
                            </div>
                            <div id="ecom-chart-3" class="mt-3 chart-shadow-primary" style="height:60px"></div>
                            <script>
                                const alumnosData = <?php echo json_encode($alumnos_data); ?>;
                                console.log('Datos para alumnos:', alumnosData);

                                if (alumnosData.length > 0) {
                                    am4core.ready(function() {
                                        am4core.useTheme(am4themes_animated);
                                        const chart = am4core.create("ecom-chart-3", am4charts.XYChart);
                                        chart.data = alumnosData;

                                        const categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                                        categoryAxis.dataFields.category = "mes";
                                        categoryAxis.renderer.grid.template.opacity = 0;
                                        categoryAxis.renderer.labels.template.disabled = true;

                                        const valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                                        valueAxis.renderer.grid.template.opacity = 0;
                                        valueAxis.renderer.labels.template.disabled = true;

                                        const series = chart.series.push(new am4charts.LineSeries());
                                        series.dataFields.valueY = "valor";
                                        series.dataFields.categoryX = "mes";
                                        series.strokeWidth = 2;
                                        series.stroke = am4core.color("#FF6384");

                                        chart.padding(0, 0, 0, 0);
                                    });
                                }
                            </script>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 mb-4">
                    <div class="card bg-pattern-2 bg-primary text-white shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="lnr lnr-chart-bars display-4"></div>
                                <div class="ml-3 text-center">
                                    <div class="small">Votos Emitidos</div>
                                    <div class="text-large"><?php echo $total_votos; ?></div>
                                </div>
                            </div>
                            <div id="order-chart-1" class="mt-3 chart-shadow" style="height:60px"></div>
                            <script>
                                const votosDataSmall = <?php echo json_encode($votos_data); ?>;
                                console.log('Datos para votos:', votosDataSmall);

                                if (votosDataSmall.length > 0) {
                                    am4core.ready(function() {
                                        am4core.useTheme(am4themes_animated);
                                        const chart = am4core.create("order-chart-1", am4charts.XYChart);
                                        chart.data = votosDataSmall;

                                        const categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                                        categoryAxis.dataFields.category = "mes";
                                        categoryAxis.renderer.grid.template.opacity = 0;
                                        categoryAxis.renderer.labels.template.disabled = true;

                                        const valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                                        valueAxis.renderer.grid.template.opacity = 0;
                                        valueAxis.renderer.labels.template.disabled = true;

                                        const series = chart.series.push(new am4charts.LineSeries());
                                        series.dataFields.valueY = "valor";
                                        series.dataFields.categoryX = "mes";
                                        series.strokeWidth = 2;
                                        series.stroke = am4core.color("#36A2EB");

                                        chart.padding(0, 0, 0, 0);
                                    });
                                }
                            </script>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-3">
                            <div class="row no-gutters row-bordered row-border-light h-100">
                                <div class="col-sm-6 col-md-4 col-lg-3 d-flex align-items-center mb-2">
                                    <div class="card-body media align-items-center text-dark">
                                        <i class="lnr lnr-users display-4 d-block text-primary"></i>
                                        <span class="media-body d-block ml-3">
                                            <span class="text-big mr-1 text-primary"><?php echo $total_candidatos; ?></span><br>
                                            <small class="text-muted">Candidatos</small>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-3 d-flex align-items-center mb-2">
                                    <div class="card-body media align-items-center text-dark">
                                        <i class="lnr lnr-map display-4 d-block text-primary"></i>
                                        <span class="media-body d-block ml-3">
                                            <span class="text-big"><span class="mr-1 text-primary"><?php echo $total_mesas; ?></span>Mesas</span><br>
                                            <small class="text-muted">De Sufragio</small>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-3 d-flex align-items-center mb-2">
                                    <div class="card-body media align-items-center text-dark">
                                        <i class="lnr lnr-checkmark-circle display-4 d-block text-primary"></i>
                                        <span class="media-body d-block ml-3">
                                            <span class="text-big"><span class="mr-1 text-primary"><?php echo $porcentaje_participacion; ?>%</span> Participación</span><br>
                                            <small class="text-muted">En el proceso</small>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-3 d-flex align-items-center mb-2">
                                    <div class="card-body media align-items-center text-dark">
                                        <i class="lnr lnr-calendar display-4 d-block text-primary"></i>
                                        <span class="media-body d-block ml-3">
                                            <span class="text-big"><span class="mr-1 text-primary">1</span> Proceso Activo</span><br>
                                            <small class="text-muted">En curso</small>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda fila: Estadísticas adicionales -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-body p-3">
                    <div class="row no-gutters row-bordered row-border-light h-100">
                        <div class="col-md-6 col-lg-3 d-flex align-items-center mb-3">
                            <div class="card-body p-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <i class="lnr lnr-users text-primary display-4"></i>
                                    </div>
                                    <div class="col">
                                        <h6 class="mb-0 text-muted">Total <span class="text-primary">Alumnos</span></h6>
                                        <h4 class="mt-2 mb-0"><?php echo $total_alumnos; ?></h4>
                                    </div>
                                </div>
                                <p class="mb-0 text-muted small">Registrados en el sistema</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 d-flex align-items-center mb-3">
                            <div class="card-body p-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <i class="lnr lnr-rocket text-primary display-4"></i>
                                    </div>
                                    <div class="col">
                                        <h6 class="mb-0 text-muted"><span class="text-primary">Candidatos</span> Inscritos</h6>
                                        <h4 class="mt-2 mb-0"><?php echo $total_candidatos; ?></h4>
                                    </div>
                                </div>
                                <p class="mb-0 text-muted small">En el proceso actual</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 d-flex align-items-center mb-3">
                            <div class="card-body p-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <i class="lnr lnr-map-marker text-primary display-4"></i>
                                    </div>
                                    <div class="col">
                                        <h6 class="mb-0 text-muted">Mesas de <span class="text-primary">Sufragio</span></h6>
                                        <h4 class="mt-2 mb-0"><?php echo $total_mesas; ?></h4>
                                    </div>
                                </div>
                                <p class="mb-0 text-muted small">Configuradas</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 d-flex align-items-center mb-3">
                            <div class="card-body p-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <i class="lnr lnr-pie-chart text-primary display-4"></i>
                                    </div>
                                    <div class="col">
                                        <h6 class="mb-0 text-muted">Votos <span class="text-primary">Totales</span></h6>
                                        <h4 class="mt-2 mb-0"><?php echo $total_votos; ?></h4>
                                    </div>
                                </div>
                                <p class="mb-0 text-muted small">Emitidos hasta ahora</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tercera fila: Tareas y Detalles -->
    <div class="row">
        <div class="col-xl-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header with-elements">
                    <h6 class="card-header-title mb-0">Procesos Electorales Recientes</h6>
                    <div class="card-header-elements ml-auto">
                        <button type="button" class="btn btn-default btn-xs md-btn-flat">Ver más</button>
                    </div>
                </div>
                <div class="card-body p-3" style="height: 310px" id="procesos-inner">
                    <ul class="list-group">
                        <?php foreach ($procesos_result as $proceso): ?>
                            <li class="list-group-item">
                                <strong><?php echo htmlspecialchars($proceso['nombre']); ?></strong><br>
                                <small>Del <?php echo htmlspecialchars($proceso['fecha_inicio']); ?> al <?php echo htmlspecialchars($proceso['fecha_fin']); ?> - <?php echo $proceso['activo'] ? 'Activo' : 'Inactivo'; ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xl-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header with-elements pb-0">
                    <h6 class="card-header-title mb-0">Detalles de Votación</h6>
                    <div class="card-header-elements ml-auto p-0">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#votos-stats">Estadísticas de Votos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#ultimos-votos">Últimos Votos</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="nav-tabs-top">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="votos-stats">
                                <div style="height: 330px" id="tab-table-1">
                                    <table class="table table-hover card-table">
                                        <thead>
                                            <tr>
                                                <th>Candidato</th>
                                                <th>Votos</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($votos_por_candidato_result as $row): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellidos']); ?></td>
                                                    <td><?php echo $row['votos']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <a href="javascript:" class="card-footer d-block text-center text-dark small font-weight-semibold">VER MÁS</a>
                            </div>
                            <div class="tab-pane fade" id="ultimos-votos">
                                <div style="height: 330px" id="tab-table-2">
                                    <table class="table table-hover card-table">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Alumno</th>
                                                <th>Candidato</th>
                                                <th>Mesa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($ultimos_votos_result as $voto): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($voto['fecha_voto']); ?></td>
                                                    <td><?php echo htmlspecialchars($voto['alumno_nombre'] . ' ' . $voto['alumno_apellidos']); ?></td>
                                                    <td><?php echo htmlspecialchars($voto['candidato_nombre'] . ' ' . $voto['candidato_apellidos']); ?></td>
                                                    <td><?php echo htmlspecialchars($voto['mesa']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <a href="javascript:" class="card-footer d-block text-center text-dark small font-weight-semibold">VER MÁS</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ content ] End -->

<?php include 'include/footer.php'; ?>

<!-- Incluir librerías de amCharts (si no están en header.php o footer.php) -->
<script src="assets/libs/chart-am4/core.js"></script>
<script src="assets/libs/chart-am4/charts.js"></script>
<script src="assets/libs/chart-am4/animated.js"></script>