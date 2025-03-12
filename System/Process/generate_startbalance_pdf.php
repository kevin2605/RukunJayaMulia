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
        die("Error: Data invoice tidak ditemukan.");
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
        p.ProductName AS NamaProduk, 
        md.FlowIn AS Masuk, 
        md.FlowOut AS Keluar, 
        md.UnitCD AS Satuan, 
        md.Description AS Keterangan 
    FROM 
        mutationdetail md
    JOIN 
        product p ON md.ProductCD = p.ProductCD
    WHERE 
        md.MutationID = '$MutationID'";

    $resultDetail = mysqli_query($conn, $queryDetail);

    $pdf = new PDF_Rotate();
    $pdf->AddPage('L', 'A5');

    $currentDate = date('d-m-Y');

    $pdf->Rotate(0);

    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(15, 15);
    $pdf->Cell(0, 10, 'PT. INDOPACK MULTI PERKASA                    Mutasi Saldo Awal Produk  ', 0, 1, 'C');
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
    $pdf->SetX(130);
    $pdf->Cell(0, 7, 'No. Mutasi              : ' . $header['MutationID'], 0, 1);
    $pdf->SetX(130);
    $pdf->Cell(0, 7, 'Tipe Mutasi             : Saldo Awal', 0, 1);
    $pdf->SetX(130);
    $pdf->Cell(0, 7, 'Tgl. Mutasi              : ' . $header['CreatedOn'], 0, 1);
    $pdf->SetX(130);
    $pdf->MultiCell(50, 7, 'Keterangan             :' . $header['Description'], );
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 10, 'No', 'T,B', 0, 'C');
    $pdf->Cell(50, 10, 'Nama Produk', 'T,B', 0, 'L');
    $pdf->Cell(55, 10, 'Masuk', 'T,B', 0, 'R');
    $pdf->Cell(55, 10, 'Keluar', 'T,B', 0, 'C');
    $pdf->Cell(15, 10, 'Satuan', 'T,B', 1, 'R');

    $pdf->SetFont('Arial', '', 10);
    $no = 1;
    $lineHeight = 5;
    $extraSpacing = 1;

    while ($row = mysqli_fetch_array($resultDetail)) {
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();
        $pdf->Cell(10, $lineHeight, $no++, 0, 0, 'C');
        $pdf->MultiCell(89, $lineHeight, $row['NamaProduk'], 0, 'L');
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
    echo "Error: ID Invoice tidak ditemukan.";
}

ob_end_flush();
?>