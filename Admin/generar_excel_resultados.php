<?php
require_once '../vendor/autoload.php';
require_once 'model/conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

try {
    $spreadsheet = new Spreadsheet();

    // Función para generar hoja por nivel
    function generarHojaPorNivel($spreadsheet, $nivel, $conexion) {
        $sheet = $spreadsheet->createSheet();
        $titulo = $nivel === '' ? 'General' : ucfirst($nivel);
        $sheet->setTitle($titulo);

        // Headers
        $headers = ['Posición', 'Candidato', 'Votos', 'Porcentaje (%)'];
        $sheet->fromArray($headers, null, 'A1');

        // Consulta similar al backend
        $sql = "
            SELECT 
                COALESCE(CONCAT(a.nombre, ' ', a.apellidos), 'Voto Nulo') AS nombre,
                COUNT(v.id_voto) AS votos
            FROM voto v
            LEFT JOIN candidato c ON v.id_candidato = c.id_candidato
            LEFT JOIN alumnos a ON c.id_alumno = a.id_alumno
            WHERE 1=1
        ";
        $params = [];
        if ($nivel !== '') {
            $sql .= " AND a.nivel = ?";
            $params[] = $nivel;
        }
        $sql .= " GROUP BY v.id_candidato ORDER BY votos DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->execute($params);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total_votos = array_sum(array_column($resultados, 'votos'));

        // Datos con posición y porcentaje
        $row = 2;
        foreach ($resultados as $index => $rowData) {
            $posicion = $index + 1;
            $porcentaje = $total_votos > 0 ? round(($rowData['votos'] / $total_votos) * 100, 2) : 0;
            $sheet->setCellValue("A$row", $posicion . '° Lugar');
            $sheet->setCellValue("B$row", $rowData['nombre']);
            $sheet->setCellValue("C$row", $rowData['votos']);
            $sheet->setCellValue("D$row", $porcentaje . '%');

            // Estilos para top 3
            if ($posicion <= 3) {
                $fill = $sheet->getStyle("A$row:D$row");
                $fill->getFill()->setFillType(Fill::FILL_SOLID);
                if ($posicion === 1) $fill->getFill()->getStartColor()->setRGB('90EE90'); // Verde
                elseif ($posicion === 2) $fill->getFill()->getStartColor()->setRGB('FFFF99'); // Amarillo
                else $fill->getFill()->getStartColor()->setRGB('ADD8E6'); // Azul
            }
            $row++;
        }

        // Total en pie
        $sheet->setCellValue("A$row", 'Total Votos');
        $sheet->setCellValue("C$row", $total_votos);
        $sheet->getStyle("A$row:C$row")->getFont()->setBold(true);

        // Auto-size y alineación
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    // Generar hojas
    generarHojaPorNivel($spreadsheet, '', $conexion); // General
    generarHojaPorNivel($spreadsheet, 'primaria', $conexion);
    generarHojaPorNivel($spreadsheet, 'secundaria', $conexion);

    // Remover hoja por defecto
    $spreadsheet->removeSheetByIndex(0);

    // Headers para descarga
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="resultados_por_nivel.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
} catch (Exception $e) {
    header('Location: resultados.php?error=' . urlencode('Error al generar el archivo Excel: ' . $e->getMessage()));
    exit;
}
?>