<?php
require('../fpdf186/rotation.php');
include "../DBConnection.php";

date_default_timezone_set("Asia/Jakarta");

if (isset($_GET['PurchaseOrderID'])) {
    $purchaseOrderID = $_GET['PurchaseOrderID'];

    $queryHeader = "SELECT * FROM purchaseorderheader WHERE PurchaseOrderID = '$purchaseOrderID'";
    $resultHeader = mysqli_query($conn, $queryHeader);
    $header = mysqli_fetch_array($resultHeader);

    $supplierNum = $header['SupplierNum'];
    $querySupplier = "SELECT * FROM supplier WHERE SupplierNum = '$supplierNum'";
    $resultSupplier = mysqli_query($conn, $querySupplier);
    $supplier = mysqli_fetch_array($resultSupplier);

    $queryDetail = "SELECT pod.ItemCD, pod.Quantity, pod.UnitCD, pod.Price, pod.Subtotal, m.MaterialName, m.Desc_1 
                    FROM purchaseorderdetail pod 
                    JOIN material m ON pod.ItemCD = m.MaterialCD 
                    WHERE pod.PurchaseOrderID = '$purchaseOrderID'";
    $resultDetail = mysqli_query($conn, $queryDetail);
    $currentDate = date('d-m-Y');

    ob_start();

    $pdf = new PDF_Rotate();
    $pdf->AddPage();

    $printCount = $header['PrintCount'];
    $watermarkText = 'ORIGINAL';

    if ($printCount >= 2) {
        $watermarkText = 'COPY';
    }

    $pdf->SetFont('Arial', 'B', 70);
    $pdf->SetTextColor(230, 230, 230);
    $pdf->Rotate(45, 105, 200);
    $pdf->SetXY(0, 1);
    $pdf->Text(135, 140, $watermarkText);
    $pdf->Rotate(0);

    // Header PO
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
    $pdf->Ln(2);
    $pdf->SetX(10);
    $pdf->Cell(100, 5, 'Supplier      : ' . $supplier['SupplierName'], 0, 1);
    $pdf->SetX(10);
    $pdf->MultiCell(100, 5, 'Alamat        : ' . $supplier['SupplierAdd'], 0, 1);

    $pdf->SetXY(120, 15);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Purchase Order', 0, 1, 'L');
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, 'No. PO ', 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, 'Tanggal', 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, 'Telepon', 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, 'Keterangan', 0, 1, 'L');

    $pdf->SetXY(140, 25);
    $pdf->Cell(0, 6, ': ' . $header['PurchaseOrderID'], 0, 1);
    $pdf->SetX(140);
    $pdf->Cell(0, 6, ': ' . $header['CreatedOn'], 0, 1);
    $pdf->SetX(140);
    $pdf->Cell(0, 6, ': ' . $supplier['Telepon'], 0, 1);
    $pdf->SetX(140);
    $pdf->MultiCell(60, 6, ': ' . $header['Description'], 0, 1);
    $pdf->Ln(25);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 10, 'No', 'T,B', 0, 'C');
    $pdf->Cell(35, 10, 'Nama Bahan Baku', 'T,B', 0, 'C');
    $pdf->Cell(45, 10, 'Jumlah', 'T,B', 0, 'R');
    $pdf->Cell(32, 10, 'Satuan', 'T,B', 0, 'C');
    $pdf->Cell(30, 10, 'Harga (exclude)', 'T,B', 0, 'R');
    $pdf->Cell(32, 10, 'Subtotal', 'T,B', 0, 'R');
    $pdf->Cell(8, 10, '', 'T,B', 0, 'R');
    $pdf->Ln();

    $no = 1;
    $subtotalc = 0;

    while ($row = mysqli_fetch_array($resultDetail)) {
        $subtotalc += $row['Subtotal'];
        $pdf->SetFont('Arial', '', 10);
    
        $pdf->Cell(11, 6, $no++, 0, 0, 'C');
    
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();
        $pdf->MultiCell(60, 6, $row['MaterialName'] . "\nKeterangan: " . $row['Desc_1'], 0, 'L');
        $pdf->SetXY($startX + 50, $startY);
    
        $pdf->Cell(27, 6, number_format($row['Quantity'], 0, ',', ''), 0, 0, 'R');
        $pdf->Cell(32, 6, $row['UnitCD'], 0, 0, 'C');
        $pdf->Cell(25, 6, number_format($row['Price'], 0, ',', '.'), 0, 0, 'R');
        $pdf->Cell(43, 6, number_format($row['Subtotal'], 0, ',', '.'), 0, 1,'R');
    }

    $X = $pdf->GetX();
    $Y = $pdf->GetY();
    $pdf->SetXY($X, $Y+20);
    $pdf->Cell(0, 0, '', 'B');
    $pdf->Ln();

    $tax = $subtotalc * 0.11;

    $total = $subtotalc + $tax;

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(130, 8, 'Subtotal :', 0, 0, 'R');
    $pdf->Cell(58, 8, number_format($subtotalc, 0,',','.'), 0, 1, 'R');
    $pdf->Cell(130, 8, 'PPN :', 0, 0, 'R');
    $pdf->Cell(58, 8, number_format($tax, 0,',','.'), 0, 1, 'R');
    $pdf->Cell(130, 8, 'Total :', 0, 0, 'R');
    $pdf->Cell(58, 8, number_format($total, 0,',','.'), 0, 1, 'R');


    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(190, 8, 'Terbilang: ' . terbilang($total) . ' Rupiah', 0, 1, 'R');

    ob_end_clean();

    $pdf->Output('I', 'Purchase_Order_' . $header['PurchaseOrderID'] . '.pdf');

    $updatePrintCount = "UPDATE purchaseorderheader SET PrintCount = PrintCount + 1 WHERE PurchaseOrderID = '$purchaseOrderID'";
    mysqli_query($conn, $updatePrintCount);
}

function terbilang($x)
{
    $abil = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
    if ($x < 12)
        return " " . $abil[$x];
    elseif ($x < 20)
        return Terbilang($x - 10) . " Belas";
    elseif ($x < 100)
        return Terbilang($x / 10) . " Puluh" . Terbilang($x % 10);
    elseif ($x < 200)
        return " Seratus" . Terbilang($x - 100);
    elseif ($x < 1000)
        return Terbilang($x / 100) . " Ratus" . Terbilang($x % 100);
    elseif ($x < 2000)
        return " Seribu" . Terbilang($x - 1000);
    elseif ($x < 1000000)
        return Terbilang($x / 1000) . " Ribu" . Terbilang($x % 1000);
    elseif ($x < 1000000000)
        return Terbilang($x / 1000000) . " Juta" . Terbilang($x % 1000000);
}
?>