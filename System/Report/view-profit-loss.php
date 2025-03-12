<!DOCTYPE html>
<html lang="en">
  <head>
    <?php 
      include "../headcontent.php"; 
      include "../DBConnection.php";
    ?>
  </head>
  <body> 
    <!-- loader starts-->
    <div class="loader-wrapper">
      <div class="theme-loader">    
        <div class="loader-p"></div>
      </div>
    </div>
    <!-- loader ends-->
    <!-- tap on top starts-->
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <!-- tap on tap ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
      <!-- Page Header Start-->
      <div class="page-header">
      
      <?php include "../topmenu.php"; ?>

      </div>
      <!-- Page Header Ends-->
      <!-- Page Body Start-->
      <div class="page-body-wrapper">
        <!-- Page Sidebar Start-->

        <?php include "../sidemenu.php"; ?>

        <!-- Page Sidebar Ends-->
        <div class="page-body">
          <div class="container-fluid">
            <div class="page-title">
              <div class="row">
                <div class="col-sm-6 ps-0">
                  <h3>LABA DAN RUGI</h3>
                </div>
                <div class="col-sm-6 pe-0">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">                                       
                        <svg class="stroke-icon">
                          <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                        </svg></a></li>
                    <li class="breadcrumb-item">Report</li>
                    <li class="breadcrumb-item">Keuangan</li>
                    <li class="breadcrumb-item">Laba Rugi</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid starts-->
          <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <?php
                        //PERHITUNGAN
                        $array = array();
                        $ctr = 0;
                        $startdate = $_POST["startdate"];
                        $enddate = $_POST["enddate"];
                        $query = "SELECT AccountCD,SUM(Debit) AS DEBIT,SUM(Credit) AS CREDIT FROM journaldata WHERE JournalDate >= '$startdate' AND JournalDate <= '$enddate' GROUP BY 1";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_array($result)) {
                            $array[$ctr][0] = $row["AccountCD"];
                            $array[$ctr][1] = $row["DEBIT"];
                            $array[$ctr][2] = $row["CREDIT"];
                            $ctr++;
                        }
                    ?>
                    <div class="card">
                        <div class="card-body">
                            <!-- PENDAPATAN -->
                            <div class="row"><b>PENDAPATAN</b></div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspPenjualan</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $penjualan = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '4-1000'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $penjualan = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $penjualan = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=4-1000&account_name=Penjualan&startdate=$startdate&enddate=$enddate'>" . number_format($penjualan,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspRetur & Potongan Pendapatan</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $retur = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '4-2000'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $retur = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $retur = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=4-2000&account_name=Retur dan Potongan Pendapatan&startdate=$startdate&enddate=$enddate'>" . number_format($retur,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">--------------------------------------------------------------------------------------------------------</div>
                            </div>
                            <!-- HARGA POKOK -->
                            <div class="row"><b>HARGA POKOK</b></div>
                            <!-- BI. BAHAN BAKU -->
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi. Bahan Baku</div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspPersediaan Awal Bahan</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php
                                        $query = "SELECT Pers_Akhir_Bahan FROM datapersediaan WHERE Tanggal= '$startdate'";
                                        $result = mysqli_query($conn, $query);
                                        $row = mysqli_fetch_assoc($result);
                                        $persawalbahan = $row["Pers_Akhir_Bahan"];
                                        echo number_format($persawalbahan, 0, ',', '.');
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspPembelian Bahan</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $pembelian = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '5-1100'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $pembelian = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $pembelian = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=5-1100&account_name=Pembelian Bahan&startdate=$startdate&enddate=$enddate'>" . number_format($pembelian,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspPersediaan Akhir Bahan</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php
                                        $query = "SELECT Pers_Akhir_Bahan FROM datapersediaan WHERE Tanggal= '$enddate'";
                                        $result = mysqli_query($conn, $query);
                                        $row = mysqli_fetch_assoc($result);
                                        $persakhirbahan = $row["Pers_Akhir_Bahan"];
                                        echo number_format($persakhirbahan, 0, ',', '.');
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">--------------------------------------------------------------------------------------------------------</div>
                            </div>
                            <!-- BI. UPANG LANGSUNG -->
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi. Upah Langsung</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $upahlangsung = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '5-2000'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $upahlangsung = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $upahlangsung = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=5-2000&account_name=Bi. Upah Langsung&startdate=$startdate&enddate=$enddate'>" . number_format($upahlangsung,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi Pabrikasi & Penyusutan Mesin</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $pabrikasi = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '5-3700'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $pabrikasi = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $pabrikasi = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=5-2000&account_name=Bi Pabrikasi dan Penyusutan Mesin&startdate=$startdate&enddate=$enddate'>" . number_format($pabrikasi,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">--------------------------------------------------------------------------------------------------------</div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbsp(+) Persd Awal Brg Dlm Proses</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php
                                        $query = "SELECT Pers_Akhir_Brg_Dlm_Proses FROM datapersediaan WHERE Tanggal= '$startdate'";
                                        $result = mysqli_query($conn, $query);
                                        $row = mysqli_fetch_assoc($result);
                                        $persawalbrgdlmproses = $row["Pers_Akhir_Brg_Dlm_Proses"];
                                        echo number_format($persawalbrgdlmproses, 0, ',', '.');
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbsp(-) Persd Akhir Brg Dlm Proses</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php
                                        $query = "SELECT Pers_Akhir_Brg_Dlm_Proses FROM datapersediaan WHERE Tanggal= '$enddate'";
                                        $result = mysqli_query($conn, $query);
                                        $row = mysqli_fetch_assoc($result);
                                        $persakhirbrgdlmproses = $row["Pers_Akhir_Brg_Dlm_Proses"];
                                        echo number_format($persakhirbrgdlmproses, 0, ',', '.');
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbsp(+) Persd Awal Brg Jadi</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php
                                        $query = "SELECT Pers_Akhir_Brg_Jadi FROM datapersediaan WHERE Tanggal= '$startdate'";
                                        $result = mysqli_query($conn, $query);
                                        $row = mysqli_fetch_assoc($result);
                                        $persawalbrgjadi = $row["Pers_Akhir_Brg_Jadi"];
                                        echo number_format($persawalbrgjadi, 0, ',', '.') ;
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbsp(-) Persd Akhir Brg Jadi</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php
                                        $query = "SELECT Pers_Akhir_Brg_Jadi FROM datapersediaan WHERE Tanggal= '$enddate'";
                                        $result = mysqli_query($conn, $query);
                                        $row = mysqli_fetch_assoc($result);
                                        $persakhirbrgjadi = $row["Pers_Akhir_Brg_Jadi"];
                                        echo number_format($persakhirbrgjadi, 0, ',', '.');
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">--------------------------------------------------------------------------------------------------------</div>
                            </div>
                            <!-- BIAYA OPERASI -->
                            <div class="row">
                                <div class="col-3"><b>BIAYA OPERASI :</b></div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Penjualan :</b></div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Bongkar/Muat</b></div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $bongkarmuat = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '6-1100'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $bongkarmuat = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $bongkarmuat = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=6-1100&account_name=Bi.Bongkar/Muat&startdate=$startdate&enddate=$enddate'>" . number_format($bongkarmuat,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Kendaraan Niaga</b></div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $kendaraanniaga = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '6-1200'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $kendaraanniaga = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $kendaraanniaga = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=6-1200'&account_name=Bi.Kendaraan Niaga&startdate=$startdate&enddate=$enddate'>" . number_format($kendaraanniaga,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Penyusutan Kendaaran Niaga</b></div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $penyusutankendaraanniaga = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '6-1210'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $penyusutankendaraanniaga = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $penyusutankendaraanniaga = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=6-1210&account_name=Bi.Penyusutan Kendaaran Niaga&startdate=$startdate&enddate=$enddate'>" . number_format($penyusutankendaraanniaga,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Exim</b></div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $exim = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '6-1600'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $exim = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $exim = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=6-1600&account_name=Bi.Exim&startdate=$startdate&enddate=$enddate'>" . number_format($exim,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Operasional Penjualan</b></div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $operasionalpenjualan = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '6-1400'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $operasionalpenjualan = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $operasionalpenjualan = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=6-1400&account_name=Bi.Operasional Penjualan&startdate=$startdate&enddate=$enddate'>" . number_format($operasionalpenjualan,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Promosi</b></div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $promosi = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '6-1500'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $promosi = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $promosi = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=6-1500&account_name=Bi.Promosi&startdate=$startdate&enddate=$enddate'>" . number_format($promosi,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">--------------------------------------------------------------------------------------------------------</div>
                            </div>
                            <!-- Bi.Umum & Administrasi -->
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Umum & Administrasi :</b></div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Upah Karyawan</b></div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $upahdanthr = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '6-2110'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $upahdanthr = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $upahdanthr = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=6-2110&account_name=Bi.Upah Karyawan&startdate=$startdate&enddate=$enddate'>" . number_format($upahdanthr,0,',','.')."</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Tunjangan</b></div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $tunjangan = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '6-2120'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $tunjangan = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $tunjangan = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=6-2120&account_name=Bi.Tunjangan&startdate=$startdate&enddate=$enddate'>" . number_format($tunjangan,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.ATK/Percetakan</b></div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $atkdanpercetakan = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '6-2200'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $atkdanpercetakan = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $atkdanpercetakan = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=6-2120&account_name=Bi.Tunjangan&startdate=$startdate&enddate=$enddate'>" . number_format($atkdanpercetakan,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Listrik/Telepon</b></div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $listrikdantelepon = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '6-2400'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $listrikdantelepon = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $listrikdantelepon = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=6-2400&account_name=Bi.Listrik/Telepon&startdate=$startdate&enddate=$enddate'>" . number_format($listrikdantelepon,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.luran/Retribusi</b></div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $iurandanretribusi = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '6-2600'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $iurandanretribusi = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $iurandanretribusi = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=6-2600&account_name=Bi.luran/Retribusi&startdate=$startdate&enddate=$enddate'>" . number_format($iurandanretribusi,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Makan/Minum</b></div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $makandanminum = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '6-2720'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $makandanminum = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $makandanminum = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=6-2720&account_name=Bi.Makan/Minum&startdate=$startdate&enddate=$enddate'>" . number_format($makandanminum,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Entertain/Sumbangan</b></div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $entertainatausumbangan = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '6-2730'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $entertainatausumbangan = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $entertainatausumbangan = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=6-2730&account_name=Bi.Entertain/Sumbangan&startdate=$startdate&enddate=$enddate'>" . number_format($entertainatausumbangan,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Keperluan Kantor</b></div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $keperluankantor = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '6-2710'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $keperluankantor = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $keperluankantor = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=6-2710&account_name=Bi.Keperluan Kantor&startdate=$startdate&enddate=$enddate'>" . number_format($keperluankantor,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Pnyst Mebel & Perlkp Kantor</b></div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $penyusutanmebel = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '6-2910'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $penyusutanmebel = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $penyusutanmebel = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=6-2910&account_name=Bi.Pnyst Mebel dan Perlkp Kantor&startdate=$startdate&enddate=$enddate'>" . number_format($penyusutanmebel,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">--------------------------------------------------------------------------------------------------------</div>
                            </div>
                            <!-- BIAYA/PENDAPATAN NON OPERASI -->
                            <div class="row">
                                <div class="col-3">BIAYA/PENDAPATAN NON OPERASI</div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Administrasi/Provisi Bank</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $adminatauprovisi = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '9-1000'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $adminatauprovisi = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $adminatauprovisi = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=9-1000&account_name=Bi.Administrasi/Provisi Bank&startdate=$startdate&enddate=$enddate'>" . number_format($adminatauprovisi,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Bunga Pinjaman Bank</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $bungapinjamanbank = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '9-4100'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $bungapinjamanbank = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $bungapinjamanbank = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=9-4100&account_name=Bi.Bunga Pinjaman Bank&startdate=$startdate&enddate=$enddate'>" . number_format($bungapinjamanbank,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.PPh Bunga Jasa Giro</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $pphbungajasagiro = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '9-6100'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $pphbungajasagiro = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $pphbungajasagiro = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=9-6100&account_name=Bi.PPh Bunga Jasa Giro&startdate=$startdate&enddate=$enddate'>" . number_format($pphbungajasagiro,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.PPh Pihak Lain Yg Ditanggung</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $pphpihaklain = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '9-5000'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $pphpihaklain = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $pphpihaklain = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=9-5000&account_name=Bi.PPh Pihak Lain Yg Ditanggung&startdate=$startdate&enddate=$enddate'>" . number_format($pphpihaklain,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Sewa</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $sewa = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '9-2000'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $sewa = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $sewa = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=9-2000&account_name=Bi.Sewa&startdate=$startdate&enddate=$enddate'>" . number_format($sewa,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspBi.Asuransi</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $asuransi = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '9-3000'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $asuransi = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $asuransi = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=9-3000&account_name=Bi.Asuransi&startdate=$startdate&enddate=$enddate'>" . number_format($asuransi,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspPembulatan</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $pembulatan = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '9-9000'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $pembulatan = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $pembulatan = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=9-3000&account_name=Bi.Asuransi&startdate=$startdate&enddate=$enddate'>" . number_format($pembulatan,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspPendapatan Jasa Giro</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $pendapatanjasagiro = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '8-1500'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $pendapatanjasagiro = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $pendapatanjasagiro = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=8-1500&account_name=Pendapatan Jasa Giro&startdate=$startdate&enddate=$enddate'>" . number_format($pendapatanjasagiro,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">&nbsp&nbsp&nbspPendapatan Rupa-2</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">
                                    <?php 
                                        $pendapatanruparupa = 0;
                                        for($i=0;$i<$ctr;$i++){
                                            if($array[$i][0]== '8-2000'){
                                                if(isset($array[$i][1]) || isset($array[$i][2])){
                                                    $pendapatanruparupa = $array[$i][1] - $array[$i][2];
                                                }else{
                                                    $pendapatanruparupa = 0;
                                                }
                                                echo "<a href='detail-account.php?account_id=8-2000&account_name=Pendapatan Rupa-2&startdate=$startdate&enddate=$enddate'>" . number_format($pendapatanruparupa,0,',','.') . "</a>";
                                                break;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <!-- LABA BERSIH SBLM PPh (komersial) -->
                            <!--
                            <div class="row">
                                <div class="col-3">LABA BERSIH SBLM PPh (komersial)</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">53.723.337</div>
                            </div>
                            -->
                            <!-- Koreksi Fiskal Positip -->
                            <!--
                            <div class="row">
                                <div class="col-3"><b>Koreksi Fiskal Positip</b></div>
                            </div>
                            <div class="row">
                                <div class="col-5">1) luran BPJS yg seluruhnya ditanggung perusahaan</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">0</div>
                            </div>
                            <div class="row">
                                <div class="col-5">2) Bi.makan-minum bersifat natura (non deductible)</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">0</div>
                            </div>
                            <div class="row">
                                <div class="col-5">3) Tidak ada daftar nominatif</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">0</div>
                            </div>
                            <div class="row">
                                <div class="col-5">4) Bi.PPh dari bunga jasa giro (non deductible)</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">0</div>
                            </div>
                            <div class="row">
                                <div class="col-5">5) PPh Pihak Lain yg Ditanggung (Idk diakui sbg penghasilan ybs)</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">0</div>
                            </div>
                            -->
                            <!-- Koreksi Fiskal Negatip -->
                            <!--
                            <div class="row">
                                <div class="col-3"><b>Koreksi Fiskal Negatip</b></div>
                            </div>
                            <div class="row">
                                <div class="col-5">6) Pendapatan bunga rekening koran bank (telah dipotong PPh bersifat final)</div>
                                <div class="col-1 text-end">Rp</div>
                                <div class="col-1 text-end">0</div>
                            </div>
                            -->
                            <hr>
                            <a class="btn btn-secondary" href="../Report/report-laba-rugi.php">Back</a>
                        </div>
                    </div>
                </div>
            </div>
          </div>
          <!-- Container-fluid Ends-->
        </div>
        <!-- footer start-->
        <footer class="footer">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-6 p-0 footer-copyright">
                <p class="mb-0">Copyright 2023 © Dunzo theme by pixelstrap.</p>
              </div>
              <div class="col-md-6 p-0">
                <p class="heart mb-0">Hand crafted &amp; made with
                  <svg class="footer-icon">
                    <use href="../../assets/svg/icon-sprite.svg#heart"></use>
                  </svg>
                </p>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </div>
    <!-- latest jquery-->
    <script src="../../assets/js/jquery.min.js"></script>
    <!-- Bootstrap js-->
    <script src="../../assets/js/bootstrap/bootstrap.bundle.min.js"></script>
    <!-- feather icon js-->
    <script src="../../assets/js/icons/feather-icon/feather.min.js"></script>
    <script src="../../assets/js/icons/feather-icon/feather-icon.js"></script>
    <!-- scrollbar js-->
    <script src="../../assets/js/scrollbar/simplebar.js"></script>
    <script src="../../assets/js/scrollbar/custom.js"></script>
    <!-- Sidebar jquery-->
    <script src="../../assets/js/config.js"></script>
    <!-- Plugins JS start-->
    <script src="../../assets/js/sidebar-menu.js"></script>
    <script src="../../assets/js/sidebar-pin.js"></script>
    <script src="../../assets/js/slick/slick.min.js"></script>
    <script src="../../assets/js/slick/slick.js"></script>
    <script src="../../assets/js/header-slick.js"></script>
    <script src="../../assets/js/form-validation-custom.js"></script>
    <script src="../../assets/js/height-equal.js"></script>
    <script src="../../assets/js/notify/bootstrap-notify.min.js"></script>
    <script src="../../assets/js/datatable/datatables/jquery.dataTables.min.js"></script>
    <script src="../../assets/js/datatable/datatables/datatable.custom.js"></script>
    <script src="../../assets/js/tooltip-init.js"></script>
    <script src="../../assets/js/modalpage/validation-modal.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="../../assets/js/script.js"></script>
    <!-- Plugin used-->
  </body>
</html>