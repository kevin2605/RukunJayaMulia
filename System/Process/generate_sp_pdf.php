<?php

require('../fpdf186/rotation.php');
include "../DBConnection.php";

date_default_timezone_set("Asia/Jakarta");

if (isset($_GET['SalesOrderID'])) {
    $SalesOrderID = $_GET['SalesOrderID'];
    $queryHeader = "SELECT * FROM salesorderheader WHERE SalesOrderID = '$SalesOrderID'";
    $resultHeader = mysqli_query($conn, $queryHeader);


    if (!$resultHeader || mysqli_num_rows($resultHeader) == 0) {
        die("Error: Data Sales Order tidak ditemukan.");
    }
    $header = mysqli_fetch_array($resultHeader);

    $queryDetail = "
    SELECT sod.ProductCD, p.ProductName, sod.Quantity, sod.Price, sod.Discount, sod.QuantitySent FROM (salesorderdetail sod JOIN product p ON sod.ProductCD=p.ProductCD) WHERE SalesOrderID = '$SalesOrderID'";
    $resultDetail = mysqli_query($conn, $queryDetail);

    $querySO = "SELECT soh.SalesOrderID, soh.CreatedOn, soh.CreatedBy, soh.Marketing, soh.Description, soh.Approval, soh.ApprovalStatus, soh.ApprovalBy, soh.ApprovalOn, soh.Logo, c.CustID, c.CustName, c.ShipmentAddress, c.NPWPNum, c.PhoneNumOne,
    c.Email, soh.Finish FROM (salesorderheader soh JOIN customer c ON soh.CustID=c.CustID) WHERE SalesOrderID= '$SalesOrderID'";
    $resultSO = mysqli_query($conn, $querySO);
    $pdf = new PDF_Rotate();
    $pdf->AddPage('P', 'A4');

    // $finishStatus = $header['Status'];
    // $watermarkText = '';

    // if ($finishStatus == 1) {
    //     $watermarkText = 'COMPLETE';

    //     $pdf->SetFont('Arial', '', 10);
    //     $pdf->ln(80);
    //     $pdf->SetX(10);
    //     $pdf->Cell(70, 5, 'Hasil Produksi          : ' . $resultData['total_hasil'], 0, 1, 'L'); // Baris 1
    //     $pdf->SetX(10);
    //     $pdf->Cell(55, 5, 'Kerusakan Produksi : ' . $resultData['total_rusak'], 0, 1, 'L'); // Baris 2
    // } elseif ($finishStatus == 0) {
    //     $watermarkText = '';
    // }

    $pdf->SetFont('Arial', 'B', 70);
    $pdf->SetTextColor(230, 230, 230);
    $pdf->Rotate(45, 105, 200);
    $pdf->SetXY(0, 1);
    // $pdf->Text(130, 120, $watermarkText);
    $pdf->Rotate(0);
    $currentDate = date('d-m-Y');
    $pdf->Rotate(0);

    $imagePath = '../../assets/images/logo.jpg';
    $pdf->Image($imagePath, 10, 5, 60, 40);

    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(80, 15);
    // $pdf->Cell(0, 10, 'PT. INDOPACK MULTI PERKASA', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetXY(90, 10);
    $pdf->Cell(100, 6, 'Pergudangan SAFE N LOCK, Blok K 1707 - 1708', 0, 1, 'C');
    $pdf->SetX(90);
    $pdf->Cell(100, 6, 'Jl Lingkar timur KM 5,5', 0, 1, 'C');
    $pdf->SetX(90);
    $pdf->Cell(100, 6, 'Telp : +623158259871 , Fax, +623158259872', 0, 1, 'C');
    $pdf->SetX(90);
    $pdf->Cell(100, 6, 'Wechat / Skype / Line : papercupindonesia', 0, 1, 'C');
    $pdf->SetX(90);
    $pdf->Cell(100, 6, 'Sidoarjo - Indonesia', 0, 1, 'C');

    $pdf->SetLineWidth(0.5);
    $pdf->Cell(185, 5, '', 'B', 10, 'R');
    $pdf->SetLineWidth(0.2);
    $pdf->MultiCell(100, 7, '', '');

    $pdf->SetXY(10, 50);
    $pdf->SetFont('Arial', '', 10);
    $rowso = mysqli_fetch_assoc($resultSO);
    $pdf->SetX(10);
    $pdf->Cell(0, 5, 'Kepada Yth, ', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(0, 5, 'Bapak/Ibu', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(0, 5, 'Alamat', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(0, 5, 'NPWP/Nik', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(0, 5, 'No. HP', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(0, 5, 'Logo', 0, 1);
    
    $pdf->SetXY(20, 55);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetX(40);
    $pdf->Cell(0, 5, ': ' . $rowso['CustName'], 0, 1);
    $pdf->SetX(40);
    $pdf->Cell(0, 5, ': ' . $rowso['ShipmentAddress'], 0, 1);
    $pdf->SetX(40);
    $pdf->Cell(0, 5, ': ' . $rowso['NPWPNum'], 0, 1);
    $pdf->SetX(40);
    $pdf->Cell(0, 5, ': ' . $rowso['PhoneNumOne'], 0, 1);
    $pdf->SetX(40);
    $pdf->Cell(0, 5, ': ' . $rowso['Logo'], 0, 1);
    $pdf->Ln(2);
    $pdf->SetX(10);
    $pdf->Cell(29, 5, 'Berikut Perincian Pemesanan        ', 0, 0);
    $pdf->SetX(42);

    $pdf->Ln(6);

    $pdf->SetFont('Arial', 'B', 10);
    // $pdf->Cell(25, 10, 'K. Produksi', 'T,B,R,L', 0, 'C');
    $pdf->Cell(100, 10, 'Keterangan', 'T,B,R,L', 0, 'L');
    $pdf->Cell(25, 10, 'Jumlah', 'T,B,R,L', 0, 'R');
    $pdf->Cell(30, 10, 'Harga (Inc PPN)', 'T,B,R,L', 0, 'R');
    $pdf->Cell(30, 10, 'Total', 'T,B,R,L', 1, 'R');

    $pdf->SetFont('Arial', '', 10);
    $lineHeight = 5;
    $extraSpacing = 0;

    $pdf->SetFont('Arial', '', 10);
    $no = "-";
    $lineHeight = 5;
    $totalNet = 0;

    while ($row = mysqli_fetch_array($resultDetail)) {
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();

        $pdf->SetXY($startX + 0, $startY);
        $pdf->MultiCell(100, $lineHeight, $row['ProductName'], 1, 'L');
        $namaProdukHeight = $pdf->GetY() - $startY;
        $maxCellHeight = max($namaProdukHeight, $lineHeight);

        $price = $row['Price'];
        $quantity = $row['Quantity'];
        $discount = $row['Discount'];
        $nettPrice = $price - $discount;
        $totalPrice = ($price - $discount) * $quantity;
        $totalNet += $totalPrice;

        $pdf->SetXY($startX, $startY);
        // $pdf->Cell(25, $maxCellHeight, $no++, 1, 0, 'C');
        $pdf->SetXY($startX + 0, $startY);
        $pdf->MultiCell(100, $lineHeight, $row['ProductName'], 1, 'L');

        $pdf->SetXY($startX + 100, $startY);
        $keluarValue = ($quantity == 0) ? '-' : $quantity;
        $pdf->Cell(25, $maxCellHeight, number_format($keluarValue, 0, ',', '.'), 1, 0, 'R');

        $pdf->SetXY($startX + 125, $startY);
        $pdf->Cell(30, $maxCellHeight, number_format($nettPrice, 0, ',', '.'), 1, 0, 'R');

        $pdf->SetXY($startX + 155, $startY);
        $pdf->Cell(30, $maxCellHeight, number_format($totalPrice, 0, ',', '.'), 1, 0, 'R');

        $pdf->SetY($startY + $maxCellHeight);
    }

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(155, 10, '', 0, 0, 'R');
    $pdf->Cell(30, 10, number_format($totalNet, 0, ',', '.'), 1, 0, 'R');

    $pdf->SetFont('Arial', '', 10);

    // $pdf->Cell(185, 10, '', 'B', 1, 'R');
    $pdf->Ln(20);
    $pdf->SetX(10);
    $pdf->Cell(0, 5, 'Syarat dan Kondisi : ', 0, 1);
    $pdf->Cell(0, 5, '1. Harga di atas sudah include ppn ', 0, 1);
    $pdf->Cell(0, 5, '2. DP 50% dari total surat pemesanan. Sebesar : Rp ' . number_format($totalNet * 0.5, 0, ',', '.'), 0, 1);
    $pdf->Cell(0, 5, '3. Pembayaran Pelunasan pada saat barang akan dikirim ', 0, 1);
    $pdf->Cell(0, 5, '4. Lama Pengerjaan -+ 45 hari kerja dihitung dariantara ACC design atau ', 0, 1);
    $pdf->SetX(14);
    $pdf->Cell(0, 5, 'pembayaran DP yang mana terakhir yang kita terima ', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(0, 5, '5. Pembayaran DP dan pelunasan ke rekening : ', 0, 1);
    $pdf->SetX(14);
    $pdf->Cell(0, 5, 'BCA cabang Galaxy', 0, 1);
    $pdf->SetX(14);
    $pdf->Cell(0, 5, 'a.n : PT INDOPACK MULTI PERKASA', 0, 1);
    $pdf->SetX(14);
    $pdf->Cell(0, 5, 'Nomer : 7880-58-7271', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(0, 5, '6. Toleransi produksi -+ 10%. dari jumlah pesanan ', 0, 1);
    $pdf->Cell(0, 5, '7. Transfer DP akan dipakai sebagai bukti persetujuan surat pesanan diatas, maka', 0, 1);
    $pdf->SetX(14);
    $pdf->Cell(0, 5, 'tidak diperlukan tanda tangan dari kedua belah pihak, Untuk transfer harap disertakan Nomer SP', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(0, 5, '8. Biaya pengiriman free untuk wilayah Jawa timur dan jawa tengah', 0, 1);
    $pdf->Cell(0, 5, '9. Harga penawaran berlaku selama 1 minggu setelah surat penawaran dikirim', 0, 1);
    $pdf->Ln(25);
    $pdf->SetX(120);
    $pdf->Cell(0, 5, 'Hormat Kami,', 0, 1, 'C');
    $pdf->Ln(15);
    $pdf->SetX(120);
    $pdf->Cell(0, 5, 'PT. INDOPACK MULTI PERKASA', 0, 1, 'C');


    $pdf->Output('I', 'SPK_' . ($header['SalesOrderID'] ?? 'Unknown') . '.pdf');

    // $updatePrintCount = "UPDATE receptioninvoiceheader SET PrintCount = PrintCount + 1 WHERE RCV_InvoiceID = '$RCV_InvoiceID'";
    // mysqli_query($conn, $updatePrintCount);

} else {
    echo "Error: ID tidak ditemukan.";
}

ob_end_flush();
?>