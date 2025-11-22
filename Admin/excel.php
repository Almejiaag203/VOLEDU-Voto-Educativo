<?php
require_once '../model/conexion.php';
require_once '../vendor/autoload.php'; // Si usas Composer para PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$id_proceso = $_GET['id_proceso'] ?? 1; // Ajusta según el proceso activo

try {
    // Consulta para asignaciones de secundaria
    $stmt_sec = $conexion->prepare("
        SELECT a.nombre, a.apellidos, a.dni, a.grado, a.seccion, m.numero AS mesa_numero, m.ubicacion
        FROM asignacion_mesa am
        JOIN alumnos a ON am.id_alumno = a.id_alumno
        JOIN mesa_sufragio m ON am.id_mesa = m.id_mesa
        WHERE am.id_proceso = ? AND a.nivel = 'secundaria' AND a.activo = 1
        ORDER BY FIELD(a.grado, 'PRIMERO', 'SEGUNDO', 'TERCERO', 'CUARTO', 'QUINTO'), a.apellidos, a.nombre
    ");
    $stmt_sec->execute([$id_proceso]);
    $asignaciones_sec = $stmt_sec->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para asignaciones de primaria
    $stmt_prim = $conexion->prepare("
        SELECT a.nombre, a.apellidos, a.dni, a.grado, a.seccion, m.numero AS mesa_numero, m.ubicacion
        FROM asignacion_mesa am
        JOIN alumnos a ON am.id_alumno = a.id_alumno
        JOIN mesa_sufragio m ON am.id_mesa = m.id_mesa
        WHERE am.id_proceso = ? AND a.nivel = 'primaria' AND a.activo = 1
        ORDER BY FIELD(a.grado, 'PRIMERO', 'SEGUNDO', 'TERCERO', 'CUARTO', 'QUINTO', 'SEXTO'), a.apellidos, a.nombre
    ");
    $stmt_prim->execute([$id_proceso]);
    $asignaciones_prim = $stmt_prim->fetchAll(PDO::FETCH_ASSOC);

    $spreadsheet = new Spreadsheet();

    // Hoja 1: Secundaria
    $sheet_sec = $spreadsheet->createSheet();
    $sheet_sec->setTitle('Secundaria');
    $sheet_sec->fromArray(['Nombre', 'Apellidos', 'DNI', 'Grado', 'Sección', 'Mesa Número', 'Ubicación'], null, 'A1');
    $sheet_sec->fromArray($asignaciones_sec, null, 'A2');

    // Estilos para secundaria (fondo azul)
    $headerStyle_sec = $sheet_sec->getStyle('A1:G1');
    $headerStyle_sec->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ADD8E6');
    $headerStyle_sec->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet_sec->getStyle('A1:G' . (count($asignaciones_sec) + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Hoja 2: Primaria
    $sheet_prim = $spreadsheet->createSheet();
    $sheet_prim->setTitle('Primaria');
    $sheet_prim->fromArray(['Nombre', 'Apellidos', 'DNI', 'Grado', 'Sección', 'Mesa Número', 'Ubicación'], null, 'A1');
    $sheet_prim->fromArray($asignaciones_prim, null, 'A2');

    // Estilos para primaria (fondo verde)
    $headerStyle_prim = $sheet_prim->getStyle('A1:G1');
    $headerStyle_prim->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('90EE90');
    $headerStyle_prim->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet_prim->getStyle('A1:G' . (count($asignaciones_prim) + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Remover hoja por defecto
    $spreadsheet->removeSheetByIndex(0);

    // Guardar archivo
    $writer = new Xlsx($spreadsheet);
    $filename = "asignaciones_por_nivel_$id_proceso.xlsx";
    $writer->save($filename);

    // Descargar
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    readfile($filename);
    unlink($filename); // Limpiar archivo temporal

} catch (Exception $e) {
    echo "Error al generar Excel: " . $e->getMessage();
}
?>