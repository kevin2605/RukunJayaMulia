<?php

include "DBConnection.php";

// Set timezone
date_default_timezone_set("Asia/Jakarta");

$date = date('Y-m-d');

//persediaan akhir bahan
$query = "SELECT SUM(StockQty*AvgPrice) AS SupplyAkhirBahan FROM material";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$pers_akhir_bahan = $row["SupplyAkhirBahan"];
echo "Persediaan Akhir Bahan : Rp ". number_format($pers_akhir_bahan, 2, '.', ',') . "<br>";


//persediaan akhir bahan dalam proses
$query = "SELECT SUM((p.MaterialOut*m.AvgPrice)*(1-((p.ExactOutcome+p.ProdLoss)/p.EstimateOutcome))) AS SupplyAkhirBrgProses
FROM productionorder p, material m
WHERE p.MaterialCD = m.MaterialCD";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$pers_akhir_brg_dlm_proses = $row["SupplyAkhirBrgProses"];
echo "Persediaan Akhir Brg Dlm Proses : Rp ". number_format($pers_akhir_brg_dlm_proses, 2, '.', ',') . "<br>";

//persediaan akhir barang jadi
$query = "SELECT SUM(StockQty*ModalPrice) AS SupplyAkhirBarangJadi FROM product";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$pers_akhir_brg_jadi = $row["SupplyAkhirBarangJadi"];
echo "Persediaan Akhir Brg Jadi : Rp ". number_format($pers_akhir_brg_jadi, 2, '.', ',') . "<br>";

//persediaan barang penunjang produksi
$query = "SELECT SUM(StockQty*AvgPrice) AS nilaiBPP FROM supportinggoods";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$nilaiBPP = $row["nilaiBPP"];
echo "Persediaan Barang Penunjang Produksi : Rp ". number_format($nilaiBPP, 2, '.', ',') . "<br>";

//persediaan barang sparepart
$query = "SELECT SUM(StockQty*BuyPrice) AS nilaiSP FROM sparepart";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$nilaiSP = $row["nilaiSP"];
echo "Persediaan Sparepart : Rp ". number_format($nilaiSP, 2, '.', ',') . "<br>";

//penjumlahan bpp dan sp
$pers_brg_ppsp = $nilaiBPP + $nilaiSP;

$query = "INSERT INTO `datapersediaan`(`Tanggal`, `Pers_Akhir_Bahan`, `Pers_Akhir_Brg_Dlm_Proses`, `Pers_Akhir_Brg_Jadi`, `Pers_Brg_PPSP`)
          VALUES ('$date','$pers_akhir_bahan','$pers_akhir_brg_dlm_proses','$pers_akhir_brg_jadi','$pers_brg_ppsp')";
$result = mysqli_query($conn, $query);

?>