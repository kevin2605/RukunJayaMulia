<?php
require('../fpdf186/rotation.php');
include "../DBConnection.php";

date_default_timezone_set("Asia/Jakarta");

ob_start();

if (isset($_GET['MutationID'])) {
    $MutationID = $_GET['MutationID'];

    $queryHeader = "SELECT * FROM mutationheader WHERE MutationID = '$MutationID'";
    $resultHeader = mysqli_query($conn, $queryHeader);

    if (!$resultHeader || mysqli_num_rows($resultHeader) == 0) {
        die("Error: Data tidak ditemukan.");
    }
    $header = mysqli_fetch_array($resultHeader);

    $MutationID = $header['MutationID'] ?? '';
    $CreatedOn = $header['CreatedOn'] ?? '';
    $CreatedBy = $header['CreatedBy'] ?? '';
    $CategoryCD = $header['CategoryCD'] ?? '';
    $Description = $header['Description'] ?? '';

    $queryDetail = "
    SELECT 
        md.MutationID, 
        p.MaterialName AS NamaMaterial, 
        md.FlowIn AS Masuk, 
        md.FlowOut AS Keluar, 
        md.UnitCD AS Satuan, 
        md.Description AS Keterangan 
    FROM 
        mutationdetailmat md
    JOIN 
        material p ON md.MaterialCD = p.MaterialCD
    WHERE 
        md.MutationID = '$MutationID'";

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
    $pdf->Cell(100, 6, 'Pergudangan SAFE N LOCK, Blok K 1707 - 1708', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 6, 'Jl Lingkar timur KM 5,5', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 6, 'Telp : +623158259871 , Fax, +623158259872', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 6, 'Wechat / Skype / Line : papercupindonesia', 0, 1);
    $pdf->Ln(10);
    $pdf->SetXY(90, 25);
    $pdf->Cell(0, 0, '', 0, 1);
    $pdf->SetXY(120, 15);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Mutasi Bahan PC8', 0, 1, 'L');
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, 'No. PO ', 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, 'Tipe Mutasi', 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, 'Telepon', 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, 'Keterangan', 0, 1, 'L');

    $pdf->SetXY(140, 25);
    $pdf->Cell(0, 6, ': ' . $header['MutationID'], 0, 1);
    $pdf->SetX(140);
    $pdf->Cell(0, 6, ': Penunjang Produksi', 0, 1);
    $pdf->SetX(140);
    $pdf->Cell(0, 6, ': ' . $header['CreatedOn'], 0, 1);
    $pdf->SetX(140);
    $pdf->MultiCell(60, 6, ': ' . $header['Description'], 0, 1);
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 8, 'No', 'T,B', 0, 'C');
    $pdf->Cell(50, 8, 'Nama Produk', 'T,B', 0, 'L');
    $pdf->Cell(55, 8, 'Masuk', 'T,B', 0, 'R');
    $pdf->Cell(55, 8, 'Keluar', 'T,B', 0, 'C');
    $pdf->Cell(15, 8, 'Satuan', 'T,B', 1, 'R');

    $pdf->SetFont('Arial', '', 10);
    $no = 1;
    $lineHeight = 6;
    $extraSpacing = 1;

    while ($row = mysqli_fetch_array($resultDetail)) {
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();
        $pdf->Cell(10, $lineHeight, $no++, 0, 0, 'C');
        $pdf->MultiCell(85, $lineHeight, $row['NamaMaterial'], 0, 'L');
        $namaProdukHeight = $pdf->GetY() - $startY;

        $pdf->SetXY($startX + 50, $startY);

        $descStartY = $pdf->GetY();

        $maxCellHeight = max($namaProdukHeight, $lineHeight);
        $masukValue = ($row['Masuk'] == 0) ? '-' : $row['Masuk'];
        $keluarValue = ($row['Keluar'] == 0) ? '-' : $row['Keluar'];
        $pdf->SetXY($startX + 50, $startY);
        $pdf->Cell(60, $lineHeight, $masukValue, 0, 0, 'R');
        $pdf->Cell(65, $lineHeight, $keluarValue, 0, 0, 'C');
        $pdf->Cell(7, $lineHeight, $row['Satuan'], 0, 0, 'R');

        $pdf->Ln($maxCellHeight + $extraSpacing);
    }

    $pdf->Cell(185, 10, '', 'B', 0, 'R');
    $pdf->Output('I', 'Invoice_' . ($header['InvoiceID'] ?? 'Unknown') . '.pdf');

    $updatePrintCount = "UPDATE receptioninvoiceheader SET PrintCount = PrintCount + 1 WHERE RCV_InvoiceID = '$RCV_InvoiceID'";
    mysqli_query($conn, $updatePrintCount);

} else {
    echo "Error: ID tidak ditemukan.";
}

// Mengakhiri output buffer
ob_end_flush();
?>