<?php
require('../fpdf186/rotation.php');
include "../DBConnection.php";

date_default_timezone_set("Asia/Jakarta");

ob_start();

if (isset($_GET['SlipNum'])) {
    $SlipNum = $_GET['SlipNum'];

    $queryHeader = "SELECT es.SlipNum, es.NIK, e.EmpFrontName, e.EmpLastName, e.Position, es.Periode, es.CreatedOn, es.PrintDate
                    FROM employee e, empsalaryheader es
                    WHERE e.NIK=es.NIK
                          AND es.SlipNum = '$SlipNum'";
    $resultHeader = mysqli_query($conn, $queryHeader);

    if (!$resultHeader || mysqli_num_rows($resultHeader) == 0) {
        die("Error: Data tidak ditemukan.");
    }
    $header = mysqli_fetch_array($resultHeader);

    $NIK = $header['NIK'] ?? '';
    $CreatedOn = $header['CreatedOn'] ?? '';
    $Periode = $header['Periode'] ?? '';
    $PrintDate = $header['PrintDate'] ?? '';

    $queryDetail = "SELECT s.ComponentType, s.ComponentName, es.ComponentValue, es.Multiplier
                    FROM empsalarydetail es, salarycomponent s
                    WHERE es.ComponentCode=s.ComponentCode
                          AND es.SlipNum = '$SlipNum'";

    $resultDetail = mysqli_query($conn, $queryDetail);

    // Inisialisasi FPDF
    $pdf = new PDF_Rotate();
    $pdf->AddPage('P','A4');

    $currentDate = date('d-m-Y');

    $pdf->Rotate(0);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetXY(10, 5);
    $pdf->Cell(0, 5, 'PT. INDOPACK MULTI PERKASA', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(100, 5, 'Pergudangan SAFE N LOCK, Blok K 1707 - 1708', 0, 1);
    $pdf->SetX(10);
    $pdf->Cell(100, 5, 'Telp : +623158259871 , Fax, +623158259872', 0, 1);

    $pdf->SetXY(10, 25);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(100, 5, 'No. Slip', 0, 1);
    $pdf->Cell(100, 5, 'Tanggal', 0, 1);
    $pdf->Cell(100, 5, 'Periode', 0, 1);

    $pdf->SetXY(25, 25);
    $pdf->Cell(0, 5, ': ' . $header['SlipNum'], 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 5, ': ' . $header['CreatedOn'], 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 5, ': ' . $header['Periode'], 0, 1);

    $pdf->SetXY(100, 5);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 5, 'SLIP GAJI KARYAWAN', 0, 1, 'L');
    
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetXY(100,25);
    $pdf->Cell(0, 5, 'NIK', 0, 1);
    $pdf->SetX(100);
    $pdf->Cell(0, 5, 'Nama Karyawan', 0, 1);
    $pdf->SetX(100);
    $pdf->Cell(0, 5, 'Jabatan', 0, 1);

    $pdf->SetXY(130, 25);
    $pdf->Cell(0, 5, ': ' . $header['NIK'], 0, 1);
    $pdf->SetX(130);
    $pdf->Cell(0, 5, ': ' . $header['EmpFrontName'] . " " . $header['EmpLastName'], 0, 1);
    $pdf->SetX(130);
    $pdf->Cell(0, 5, ': ' . $header['Position'], 0, 1);
    $pdf->Ln(7);
    $pdf->Cell(0, 5, 'DETAIL SLIP GAJI', 0, 1);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(10, 7, 'No', 'T,B', 0, 'C');
    $pdf->Cell(25, 7, 'Tipe', 'T,B', 0, 'L');
    $pdf->Cell(70, 7, 'Keterangan', 'T,B', 0, 'L');
    $pdf->Cell(30, 7, 'Keterangan Gaji', 'T,B', 0, 'L');
    $pdf->Cell(25, 7, 'Pendapatan', 'T,B', 0, 'L');
    $pdf->Cell(25, 7, 'Pengurangan', 'T,B', 0, 'L');
    $pdf->Ln();

    $ctr = 0;
    $pendapatan = 0;
    $potongan = 0;
    while($rowDetail = mysqli_fetch_array($resultDetail)){
        $ctr++;
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(10, 7, $ctr, 0, 0, 'C');
        $pdf->Cell(25, 7, $rowDetail["ComponentType"], 0, 0, 'L');
        $pdf->Cell(70, 7, $rowDetail["ComponentName"], 0, 0, 'L');
        $pdf->Cell(30, 7, $rowDetail["ComponentValue"] . " * " . $rowDetail["Multiplier"], 0, 0, 'L');

        //pendapatan atau potongan
        if($rowDetail["ComponentType"] == "PENDAPATAN"){
            $pendapatan += $rowDetail["ComponentValue"]*$rowDetail["Multiplier"];
            $pdf->Cell(25, 7, number_format($rowDetail["ComponentValue"]*$rowDetail["Multiplier"], 0, ',', '.'), 0, 0, 'L');
            $pdf->Cell(25, 7, '', 0, 0, 'L');
        }else if($rowDetail["ComponentType"] == "POTONGAN"){
            $potongan += $rowDetail["ComponentValue"]*$rowDetail["Multiplier"];
            $pdf->Cell(25, 7, '', 0, 0, 'L');
            $pdf->Cell(25, 7, number_format($rowDetail["ComponentValue"]*$rowDetail["Multiplier"], 0, ',', '.'), 0, 0, 'L');
        }
        $pdf->Ln();
    }


    $pdf->SetY(240);
    $pdf->Cell(0, 0, '', 'B');
    $pdf->SetX(145);
    $pdf->Cell(25, 7, number_format($pendapatan, 0, ',', '.'), 0, 0, 'L');
    $pdf->Cell(25, 7, number_format($potongan, 0, ',', '.'), 0, 0, 'L');
    $pdf->Ln();
    $pdf->SetY(255);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(130, 5, 'TAKE HOME PAY :', 0, 0, 'R');
    $pdf->Cell(59, 5, 'Rp. ' . number_format($pendapatan - $potongan, 0, ',', '.'), 0, 1, 'R');

    $pdf->Cell(130, 5, "", 0, 0, 'R');
    $pdf->Cell(59, 5, "KOMPLAIN GAJI MAKSIMAL 3x24 JAM", 0, 1, 'R');

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(10,255);
    $pdf->Cell(35, 6, 'Karyawan', 0, 0, 'C');
    $pdf->Cell(35, 6, 'Kasir', 0, 0, 'C');
    $pdf->SetXY(10,265);
    $pdf->Cell(35, 8, '(.......................)', 0, 0, 'C');
    $pdf->Cell(35, 8, '(.......................)', 0, 0, 'C');
} else {
    echo "Error: No. Slip Gaji tidak ditemukan.";
}

// Mengakhiri output buffer
ob_end_flush();

$pdf->Output('I', $header['SlipNum'] . '.pdf');
?>