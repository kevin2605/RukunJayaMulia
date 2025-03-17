<?php
require('../fpdf186/rotation.php');
include "../DBConnection.php";

date_default_timezone_set("Asia/Jakarta");

if (isset($_GET['InvoiceID'])) {
    $invoiceID = $_GET['InvoiceID'];

    $queryHeader = "SELECT * FROM invoiceheader WHERE InvoiceID = '$invoiceID'";
    $resultHeader = mysqli_query($conn, $queryHeader);
    $header = mysqli_fetch_array($resultHeader);

    $custID = $header['CustID'];
    $queryCustomer = "SELECT CustName, ShipmentAddress, CityName FROM customer WHERE CustID = '$custID'";
    $resultCustomer = mysqli_query($conn, $queryCustomer);
    $customer = mysqli_fetch_array($resultCustomer);

    $salesOrderID = $header['SalesOrderID'];
    $querySalesOrder = "SELECT Marketing, Description FROM salesorderheader WHERE SalesOrderID = '$salesOrderID'";
    $resultSalesOrder = mysqli_query($conn, $querySalesOrder);
    $salesOrder = mysqli_fetch_array($resultSalesOrder);

    $marketingUserID = $salesOrder['Marketing'];
    $querySalesman = "SELECT Username FROM systemuser WHERE UserID = '$marketingUserID'";
    $resultSalesman = mysqli_query($conn, $querySalesman);
    $salesman = mysqli_fetch_array($resultSalesman);

    $query = "SELECT JournalCD, JournalName, JournalType FROM journal";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $journalData = $result->fetch_assoc();
        $journalCD = $journalData['JournalCD'];
        $journalName = $journalData['JournalName'];
        $journalType = $journalData['JournalType'];
    } else {
        // Jika tidak ada data
        $journalCD = 'N/A';
        $journalName = 'N/A';
        $journalType = 'N/A';
    }


    $currentDate = date('d-m-Y');

    $queryDetail = "SELECT ivd.ProductCD, p.ProductName, p.UnitCD, ivd.Quantity, ivd.Price, ivd.Discount, ivd.Subtotal 
    FROM invoicedetail ivd 
    JOIN product p ON ivd.ProductCD = p.ProductCD 
    WHERE ivd.InvoiceID = '$invoiceID'";
    $resultDetail = mysqli_query($conn, $queryDetail);

    $pdf = new PDF_Rotate();
    $pdf->AddPage();

    $printCount = $header['PrintCount'];
    $invoiceStatus = $header['InvoiceStatus'];
    $watermarkText = '';
    if ($invoiceStatus == 1) {
        $watermarkText = 'LUNAS';
    } elseif ($printCount == 1) {
        $watermarkText = 'ORIGINAL';
    } elseif ($printCount > 1) {
        $watermarkText = 'COPY';
    }


    ob_start();
    /*
    $pdf->SetFont('Arial', 'B', 70);
    $pdf->SetTextColor(230, 230, 230);
    $pdf->Rotate(45, 105, 200);
    $pdf->SetXY(100, 60);
    $pdf->Cell(105, 170, $watermarkText, 0, 1, 'C');
    $pdf->Rotate(0);
    */

    $pdf->SetFont('Arial', 'B', 15);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(10, 5);
    $pdf->Cell(0, 10, 'PT. INDOPACK MULTI PERKASA', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetX(10);
    $pdf->Ln(2);
    $pdf->SetX(10);
    $pdf->Cell(100, 5, 'No. Nota', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 5, 'Tanggal', 0, 1);

    $pdf->Ln(2);
    $pdf->SetX(10);
    $pdf->Cell(0, 5, 'Marketing', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(0, 5, 'Keterangan', 0, 1);

    $pdf->SetXY(30,17);
    $pdf->Cell(100, 5, ': ' . $header['InvoiceID'], 0, 1);
    $pdf->SetX(30);
    $pdf->Cell(100, 5, ': ' . $header['CreatedOn'], 0, 1);

    $pdf->SetXY(30,29);
    $pdf->Cell(100, 5, ': ' . $salesman["Username"], 0, 1);
    $pdf->SetX(30);
    $pdf->MultiCell(70, 5, ': ' . $header['Description'], 0, 1);

    $pdf->SetXY(120, 5);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 10, 'NOTA PENJUALAN', 0, 1, 'L');

    $pdf->SetFont('Arial', '', 9);
    $pdf->SetXY(120,17);
    $pdf->Cell(0, 5,'Kepada Yth ', 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(22, 5,'Alamat', 0, 0);

    $pdf->SetXY(140,17);
    $pdf->Cell(0, 5,': ' . $customer["CustName"], 0, 'L');
    $pdf->SetXY(140,22);
    $pdf->MultiCell(60, 5,': ' . $customer["ShipmentAddress"], 0,'L');

    $lineHeight = 8;
    $totalRows = mysqli_num_rows($resultDetail);
    $totalHeight = $totalRows * $lineHeight;

    // Header
    $pdf->SetXY(10,45);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(15, 8, 'No', 'T,B', 0, 'C');
    $pdf->Cell(25, 8, 'Nama Barang', 'T,B', 0, 'C');
    $pdf->Cell(70, 8, 'QTY', 'T,B', 0, 'R');
    $pdf->Cell(20, 8, 'Satuan', 'T,B', 0, 'L');
    $pdf->Cell(15, 8, 'Price', 'T,B', 0, 'R');
    $pdf->Cell(15, 8, 'Discount', 'T,B', 0, 'R');
    $pdf->Cell(30, 8, 'Subtotal  ', 'T,B', 0, 'R');
    $pdf->Ln();

    $subtotal = 0;
    $totalexec = 0;
    $no = 1;
    $lineHeight = 6;
    $lineHeightE = 5;

    $maxNameLength = 50;

    while ($rowd = mysqli_fetch_array($resultDetail)) {
        $subtotal += $rowd["Subtotal"];
        $totalexec += $rowd["Price"] * $rowd["Quantity"];

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(15, $lineHeight, $no++, 0, 0, 'C');

        $nameLength = strlen($rowd['ProductName']);
        if ($nameLength > $maxNameLength) {
            $lineHeightUsed = $lineHeightE;
        } else {
            $lineHeightUsed = $lineHeight;
        }

        $pdf->SetX(25);
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();

        $pdf->MultiCell(65, $lineHeightUsed, $rowd['ProductName'], 0, 'L');

        $cellHeight = max($pdf->GetY() - $startY, $lineHeight);

        $pdf->SetXY($startX + 55, $startY);
        $pdf->Cell(40, $lineHeightUsed, number_format($rowd['Quantity'], 0, ',', '.'), 0, 0, 'R');
        $pdf->Cell(30, $lineHeightUsed, $rowd['UnitCD'], 0, 0, 'L');
        $pdf->Cell(5, $lineHeightUsed, number_format($rowd['Price'], 0), 0, 0, 'R');
        $pdf->Cell(6, $lineHeightUsed, number_format($rowd['Discount'], 0), 0, 0, 'R');
        $pdf->Cell(38, $lineHeightUsed, number_format($rowd['Subtotal'], 0, ',', '.'), 0, 1, 'R');

        $pdf->SetY($startY + $cellHeight);
    }
    $pdf->SetY(220);
    $pdf->Cell(0, 0, '', 'B');

    $pdf->SetFont('Arial', 'B', 9);


    $diskon = $totalexec - $subtotal;
    $beforetax = $subtotal / 1.11;
    $tax = $beforetax * 0.11;


    $queryHeader = "SELECT DPAmount, Cashdisc FROM invoiceheader WHERE InvoiceID='$invoiceID'";
    $resultHeader = mysqli_query($conn, $queryHeader);
    $rowHeader = mysqli_fetch_array($resultHeader);
    $dpAmount = $rowHeader['DPAmount'];
    $cashdisc = $rowHeader['Cashdisc'];

    $diskonCash = 0;
    if ($cashdisc == 1) {
        $diskonCash = $subtotal * 0.02; // 2% dari subtotal
        $beforetax = ($subtotal - $diskonCash - $dpAmount) / 1.11;
        $tax = $beforetax * 0.11;
    }else if($cashdisc == 2){
        $diskonCash = $subtotal * 0.04; // 4% dari subtotal
        $beforetax = ($subtotal - $diskonCash - $dpAmount) / 1.11;
        $tax = $beforetax * 0.11;
    }else {
        $beforetax = ($subtotal - $dpAmount) / 1.11;
        $tax = $beforetax * 0.11;
    }

    // Hitung Total Net Setelah Diskon Cash
    $totalNet = $subtotal - $dpAmount - $diskonCash;
    $subtotalinv = $subtotal;

    //CEK TANGGAL DP
    if($dpAmount != 0){
        $queryDP = "SELECT dp.CreatedOn
                    FROM invoiceheader i, salesorderheader s, downpaymentheader dp
                    WHERE dp.SalesOrderID=s.SalesOrderID
                          AND i.SalesOrderID=s.SalesOrderID
                          AND i.InvoiceID='$invoiceID'";
        $resultDP = mysqli_query($conn, $queryDP);
        $rowDP = mysqli_fetch_assoc($resultDP);
        $dpDate = substr($rowDP["CreatedOn"],0,10);
    }

    function terbilang($angka)
    {
        $angka = (int) $angka;
        $baca = array(
            0 => 'Nol',
            1 => 'Satu',
            2 => 'Dua',
            3 => 'Tiga',
            4 => 'Empat',
            5 => 'Lima',
            6 => 'Enam',
            7 => 'Tujuh',
            8 => 'Delapan',
            9 => 'Sembilan',
            10 => 'Sepuluh',
            11 => 'Sebelas',
            12 => 'Dua belas',
            13 => 'Tiga belas',
            14 => 'Empat belas',
            15 => 'Lima belas',
            16 => 'Enam belas',
            17 => 'Tujuh belas',
            18 => 'Delapan belas',
            19 => 'Sembilan belas',
            20 => 'Dua puluh',
            30 => 'Tiga puluh',
            40 => 'Empat puluh',
            50 => 'Lima puluh',
            60 => 'Enam puluh',
            70 => 'Tujuh puluh',
            80 => 'Delapan puluh',
            90 => 'Sembilan puluh',
            100 => 'Seratus',
            1000 => 'Seribu',
            1000000 => 'Juta'
        );

        if ($angka < 20) {
            return $baca[$angka];
        } else if ($angka < 100) {
            $puluhan = floor($angka / 10) * 10;
            $satuan = $angka % 10;
            return $baca[$puluhan] . ($satuan ? ' ' . $baca[$satuan] : '');
        } else if ($angka < 1000) {
            $ratusan = floor($angka / 100) * 100;
            $sisa = $angka % 100;
            return $baca[$ratusan / 100] . ' Ratus' . ($sisa ? ' ' . terbilang($sisa) : '');
        } else if ($angka < 1000000) {
            $ribuan = floor($angka / 1000);
            $sisa = $angka % 1000;
            return terbilang($ribuan) . ' Ribu' . ($sisa ? ' ' . terbilang($sisa) : '');
        } else if ($angka < 1000000000) {
            $jutaan = floor($angka / 1000000);
            $sisa = $angka % 1000000;
            return terbilang($jutaan) . ' Juta' . ($sisa ? ' ' . terbilang($sisa) : '');
        } else {
            return 'Angka terlalu besar';
        }
    }
    // Cetak Total Invoice
    $terbilang = terbilang($totalNet) . ' Rupiah';

    // Total Invoice
    $pdf->SetX(10);
    $pdf->SetFont('Arial', 'I', 8);
    $terbilangText = 'Terbilang: ' . $terbilang;
    $pdf->Cell(150, 8, $terbilangText, 0, 1, 'L');
    $pdf->Cell(100, 5, 'Bank BCA Cab. Galaxy', 0, 1, 'L');
    $pdf->Cell(100, 5, 'A/N : PT. INDOPACK MULTI PERKASA', 0, 1, 'L');
    $pdf->Cell(100, 5, 'ACC : 7880587271', 0, 1, 'L');


    $pdf->SetY(240);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(130, 5, 'Total Invoice:', 0, 0, 'R');
    //$pdf->Cell(59, 5, number_format($totalexec, 2, ',', '.'), 0, 1, 'R');
    $pdf->Cell(59, 5, number_format($subtotal, 2, ',', '.'), 0, 1, 'R');

    $pdf->Cell(130, 5, '( '.$dpDate.' ) DP:', 0, 0, 'R');
    $pdf->Cell(59, 5, number_format($dpAmount, 2, ',', '.'), 0, 1, 'R');

    //$pdf->Cell(130, 5, 'Diskon:', 0, 0, 'R');
    //$pdf->Cell(59, 5, number_format($diskon, 2, ',', '.'), 0, 1, 'R');

    if ($cashdisc == 1) {
        $pdf->Cell(130, 5, 'Diskon Cash :', 0, 0, 'R');
        $pdf->Cell(59, 5, number_format($diskonCash, 2, ',', '.'), 0, 1, 'R');
    }else if ($cashdisc == 2) {
        $pdf->Cell(130, 5, 'Diskon Cash :', 0, 0, 'R');
        $pdf->Cell(59, 5, number_format($diskonCash, 2, ',', '.'), 0, 1, 'R');
    }

    $pdf->Cell(130, 5, 'DPP:', 0, 0, 'R');
    $pdf->Cell(59, 5, number_format($beforetax, 2, ',', '.'), 0, 1, 'R');

    $pdf->Cell(130, 5, 'PPN:', 0, 0, 'R');
    $pdf->Cell(59, 5, number_format($tax, 2, ',', '.'), 0, 1, 'R');

    $pdf->Cell(130, 5, 'Total (NET):', 0, 0, 'R');
    $pdf->Cell(59, 5, number_format($totalNet, 2, ',', '.'), 'T', 1, 'R');
    $pdf->SetX(10);

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(10,255);
    $pdf->Cell(25, 6, 'Kasir', 0, 0, 'C');
    $pdf->Cell(35, 6, 'Pelanggan', 0, 0, 'C');
    $pdf->Cell(25, 6, 'Driver', 0, 1, 'C');
    $pdf->SetXY(10,265);
    $pdf->Cell(25, 8, '(.......................)', 0, 0, 'C');
    $pdf->Cell(35, 8, '(.......................)', 0, 0, 'C');
    $pdf->Cell(25, 8, '(.......................)', 0, 1, 'C');

    
    // Fungsi untuk mencetak surat jalan
    function printSuratJalan($pdf, $header, $customer, $salesOrder, $salesman, $currentDate, $conn)
    {
        if (isset($header['InvoiceID'])) {
            $invoiceID = $header['InvoiceID'];

            // Query untuk mendapatkan detail produk
            $queryDetail = "SELECT ivd.ProductCD, p.ProductName, p.UnitCD, p.BoxLength, p.BoxWidth, p.BoxHeight, p.PcsPerBox, ivd.Quantity, ivd.Discount 
                        FROM invoicedetail ivd 
                        JOIN product p ON ivd.ProductCD = p.ProductCD 
                        WHERE ivd.InvoiceID = '$invoiceID'";
            $resultDetail = mysqli_query($conn, $queryDetail);

            // Header Surat Jalan
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 15);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetXY(10, 5);
            $pdf->Cell(0, 10, 'PT. INDOPACK MULTI PERKASA', 0, 1, 'L');
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetX(10);
            $pdf->Ln(2);
            $pdf->SetX(10);
            $pdf->Cell(100, 5, 'No. Nota', 0, 1);
            $pdf->SetX(10);
            $pdf->Cell(100, 5, 'Tanggal', 0, 1);
        
            $pdf->Ln(2);
            $pdf->SetX(10);
            $pdf->Cell(0, 5, 'Marketing', 0, 1);
            $pdf->SetX(10);
            $pdf->Cell(0, 5, 'Keterangan', 0, 1);
        
            $pdf->SetXY(30,17);
            $pdf->Cell(100, 5, ': ' . $header['InvoiceID'], 0, 1);
            $pdf->SetX(30);
            $pdf->Cell(100, 5, ': ' . $header['CreatedOn'], 0, 1);
        
            $pdf->SetXY(30,29);
            $pdf->Cell(100, 5, ': ' . $salesman['Username'], 0, 1);
            $pdf->SetX(30);
            $pdf->MultiCell(70, 5, ': ' . $salesOrder['Description'], 0, 1);
        
            $pdf->SetXY(120, 5);
            $pdf->SetFont('Arial', 'B', 15);
            $pdf->Cell(0, 10, 'SURAT JALAN', 0, 1, 'L');
        
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetXY(120,17);
            $pdf->Cell(0, 5,'Kepada Yth ', 0, 1);
            $pdf->SetX(120);
            $pdf->Cell(22, 5,'Alamat', 0, 0);
        
            $pdf->SetXY(140,17);
            $pdf->Cell(0, 5,': ' . $customer["CustName"], 0,'L');
            $pdf->SetXY(140,22);
            $pdf->MultiCell(60, 5,': ' . $customer["ShipmentAddress"], 0,'L');

            $pdf->Ln(10);
            
            // Header
            $pdf->SetXY(10,45);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(15, 8, 'No', 'T,B', 0, 'C');
            $pdf->Cell(70, 8, 'Nama Barang', 'T,B', 0, 'L');
            $pdf->Cell(15, 8, 'QTY', 'T,B', 0, 'R');
            $pdf->Cell(15, 8, 'Satuan', 'T,B', 0, 'L');
            $pdf->Cell(21, 8, 'Packing/Dos', 'T,B', 0, 'L');
            $pdf->Cell(14, 8, 'Satuan', 'T,B', 0, 'L');
            $pdf->Cell(20, 8, 'Dos', 'T,B', 0, 'L');
            //$pdf->Cell(17, 8, 'Length', 'T,B', 0, 'L');
            //$pdf->Cell(17, 8, 'Width', 'T,B', 0, 'L');
            //$pdf->Cell(17, 8, 'Height', 'T,B', 0, 'L');
            $pdf->Cell(20, 8, 'Kubikasi', 'T,B', 0, 'R');
            $pdf->Ln();

            // Isi Tabel
            $subtotal = 0;
            $totalexec = 0;
            $no = 1;
            $lineHeight = 6;
            $lineHeightE = 5;

            $maxNameLength = 70;
            $no = 1;
            $totalKubikasi = 0;
            while ($rowd = mysqli_fetch_array($resultDetail)) {
                $subtotal += $rowd["Subtotal"];
                $totalexec += $rowd["Price"] * $rowd["Quantity"];

                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(15, $lineHeight, $no++, 0, 0, 'C');

                $nameLength = strlen($rowd['ProductName']);
                if ($nameLength > $maxNameLength) {
                    $lineHeightUsed = $lineHeightE;
                } else {
                    $lineHeightUsed = $lineHeight;
                }

                $pdf->SetX(25);
                $startX = $pdf->GetX();
                $startY = $pdf->GetY();

                $pdf->MultiCell(70, $lineHeightUsed, $rowd['ProductName'], 0, 'L');

                $cellHeight = max($pdf->GetY() - $startY, $lineHeight);

                $pdf->SetXY($startX + 70, $startY);

                $pdf->Cell(15, $lineHeightUsed, number_format($rowd['Quantity'], 0, ',', '.'), 0, 0, 'R');
                $pdf->Cell(15, $lineHeightUsed, $rowd['UnitCD'], 0, 0, 'L');
                $pdf->Cell(21, $lineHeightUsed, number_format($rowd['PcsPerBox'], 0, ',', '.'), 0, 0, 'R');
                $pdf->Cell(14, $lineHeightUsed, $rowd['UnitCD'], 0, 0, 'L');
                $pdf->Cell(20, $lineHeightUsed, number_format($rowd['Quantity']/$rowd['PcsPerBox'], 0, ',', '.'), 0, 0, 'L');

                //count kubikasi satuan m3
                $kubikasi = (($rowd['BoxLength']*$rowd['BoxWidth']*$rowd['BoxHeight'])/1000000)*($rowd['Quantity']/$rowd['PcsPerBox']);
                $pdf->Cell(20, $lineHeightUsed, number_format($kubikasi, 1, ',', '.'), 0, 0, 'R');
                $totalKubikasi += $kubikasi;

                $pdf->SetY($startY + $cellHeight);
            }
            $pdf->Cell(190, 0, '', 'B', 1, 'C');

            //CHECK ADA EKSPEDISI ATAU TIDAK
            if($header["ExpeditionID"] != NULL && $header["ExpeditionID"] != ""){
                // Query untuk mendapatkan detail produk
                $queryExp = "SELECT ExpeditionName, Address 
                            FROM expedition
                            WHERE ExpeditionID = '".$header["ExpeditionID"]."'";
                $resultExp = mysqli_query($conn, $queryExp);
                $exp = mysqli_fetch_assoc($resultExp);
                $pdf->Ln(4);
                $pdf->Cell(100, 5, 'Ekspedisi : ' . $exp["ExpeditionName"], 0, 1, 'L');
                $pdf->Cell(100, 5, 'Alamat     : ' . $exp["Address"], 0, 0, 'L');
            }

            // Tambahkan tanda tangan atau catatan jika perlu
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetXY(10,255);
            $pdf->Cell(25, 6, 'Kasir', 0, 0, 'C');
            $pdf->Cell(35, 6, 'Pelanggan', 0, 0, 'C');
            $pdf->Cell(25, 6, 'Driver', 0, 1, 'C');
            $pdf->SetXY(10,265);
            $pdf->Cell(25, 8, '(.......................)', 0, 0, 'C');
            $pdf->Cell(35, 8, '(.......................)', 0, 0, 'C');
            $pdf->Cell(25, 8, '(.......................)', 0, 1, 'C');
        }
    }

    // Setelah mencetak invoice, panggil fungsi untuk mencetak surat jalan
    printSuratJalan($pdf, $header, $customer, $salesOrder, $salesman, $currentDate, $conn);

    ob_end_clean();

    $pdf->Output('I', 'Invoice_' . $header['InvoiceID'] . '.pdf');

    $updatePrintCount = "UPDATE invoiceheader SET PrintCount = PrintCount + 1 WHERE InvoiceID = '$invoiceID'";
    mysqli_query($conn, $updatePrintCount);
}