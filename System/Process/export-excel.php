<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
include "../DBConnection.php";

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$NPWPSELLER = "0315218073617000";

//FORMAT E-FAKTUR
if (isset($_POST["btnSearch"]) && isset($_POST["formEFaktur"])) {
    $startDate = $_POST['startdate'];
    $endDate = $_POST['enddate'];

    $query = "SELECT 
        i.InvoiceID AS FK,
        '1' AS KD_JENIS_TRANSAKSI,
        '0' AS FG_PENGGANTI,
        i.TaxInvoiceNumber AS NOMOR_FAKTUR,
        MONTH(i.CreatedOn) AS MASA_PAJAK,
        YEAR(i.CreatedOn) AS TAHUN_PAJAK,
        i.TaxInvoiceDate AS TANGGAL_FAKTUR,
        c.CustID,
        c.NPWPNum AS NPWP,
        c.NPWPName AS NAMA,
        c.NPWPAddress AS ALAMAT_LENGKAP,
        i.TotalInvoice AS JUMLAH_DPP,
        i.DPAmount,
        (i.TotalInvoice - i.DPAmount) * 0.1 AS JUMLAH_PPN,
        0 AS JUMLAH_PPNBM,
        0 AS ID_KETERANGAN_TAMBAHAN,
        0 AS FG_UANG_MUKA,
        i.DPAmount AS UANG_MUKA_DPP,
        (i.DPAmount * 0.1) AS UANG_MUKA_PPN,
        0 AS UANG_MUKA_PPNBM,
        0 AS REFERENSI,
        0 AS KODE_DOKUMEN_PENDUKUNG
        FROM invoiceheader i
        JOIN customer c ON i.CustID = c.CustID
        WHERE i.CreatedOn BETWEEN ? AND ?
        AND i.TaxInvoiceNumber != ''";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $spreadsheet = new Spreadsheet();

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'FK, KD_JENIS_TRANSAKSI, FG_PENGGANTI, NOMOR_FAKTUR, MASA_PAJAK, TAHUN_PAJAK, TANGGAL_FAKTUR, NPWP, NAMA, ALAMAT_LENGKAP, JUMLAH_DPP, JUMLAH_PPN, JUMLAH_PPNBM, ID_KETERANGAN_TAMBAHAN, FG_UANG_MUKA, UANG_MUKA_DPP, UANG_MUKA_PPN, UANG_MUKA_PPNBM, REFERENSI, KODE_DOKUMEN_PENDUKUNG');

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A2', 'LT, NPWP, NAMA, JALAN, BLOK, NOMOR, RT, RW, KECAMATAN, KELURAHAN, KABUPATEN, PROVINSI, KODE_POS, TELEPON');

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A3', 'OF, KODE_OBJEK, NAMA, HARGA_SATUAN, JUMLAH_BARANG, HARGA_TOTAL, DISKON, DPP, PPN, TARIF PPNBM, PPNBM');

    $rowNumber = 4;
    $invoiceIDs = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $totalInvoice = $row['JUMLAH_DPP'];
            $dpAmount = $row['DPAmount'];
            $remainingDPP = $totalInvoice - $dpAmount;

            if ($dpAmount > 0) {
                $dppAfterDP = $remainingDPP / 1.1;
                $ppnAfterDP = $remainingDPP - $dppAfterDP;
            } else {
                $dppAfterDP = $totalInvoice / 1.1;
                $ppnAfterDP = $totalInvoice - $dppAfterDP;
            }

            // Membulatkan hasil perhitungan ke bilangan bulat
            $dppAfterDP = round($dppAfterDP);
            $ppnAfterDP = round($ppnAfterDP);
            $dpAmount = round($dpAmount);
            $dpAmountPPN = round($dpAmount * 0.1);

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue(
                    'A' . $rowNumber,
                    "FK," .
                    $row['KD_JENIS_TRANSAKSI'] . ',' .
                    $row['FG_PENGGANTI'] . ',' .
                    str_replace(['.', '-'], '', $row['NOMOR_FAKTUR']) . ',' .
                    $row['MASA_PAJAK'] . ',' .
                    $row['TAHUN_PAJAK'] . ',' .
                    DateTime::createFromFormat('Y-m-d', $row['TANGGAL_FAKTUR'])->format('d/m/Y') . ',' .
                    $row['NPWP'] . ',' .
                    $row['NAMA'] . ',' .
                    str_replace(',', '', $row['ALAMAT_LENGKAP']) . ',' .  // Menghapus koma
                    $dppAfterDP . ',' .
                    $ppnAfterDP . ',' .
                    $row['JUMLAH_PPNBM'] . ',' .
                    $row['ID_KETERANGAN_TAMBAHAN'] . ',' .
                    $row['FG_UANG_MUKA'] . ',' .
                    $dpAmount . ',' .
                    $dpAmountPPN . ',' .
                    '0,' .
                    $row['FK'] . ',' .
                    $row['KODE_DOKUMEN_PENDUKUNG']
                );
            $rowNumber++;
            $query3 = "SELECT 
              invoicedetail.InvoiceID, 
              invoicedetail.ProductCD, 
              invoicedetail.Quantity, 
              invoicedetail.Price, 
              invoicedetail.Discount, 
              (invoicedetail.Quantity * invoicedetail.Price) AS Subtotal,
              product.ProductName
           FROM 
              invoicedetail
           INNER JOIN 
              product ON invoicedetail.ProductCD = product.ProductCD
           WHERE 
              invoicedetail.InvoiceID = ?";

            $stmt3 = $conn->prepare($query3);
            $stmt3->bind_param("s", $row['FK']);
            $stmt3->execute();
            $result3 = $stmt3->get_result();

            if ($result3->num_rows > 0) {
                while ($row3 = $result3->fetch_assoc()) {
                    $ppn = $row3['Subtotal'] * 0.1;
                    $dpp = $row3['Subtotal'] - $row3['Discount'];
                    $ppnbm = 0;
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue(
                            'A' . $rowNumber,
                            'OF,' .
                            $row3['ProductCD'] . ',' .
                            $row3['ProductName'] . ',' .  // Menambahkan ProductName
                            $row3['Price'] . ',' .
                            $row3['Quantity'] . ',' .
                            $row3['Subtotal'] . ',' .
                            $row3['Discount'] . ',' .
                            $dpp . ',' .
                            $ppn . ',' .
                            $ppnbm . ',' .
                            '0'
                        );
                    $rowNumber++;
                }
            } else {
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . $rowNumber, "No detail data found for invoice " . $row['FK']);
                $rowNumber++;
            }

            $stmt3->close();
        }
    } else {
        echo "<tr><td colspan='20'>No results found.</td></tr>";
    }

    $spreadsheet->getActiveSheet()->setTitle('Invoice');

    $writer = new Xlsx($spreadsheet);
    $filename = "Pajak_" . date('d/m/Y') . "_efaktur.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;

    $conn->close();
}

