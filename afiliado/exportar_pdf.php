<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");
require_once('../lib/fpdf.php');

$id_departamento = $_SESSION['id_departamento'];

$query = "SELECT id_tramite, tipo_tramite, estado, fecha_creacion 
FROM tramites 
WHERE id_departamento = '$id_departamento'";

$resultado = mysqli_query($conn, $query);

$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont('Arial','B',16);
$pdf->Cell(190,10,'Reporte de Tramites',0,1,'C');

$pdf->Ln(10);

$pdf->SetFont('Arial','B',12);

$pdf->Cell(30,10,'ID',1);
$pdf->Cell(60,10,'Tipo',1);
$pdf->Cell(40,10,'Estado',1);
$pdf->Cell(60,10,'Fecha',1);
$pdf->Ln();

$pdf->SetFont('Arial','',10);

while($fila = mysqli_fetch_assoc($resultado)){
    
    $pdf->Cell(30,10,$fila['id_tramite'],1);
    $pdf->Cell(60,10,$fila['tipo_tramite'],1);
    $pdf->Cell(40,10,$fila['estado'],1);
    $pdf->Cell(60,10,$fila['fecha_creacion'],1);
    $pdf->Ln();
}

$pdf->Output();
?>