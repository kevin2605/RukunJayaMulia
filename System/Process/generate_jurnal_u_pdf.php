<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require('../fpdf186/rotation.php');
include "../DBConnection.php";

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

date_default_timezone_set("Asia/Jakarta");

$startdate = isset($_GET['startdate']) ? $_GET['startdate'] : '';
$enddate = isset($_GET['enddate']) ? $_GET['enddate'] : '';


$queryHeader = "SELECT 
        h.GenJourID, h.JournalDate, h.MemoID, h.MemoDesc, h.Description as HeaderDesc
    FROM genjournalheader h";

if (!empty($startdate) && !empty($enddate)) {
    $queryHeader .= " WHERE h.JournalDate BETWEEN '$startdate' AND '$enddate'";
}
$queryHeader .= " ORDER BY h.JournalDate ASC";

$resultHeader = mysqli_query($conn, $queryHeader);

if (!$resultHeader) {
    die("Error: " . mysqli_error($conn));
}

class PDF extends PDF_Rotate{
    function Header(){
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'PT INDOPACK MULTI PERKASA', 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 3, 'Sidoarjo', 0, 1, 'C');
        $this->Ln(2);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'General Journal', 0, 1, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage('L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, ' ' . $startdate . ' To ' . $enddate, 0, 1, 'C');
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 10, 'ID#', 0);
$pdf->Cell(25, 10, 'Journal Date', 0);
$pdf->Cell(30, 10, 'Account CD', 0);
$pdf->Cell(50, 10, 'Account Name', 0);
$pdf->Cell(25, 10, 'Debit', 0);
$pdf->Cell(25, 10, 'Credit', 0);
$pdf->Ln();
$pdf->SetFont('Arial', '', 10);

$previousGenJourID = '';
while ($rowHeader = mysqli_fetch_array($resultHeader)) {
    $headerY = $pdf->GetY();
    $pdf->Cell(0, 0, '', 'T');
    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(25, 8, $rowHeader['GenJourID'], 0);
    $pdf->Cell(25, 8, $rowHeader['JournalDate'], 0);
    $pdf->Cell(100, 8, $rowHeader['MemoID'], 0);
    $pdf->SetX(200);
    $pdf->Cell(25,5,"Keterangan :",0);
    $pdf->MultiCell(60,5,$rowHeader["HeaderDesc"],0);

    $pdf->SetY($headerY + 8);
    $queryDetail = "SELECT * from genjournaldetail WHERE GenJourID='".$rowHeader["GenJourID"]."'";
    $resultDetail = mysqli_query($conn, $queryDetail);
    while ($rowDetail = mysqli_fetch_array($resultDetail)) {
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50);
        $pdf->Cell(30, 7, $rowDetail['AccountCD'], 0);
        $pdf->Cell(50, 7, $rowDetail['AccountName'], 0);
        $pdf->Cell(25, 7, number_format($rowDetail['Debit'],0,',','.'), 0);
        $pdf->Cell(25, 7, number_format($rowDetail['Credit'],0,',','.'), 0);

        $pdf->Ln();
    }
}

$pdf->Output('I', 'Laporan_Jurnal_Umum.pdf');

mysqli_close($conn);

?>