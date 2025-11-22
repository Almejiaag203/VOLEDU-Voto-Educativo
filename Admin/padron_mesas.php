<?php
require_once '../model/conexion.php';
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

$id_proceso = $_GET['id_proceso'] ?? 1;

try {
    // Consulta de todas las mesas activas del proceso
    $stmt_mesas = $conexion->prepare("
        SELECT m.id_mesa, m.numero, m.nivel
        FROM mesa_sufragio m
        WHERE m.id_proceso = ? AND m.activo = 1
        ORDER BY m.nivel DESC, m.numero
    ");
    $stmt_mesas->execute([$id_proceso]);
    $mesas = $stmt_mesas->fetchAll(PDO::FETCH_ASSOC);

    if (empty($mesas)) {
        throw new Exception("No se encontraron mesas para el proceso especificado.");
    }

    $spreadsheet = new Spreadsheet();

    // Consulta para obtener alumnos por mesa
    $stmt_alumnos = $conexion->prepare("
        SELECT a.nombre, a.apellidos, a.dni, a.grado, a.seccion
        FROM asignacion_mesa am
        JOIN alumnos a ON am.id_alumno = a.id_alumno
        WHERE am.id_mesa = ? AND am.id_proceso = ? AND a.activo = 1
        ORDER BY a.apellidos, a.nombre
    ");

    $sheet_index = 0;
    foreach ($mesas as $mesa) {
        $id_mesa = $mesa['id_mesa'];
        $mesa_numero = $mesa['numero'];
        $nivel_mesa = $mesa['nivel'];

        $stmt_alumnos->execute([$id_mesa, $id_proceso]);
        $alumnos = $stmt_alumnos->fetchAll(PDO::FETCH_ASSOC);

        if ($sheet_index > 0) {
            $sheet = $spreadsheet->createSheet();
        } else {
            $sheet = $spreadsheet->getActiveSheet();
        }
        $sheet->setTitle($mesa_numero);
        $sheet_index++;

        // Encabezados
        $headers = ['DNI', 'Nombre Completo', 'Grado', 'Sección', 'Firma', 'Huella'];
        $sheet->fromArray($headers, null, 'A1');

        // Preparar datos
        $data = [];
        foreach ($alumnos as $alumno) {
            $data[] = [
                $alumno['dni'],
                $alumno['apellidos'] . ', ' . $alumno['nombre'],
                $alumno['grado'],
                $alumno['seccion'],
                '', // Firma vacía
                ''  // Huella vacía
            ];
        }

        // Insertar datos
        $sheet->fromArray($data, null, 'A2');

        // Estilos
        $headerStyle = $sheet->getStyle('A1:F1');
        $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($nivel_mesa === 'Primaria' ? '90EE90' : 'ADD8E6');
        $headerStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $headerStyle->getFont()->setBold(true);

        // Bordes para todas las celdas
        $sheet->getStyle('A1:F' . (count($alumnos) + 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Alineación centrada
        $sheet->getStyle('A1:D' . (count($alumnos) + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:D' . (count($alumnos) + 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Ajustar ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(15); // DNI
        $sheet->getColumnDimension('B')->setWidth(30); // Nombre Completo
        $sheet->getColumnDimension('C')->setWidth(15); // Grado
        $sheet->getColumnDimension('D')->setWidth(10); // Sección
        $sheet->getColumnDimension('E')->setWidth(40); // Firma (más ancha)
        $sheet->getColumnDimension('F')->setWidth(40); // Huella (más ancha)

        // Ajustar altura de filas para todas las filas de datos
        for ($row = 2; $row <= count($alumnos) + 1; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(40); // Altura suficiente para huella
        }
        // Altura del encabezado
        $sheet->getRowDimension(1)->setRowHeight(20);
    }

    if ($sheet_index > 0 && $spreadsheet->getSheetCount() > $sheet_index) {
        $spreadsheet->removeSheetByIndex(0);
    }

    $filename = "padron_mesas_{$id_proceso}.xlsx";
    $writer = new Xlsx($spreadsheet);
    $writer->save($filename);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    readfile($filename);
    unlink($filename);

} catch (Exception $e) {
    echo "Error al generar el padrón: " . $e->getMessage();
}
?>