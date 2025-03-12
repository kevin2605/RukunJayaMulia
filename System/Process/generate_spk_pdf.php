<?php
require('../fpdf186/rotation.php');
include "../DBConnection.php";

date_default_timezone_set("Asia/Jakarta");

ob_start();

if (isset($_GET['ProductionOrderID'])) {
    $ProductionOrderID = $_GET['ProductionOrderID'];

    $queryHeader = "SELECT po.ProductionOrderID, po.CreatedOn, po.Description, mc.MachineName, m.MaterialName, po.MaterialOut, po.UnitCD, p.ProductName, po.Status
                    FROM productionorder po, material m, machine mc, product p
                     WHERE  po.MachineCD = mc.MachineCD
                            AND po.MaterialCD = m.MaterialCD
                            AND po.ProductCD = p.ProductCD
                            AND po.ProductionOrderID = '$ProductionOrderID'";
    $resultHeader = mysqli_query($conn, $queryHeader);

    if (!$resultHeader || mysqli_num_rows($resultHeader) == 0) {
        die("Error: Data tidak ditemukan.");
    }
    $header = mysqli_fetch_array($resultHeader);

    $ProductionOrderID = $header['ProductionOrderID'] ?? '';
    $CreatedOn = $header['CreatedOn'] ?? '';
    $Description = $header['Description'] ?? '';
    $MachineName = $header['MachineName'] ?? '';
    $MaterialName = $header['MaterialName'] ?? '';
    $UnitCD = $header['UnitCD'] ?? '';
    $MaterialOut = $header['MaterialOut'] ?? '';
    $ProductName = $header['ProductName'] ?? '';
    $Status = $header['Status'] ?? '';

    $pdf = new PDF_Rotate();
    $pdf->AddPage('L', 'A5');

    $currentDate = date('d-m-Y');

    $pdf->Rotate(0);

    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(10, 10);
    $pdf->Cell(0, 10, 'PT. INDOPACK MULTI PERKASA', 0, 1, 'L');
    
    /*
    $pdf->Ln(10);
    $pdf->SetXY(90, 25);
    $pdf->Cell(0, 0, '', 0, 1);
    $pdf->SetXY(120, 15);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'SPK Produksi', 0, 1, 'L');
    */
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetX(10);
    $pdf->Cell(0, 6, 'No. SPK ', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(0, 6, 'Tanggal', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(0, 6, 'Keterangan', 0, 1, 'L');

    $pdf->SetXY(30, 20);
    $pdf->Cell(0, 6, ': ' . $header['ProductionOrderID'], 0, 1);
    $pdf->SetX(30);
    $pdf->Cell(0, 6, ': ' . $header['CreatedOn'], 0, 1);
    $pdf->SetX(30);
    $pdf->MultiCell(60, 6, ': ' . $header['Description'], 0, 1);

    $pdf->SetXY(120, 20);
    $pdf->Cell(0, 0, '', 0, 1);
    $pdf->SetXY(120, 10);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'SPK Produksi', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(120, 20);
    $pdf->Cell(0, 6, 'Mesin', 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, 'Produk', 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, 'Status', 0, 1);

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(140, 20);
    $pdf->Cell(0, 6, ': '. $MachineName, 0, 1);
    $pdf->SetX(140);
    $pdf->Cell(0, 6, ': ' . $ProductName, 0, 1);
    $pdf->SetX(140);
    $pdf->Cell(0, 6, $Status == 1 ? ': COMPLETE' : ': PROSES', 0, 1);
    $pdf->Ln(7);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 10, 'No', 'T,B', 0, 'C');
    $pdf->Cell(65, 10, 'Nama Produk', 'T,B', 0, 'L');
    $pdf->Cell(55, 10, 'Qty', 'T,B', 0, 'R');
    $pdf->Cell(55, 10, 'Satuan', 'T,B', 0, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(10,57);
    $pdf->Cell(10, 6, '1', 0, 1, 'C');
    $pdf->SetXY(20,57);
    $pdf->Cell(65, 6, $MaterialName, 0, 'L');
    $pdf->SetXY(85,57);
    $pdf->Cell(55, 6, $MaterialOut, 0, 0, 'R');
    $pdf->SetXY(161,57);
    $pdf->Cell(55, 6, $UnitCD, 0, 0, 'L');

    $pdf->SetXY(10,56);
    $pdf->Cell(185, 10, '', 'B', 0, 'R');
    $pdf->Output('I', ($header['ProductionOrderID'] ?? 'Unknown') . '.pdf');
    

    $updatePrintCount = "UPDATE receptioninvoiceheader SET PrintCount = PrintCount + 1 WHERE RCV_InvoiceID = '$RCV_InvoiceID'";
    mysqli_query($conn, $updatePrintCount);

} else {
    echo "Error: ID tidak ditemukan.";
}

// Mengakhiri output buffer
ob_end_flush();
?>