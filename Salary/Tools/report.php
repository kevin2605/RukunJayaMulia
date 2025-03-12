<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  include "../headcontent.php";
  session_start();
  include "../DBConnection.php";
  $userID = $_COOKIE['UserID'];

  $query = "SELECT commission FROM useraccesslevel WHERE UserID = '$userID'";
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($result);

  $hasCRUDAccess = strpos($row['commission'], 'C') !== false || // Create
    strpos($row['commission'], 'R') !== false || // Read
    strpos($row['commission'], 'U') !== false || // Update
    strpos($row['commission'], 'D') !== false;  // Delete
  
  $accessDenied = !$hasCRUDAccess;
  ?>

  <!-- AJAX SCRIPT and DYNAMIC TABLE -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
  <!-- script sweetaler2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- use xlsx.mini.min.js from version 0.20.3 -->
  <script lang="javascript" src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.mini.min.js"></script>

  <script>
    $("document").ready(function () {
        $( "#month" ).datepicker({dateFormat: 'mm'});
    });
  </script>
</head>
<style>
  .hidden {
    display: none;
  }
</style>

<body>
  <?php if ($accessDenied): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      window.addEventListener('DOMContentLoaded', (event) => {
        Swal.fire({
          icon: 'error',
          title: 'Akses Ditolak',
          text: 'Anda tidak memiliki akses.',
          confirmButtonColor: '#3085d6',
          confirmButtonText: 'OK'
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = '../Dashboard/'; // Redirect ke halaman lain atau homepage
          }
        });
      });
    </script>
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
                  <h3>REPORT</h3>
                </div>
                <div class="col-sm-6 pe-0">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">
                        <svg class="stroke-icon">
                          <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                        </svg></a></li>
                    <li class="breadcrumb-item">Tools</li>
                    <li class="breadcrumb-item">Report</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid starts-->
          <div class="container-fluid <?php echo $accessDenied ? 'hidden' : ''; ?>">
          <?php endif; ?>
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
                        <h3>REPORT</h3>
                      </div>
                      <div class="col-sm-6 pe-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">
                                <svg class="stroke-icon">
                                <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                                </svg></a></li>
                            <li class="breadcrumb-item">Tools</li>
                            <li class="breadcrumb-item">Report</li>
                        </ol>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3>FILTER REPORT</h3>
                            </div>
                            <div class="card-body">
                                <form class="form theme-form" method="POST">
                                <div class="row">
                                    <div class="col-md-4">
                                    <div class="row">
                                        <div class="mb-3 row">
                                        <label class="col-sm-3">Bulan</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" id="month" name="month" list="monthOptions" <?php if(isset($_POST["month"])){echo 'value="'.$_POST["month"].'"';} ?> required>
                                            <datalist id="monthOptions">
                                                <option>Januari</option>
                                                <option>Februari</option>
                                                <option>Maret</option>
                                                <option>April</option>
                                                <option>Mei</option>
                                                <option>Juni</option>
                                                <option>Juli</option>
                                                <option>Agustus</option>
                                                <option>September</option>
                                                <option>Oktober</option>
                                                <option>November</option>
                                                <option>Desember</option>
                                            </datalist>
                                        </div>
                                        </div>
                                        <div class="mb-3 row">
                                        <label class="col-sm-3">Tahun</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" id="year" name="year" list="yearOptions" <?php if(isset($_POST["year"])){echo 'value="'.$_POST["year"].'"';} ?> required>
                                            <datalist id="yearOptions">
                                                <?php
                                                    $startyear = date('Y')-2;
                                                    $endyear = date('Y')+2;
                                                    for($i = $startyear; $i <= $endyear; $i++){
                                                        echo '<option>'.$i.'</option>';
                                                    }
                                                ?>
                                            </datalist>
                                        </div>
                                        </div>
                                        <div class="mb-3 row">
                                        <label class="col-sm-3">Tipe</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" id="tipe" name="tipe" list="tipeOptions" <?php if(isset($_POST["tipe"])){echo 'value="'.$_POST["tipe"].'"';} ?> required>
                                            <datalist id="tipeOptions">
                                                <option>BULANAN</option>;
                                                <option>MINGGUAN</option>;
                                            </datalist>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <button class="btn btn-primary" name="btnSearch"><i class="fa fa-search"></i> SEARCH</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3>PERHITUNGAN KOMISI MARKETING</h3>
                            </div>
                            <div class="card-body">
                              <?php
                                  if(is_null($_POST["month"]) && is_null($_POST["year"]) && is_null($_POST["tipe"])){
                                    echo "<small>No data found.</small>";
                                  }else{
                                    $months = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
                                    $month = array_search($_POST["month"], $months);
                                    $query ="SELECT c.CreditPaymentID, c.CreatedOn, i.InvoiceID, s.SalesOrderID, su.Name AS Marketing, c.TotalPayment
                                             FROM creditpaymentdetail c, invoiceheader i, salesorderheader s, systemuser su
                                             WHERE c.InvoiceID = i.InvoiceID
                                                   AND i.SalesOrderID = s.SalesOrderID
                                                   AND s.Marketing = su.UserID
                                                   AND SUBSTR(c.CreatedOn,1,4) = ".$_POST["year"]."
                                                   AND SUBSTR(c.CreatedOn,6,2) = ".str_pad($month+1, 2, "0", STR_PAD_LEFT)."
                                                   AND su.UserID='".$_POST["marketing"]."'";
                              ?>
                                    <!-- tabel -->
                                    <h3>Pembayaran</h3>
                                    <div class="table-responsive custom-scrollbar">
                                      <table class="table table-light">
                                        <thead>
                                          <tr>
                                            <th scope="col">No. Pembayaran</th>
                                            <th scope="col">Tgl. Pembayaran</th>
                                            <th scope="col">No. Invoice</th>
                                            <th scope="col">No. Sales Order</th>
                                            <th scope="col">Marketing</th>
                                            <th scope="col">Jumlah Pembayaran</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <?php
                                            $totalPayment = 0;
                                            $result = mysqli_query($conn, $query);
                                            while ($row = mysqli_fetch_array($result)) {
                                              echo '<tr>
                                                      <th scope="row">'. $row["CreditPaymentID"] .'</th>
                                                      <td>'. substr($row["CreatedOn"],0,10) .'</td>
                                                      <td>'. $row["InvoiceID"] .'</td>
                                                      <td>'. $row["SalesOrderID"] .'</td>
                                                      <td>'. $row["Marketing"] .'</td>
                                                      <td>Rp '. number_format($row["TotalPayment"], 0, ',', '.') .'</td>
                                                    </tr>';
                                              $totalPayment += $row["TotalPayment"];
                                            }
                                          ?>
                                        </tbody>
                                      </table>
                                    </div>
                                    <br>
                                    <h3>Uang Muka</h3>
                                    <?php
                                      $queryDP ="SELECT dh.DPID, dh.CreatedOn, dh.SalesOrderID, su.Name AS Marketing, dd.Amount
                                      FROM downpaymentheader dh, salesorderheader so, downpaymentdetail dd, systemuser su
                                      WHERE dh.DPID=dd.DPID
                                            AND dh.SalesOrderID = so.SalesOrderID
                                            AND so.Marketing = su.UserID
                                            AND SUBSTR(dh.CreatedOn,1,4) = ".$_POST["year"]."
                                            AND SUBSTR(dh.CreatedOn,6,2) = ".str_pad($month+1, 2, "0", STR_PAD_LEFT)."
                                            AND su.UserID='".$_POST["marketing"]."'";
                                    ?>
                                    <div class="table-responsive custom-scrollbar">
                                      <table class="table table-light">
                                        <thead>
                                          <tr>
                                            <th scope="col">No. Pembayaran</th>
                                            <th scope="col">Tgl. Pembayaran</th>
                                            <th scope="col">No. DP</th>
                                            <th scope="col">No. Sales Order</th>
                                            <th scope="col">Marketing</th>
                                            <th scope="col">Jumlah Pembayaran</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <?php
                                            $ctr = 0;
                                            $resultdp = mysqli_query($conn, $queryDP);
                                            while ($rowdp = mysqli_fetch_array($resultdp)) {
                                              $ctr++;
                                              echo '<tr>
                                                      <th scope="row">'. $ctr .'</th>
                                                      <td>'. substr($rowdp["CreatedOn"],0,10) .'</td>
                                                      <td>'. $rowdp["DPID"] .'</td>
                                                      <td>'. $rowdp["SalesOrderID"] .'</td>
                                                      <td>'. $rowdp["Marketing"] .'</td>
                                                      <td>Rp '. number_format($rowdp["Amount"], 0, ',', '.') .'</td>
                                                    </tr>';
                                              $totalPayment += $rowdp["Amount"];
                                            }
                                          ?>
                                        </tbody>
                                      </table>
                                    </div>
                                    <br>
                                    Total Pembayaran : <?php echo number_format($totalPayment, 0, ',', '.'); ?>
                                    <br><br>
                                    <form action="../Process/export-commission.php" method="POST" target="_blank">
                                      <input type="hidden" name="month" value="<?php echo str_pad($month+1, 2, "0", STR_PAD_LEFT); ?>">
                                      <input type="hidden" name="year" value="<?php echo $_POST["year"]; ?>">
                                      <input type="hidden" name="marketing" value="<?php echo $_POST["marketing"]; ?>">
                                      <button class="btn btn-secondary" name="btnDownload"><i class="fa fa-download"></i> Export</button>
                                    </form>
                              <?php
                                  }
                              ?>
                            </div>
                        </div>
                    </div>
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
  <script src="../../assets/js/notify/bootstrap-notify.min.js"></script>
  <script src="../../assets/js/datatable/datatables/jquery.dataTables.min.js"></script>
  <script src="../../assets/js/datatable/datatable-extension/dataTables.buttons.min.js"></script>
  <script src="../../assets/js/datatable/datatable-extension/jszip.min.js"></script>
  <script src="../../assets/js/datatable/datatable-extension/buttons.colVis.min.js"></script>
  <script src="../../assets/js/datatable/datatable-extension/pdfmake.min.js"></script>
  <script src="../../assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js"></script>
  <script src="../../assets/js/datatable/datatable-extension/buttons.html5.min.js"></script>
  <script src="../../assets/js/datatable/datatable-extension/custom.js"></script>
  <!-- Plugins JS Ends-->
  <!-- Theme js-->
  <script src="../../assets/js/script.js"></script>
  <!-- Plugin used-->
</body>

</html>