//FORMAT CORETAX
if (isset($_POST["btnSearch"]) && isset($_POST["formCoretax"])) {
    $startDate = $_POST['startdate'];
    $endDate = $_POST['enddate'];
    
    $workbook = new Spreadsheet(); //NEW EXCEL

    //CREATE SHEET FAKTUR
    $sheetFaktur = $workbook->getActiveSheet();
    $sheetFaktur->setTitle('Faktur'); 

    //NPWP PENJUAL
    $sheetFaktur->mergeCells('A1:B1');
    $sheetFaktur->setCellValue('A1','NPWP Penjual');
    $sheetFaktur->setCellValue('C1',"0315218073617000");

    //HEADER FAKTUR
    $sheetFaktur->setCellValue('A3','Baris');
    $sheetFaktur->getColumnDimension('A')->setWidth('20');
    $sheetFaktur->setCellValue('B3','Tanggal Faktur');
    $sheetFaktur->getColumnDimension('B')->setWidth('25');
    $sheetFaktur->setCellValue('C3','Jenis Faktur');
    $sheetFaktur->getColumnDimension('C')->setWidth('25');
    $sheetFaktur->setCellValue('D3','Kode Transaksi');
    $sheetFaktur->getColumnDimension('D')->setWidth('25');
    $sheetFaktur->setCellValue('E3','Keterangan Tambahan');
    $sheetFaktur->getColumnDimension('E')->setWidth('25');
    $sheetFaktur->setCellValue('F3','Dokumen Pendukung');
    $sheetFaktur->getColumnDimension('F')->setWidth('25');
    $sheetFaktur->setCellValue('G3','Periode Dok Pendukung');
    $sheetFaktur->getColumnDimension('G')->setWidth('25');
    $sheetFaktur->setCellValue('H3','Referensi');
    $sheetFaktur->getColumnDimension('H')->setWidth('25');
    $sheetFaktur->setCellValue('I3','Cap Fasilitas');
    $sheetFaktur->getColumnDimension('I')->setWidth('25');
    $sheetFaktur->setCellValue('J3','ID TKU Penjual');
    $sheetFaktur->getColumnDimension('J')->setWidth('25');
    $sheetFaktur->setCellValue('K3','NPWP/NIK Pembeli');
    $sheetFaktur->getColumnDimension('K')->setWidth('25');
    $sheetFaktur->setCellValue('L3','Jenis ID Pembeli');
    $sheetFaktur->getColumnDimension('L')->setWidth('25');
    $sheetFaktur->setCellValue('M3','Negara Pembeli');
    $sheetFaktur->getColumnDimension('M')->setWidth('25');
    $sheetFaktur->setCellValue('N3','Nomor Dokumen Pembeli');
    $sheetFaktur->getColumnDimension('N')->setWidth('25');
    $sheetFaktur->setCellValue('O3','Nama Pembeli');
    $sheetFaktur->getColumnDimension('O')->setWidth('25');
    $sheetFaktur->setCellValue('P3','Alamat Pembeli');
    $sheetFaktur->getColumnDimension('P')->setWidth('25');
    $sheetFaktur->setCellValue('Q3','Email Pembeli');
    $sheetFaktur->getColumnDimension('Q')->setWidth('25');
    $sheetFaktur->setCellValue('R3','ID TKU Pembeli');
    $sheetFaktur->getColumnDimension('R')->setWidth('25');

    //CREATE SHEET DETAIL FAKTUR
    $workbook->createSheet();
    $sheetDetail = $workbook->setActiveSheetIndex(1);
    $sheetDetail->setTitle('DetailFaktur');
    $sheetDetail->setCellValue('A1','Baris');
    $sheetDetail->getColumnDimension('A')->setWidth('20');
    $sheetDetail->setCellValue('B1','Barang/Jasa');
    $sheetDetail->getColumnDimension('B')->setWidth('25');
    $sheetDetail->setCellValue('C1','Kode Barang Jasa');
    $sheetDetail->getColumnDimension('C')->setWidth('25');
    $sheetDetail->setCellValue('D1','Nama Barang/Jasa');
    $sheetDetail->getColumnDimension('D')->setWidth('25');
    $sheetDetail->setCellValue('E1','Nama Satuan Ukur');
    $sheetDetail->getColumnDimension('E')->setWidth('25');
    $sheetDetail->setCellValue('F1','Harga Satuan');
    $sheetDetail->getColumnDimension('F')->setWidth('25');
    $sheetDetail->setCellValue('G1','Jumlah Barang Jasa');
    $sheetDetail->getColumnDimension('G')->setWidth('25');
    $sheetDetail->setCellValue('H1','Total Diskon');
    $sheetDetail->getColumnDimension('H')->setWidth('25');
    $sheetDetail->setCellValue('I1','DPP');
    $sheetDetail->getColumnDimension('I')->setWidth('25');
    $sheetDetail->setCellValue('J1','DPP Nilai Lain');
    $sheetDetail->getColumnDimension('J')->setWidth('25');
    $sheetDetail->setCellValue('K1','Tarif PPN');
    $sheetDetail->getColumnDimension('K')->setWidth('25');
    $sheetDetail->setCellValue('L1','PPN');
    $sheetDetail->getColumnDimension('L')->setWidth('25');
    $sheetDetail->setCellValue('M1','Tarif PPnBM');
    $sheetDetail->getColumnDimension('M')->setWidth('25');
    $sheetDetail->setCellValue('N1','PPnBM');
    $sheetDetail->getColumnDimension('N')->setWidth('25');
    
    //FILL HEADER FAKTUR
    $qHeader = "SELECT inv.InvoiceID, inv.CreatedOn, c.Email, c.NIK, c.KTPName, c.KTPAddress, c.NPWPName, c.NPWPNum, c.NPWPAddress, inv.TotalInvoice
                FROM invoiceheader inv, customer c 
                WHERE inv.CustID = c.CustID
                      AND inv.CreatedOn >= '$startDate' AND inv.CreatedOn <= '$endDate'
                      ORDER BY 2 ASC";
    $rHeader = mysqli_query($conn, $qHeader);
    
    $exRow = 4;
    $baris = 0;
    $barisd = 2;
    while ($rowHeader = mysqli_fetch_array($rHeader)) {
        $sheetFaktur = $workbook->setActiveSheetIndex(0);//SET SHEET AKTIF INDEX 0 -> FAKTUR
        $baris++;
        for($ctr=0;$ctr<18;$ctr++) {
            switch( $ctr ) {
                case '0': {
                    //Baris
                    $sheetFaktur->setCellValue('A' . $exRow, $baris . "");
                    break;
                }
                case '1': {
                    //Tanggal Faktur
                    $date = new DateTime(substr($rowHeader["CreatedOn"],0,10));
                    $sheetFaktur->setCellValue('B' . $exRow, $date->format('d/m/Y'));
                    break;
                }
                case '2': {
                    //Jenis Faktur
                    $sheetFaktur->setCellValue('C' . $exRow, 'Normal');
                    break;
                }
                case '3': {
                    //Kode Transaksi
                    $sheetFaktur->setCellValue('D' . $exRow, '04');
                    break;
                }
                case '4': {
                    //Keterangan Tambahan
                    break;
                }
                case '5': {
                    //Dokumen Pendukung
                    
                    break;
                }
                case '6': {
                    //Periode Dok Pendukung
                    
                    break;
                }
                case '7': {
                    //Referensi
                    $sheetFaktur->setCellValue('H' . $exRow, $rowHeader["InvoiceID"]);
                    break;
                }
                case '8': {
                    //Cap Fasilitas
                    break;
                }
                case '9': {
                    //ID TKU Penjual
                    $sheetFaktur->setCellValue('J' . $exRow, str_pad($NPWPSELLER,22,"0",STR_PAD_RIGHT));
                    break;
                }
                case '10': {
                    //NPWP/NIK Pembeli
                    if(isset($rowHeader["NPWPNum"]) && $rowHeader["NPWPNum"] != '-'){
                        //$temp = str_pad($rowHeader["NPWPNum"],17," ",STR_PAD_RIGHT);
                        $temp = $rowHeader["NPWPNum"];
                    }else if(isset($rowHeader["NIK"])){
                        $temp = "0000000000000000";
                    }
                    $sheetFaktur->setCellValue('K' . $exRow, $temp);
                    break;
                }
                case '11': {
                    //Jenis ID Pembeli
                    if(isset($rowHeader["NPWPNum"]) && $rowHeader["NPWPNum"] != '-'){
                        $temp = "TIN";
                    }else if(isset($rowHeader["NIK"])){
                        $temp = "National ID";
                    }
                    $sheetFaktur->setCellValue('L' . $exRow, $temp);
                    break;
                }
                case '12': {
                    //Negara Pembeli
                    $sheetFaktur->setCellValue('M' . $exRow, 'IDN');
                    break;
                }
                case '13': {
                    $temp = "-";
                    //Nomor Dokumen Pembeli
                    if($rowHeader["NIK"] != "" && $rowHeader["NIK"] != '-'){
                        //$temp = str_pad($rowHeader["NIK"],17," ",STR_PAD_RIGHT);
                        $temp = $rowHeader["NIK"] . "'";
                    }
                    $sheetFaktur->setCellValue('N' . $exRow, $temp);
                    break;
                }
                case '14': {
                    //Nama Pembeli (sesuai npwp/ktp)
                    if(isset($rowHeader["NPWPName"]) && $rowHeader["NPWPName"] != '-'){
                        $temp = str_replace('.','',$rowHeader["NPWPName"]);
                    }else if(isset($rowHeader["KTPName"])){
                        $temp = $rowHeader["KTPName"];
                    }
                    $sheetFaktur->setCellValue('O' . $exRow, $temp);
                    break;
                }
                case '15': {
                    //Alamat Pembeli
                    if(isset($rowHeader["NPWPAddress"]) && $rowHeader["NPWPAddress"] != '-'){
                        $temp = $rowHeader["NPWPAddress"];
                    }else if(isset($rowHeader["KTPAddress"])){
                        $temp = $rowHeader["KTPAddress"];
                    }
                    $sheetFaktur->setCellValue('P' . $exRow, $temp);
                    break;
                }
                case '16': {
                    //Email Pembeli
                    if(isset($rowHeader["Email"])){
                        $temp = $rowHeader["Email"];
                    }else{
                        $temp = "";
                    }
                    $sheetFaktur->setCellValue('Q' . $exRow, '-');
                    break;
                }
                case '17': {
                    //ID TKU Pembeli
                    if(isset($rowHeader["NPWPNum"]) && $rowHeader["NPWPNum"] != '-'){
                        $temp = str_pad($rowHeader["NPWPNum"],22,"0",STR_PAD_RIGHT);
                    }else if(isset($rowHeader["NIK"])){
                        $temp = "000000";
                    }
                    $sheetFaktur->setCellValue('R' . $exRow, $temp);
                    break;
                }
            }
        }
        $exRow++;

        //FILL HEADER DETAIL FAKTUR
        $sheetDetail = $workbook->setActiveSheetIndex(1);//SET SHEET AKTIF INDEX 1 -> DETAIL FAKTUR
        
        $qDetail = "SELECT inv.InvoiceID, inv.ProductCD, p.ProductName, inv.Price, inv.Quantity, inv.Discount
                    FROM invoicedetail inv, product p
                    WHERE inv.ProductCD = p.ProductCD
                          AND inv.InvoiceID = '".$rowHeader["InvoiceID"]."'";
        $rDetail = mysqli_query($conn, $qDetail);
        while ($rowDetail = mysqli_fetch_array($rDetail)) {
            for($ctr=0;$ctr<14;$ctr++) {
                switch( $ctr ) {
                    case '0': {
                        //Baris
                        $sheetDetail->setCellValue('A' . $barisd, strval($baris));
                        break;
                    }
                    case '1': {
                        //Baran/Jasa
                        $sheetDetail->setCellValue('B' . $barisd, 'A');
                        break;
                    }
                    case '2': {
                        //Kode Barang Jasa
                        $sheetDetail->setCellValue('C' . $barisd, '000000');
                        break;
                    }
                    case '3': {
                        //Nama Barang/Jasa
                        $sheetDetail->setCellValue('D' . $barisd, $rowDetail["ProductName"]);
                        break;
                    }
                    case '4': {
                        //Nama Satuan Ukur
                        $sheetDetail->setCellValue('E' . $barisd, 'UM.0018');
                        break;
                    }
                    case '5': {
                        //Harga Satuan
                        $price = $rowDetail["Price"]/1.11; //SEBELUM PPN
                        $sheetDetail->setCellValue('F' . $barisd, number_format($price, 2, '.', ''));
                        break;
                    }
                    case '6': {
                        //Jumlah Barang Jasa
                        $sheetDetail->setCellValue('G' . $barisd, $rowDetail["Quantity"]);
                        break;
                    }
                    case '7': {
                        //Total Diskon
                        $discPrice = $rowDetail["Discount"]/1.11; //SEBELUM PPN
                        $totDisc = $discPrice*$rowDetail["Quantity"];
                        $sheetDetail->setCellValue('H' . $barisd, number_format($totDisc, 2, '.', ''));
                        break;
                    }
                    case '8': {
                        //DPP
                        $DPP = ($price*$rowDetail["Quantity"])-$totDisc;
                        $sheetDetail->setCellValue('I' . $barisd, number_format($DPP, 2, '.', ''));
                        break;
                    }
                    case '9': {
                        //DPP Nilai Lain
                        $DPPLain = $DPP * 11 / 12;
                        $sheetDetail->setCellValue('J' . $barisd, number_format($DPPLain, 2, '.', ''));
                        break;
                    }
                    case '10': {
                        //Tarif PPN
                        $sheetDetail->setCellValue('K' . $barisd, '12');
                        break;
                    }
                    case '11': {
                        //PPN
                        $PPN = $DPP * 0.11;
                        $sheetDetail->setCellValue('L' . $barisd, number_format($PPN, 2, '.', ''));
                        break;
                    }
                    case '12': {
                        //Tarif PPnBM
                        $sheetDetail->setCellValue('M' . $barisd, '0');
                        break;
                    }
                    case '13': {
                        //PPnBM
                        $sheetDetail->setCellValue('N' . $barisd, '0');
                        break;
                    }
                }
            }
            $barisd++;
        }
    }

    //CREATE END DI HALAMAN DETAIL FAKTUR
    $sheetDetail = $workbook->setActiveSheetIndex(1);
    $sheetDetail->setCellValue('A' . $barisd,strval('END'));
    //$sheetDetail->getStyle('A' . $barisd)->getFont()->setBold(true);
    
    //CREATE END DI HALAMAN FAKTUR
    $sheetFaktur = $workbook->setActiveSheetIndex(0);
    $sheetFaktur->setCellValue('A' . $exRow,strval('END'));
    //$sheetFaktur->getStyle('A' . $exRow)->getFont()->setBold(true);


    $filename = "Pajak_" . date('d/m/Y') . "_coretax.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $writer = IOFactory::createWriter($workbook,'Xlsx');
    $writer->save('php://output');
    exit;
    
    $conn->close();
}

