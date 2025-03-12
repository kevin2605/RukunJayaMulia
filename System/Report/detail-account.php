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
                  <h3>DETAIL KODE AKUN</h3>
                </div>
                <div class="col-sm-6 pe-0">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">                                       
                        <svg class="stroke-icon">
                          <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                        </svg></a></li>
                    <li class="breadcrumb-item">Report</li>
                    <li class="breadcrumb-item">Detil Kode Akun</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid starts-->
          <div class="container-fluid">
            <div class="row">
            <div class="col-sm-12">
                <div class="card">
                  <div class="card-header">
                    <h3><?php echo $_GET["account_name"]; ?></h3>
                    <span>
                        Kode Akun : <?php echo $_GET["account_id"]; ?> | Periode : <?php echo $_GET["startdate"] . " to " . $_GET["enddate"]; ?>
                    </span>
                  </div>
                  <?php 
                    if($_GET["account_id"] == "4-1000"){ //PENJUALAN
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '4-1000' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "4-2000"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '4-2000' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "5-1100"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '5-1100' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "5-2000"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '5-2000' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "5-3700"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '5-3700' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "6-1100"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '6-1100' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "6-1200"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '6-1200' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "6-1600"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '6-1600' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "6-1400"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '6-1400' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "6-1500"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '6-1500' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "6-2110"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '6-2110' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "6-2120"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '6-2120' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "6-2200"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '6-2200' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "6-2400"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '6-2400' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "6-2600"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '6-2600' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "6-2720"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '6-2720' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "6-2730"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '6-2730' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "6-2710"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '6-2710' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "6-2910"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '6-2910' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "9-1000"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '9-1000' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "9-6100"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '9-6100' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "9-5000"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '9-5000' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "9-2000"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '9-2000' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "9-3000"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '9-3000' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "9-9000"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '9-9000' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "8-1500"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '8-1500' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "8-2000"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '8-2000' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }else if($_GET["account_id"] == "8-2000"){ //RETUR
                      $ctr = 0;
                      $query = "SELECT * FROM journaldata WHERE AccountCD = '8-2000' AND JournalDate >= '".$_GET["startdate"]."' AND JournalDate <= '".$_GET["enddate"]."'";
                      $result = mysqli_query($conn,$query);
                  ?>
                  <div class="table-responsive custom-scrollbar signal-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:1%">No</th>
                          <th style="width:3%">Tanggal</th>
                          <th style="width:3%">Kode Akun</th>
                          <th style="width:5%">Referensi</th>
                          <th style="width:5%">Nominal</th>
                          <th style="width:10%">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $total = 0;
                            while($row = mysqli_fetch_array($result)){
                                $ctr++;
                                echo '<tr>
                                        <th scope="row">'.$ctr.'</th>
                                        <td>'.$row["JournalDate"].'</td>
                                        <td>'.$row["AccountCD"].'</td>
                                        <td>'.$row["Notes"].'</td>
                                        <td>Rp '.number_format($row["Debit"],0,',','.').'</td>
                                        <td>'.$row["Description"].'</td>
                                      </tr>';
                                $total += $row["Debit"];
                            }
                            echo '<tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>Rp '.number_format($total,0,',','.').'</td>
                                  </tr>';
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <?php
                    }
                  ?>
                </div>
                <input type="button" class="btn btn-secondary" value="Back" onclick="history.back()">
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