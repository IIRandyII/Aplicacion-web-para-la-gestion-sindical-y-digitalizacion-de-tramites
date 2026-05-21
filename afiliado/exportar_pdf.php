<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");
require_once('../lib/fpdf.php');

$id_departamento = $_SESSION['id_departamento'];
$filtro          = $_GET['filtro'] ?? 'mes';

switch ($filtro) {
    case 'dia':
        $condicionFecha = "AND DATE(fecha_creacion) = CURDATE()";
        $periodoTexto   = "Hoy " . date('d/m/Y');
        break;
    case 'semana':
        $condicionFecha = "AND YEARWEEK(fecha_creacion, 1) = YEARWEEK(CURDATE(), 1)";
        $periodoTexto   = "Esta semana";
        break;
    case 'anio':
        $condicionFecha = "AND YEAR(fecha_creacion) = YEAR(CURDATE())";
        $periodoTexto   = "Anio " . date('Y');
        break;
    default:
        $condicionFecha = "AND MONTH(fecha_creacion) = MONTH(CURDATE()) AND YEAR(fecha_creacion) = YEAR(CURDATE())";
        $periodoTexto   = date('F Y');
}

// ===============================
// CONSULTAS DE DATOS
// ===============================

// Por estado
$sql  = "SELECT estado, COUNT(id_tramite) AS total FROM tramites WHERE id_departamento = ? $condicionFecha GROUP BY estado";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_departamento);
$stmt->execute();
$porEstado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Por tipo
$sql  = "SELECT tipo_tramite, COUNT(id_tramite) AS total FROM tramites WHERE id_departamento = ? $condicionFecha GROUP BY tipo_tramite ORDER BY total DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_departamento);
$stmt->execute();
$porTipo = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// ===============================
// GENERAR PDF CON FPDF
// ===============================
class PDF extends FPDF {

    function Header() {
        $this->SetFillColor(0, 40, 85);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 12, 'Reporte de Tramites - Seccion 49', 0, 1, 'C', true);
        $this->SetTextColor(0, 0, 0);
        $this->Ln(3);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }

    function TituloSeccion($titulo) {
        $this->SetFillColor(37, 99, 235);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 9, $titulo, 0, 1, 'L', true);
        $this->SetTextColor(0, 0, 0);
        $this->Ln(2);
    }

    function TablaEncabezado($cols, $anchos) {
        $this->SetFillColor(0, 40, 85);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 10);
        foreach ($cols as $i => $col) {
            $this->Cell($anchos[$i], 8, $col, 1, 0, 'C', true);
        }
        $this->Ln();
        $this->SetTextColor(0, 0, 0);
    }

    function TablaFila($datos, $anchos, $fill = false) {
        $this->SetFillColor(219, 234, 254);
        $this->SetFont('Arial', '', 9);
        foreach ($datos as $i => $dato) {
            $this->Cell($anchos[$i], 7, $dato, 1, 0, 'C', $fill);
        }
        $this->Ln();
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();

// Período y fecha
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 6, 'Periodo: ' . $periodoTexto . '   |   Generado: ' . date('d/m/Y H:i'), 0, 1, 'R');
$pdf->Ln(3);

// Tabla por estado
$pdf->TituloSeccion('Tramites por estado');
$pdf->TablaEncabezado(['Estado', 'Total'], [130, 55]);
$fill = false;
foreach ($porEstado as $fila) {
    $pdf->TablaFila([$fila['estado'], $fila['total']], [130, 55], $fill);
    $fill = !$fill;
}

$pdf->Ln(8);

// Tabla por tipo
$pdf->TituloSeccion('Tramites por tipo');
$pdf->TablaEncabezado(['Tipo de tramite', 'Total'], [130, 55]);
$fill = false;
foreach ($porTipo as $fila) {
    $pdf->TablaFila([$fila['tipo_tramite'], $fila['total']], [130, 55], $fill);
    $fill = !$fill;
}

$pdf->Output('reporte_tramites_' . date('d-m-Y') . '.pdf', 'D');
?>