//FORMAT REPORT CORETAX
if (isset($_POST["btnSearch3"]) && isset($_POST["formCoretax"])) {
    $startDate = $_POST['startdate'];
    $endDate = $_POST['enddate'];

    $qHeader = "SELECT inv.InvoiceID, inv.CreatedOn, c.Email, c.NIK, c.KTPName, c.KTPAddress, c.NPWPName, c.NPWPNum, c.NPWPAddress, inv.TotalInvoice
                FROM invoiceheader inv, customer c 
                WHERE inv.CustID = c.CustID
                      AND inv.CreatedOn >= '$startDate' AND inv.CreatedOn <= '$endDate'
                      ORDER BY 2 ASC";
    $rHeader = mysqli_query($conn, $qHeader);

    //EXCEL LAPORAN INVOICE YANG DI EXPORT =======================================================
    
    $workbook2 = new Spreadsheet(); //NEW EXCEL

    //CREATE SHEET FAKTUR
    $sheetInvoice = $workbook2->getActiveSheet();
    $sheetInvoice->setTitle('Daftar_Invoice');
    $sheetInvoice->setCellValue('B2','No. Invoice');
    $sheetInvoice->getColumnDimension('B')->setWidth('25');
    $sheetInvoice->setCellValue('C2','Nama Pelanggan');
    $sheetInvoice->getColumnDimension('C')->setWidth('25');
    $sheetInvoice->setCellValue('D2','DPP');
    $sheetInvoice->getColumnDimension('D')->setWidth('25');
    $sheetInvoice->setCellValue('E2','PPN');
    $sheetInvoice->getColumnDimension('E')->setWidth('25');

    $ctrRow = 2;
    while ($rowHeader = mysqli_fetch_array($rHeader)) {
        $ctrRow++;
        for($ctr=0;$ctr<4;$ctr++) {
            switch( $ctr ) {
                case '0': {
                    //NO INVOICE
                    $sheetInvoice->setCellValue('B' . $ctrRow, $rowHeader["InvoiceID"]);
                    break;
                }
                case '1': {
                    //NAMA PELANGGAN
                    if($rowHeader["NPWPName"] != "-" && $rowHeader["NPWPName"] != ""){
                        $custName = $rowHeader["NPWPName"];
                    }else if($rowHeader["KTPName"] != "-" && $rowHeader["KTPName"] != ""){
                        $custName = $rowHeader["KTPName"];
                    }
                    $sheetInvoice->setCellValue('C' . $ctrRow, $custName);
                    break;
                }
                case '2': {
                    //DPP
                    $dpp = $rowHeader["TotalInvoice"] / 1.11;
                    $sheetInvoice->setCellValue('D' . $ctrRow, number_format($dpp, 0, ',', '.'));
                    break;
                }
                case '3': {
                    //PPN
                    $ppn = $rowHeader["TotalInvoice"] - $dpp;
                    $sheetInvoice->setCellValue('E' . $ctrRow, number_format($ppn, 0, ',', '.'));
                    break;
                }
            }
        }
    }
    
    $filename = "Report_" . date('d/m/Y') . "_coretax.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = IOFactory::createWriter($workbook2,'Xlsx');
    $writer->save('php://output');
    exit;
}
?>