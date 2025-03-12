<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  include "../headcontent.php";
  session_start();
  include "../DBConnection.php";
  $userID = $_COOKIE['UserID'];

  $query = "SELECT rInvoice FROM useraccesslevel WHERE UserID = '$userID'";
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($result);

  $hasCRUDAccess = strpos($row['rInvoice'], 'C') !== false || // Create
    strpos($row['rInvoice'], 'R') !== false || // Read
    strpos($row['rInvoice'], 'U') !== false || // Update
    strpos($row['rInvoice'], 'D') !== false;  // Delete
  
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
    /*
      function submitFilter(){
          var customer = document.getElementById("customer").value;
          var startdate = document.getElementById("startdate").value;
          var enddate = document.getElementById("enddate").value;
          var startdatefaktur = document.getElementById("startdatefaktur").value;
          var enddatefaktur = document.getElementById("enddatefaktur").value;

          $.ajax({
              type: "POST",
              url: "../Process/reportInvoice.php", 
              data: "customer="+customer+"&startdate="+startdate+"&enddate="+enddate+"&startdatefaktur="+startdatefaktur+"&enddatefaktur="+enddatefaktur,
              success: function(result){
                  $("#export-button tbody tr").remove(); 
                  var res = JSON.parse(result);
                  console.log(res.length);
                  $.each(res, function(index, value) {
                      let dpp = value.TotalInvoice/1.11;
                      let ppn = value.TotalInvoice - dpp;
                      $('#export-button tbody').append("<tr><td>"+ value.InvoiceID +"</td><td>"+ value.CreatedOn.substring(0,10) +"</td><td>"+ value.CustName +"</td><td>"+ value.NPWPNum +"</td><td>"+ value.TaxInvoiceNumber +"</td><td>"+ value.TaxInvoiceDate +"</td><td>"+ numeral(value.TotalInvoice).format("0,0.00") +"</td><td> 0 </td><td>"+ numeral(value.TotalInvoice).format("0,0.00") +"</td><td>"+ numeral(dpp).format("0,0.00") +"</td><td>"+ numeral(ppn).format("0,0.00") +"</td><td>"+ numeral(value.TotalInvoice).format("0,0.00") +"</td></tr>");
                  });
                  if(res.length < 1){
                      Swal.fire({
                          position: "center",
                          icon: "error",
                          title: "Pencarian tidak ditemukan!",
                          showConfirmButton: false,
                          timer:2000
                      });
                  }
              }
          });
      }*/
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
                  <h3>REPORT INVOICE</h3>
                </div>
                <div class="col-sm-6 pe-0">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">
                        <svg class="stroke-icon">
                          <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                        </svg></a></li>
                    <li class="breadcrumb-item">Report</li>
                    <li class="breadcrumb-item">Penjualan</li>
                    <li class="breadcrumb-item">Invoice</li>
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
                        <h3>REPORT PIUTANG</h3>
                      </div>
                      <div class="col-sm-6 pe-0">
                        <ol class="breadcrumb">
                          <li class="breadcrumb-item"><a href="index.html">
                              <svg class="stroke-icon">
                                <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                              </svg></a></li>
                          <li class="breadcrumb-item">Report</li>
                          <li class="breadcrumb-item">Piutang</li>
                        </ol>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="card">
                      <div class="card-header">
                        <h3>FILTER</h3>
                      </div>
                      <div class="card-body">
                        <form class="form theme-form" method="POST">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="row">
                                <div class="mb-3 row">
                                  <label class="col-sm-2">Customer</label>
                                  <div class="col-sm-10">
                                    <input class="form-control" id="customer" name="customer" list="custOptions"
                                      placeholder="-- Pilih Pelanggan --">
                                    <datalist id="custOptions">
                                      <?php
                                      $queryc = "SELECT * FROM customer";
                                      $resultc = mysqli_query($conn, $queryc);
                                      while ($rowc = mysqli_fetch_array($resultc)) {
                                        echo '<option value="' . $rowc["CustID"] . ' - ' . $rowc["CustName"] . '"></option>';
                                      }
                                      ?>
                                    </datalist>
                                  </div>
                                </div>
                                <div class="mb-3 row">
                                  <label class="col-sm-2">Tanggal Awal</label>
                                  <div class="col-sm-10">
                                    <input class="form-control" id="startdate" name="startdate" type="date">
                                  </div>
                                </div>
                                <div class="mb-3 row">
                                  <label class="col-sm-2">Tanggal Akhir</label>
                                  <div class="col-sm-10">
                                    <input class="form-control" id="enddate" name="enddate" type="date">
                                  </div>
                                </div>
                                <div class="mb-3 row">
                                  <label class="col-sm-2">Marketing</label>
                                  <div class="col-sm-10">
                                    <input class="form-control" id="marketing" name="marketing" list="mktOptions" placeholder="-- Pilih Marketing --">
                                    <datalist id="mktOptions">
                                      <?php
                                      $querym = "SELECT DISTINCT(so.Marketing), su.Name FROM salesorderheader so, systemuser su WHERE so.Marketing=su.UserID ORDER BY 1";
                                      $resultm = mysqli_query($conn, $querym);
                                      while ($rowm = mysqli_fetch_array($resultm)) {
                                        echo '<option value="' . $rowm["Marketing"] . ' - ' . $rowm["Name"] . '"></option>';
                                      }
                                      ?>
                                    </datalist>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="row">
                                <div class="mb-3 row">
                                  <label class="col-sm-2">Tanggal Awal Faktur</label>
                                  <div class="col-sm-10">
                                    <input class="form-control" id="startdatefaktur" name="startdatefaktur" type="date">
                                  </div>
                                </div>
                                <div class="mb-3 row">
                                  <label class="col-sm-2">Tanggal Akhir Faktur</label>
                                  <div class="col-sm-10">
                                    <input class="form-control" id="enddatefaktur" name="enddatefaktur" type="date">
                                  </div>
                                </div>
                                <div class="mb-3 row">
                                  <label class="col-sm-2">Status</label>
                                  <div class="col-sm-10">
                                    <input class="form-control" id="status" name="status" list="statusOptions" placeholder="-- Pilih Status --">
                                    <datalist id="statusOptions">
                                        <option>Lunas</option>
                                        <option>Belum Lunas</option>
                                    </datalist>
                                  </div>
                                </div>
                                <div class="mb-3 row">
                                  <label class="col-sm-2">Tipe</label>
                                  <div class="col-sm-10">
                                    <input class="form-control" id="tipe" name="tipe" list="tipeOptions"
                                      placeholder="-- Pilih Tipe --">
                                    <datalist id="tipeOptions">
                                      <option value="Polos"></option>
                                      <option value="Printing"></option>
                                    </datalist>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!--<button class="btn btn-primary" type="button" onclick="submitFilter()"><i class="fa fa-search"></i> Search</button>-->
                          <button class="btn btn-primary" name="btnSearch"><i class="fa fa-search"></i> Search</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="card">
                      <div class="card-header">
                        <h3>REPORT</h3>
                      </div>
                      <div class="card-body">
                        <div class="dt-ext table-responsive custom-scrollbar">
                          <table class="display" id="export-button">
                            <thead>
                              <tr>
                                <th colspan="2" class="text-center">INVOICE</th>
                                <th rowspan="2">NAMA CUSTOMER</th>
                                <th rowspan="2">NPWP</th>
                                <th colspan="2" class="text-center">FAKTUR PAJAK</th>
                                <th rowspan="2">PENJUALAN</th>
                                <th rowspan="2">UANG MUKA</th>
                                <th rowspan="2">TOTAL</th>
                                <th rowspan="2">DPP</th>
                                <th rowspan="2">PPN KELUARAN</th>
                                <th rowspan="2">TOTAL</th>
                              </tr>
                              <tr>
                                <th>NO INVOICE</th>
                                <th>TGL</th>
                                <th>NO FAKTUR</th>
                                <th>TGL</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $tAmount = 0 ;
                              $tDPP = 0;
                              $tPPN = 0;
                              if (isset($_POST["btnSearch"])) {
                                $query = "SELECT i.InvoiceID, i.CreatedOn, c.CustName, c.NPWPNum, i.TaxInvoiceNumber, i.TaxInvoiceDate, i.TotalInvoice
                                                      FROM invoiceheader i, customer c, salesorderheader s
                                                      WHERE i.CustID=c.CustID
                                                            AND i.SalesOrderID=s.SalesOrderID";

                                if ($_POST["customer"] != '') {
                                  $customers = explode(" - ", $_POST["customer"]);
                                  $query .= " AND i.CustID ='" . $customers[0] . "'";
                                }
                                if ($_POST["startdate"] != '') {
                                  $query .= " AND substr(i.CreatedOn,1,10) >='" . $_POST["startdate"] . "'";
                                }
                                if ($_POST["enddate"] != '') {
                                  $query .= " AND substr(i.CreatedOn,1,10) <='" . $_POST["enddate"] . "'";
                                }
                                if($_POST["marketing"] != ''){
                                  $marketing = explode(" - ", $_POST["marketing"])[0];
                                  $query .= " AND s.Marketing='" .$marketing. "'";
                                }
                                if ($_POST["startdatefaktur"] != '') {
                                  $query .= " AND substr(i.TaxInvoiceDate,1,10) >='" . $_POST["startdatefaktur"] . "'";
                                }
                                if ($_POST["enddatefaktur"] != '') {
                                  $query .= " AND substr(i.TaxInvoiceDate,1,10) <='" . $_POST["enddatefaktur"] . "'";
                                }
                                if($_POST["status"] != ''){
                                  if($_POST["status"] == "Lunas"){
                                    $query .= " AND i.InvoiceStatus ='2'";
                                  }else if($_POST["status"] == "Belum Lunas"){
                                    $query .= " AND i.InvoiceStatus ='0'";
                                  }
                                }
                                if ($_POST["tipe"] != '') {
                                  if ($_POST["tipe"] == "Polos") {
                                    $query .= " AND s.Logo =''";
                                  } else if ($_POST["tipe"] == "Printing") {
                                    $query .= " AND s.Logo !=''";
                                  }
                                }
                                $result = mysqli_query($conn, $query);
                                while ($row = mysqli_fetch_array($result)) {
                                  $dpp = $row["TotalInvoice"] / 1.11;
                                  $ppn = $row["TotalInvoice"] - $dpp;
                                  echo '  <tr>
                                            <td>' . $row["InvoiceID"] . '</td>
                                            <td>' . substr($row["CreatedOn"], 0, 10) . '</td>
                                            <td>' . $row["CustName"] . '</td>
                                            <td>' . $row["NPWPNum"] . '</td>
                                            <td>' . $row["TaxInvoiceNumber"] . '</td>
                                            <td>' . $row["TaxInvoiceDate"] . '</td>
                                            <td>' . number_format($row["TotalInvoice"], 0, ',', '.') . '</td>
                                            <td> 0 </td>
                                            <td>' . number_format($row["TotalInvoice"], 0, ',', '.') . '</td>
                                            <td>' . number_format($dpp, 0, ',', '.') . '</td>
                                            <td>' . number_format($ppn, 0, ',', '.') . '</td>
                                            <td>' . number_format($row["TotalInvoice"], 0, ',', '.') . '</td>
                                          </tr>';
                                  $tAmount += $row["TotalInvoice"];
                                  $tDPP += $dpp;
                                  $tPPN += $ppn;
                                }
                              }
                              ?>

                              <!-- APPEND BY AJAX -->
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-2">
                            <label>Total Piutang : <?php echo 'Rp ' .  number_format($tAmount, 0, ',', '.'); ?></label>
                          </div>
                          <div class="col-2">
                            <label>Total DPP : <?php echo 'Rp ' .  number_format($tDPP, 0, ',', '.'); ?></label>
                          </div>
                          <div class="col-2">
                            <label>Total PPN : <?php echo 'Rp ' .  number_format($tPPN, 0, ',', '.'); ?></label>
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