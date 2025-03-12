<?php
require('../fpdf186/rotation.php');
include "../DBConnection.php";

date_default_timezone_set("Asia/Jakarta");

if (isset($_GET['ReceptionID'])) {
    $receptionID = $_GET['ReceptionID'];

    // Query dan cek data header
    $queryHeader = "SELECT * FROM importreceptionheader WHERE ReceptionID = '$receptionID'";
    $resultHeader = mysqli_query($conn, $queryHeader);

    if (!$resultHeader || mysqli_num_rows($resultHeader) == 0) {
        die("Data Reception tidak ditemukan");
    }
    $header = mysqli_fetch_array($resultHeader);

    // Query untuk mengambil data detail dengan nama produk
    $queryDetail = "
    SELECT 
        rd.CreatedOn,
        rd.ItemCD, 
        p.MaterialName, 
        rd.Quantity_1, 
        rd.UnitCD_1, 
        rd.Quantity_2, 
        rd.UnitCD_2,
        rd.documentimageI_1,
        rd.documentimageI_2
    FROM importreceptiondetail rd
    LEFT JOIN material p ON TRIM(rd.ItemCD) = TRIM(p.MaterialCD) 
    WHERE rd.ReceptionID = '$receptionID'";  // Filter berdasarkan ReceptionID

    // Eksekusi query
    $resultDetail = mysqli_query($conn, $queryDetail);

    $currentDate = date('d-m-Y');

    ob_start();

    // Membuat PDF
    $pdf = new PDF_Rotate();
    $pdf->AddPage();

    // Mengatur watermark
    $printCount = $header['PrintCount'];
    $status = $header['Status'];
    $watermarkText = '';

    if ($status == 1) {
        $watermarkText = 'COMPLETE';
    } elseif ($printCount == 1) {
        $watermarkText = 'ORIGINAL';
    } elseif ($printCount > 1) {
        $watermarkText = 'COPY';
    }

    // Menambahkan watermark
    $pdf->SetFont('Arial', 'B', 70);
    $pdf->SetTextColor(230, 230, 230);
    $pdf->Rotate(45, 105, 200);
    $pdf->SetXY(0, 1);
    $pdf->Text(105, 170, $watermarkText);
    $pdf->Rotate(0);

    $pdf->SetFont('Arial', 'B', 70);
    $pdf->SetTextColor(230, 230, 230);
    $pdf->Rotate(45, 105, 200);
    $pdf->SetXY(0, 1);
    $pdf->Text(105, 170, $watermarkText);
    $pdf->Rotate(0);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(15, 15);
    $pdf->Cell(0, 10, 'PT. INDOPACK MULTI PERKASA                    Nota Penerimaan Import      ', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(10, 25);
    $pdf->Cell(100, 7, 'Pergudangan SAFE N LOCK, Blok K 1707 - 1708', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 7, 'Jl Lingkar timur KM 5,5', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 7, 'Telp : +623158259871 , Fax, +623158259872', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 7, 'Wechat / Skype / Line : papercupindonesia', 0, 1);
    $pdf->Ln(1);

    $pdf->SetX(30); // Posisi X setelah label "Alamat"

    // Menggunakan MultiCell untuk alamat
    $pdf->MultiCell(100, 7, $customer['ShipmentAddress'], 0, 'L'); // Menyesuaikan lebar MultiCell

    $pdf->SetXY(90, 25);
    $pdf->SetX(130);
    $pdf->Cell(0, 7, 'No Penerimaan  : ' . $header['ReceptionID'], 0, 1);
    $pdf->SetX(130);
    $pdf->Cell(0, 7, 'No PO                : ' . $header['PurchaseOrderID'], 0, 1);
    $pdf->SetX(130);
    $pdf->Cell(0, 7, 'Tanggal              : ' . $header['CreatedOn'], 0, 1);
    $pdf->SetX(130);
    $pdf->Cell(29, 7, 'Keterangan         : ', 0, 0);  // Label tetap di satu baris
    $pdf->MultiCell(49, 7, $header['Description'], 0, 'L');  // Isi deskripsi bisa terenter
    $pdf->Ln(10);

    $pdf->Ln(1);

    // Header untuk detail reception
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(25, 10, 'No', 'T,B', 0, 'C');
    $pdf->Cell(50, 10, 'Nama Barang', 'T,B', 0, 'L');
    $pdf->Cell(35, 10, 'Jumlah', 'T,B', 0, 'R');
    $pdf->Cell(30, 10, 'Satuan', 'T,B', 0, 'C');
    $pdf->Cell(20, 10, 'Jumlah', 'T,B', 0, 'R');
    $pdf->Cell(30, 10, 'Satuan', 'T,B', 1, 'R'); // 1 untuk pindah baris

    $no = 1;
    $subtotalc = 0;
    $lineHeight = 8;
    $lineHeightE = 5;
    $maxNameLength = 50;

    while ($row = mysqli_fetch_array($resultDetail)) {
        $subtotal = $row['Quantity'] * $row['Price'];
        $subtotalc += $subtotal;

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(25, $lineHeight, $no++, 0, 0, 'C');

        $nameLength = strlen($row['MaterialName']);
        if ($nameLength > $maxNameLength) {
            $lineHeightUsed = $lineHeightE;
        } else {
            $lineHeightUsed = $lineHeight;
        }

        $pdf->SetX(35);
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();

        $pdf->MultiCell(62, $lineHeightUsed, $row['MaterialName'], 0, 'L');

        $cellHeight = max($pdf->GetY() - $startY, $lineHeightUsed);

        $pdf->SetXY($startX + 55, $startY);
        $pdf->Cell(25, 10, number_format($row['Quantity_1'], 0), 0, 0, 'R');
        $pdf->Cell(38, 10, $row['UnitCD_1'], 0, 0, 'C');
        $pdf->Cell(10, 10, number_format($row['Quantity_2'], 0), 0, 0, 'R');
        $pdf->Cell(55, 10, $row['UnitCD_2'], 0, 1, 'C');

        $pdf->SetY($startY + $cellHeight);
        $pdf->Cell(190, 1, '', 'B', 0, 'R');
    }



    $pdf->Ln();
    ob_end_clean();

    $pdf->Output('I', 'Reception_Report_' . $header['ReceptionID'] . '.pdf');
    $updatePrintCount = "UPDATE receptionheader SET PrintCount = PrintCount + 1 WHERE ReceptionID = '$receptionID'";
    mysqli_query($conn, $updatePrintCount);
}
?>