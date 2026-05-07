<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");
require_once('../lib/fpdf.php');

// ===============================
// OBTENER TRÁMITES DEL DEPARTAMENTO
// Se filtran por el departamento
// del afiliado en sesión
// ===============================
$id_departamento = $_SESSION['id_departamento'];

$sql  = "SELECT id_tramite, tipo_tramite, estado, fecha_creacion
         FROM tramites
         WHERE id_departamento = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_departamento);
$stmt->execute();
$resultado = $stmt->get_result();

// ===============================
// GENERAR PDF CON FPDF
// ===============================
$pdf = new FPDF();
$pdf->AddPage();

// Título del reporte
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(190, 10, 'Reporte de Tramites', 0, 1, 'C');
$pdf->Ln(10);

// Encabezados de la tabla
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30,  10, 'ID',     1);
$pdf->Cell(60,  10, 'Tipo',   1);
$pdf->Cell(40,  10, 'Estado', 1);
$pdf->Cell(60,  10, 'Fecha',  1);
$pdf->Ln();

// Filas de datos
$pdf->SetFont('Arial', '', 10);

while ($fila = $resultado->fetch_assoc()) {
    $pdf->Cell(30, 10, $fila['id_tramite'],    1);
    $pdf->Cell(60, 10, $fila['tipo_tramite'],  1);
    $pdf->Cell(40, 10, $fila['estado'],        1);
    $pdf->Cell(60, 10, $fila['fecha_creacion'],1);
    $pdf->Ln();
}

// Enviar PDF al navegador
$pdf->Output();
?>