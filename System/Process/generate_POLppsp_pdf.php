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

    //query detail, check pp or sp
    if($header["CategoryCD"] == "BPP"){
        $queryDetail = "SELECT pod.ItemCD, sg.GoodsName AS ItemName, sg.Tax, pod.Quantity, pod.UnitCD, pod.Price, pod.Subtotal, sg.Desc_1
                FROM purchaseorderdetail pod 
                JOIN supportinggoods sg ON pod.ItemCD = sg.GoodsCD 
                WHERE pod.PurchaseOrderID = '$purchaseOrderID'";
        $resultDetail = mysqli_query($conn, $queryDetail);
    }else if($header["CategoryCD"] == "SPR"){
        $queryDetail = "SELECT pod.ItemCD, sp.PartName AS ItemName, sp.Tax, pod.Quantity, pod.UnitCD, pod.Price, pod.Subtotal, sp.Desc_1
                FROM purchaseorderdetail pod 
                JOIN sparepart sp ON pod.ItemCD = sp.PartCD 
                WHERE pod.PurchaseOrderID = '$purchaseOrderID'";
        $resultDetail = mysqli_query($conn, $queryDetail);
    }


    $currentDate = date('d-m-Y');

    // Memulai buffer output
    ob_start();

    // Membuat PDF
    $pdf = new PDF_Rotate();
    $pdf->AddPage();

    $printCount = $header['PrintCount'];
    $watermarkText = 'ORIGINAL';

    if ($printCount >= 2) {
        $watermarkText = 'COPY';
    }

    $pdf->SetFont('Arial', 'B', 70);
    $pdf->SetTextColor(230, 230, 230); // Warna merah muda (pink)
    $pdf->Rotate(45, 105, 200); // Rotasi teks
    $pdf->SetXY(0, 1); // Mengatur posisi watermark
    $pdf->Text(135, 140, $watermarkText);
    $pdf->Rotate(0); // Reset rotasi teks

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
    $pdf->Ln(4);
    $pdf->SetX(10);
    $pdf->Cell(100, 5, 'Supplier      : ' . $supplier['SupplierName'], 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 5, 'Alamat        : ' . $supplier['SupplierAdd'], 0, 1);
    $pdf->SetXY(90, 25);
    $pdf->Cell(0, 0, '', 0, 1);

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
    $pdf->MultiCell(65, 6, ': ' . $header['Description'], 0, 1);
    $pdf->Ln(10);
    $pdf->Ln(10);
    $pdf->Ln(10);

    // Header tabel detail PO
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 10, 'No', 'T,B', 0, 'C');
    $pdf->Cell(35, 10, 'Nama Bahan Baku', 'T,B', 0, 'C');
    $pdf->Cell(45, 10, 'Jumlah', 'T,B', 0, 'R');
    $pdf->Cell(35, 10, 'Satuan', 'T,B', 0, 'C');
    $pdf->Cell(25, 10, 'Harga (exclude)', 'T,B', 0, 'R');
    $pdf->Cell(35, 10, 'Subtotal', 'T,B', 0, 'R');
    $pdf->Cell(8, 10, '', 'T,B', 0, 'R');
    $pdf->Ln();

    // Isi tabel detail PO
    $no = 1;
    $subtotalc = 0;
    $isTax = true;
    while ($row = mysqli_fetch_array($resultDetail)) {
        $subtotalc += $row['Subtotal'];

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(11, 8, $no++, 0, 0, 'C');

        $startX = $pdf->GetX();
        $startY = $pdf->GetY();
        $pdf->MultiCell(60, 8, $row['ItemName'] . "\nKeterangan: " . $row['Desc_1'], 0, 'L');
        $pdf->SetXY($startX + 50, $startY);

        $pdf->Cell(27, 8, number_format($row['Quantity'], 0,',','.'), 0, 0, 'R');
        $pdf->Cell(32, 8, $row['UnitCD'], 0, 0, 'C');
        $pdf->Cell(25, 8, number_format($row['Price'], 0,',','.'), 0, 0, 'R');
        $pdf->Cell(43, 8, number_format($row['Subtotal'], 0,',','.'), 0, 1, 'R');
        if($row["Tax"] == 0){
            $isTax = false;
        }
    }

    $X = $pdf->GetX();
    $Y = $pdf->GetY();
    $pdf->SetXY($X, $Y+15);
    $pdf->Cell(0, 0, '', 'B');
    $pdf->Ln();

    $total = $subtotalc;

    // Total
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(130, 8, 'Subtotal :', 0, 0, 'R');
    $pdf->Cell(58, 8, number_format($subtotalc, 0,',','.'), 0, 1, 'R');
    if($isTax){
        $tax = $subtotalc * 0.11;
        $pdf->Cell(130, 8, 'PPN :', 0, 0, 'R');
        $pdf->Cell(58, 8, number_format($tax, 0,',','.'), 0, 1, 'R');
        $total = $subtotalc + $tax;
    }
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