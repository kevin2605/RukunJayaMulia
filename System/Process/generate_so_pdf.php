<?php
require('../fpdf186/rotation.php');
include "../DBConnection.php";

date_default_timezone_set("Asia/Jakarta");

if (isset($_GET['SalesOrderID'])) {
    $salesOrderID = $_GET['SalesOrderID'];

    $queryHeader = "SELECT * FROM salesorderheader WHERE SalesOrderID = '$salesOrderID'";
    $resultHeader = mysqli_query($conn, $queryHeader);

    if (!$resultHeader || mysqli_num_rows($resultHeader) == 0) {
        die("Data Sales Order tidak ditemukan");
    }
    $header = mysqli_fetch_array($resultHeader);

    $custID = $header['CustID'];
    $queryCustomer = "SELECT * FROM customer WHERE CustID = '$custID'";
    $resultCustomer = mysqli_query($conn, $queryCustomer);

    if (!$resultCustomer || mysqli_num_rows($resultCustomer) == 0) {
        die("Data Customer tidak ditemukan");
    }
    $customer = mysqli_fetch_array($resultCustomer);

    $queryDetail = "SELECT 
        sod.ProductCD, 
        sod.Quantity, 
        sod.Price, 
        COALESCE(m.ProductName, 'Unknown') as MaterialName,
        COALESCE(m.UnitCD, '-') as UnitCD 
        FROM salesorderdetail sod 
        LEFT JOIN product m ON sod.ProductCD = m.ProductCD 
        WHERE sod.SalesOrderID = '$salesOrderID'
        ORDER BY sod.ProductCD";
    $resultDetail = mysqli_query($conn, $queryDetail);

    $currentDate = date('d-m-Y');

    ob_start();

    // Membuat PDF
    $pdf = new PDF_Rotate();
    $pdf->AddPage();

    $printCount = $header['PrintCount'];
    $finishStatus = $header['Finish'];
    $watermarkText = '';

    if ($finishStatus == 1) {
        $watermarkText = 'COMPLETE';
    } elseif ($finishStatus == 2) {
        $watermarkText = 'CLOSED';
    } elseif ($printCount == 1) {
        $watermarkText = 'ORIGINAL';
    } elseif ($printCount > 1) {
        $watermarkText = 'COPY';
    }


    $pdf->SetFont('Arial', 'B', 70);
    $pdf->SetTextColor(230, 230, 230);
    $pdf->Rotate(45, 105, 200);
    $pdf->SetXY(0, 1);
    $pdf->Text(105, 170, $watermarkText);
    $pdf->Rotate(0);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(10, 15);
    $pdf->Cell(0, 10, 'PT. INDOPACK MULTI PERKASA', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetX(10);
    $pdf->Cell(0, 5, 'No. SO', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(0, 5, 'Tanggal', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(0, 5, 'Keterangan', 0, 1, 'L');

    $pdf->SetY(25);
    $pdf->SetX(30);
    $pdf->Cell(0, 5, ': ' . $header['SalesOrderID'], 0, 1);
    $pdf->SetX(30);
    $pdf->Cell(0, 5, ': ' . $header['CreatedOn'], 0, 1);
    $pdf->SetX(30);
    $pdf->Cell(0, 5, ': ' . $header['Description'], 0, 1);
    
    $pdf->SetXY(115, 15);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'SALES ORDER', 0, 1, 'L');

    $pdf->SetX(115);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(100, 5, 'Customer', 0, 1);
    $pdf->SetX(115);
    $pdf->Cell(30, 5, 'Alamat', 0, 0);

    $pdf->SetXY(133, 25);
    $pdf->Cell(70, 5, ': '. $customer['CustName'], 0, 'L');
    $pdf->SetX(133);
    $pdf->MultiCell(70, 5, ': '. $customer['ShipmentAddress'], 0, 'L');
    if($header['Logo'] !=""){
        $pdf->SetX(115);
        $pdf->Cell(30, 5, 'Logo          : '. $header['Logo'], 0, 'L');
    }
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(25, 10, 'No', 'T,B', 0, 'C');
    $pdf->Cell(50, 10, 'Nama Produk', 'T,B', 0, 'L');
    $pdf->Cell(30, 10, 'Jumlah', 'T,B', 0, 'R');
    $pdf->Cell(50, 10, 'Satuan', 'T,B', 0, 'C');
    $pdf->Cell(30, 10, 'Harga    ', 'T,B', 1, 'R'); // 1 untuk pindah baris

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

        $pdf->MultiCell(55, $lineHeightUsed, $row['MaterialName'], 0, 'L');

        $cellHeight = max($pdf->GetY() - $startY, $lineHeight);

        $pdf->SetXY($startX + 55, $startY);

        $pdf->Cell(20, $lineHeightUsed, number_format($row['Quantity'], 0), 0, 0, 'R');
        $pdf->Cell(60, $lineHeightUsed, $row['UnitCD'], 0, 0, 'C');
        $pdf->Cell(20, $lineHeightUsed, 'Rp ' . number_format($row['Price'], 0, ',', '.'), 0, 1, 'R');

        $pdf->SetY($startY + $cellHeight);
    }
    $pdf->Cell(185, 5, '    ', 'B', 1, 'R'); // 1 untuk pindah baris

    $pdf->Ln();
    ob_end_clean();

    $pdf->Output('I', 'Sales_Order_' . $header['SalesOrderID'] . '.pdf');
    $updatePrintCount = "UPDATE salesorderheader SET PrintCount = PrintCount + 1 WHERE SalesOrderID = '$salesOrderID'";
    mysqli_query($conn, $updatePrintCount);

}
?>