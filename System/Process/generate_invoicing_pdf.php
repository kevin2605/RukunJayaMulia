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
    $queryHeader = "SELECT ri.RCV_InvoiceID, ri.CreatedOn, ri.CreatedBy, ri.CategoryCD, ri.TaxInvoiceNumber, ri.TaxInvoiceDate, ri.Status, ri.DPP, ri.PPN, ri.TotalAmount,
                    r.ReceptionID, r.CreatedOn AS rcvDate, r.CategoryCD, r.Description, p.PurchaseOrderID, ri.Status, s.SupplierNum, s.SupplierName, s.SupplierAdd,
                    s.ContactName, s.ContactPhone, s.Telepon
                    FROM receptioninvoiceheader ri, receptionheader r, purchaseorderheader p, supplier s 
                    WHERE ri.ReceptionID=r.ReceptionID
                        AND r.PurchaseOrderID=p.PurchaseOrderID
                        AND p.SupplierNum=s.SupplierNum
                        AND ri.RCV_InvoiceID='".$RCV_InvoiceID."'";
    $resultHeader = mysqli_query($conn, $queryHeader);


    // Validasi hasil query
    if (!$resultHeader || mysqli_num_rows($resultHeader) == 0) {
        die("Error: Data invoice tidak ditemukan.");
    }
    $header = mysqli_fetch_array($resultHeader);

    // Data dari header
    /*
    $ReceptionID = $header['ReceptionID'] ?? '';
    $PurchaseOrderID = $header['PurchaseOrderID'] ?? '';
    $RCV_InvoiceID = $header['RCV_InvoiceID'] ?? '';
    $CategoryCD = $header['CategoryCD'] ?? '';
    $CreatedOn = $header['CreatedOn'] ?? '';
    $RcvDate = $header['rcvDate'] ?? '';
    $DPP = $header['DPP'] ?? 0;
    $PPN = $header['PPN'] ?? 0;
    $TotalAmount = $header['TotalAmount'] ?? 0;
    $TaxInvoiceNumber = $header['TaxInvoiceNumber'] ?? '';
    $TaxInvoiceDate = $header['TaxInvoiceDate'] ?? '';
    $Status = $header['Status'] ?? 0;
    */
    // Query untuk mendapatkan detail invoice BB,BPP,SPR
    if($header["CategoryCD"] == "BB"){
        $queryDetail = "SELECT 
                rid.ItemCD, 
                m.MaterialName, 
                rid.Quantity, 
                rid.UnitCD, 
                rid.Price, 
                rid.DPP, 
                rid.PPN, 
                rid.Subtotal 
            FROM receptioninvoicedetail rid
            JOIN material m ON rid.ItemCD = m.MaterialCD
            WHERE rid.RCV_InvoiceID = '$RCV_InvoiceID'";
        $resultDetail = mysqli_query($conn, $queryDetail);
    }else if($header["CategoryCD"] == "BPP"){
        $queryDetail = "SELECT 
                rid.ItemCD, 
                sg.GoodsName, 
                rid.Quantity, 
                rid.UnitCD, 
                rid.Price, 
                rid.DPP, 
                rid.PPN, 
                rid.Subtotal 
            FROM receptioninvoicedetail rid
            JOIN supportinggoods sg ON rid.ItemCD = sg.GoodsCD
            WHERE rid.RCV_InvoiceID = '$RCV_InvoiceID'";
        $resultDetail = mysqli_query($conn, $queryDetail);
    }else if($header["CategoryCD"] == "SPR"){
        $queryDetail = "SELECT 
                rid.ItemCD, 
                sp.PartName, 
                rid.Quantity, 
                rid.UnitCD, 
                rid.Price, 
                rid.DPP, 
                rid.PPN, 
                rid.Subtotal 
            FROM receptioninvoicedetail rid
            JOIN sparepart sp ON rid.ItemCD = sp.PartCD
            WHERE rid.RCV_InvoiceID = '$RCV_InvoiceID'";
        $resultDetail = mysqli_query($conn, $queryDetail);
    }

    // Inisialisasi FPDF
    $pdf = new PDF_Rotate();
    $pdf->AddPage();

    // Menentukan watermark berdasarkan status invoice
    // $currentDate = date('d-m-Y');
    $watermarkText = '';

    if ($header["Status"] == 1) {
        $watermarkText = 'LUNAS';
    } else {
        $watermarkText = 'BELUM LUNAS';
    }
    }

    //Menambahkan watermark
    $pdf->SetFont('Arial', 'B', 70);
    $pdf->SetTextColor(230, 230, 230);
    $pdf->Rotate(45, 105, 200);
    $pdf->SetXY(100, 60);
    $pdf->Cell(105, 170, $watermarkText, 0, 1, 'C');
    $pdf->Rotate(0);


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
    $pdf->MultiCell(65, 6, $header['Description'] != "" ? ': '.$header['Description'] : ": -", 0, 'L');
    $pdf->Ln(2);
    $pdf->SetXY(10,57);
    $pdf->Cell(100, 6, 'Pembayaran Kepada: ', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 6, 'Nama', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 6, 'Alamat', 0, 1);

    $pdf->SetXY(30,63);
    $pdf->Cell(100, 6, ': ' . $header["SupplierName"], 0, 1);
    $pdf->SetX(30);
    $pdf->MultiCell(100, 6, ': ' . $header["SupplierAdd"], 0, 1);

    // Menggunakan MultiCell untuk alamat
    $pdf->MultiCell(100, 7, '', ''); // Menyesuaikan lebar MultiCell

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

    $pdf->Ln(10);

    $pdf->Ln(35);

    // Header untuk detail reception
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 10, 'No', 'T,B', 0, 'C');
    $pdf->Cell(65, 10, 'Barang', 'T,B', 0, 'L');
    $pdf->Cell(30, 10, 'Jumlah', 'T,B', 0, 'R');
    $pdf->Cell(15, 10, 'Satuan', 'T,B', 0, 'L');
    $pdf->Cell(30, 10, 'Harga  (exclude) ', 'T,B', 0, 'L');
    $pdf->Cell(40, 10, 'Subtotal  ', 'T,B', 1, 'R'); // 1 untuk pindah baris


    $pdf->SetFont('Arial', '', 10);
    $no = 1;
    $lineHeight = 6;
    $lineHeightE = 1;
    $maxNameLength = 65;
    while ($detail = mysqli_fetch_array($resultDetail)) {
        $pdf->SetFont('Arial', '', 10);
        
        $itemname = "";
        if($header["CategoryCD"] == "BB"){
            $nameLength = strlen($detail['MaterialName']);
            $itemname = $detail['MaterialName'];
        }else if($header["CategoryCD"] == "BPP"){
            $nameLength = strlen($detail['GoodsName']);
            $itemname = $detail['GoodsName'];
        }else if($header["CategoryCD"] == "SPR"){
            $nameLength = strlen($detail['PartName']);
            $itemname = $detail['PartName'];
        }
        
        if ($nameLength > $maxNameLength) {
            $lineHeightUsed = $lineHeightE;
        } else {
            $lineHeightUsed = $lineHeight;
        }

        $pdf->Cell(10, $lineHeightUsed, $no++, 0, 0, 'C');

        $pdf->SetX(20);
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();

        $pdf->MultiCell(65, $lineHeightUsed, $itemname, 0, 'L');

        $cellHeight = max($pdf->GetY() - $startY, $lineHeightUsed);

        $pdf->SetXY($startX + 66, $startY);
        $pdf->Cell(29, $lineHeightUsed, number_format($detail['Quantity']), 0, 0, 'R');
        $pdf->Cell(15, $lineHeightUsed, $detail['UnitCD'], 0, 0, 'L');
        $pdf->Cell(30, $lineHeightUsed, number_format($detail['Price'], 0), 0, 0, 'L');
        $pdf->Cell(38, $lineHeightUsed, number_format($detail['Price']*$detail['Quantity'], 0), 0, 1, 'R');
    }
    $pdf->Cell(190, 10, '  ', 'B', 1, 'R'); // 1 untuk pindah baris

    // Total
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(130, 8, 'Subtotal :', 0, 0, 'R');
    $pdf->Cell(58, 8, number_format($header["DPP"], 0,',','.'), 0, 1, 'R');
    $pdf->Cell(130, 8, 'PPN :', 0, 0, 'R');
    $pdf->Cell(58, 8, number_format($header["PPN"], 0,',','.'), 0, 1, 'R');
    $pdf->Cell(130, 8, 'Total :', 0, 0, 'R');
    $pdf->Cell(58, 8, number_format($header["TotalAmount"], 0,',','.'), 0, 1, 'R');

    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(190, 8, 'Terbilang: ' . terbilang($header["TotalAmount"]) . ' Rupiah', 0, 1, 'R');


    //nama file
    $pdf->Output('I', 'Invoice_' . ($header['RCV_InvoiceID'] ?? 'Unknown') . '.pdf');

    $updatePrintCount = "UPDATE receptioninvoiceheader SET PrintCount = PrintCount + 1 WHERE RCV_InvoiceID = '$RCV_InvoiceID'";
    mysqli_query($conn, $updatePrintCount);

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
// Mengakhiri output buffer
ob_end_flush();
?>