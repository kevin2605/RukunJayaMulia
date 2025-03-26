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

//FORMAT CORETAX
if (isset($_POST["btnSearch2"]) && isset($_POST["formCoretax"])) {
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
    $qHeader = "SELECT d.DPID, d.CreatedOn, s.SalesOrderID, c.NIK, c.KTPName, c.KTPAddress, c.NPWPName, c.NPWPNum, c.NPWPAddress
                FROM downpaymentheader d, salesorderheader s, customer c 
                WHERE d.SalesOrderID = s.SalesOrderID
                      AND s.CustID = c.CustID
                      AND d.CreatedOn >= '$startDate' AND d.CreatedOn <= '$endDate'
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
                    $sheetFaktur->setCellValue('B' . $exRow, $date->format('m/d/Y'));
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
                    $sheetFaktur->setCellValue('H' . $exRow, $rowHeader["DPID"]);
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
                        $temp = str_pad($rowHeader["NPWPNum"],17," ",STR_PAD_RIGHT);
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
        
        $qDetail = "SELECT s.ProductCD, p.ProductName, s.Price, s.Quantity, s.Discount
                    FROM salesorderdetail s, product p
                    WHERE s.ProductCD = p.ProductCD
                          AND s.SalesOrderID = '".$rowHeader["SalesOrderID"]."'";
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


    $filename = "Pajak_DP_" . date('d/m/Y') . "_coretax.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = IOFactory::createWriter($workbook,'Xlsx');
    $writer->save('php://output');
    exit;
    
    $conn->close();
}

//FORMAT REPORT CORETAX
if (isset($_POST["btnSearch4"]) && isset($_POST["formCoretax"])) {
    $startDate = $_POST['startdate'];
    $endDate = $_POST['enddate'];

    $qHeader = "SELECT d.DPID, d.CreatedOn, s.SalesOrderID, c.NIK, c.KTPName, c.KTPAddress, c.NPWPName, c.NPWPNum, c.NPWPAddress, dd.Amount
                FROM downpaymentheader d, salesorderheader s, customer c, downpaymentdetail dd
                WHERE d.SalesOrderID = s.SalesOrderID
                      AND s.CustID = c.CustID
                      AND d.DPID = dd.DPID
                      AND d.CreatedOn >= '$startDate' AND d.CreatedOn <= '$endDate'
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
                    $sheetInvoice->setCellValue('B' . $ctrRow, $rowHeader["DPID"]);
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
                    $dpp = $rowHeader["Amount"] / 1.11;
                    $sheetInvoice->setCellValue('D' . $ctrRow, number_format($dpp, 0, ',', '.'));
                    break;
                }
                case '3': {
                    //PPN
                    $ppn = $rowHeader["Amount"] - $dpp;
                    $sheetInvoice->setCellValue('E' . $ctrRow, number_format($ppn, 0, ',', '.'));
                    break;
                }
            }
        }
    }
    
    $filename = "Report_DP_" . date('d/m/Y') . "_coretax.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = IOFactory::createWriter($workbook2,'Xlsx');
    $writer->save('php://output');
    exit;
}
?>