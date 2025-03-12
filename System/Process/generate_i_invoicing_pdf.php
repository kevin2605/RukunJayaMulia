<?php
// Menggunakan library FPDF dengan fitur rotasi
require('../fpdf186/rotation.php');
include "../DBConnection.php";

// Menetapkan zona waktu default
date_default_timezone_set("Asia/Jakarta");

// Memulai output buffer untuk mencegah output sebelum FPDF
ob_start();

// Memeriksa apakah parameter RCV_InvoiceID ada
if (isset($_GET['RCV_InvoiceID'])) {
    $RCV_InvoiceID = $_GET['RCV_InvoiceID'];

    // Query untuk mendapatkan data header invoice
    $queryHeader = "SELECT ri.RCV_InvoiceID, ri.CreatedOn, ri.CreatedBy, ri.DPP, ri.BM, ri.PPN, ri.PPH, ri.TotalAmount, r.ReceptionID,
                    r.CreatedOn AS rcvDate, r.CategoryCD, r.Description, p.PurchaseOrderID, ri.Status, s.SupplierNum, s.SupplierName,
                    s.SupplierAdd, s.ContactName, s.ContactPhone, s.Telepon
                    FROM importreceptioninvoiceheader ri, importreceptionheader r, importpurchaseorderheader p, supplier s 
                    WHERE ri.ReceptionID=r.ReceptionID
                        AND r.PurchaseOrderID=p.PurchaseOrderID
                        AND p.SupplierNum=s.SupplierNum
                        AND ri.RCV_InvoiceID='" . $RCV_InvoiceID . "'";
    $resultHeader = mysqli_query($conn, $queryHeader);


    // Validasi hasil query
    if (!$resultHeader || mysqli_num_rows($resultHeader) == 0) {
        die("Error: Data invoice tidak ditemukan.");
    }
    $header = mysqli_fetch_array($resultHeader);

    // Query untuk mendapatkan detail invoice
    $queryDetail = "SELECT 
            rid.ItemCD, 
            m.MaterialName, 
            rid.Quantity, 
            rid.UnitCD, 
            rid.Price, 
            
            rid.Subtotal 
        FROM importreceptioninvoicedetail rid
        JOIN material m ON rid.ItemCD = m.MaterialCD
        WHERE rid.RCV_InvoiceID = '$RCV_InvoiceID'";
    $resultDetail = mysqli_query($conn, $queryDetail);

    $pdf = new PDF_Rotate();
    $pdf->AddPage();

    $currentDate = date('d-m-Y');
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(10, 15);
    $pdf->Cell(0, 10, 'PT. INDOPACK MULTI PERKASA', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetX(10);
    $pdf->Cell(0, 6, 'No Penerimaan', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(0, 6, 'No PO', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(0, 6, 'Tanggal', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(29, 6, 'Keterangan', 0, 0);

    $pdf->SetXY(40,25);
    $pdf->MultiCell(45, 6, ': '.$header['ReceptionID'], 0, 'L');
    $pdf->SetX(40);
    $pdf->MultiCell(45, 6, ': '.$header['PurchaseOrderID'], 0, 'L');
    $pdf->SetX(40);
    $pdf->MultiCell(45, 6, ': '.$header['rcvDate'], 0, 'L');
    $pdf->SetX(40);
    $pdf->MultiCell(70, 5, $header['Description'] != "" ? ': '.$header['Description'] : ": -", 0, 'L');

    $pdf->SetXY(120, 15);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Nota Invoicing', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, 'No Invoicing', 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, 'Tanggal', 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, 'Status', 0, 1);

    $pdf->SetXY(145,25);
    $pdf->MultiCell(45, 6, ': '.$header['RCV_InvoiceID'], 0, 'L');
    $pdf->SetX(145);
    $pdf->MultiCell(45, 6, ': '.$header['CreatedOn'], 0, 'L');
    $pdf->SetX(145);
    $pdf->MultiCell(45, 6, $header['Status'] == 0 ? ': BELUM LUNAS' : ': LUNAS', 0, 'L');

    $pdf->SetXY(10,53);
    $pdf->Cell(100, 6, 'Pembayaran Kepada: ', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 6, 'Nama', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 6, 'Alamat', 0, 1);

    $pdf->SetXY(30,59);
    $pdf->Cell(100, 6, ': ' . $header["SupplierName"], 0, 1);
    $pdf->SetX(30);
    $pdf->MultiCell(100, 6, ': ' . $header["SupplierAdd"], 0, 1);

    $pdf->Ln(3);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 10, 'No', 'T,B', 0, 'C');
    $pdf->Cell(90, 10, 'Barang', 'T,B', 0, 'L');
    $pdf->Cell(15, 10, 'Jumlah', 'T,B', 0, 'R');
    $pdf->Cell(20, 10, ' Satuan', 'T,B', 0, 'L');
    $pdf->Cell(20, 10, 'Harga', 'T,B', 0, 'R');
    $pdf->Cell(33, 10, 'Subtotal  ', 'T,B', 1, 'R'); // 1 untuk pindah baris


    $pdf->SetFont('Arial', '', 10);
    $no = 1;
    $lineHeight = 8;
    $lineHeightE = 1;
    $maxNameLength = 80;
    while ($detail = mysqli_fetch_array($resultDetail)) {
        $pdf->SetFont('Arial', '', 10);
        $nameLength = strlen($detail['MaterialName']);
        if ($nameLength > $maxNameLength) {
            $lineHeightUsed = $lineHeightE;
        } else {
            $lineHeightUsed = $lineHeight;
        }

        $pdf->Cell(10, $lineHeightUsed, $no++, 0, 0, 'C');

        $pdf->SetX(20);
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();

        $pdf->MultiCell(85, $lineHeightUsed, $detail['MaterialName'], 0, 'L');

        $cellHeight = max($pdf->GetY() - $startY, $lineHeightUsed);

        $pdf->SetXY($startX + 103, $startY);
        $pdf->Cell(2, $lineHeightUsed, number_format($detail['Quantity']), 0, 0, 'R');
        $pdf->Cell(25, $lineHeightUsed, ' '.$detail['UnitCD'], 0, 0, 'L');
        $pdf->Cell(15, $lineHeightUsed, number_format($detail['Price'], 0), 0, 0, 'R');
        $pdf->Cell(32, $lineHeightUsed, number_format($detail['Subtotal'], 0), 0, 1, 'R');
    }
    $startY = $pdf->GetY();
    $pdf->SetY($startY-20);
    $pdf->Cell(189, 25, '', 'B', 0, 'R'); // 1 untuk pindah baris
    
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(130, 8, 'Subtotal:', 0, 0, 'R');
    $pdf->Cell(58, 8, number_format($header["DPP"], 0,',','.'), 0, 1, 'R');
    $pdf->Cell(130, 8, 'BM (5%):', 0, 0, 'R');
    $pdf->Cell(58, 8, number_format($header["BM"], 0,',','.'), 0, 1, 'R');
    $pdf->Cell(130, 8, 'PPN (11%):', 0, 0, 'R');
    $pdf->Cell(58, 8, number_format($header["PPN"], 0,',','.'), 0, 1, 'R');
    $pdf->Cell(130, 8, 'PPH (2,5%):', 'B', 0, 'R');
    $pdf->Cell(58, 8, number_format($header["PPH"], 0,',','.'), 'B', 1, 'R');
    $pdf->Cell(130, 8, 'Total:', 'B', 0, 'R');
    $pdf->Cell(58, 8, number_format($header["TotalAmount"], 0,',','.'), 'B', 1, 'R');

    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(190, 8, 'Terbilang: ' . terbilang($header["TotalAmount"]) . ' Rupiah', 0, 1, 'R');
    $pdf->Output('I', 'Invoice_' . ($RCV_InvoiceID ?? 'Unknown') . '.pdf');

    $updatePrintCount = "UPDATE receptioninvoiceheader SET PrintCount = PrintCount + 1 WHERE RCV_InvoiceID = '$RCV_InvoiceID'";
    mysqli_query($conn, $updatePrintCount);

} else {
    echo "Error: ID Invoice tidak ditemukan.";
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
ob_end_flush();
?>