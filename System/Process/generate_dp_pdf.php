<?php
require('../fpdf186/rotation.php');
include "../DBConnection.php";

date_default_timezone_set("Asia/Jakarta");

ob_start();

if (isset($_GET['DPID'])) {
    $DPID = $_GET['DPID'];

    $queryHeader = "SELECT * FROM downpaymentheader WHERE DPID = '$DPID'";
    $resultHeader = mysqli_query($conn, $queryHeader);

    if (!$resultHeader || mysqli_num_rows($resultHeader) == 0) {
        die("Error: Data tidak ditemukan.");
    }
    $header = mysqli_fetch_array($resultHeader);

    $CreatedOn = $header['CreatedOn'] ?? '';
    $CreatedBy = $header['CreatedBy'] ?? '';
    $CategoryCD = $header['SalesOrderID'] ?? '';
    $Description = $header['Description'] ?? '';

    $queryDetail = "
    SELECT 
        *
    FROM 
        downpaymentdetail dpd
    WHERE 
        dpd.DPID = '$DPID'";

    $resultDetail = mysqli_query($conn, $queryDetail);

    // Inisialisasi FPDF
    $pdf = new PDF_Rotate();
    $pdf->AddPage('L', 'A5');

    $currentDate = date('d-m-Y');

    $pdf->Rotate(0);

    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(10, 15);
    $pdf->Cell(0, 10, 'PT. INDOPACK MULTI PERKASA', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(10, 25);
    $pdf->Cell(100, 7, 'Pergudangan SAFE N LOCK, Blok K 1707 - 1708', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 7, 'Jl Lingkar timur KM 5,5', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 7, 'Telp : +623158259871 , Fax, +623158259872', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 7, 'Wechat / Skype / Line : papercupindonesia', 0, 1);
    $pdf->SetX(10);
    $pdf->MultiCell(100, 7, '', '');

    $pdf->SetXY(90, 25);
    $pdf->SetXY(120, 15);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Nota Uang Muka', 0, 1, 'L');
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, 'No. DP ', 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, 'Tanggal', 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, 'No. SO', 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, 'Keterangan', 0, 1, 'L');

    $pdf->SetXY(140, 25);
    $pdf->Cell(0, 6, ': ' . $header['DPID'], 0, 1);
    $pdf->SetX(140);
    $pdf->Cell(0, 6, ': ' . $header['CreatedOn'], 0, 1);
    $pdf->SetX(140);
    $pdf->Cell(0, 6, ': ' . $header['SalesOrderID'], 0, 1);
    $pdf->SetX(140);
    $pdf->MultiCell(60, 6, $header['Description'] != NULL && $header['Description'] != "" ? ': ' . $header['Description'] : ': -', 0, 1);
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 10, 'No', 'T,B', 0, 'C');
    $pdf->Cell(120, 10, 'Keterangan', 'T,B', 0, 'L');
    $pdf->Cell(55, 10, 'Nominal', 'T,B', 0, 'L');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(13, 72);
    $row = mysqli_fetch_assoc($resultDetail);
    $pdf->Cell(7, 6, '1', 0, 0, 'L');
    $pdf->Cell(120, 6, 'Pembayaran Uang Muka', 0, 0, 'L');
    $pdf->Cell(7, 6, number_format($row['Amount'], 0,',','.'), 0, 0, 'L');
    

    $pdf->SetXY(10, 70);
    $pdf->Cell(185, 10, '', 'B', 0, 'R');
    $pdf->Output('I', 'DP_' . ($header['DPID'] ?? 'Unknown') . '.pdf');

} else {
    echo "Error: ID tidak ditemukan.";
}

// Mengakhiri output buffer
ob_end_flush();
?>