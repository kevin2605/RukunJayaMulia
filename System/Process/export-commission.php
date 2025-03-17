<?php
require('../fpdf186/rotation.php');
include "../DBConnection.php";

date_default_timezone_set("Asia/Jakarta");

ob_start();

$months = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

if (!is_null($_POST["month"]) && !is_null($_POST["year"]) && !is_null($_POST["marketing"])) {

    $query ="SELECT c.CreditPaymentID, c.CreatedOn, i.InvoiceID, s.SalesOrderID, su.Name AS Marketing, c.TotalPayment
            FROM creditpaymentdetail c, invoiceheader i, salesorderheader s, systemuser su
            WHERE c.InvoiceID = i.InvoiceID
                AND i.SalesOrderID = s.SalesOrderID
                AND s.Marketing = su.UserID
                AND SUBSTR(c.CreatedOn,1,4) = ".$_POST["year"]."
                AND SUBSTR(c.CreatedOn,6,2) = ".$_POST["month"]."
                AND su.UserID='".$_POST["marketing"]."'";
    $result = mysqli_query($conn, $query);

    if (!$result || mysqli_num_rows($result) == 0) {
        die("Error: Data tidak ditemukan.");
    }


    // Inisialisasi FPDF
    $pdf = new PDF_Rotate();
    $pdf->AddPage();

    $currentDate = date('d-m-Y');

    $pdf->Rotate(0);

    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(10, 10);
    $pdf->Cell(0, 10, 'PT. INDOPACK MULTI PERKASA', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(10, 20);
    $pdf->Cell(100, 5, 'Pergudangan SAFE N LOCK, Blok K 1707 - 1708', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 5, 'Jl Lingkar timur KM 5,5', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 5, 'Telp : +623158259871 , Fax, +623158259872', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 5, 'Wechat / Skype / Line : papercupindonesia', 0, 1);
    $pdf->SetX(10);

    $pdf->SetXY(120, 10);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Komisi Marketing', 0, 1, 'L');
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetX(120);
    $pdf->Cell(0, 5, 'Bulan ', 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(0, 5, 'Tahun', 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(0, 5, 'Tgl. Cetak', 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(0, 5, 'Marketing', 0, 1, 'L');

    $pdf->SetXY(140, 20);
    $pdf->Cell(0, 5, ': ' . $months[intval($_POST["month"])-1], 0, 1);
    $pdf->SetX(140);
    $pdf->Cell(0, 5, ': ' . $_POST["year"], 0, 1);
    $pdf->SetX(140);
    $pdf->Cell(0, 5, ': ' . $currentDate, 0, 1);
    $pdf->SetX(140);
    $pdf->Cell(0, 5, ': ' . $_POST["marketing"], 0, 1);
    $pdf->Ln(10);
    
    $pdf->SetXY(10, 45);
    $pdf->Cell(30, 10, 'Rincian Penerimaan Pembayaran'); // PENERIMAAN PEMBAYARAN
    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(30, 10, 'No. Pembayaran', 'T,B', 0, 'L');
    $pdf->Cell(30, 10, 'Tgl. Pembayaran', 'T,B', 0, 'L');
    $pdf->Cell(30, 10, 'No. Invoice', 'T,B', 0, 'L');
    $pdf->Cell(30, 10, 'No. Sales Order', 'T,B', 0, 'L');
    $pdf->Cell(30, 10, 'Marketing', 'T,B', 0, 'L');
    $pdf->Cell(40, 10, 'Jumlah Pembayaran', 'T,B', 0, 'L');
    $pdf->Ln();
    
    $totalPayment = 0;
    while ($row = mysqli_fetch_array($result)) {
        $pdf->Cell(30, 10, $row["CreditPaymentID"], 'T,B', 0, 'L');
        $pdf->Cell(30, 10, substr($row["CreatedOn"],0,10), 'T,B', 0, 'L');
        $pdf->Cell(30, 10, $row["InvoiceID"], 'T,B', 0, 'L');
        $pdf->Cell(30, 10, $row["SalesOrderID"], 'T,B', 0, 'L');
        $pdf->Cell(30, 10, $row["Marketing"], 'T,B', 0, 'L');
        $pdf->Cell(40, 10, 'Rp ' . number_format($row["TotalPayment"], 0, ',', '.'), 'T,B', 0, 'L');
        $totalPayment += $row["TotalPayment"];
        $pdf->Ln();
    }
    $pdf->Cell(100, 7, 'Total Pembayaran : Rp ' . number_format($totalPayment, 0, ',', '.'), 0, 1);
    $pdf->Ln(5);
    //=============================================================================================================================

    $pdf->Cell(30, 10, 'Rincian Penerimaan DP'); // PENERIMAAN DP
    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(30, 10, 'No. Pembayaran', 'T,B', 0, 'L');
    $pdf->Cell(30, 10, 'Tgl. Pembayaran', 'T,B', 0, 'L');
    $pdf->Cell(60, 10, 'No. Sales Order', 'T,B', 0, 'L');
    $pdf->Cell(30, 10, 'Marketing', 'T,B', 0, 'L');
    $pdf->Cell(40, 10, 'Jumlah Pembayaran', 'T,B', 0, 'L');
    $pdf->Ln();
    
    $queryDP ="SELECT dph.DPID, dph.CreatedOn, dph.SalesOrderID, su.Name AS Marketing, dpd.Amount
            FROM downpaymentheader dph, downpaymentdetail dpd, salesorderheader s, systemuser su
            WHERE dph.DPID = dpd.DPID
                AND dph.SalesOrderID = s.SalesOrderID
                AND s.Marketing = su.UserID
                AND SUBSTR(dph.CreatedOn,1,4) = ".$_POST["year"]."
                AND SUBSTR(dph.CreatedOn,6,2) = ".$_POST["month"]."
                AND su.UserID='".$_POST["marketing"]."'";
    $resultDP = mysqli_query($conn, $queryDP);

    $totalDP = 0;
    while ($rowDP = mysqli_fetch_array($resultDP)) {
        $pdf->Cell(30, 10, $rowDP["DPID"], 'T,B', 0, 'L');
        $pdf->Cell(30, 10, substr($rowDP["CreatedOn"],0,10), 'T,B', 0, 'L');
        $pdf->Cell(60, 10, $rowDP["SalesOrderID"], 'T,B', 0, 'L');
        $pdf->Cell(30, 10, $rowDP["Marketing"], 'T,B', 0, 'L');
        $pdf->Cell(40, 10, 'Rp ' . number_format($rowDP["Amount"], 0, ',', '.'), 'T,B', 0, 'L');
        $totalDP += $rowDP["Amount"];
        $pdf->Ln();
    }

    $pdf->Cell(100, 7, 'Total DP : Rp ' . number_format($totalDP, 0, ',', '.'), 0, 1);

    $pdf->Output('I', 'Komisi_' . $months[intval($_POST["month"])-1] . "_" . ($_POST["marketing"] ?? 'Unknown') . '.pdf');
} else {
    echo "Error: Parameter kosong/tidak ditemukan.";
}

// Mengakhiri output buffer
ob_end_flush();
?>