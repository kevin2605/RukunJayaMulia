<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  include "../headcontent.php";
  session_start();
  include "../DBConnection.php";
  $userID = $_COOKIE['UserID'];

  $query = "SELECT payofdebt FROM useraccesslevel WHERE UserID = '$userID'";
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($result);

  $hasCRUDAccess = strpos($row['payofdebt'], 'C') !== false || // Create
    strpos($row['payofdebt'], 'R') !== false || // Read
    strpos($row['payofdebt'], 'U') !== false || // Update
    strpos($row['payofdebt'], 'D') !== false;  // Delete
  
  $accessDenied = !$hasCRUDAccess;
  ?>

  <!-- AJAX SCRIPT and DYNAMIC TABLE -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
  <!-- script sweetaler2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    function closeModal() {
      $('.modal-pelunasan-hutang').modal('toggle');
    }

    function getSuppDebt(str) {
      var totalDebt = 0;
      var supplier = str.value.split(" - ");
      var suppnum = supplier[0];
      $.ajax({
        type: "POST",
        url: "../Process/getSuppDebt.php",
        data: "suppnum=" + suppnum,
        success: function (result) {
          $("#data #datalist tr").remove();
          var res = JSON.parse(result);
          console.log(res);
          $.each(res, function (index, value) {
            var row = "<tr>" +
              "<td><input class='form-check-input' name='InvID[]' value='" + value.RCV_InvoiceID + "' onclick='fullPayment(this)' type='checkbox'></td>" +
              "<td>" + value.RCV_InvoiceID + "</td>" +
              "<td>" + value.CreatedOn + "</td>" +
              "<td>" + value.PurchaseOrderID + "</td>" +
              "<td>" + numeral(value.TotalAmount).format("0,0.00") + "</td>" +
              "<td><input class='form-control payamount' name='TotalPayment[]' type='text' onchange='checkEmpty(this)' readonly></td>" +
              "<td>" + value.TaxInvoiceNumber + "</td>" +
              "<td>" + value.TaxInvoiceDate + "</td>" +
              "</tr>";
            $('#data #datalist').append(row);
            totalDebt += Number(value.TotalAmount);
          });
          document.getElementById("totalPayment").innerHTML = "Rp 0";
          document.getElementById("totalCredit").innerHTML = "Rp " + numeral(totalDebt).format("0,0.00");
        }
      });
    }

    function fullPayment(x) {
      if (x.checked == true) {
        var payment = x.parentElement.parentElement.cells[4].innerHTML;
        x.parentElement.parentElement.cells[5].getElementsByTagName("input")[0].value = payment;
        x.parentElement.parentElement.cells[5].getElementsByTagName("input")[0].classList.add("border-success");
        updateTotalPayment();
      } else {
        x.parentElement.parentElement.cells[5].getElementsByTagName("input")[0].value = "";
        x.parentElement.parentElement.cells[5].getElementsByTagName("input")[0].classList.remove("border-success");
        updateTotalPayment();
      }
    }

    function checkEmpty(x) {
      if (x.value == "" || x.value == "0") {
        x.parentElement.parentElement.cells[0].getElementsByTagName("input")[0].checked = false;
        updateTotalPayment();
      } else {
        x.parentElement.parentElement.cells[0].getElementsByTagName("input")[0].checked = true;
        x.value = numeral(x.value).format("0,0.00");
        updateTotalPayment();
      }
    }

    function updateTotalPayment() {
      var totalPayment = 0;
      var temp = document.querySelectorAll(".payamount");
      for (var i = 0; i < temp.length; i++) {
        amount = temp[i].value.replaceAll(",", "");
        if (amount != "" && amount > 0) {
          totalPayment += parseInt(amount);
        }
      }
      document.getElementById("totalPayment").innerHTML = "Rp " + numeral(totalPayment).format("0,0.00");
    }

    function view(x) {
      document.location = "view-debt-payment.php?id=" + x.value;
    }
  </script>
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // Function to handle URL parameters
    function getQueryParams() {
      const query = window.location.search.substring(1);
      const params = new URLSearchParams(query);
      return {
        error: params.get('error')
      };
    }

    // Check URL parameters and show alert if needed
    window.addEventListener('DOMContentLoaded', (event) => {
      const params = getQueryParams();

      if (params.error === 'access_denied') {
        Swal.fire({
          icon: 'error',
          title: 'Akses Ditolak',
          text: 'Anda tidak memiliki akses untuk mengubah NPWP .',
          confirmButtonColor: '#3085d6',
          confirmButtonText: 'OK'
        });
      }
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
              <?php
              if (isset($_GET["status"])) {
                if ($_GET["status"] == "success") {
                  echo '<div class="alert txt-success border-success outline-2x alert-dismissible fade show alert-icons" role="alert">
                      <p><b> Selamat! </b>Pelunasan Hutang berhasil disimpan ke database.</p>
                      <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
                } else if ($_GET["status"] == "error") {
                  echo '<div class="alert txt-danger border-danger outline-2x alert-dismissible fade show alert-icons" role="alert">
                      <p><b> Error! </b>Terjadi kesalahan saat disimpan ke database.</p>
                      <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
                }
              }
              ?>
              <div class="row">
                <div class="col-sm-6 ps-0">
                  <h3>PELUNASAN HUTANG</h3>
                </div>
                <div class="col-sm-6 pe-0">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">
                        <svg class="stroke-icon">
                          <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                        </svg></a></li>
                    <li class="breadcrumb-item">Transaksi</li>
                    <li class="breadcrumb-item">Pelunasan Hutang</li>
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
                    <?php
                    if (isset($_GET["status"])) {
                      if ($_GET["status"] == "success") {
                        echo '<div class="alert txt-success border-success outline-2x alert-dismissible fade show alert-icons" role="alert">
                      <p><b> Selamat! </b>Pelunasan Hutang berhasil disimpan ke database.</p>
                      <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
                      } else if ($_GET["status"] == "error") {
                        echo '<div class="alert txt-danger border-danger outline-2x alert-dismissible fade show alert-icons" role="alert">
                      <p><b> Error! </b>Terjadi kesalahan saat disimpan ke database.</p>
                      <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
                      }
                    }
                    ?>
                    <div class="row">
                      <div class="col-sm-6 ps-0">
                        <h3>PELUNASAN HUTANG</h3>
                      </div>
                      <div class="col-sm-6 pe-0">
                        <ol class="breadcrumb">
                          <li class="breadcrumb-item"><a href="index.html">
                              <svg class="stroke-icon">
                                <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                              </svg></a></li>
                          <li class="breadcrumb-item">Transaksi</li>
                          <li class="breadcrumb-item">Pelunasan Hutang</li>
                        </ol>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="card">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-12">
                            <?php
                            $canCreate = false;
                            $userID = isset($_COOKIE["UserID"]) ? $_COOKIE["UserID"] : '';
                            if (!empty($userID)) {
                              $query_access = "SELECT payofdebt FROM useraccesslevel WHERE UserID = '$userID'";
                              $result_access = mysqli_query($conn, $query_access);
                              if ($result_access) {
                                $row_access = mysqli_fetch_assoc($result_access);
                                $access_level = $row_access['payofdebt'];
                                if (strpos($access_level, 'C') !== false) {
                                  $canCreate = true;
                                }
                              } else {
                                die("Error: Gagal mengambil data akses pengguna.");
                              }
                            } else {
                              die("Error: Cookie 'UserID' tidak ada atau kosong.");
                            }
                            ?>
                            <button class="btn btn-outline-primary" type="button" <?php echo $canCreate ? 'data-bs-toggle="modal" data-bs-target=".modal-pelunasan-hutang"' : 'disabled'; ?>><i
                                class="fa fa-plus-circle"></i> New</button>
                            <div class="modal fade modal-pelunasan-hutang" tabindex="-1" role="dialog"
                              aria-labelledby="myExtraLargeModal" aria-hidden="true">
                              <div class="modal-dialog modal-fullscreen">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h4 class="modal-title" id="myExtraLargeModal">Form Pelunasan Hutang</h4>
                                    <button class="btn-close py-0" type="button" data-bs-dismiss="modal"
                                      aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body dark-modal">
                                    <div class="card-body custom-input">
                                      <form class="row g-3" action="../Process/createDebtPayment.php" method="POST">
                                        <div class="row">
                                          <div class="col-3">
                                            <div class="mb-3 row">
                                              <label class="col-sm-3">Supplier<span style="color:red;">*</span></label>
                                              <div class="col-sm-9">
                                                <input class="form-control" id="supplier" name="supplier"
                                                  list="supplierOptions" onchange="getSuppDebt(this)"
                                                  placeholder="Pilih Supplier" required>
                                                <datalist id="supplierOptions">
                                                  <?php
                                                  $queryc = "SELECT * FROM supplier";
                                                  $resultc = mysqli_query($conn, $queryc);
                                                  while ($rowc = mysqli_fetch_array($resultc)) {
                                                    echo '<option value="' . $rowc["SupplierNum"] . ' - ' . $rowc["SupplierName"] . '"></option>';
                                                  }
                                                  ?>
                                                </datalist>
                                              </div>
                                            </div>
                                            <div class="mb-3 row">
                                              <label class="col-sm-3">Tanggal<span style="color:red;">*</span></label>
                                              <div class="col-sm-9">
                                                <input class="form-control" name="tanggal" type="date"
                                                  value="<?php echo date('Y-m-d H:i:s'); ?>" required>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-3">
                                            <div class="card-wrapper border rounded-3 checkbox-checked">
                                              <h6 class="sub-title">Pembayaran<span style="color:red;">*</span></h6>
                                              <div class="radio-form">
                                                <div class="form-check">
                                                  <input class="form-check-input" id="flexRadioDefault3" type="radio"
                                                    name="method" value="TUNAI" required="">
                                                  <label class="form-check-label" for="flexRadioDefault3">Tunai</label>
                                                </div>
                                                <div class="form-check">
                                                  <input class="form-check-input" id="flexRadioDefault3" type="radio"
                                                    name="method" value="TRANSFER" required="">
                                                  <label class="form-check-label"
                                                    for="flexRadioDefault3">Transfer</label>
                                                </div>
                                                <div class="form-check">
                                                  <input class="form-check-input" id="flexRadioDefault4" type="radio"
                                                    name="method" value="BGCH" required="">
                                                  <label class="form-check-label"
                                                    for="flexRadioDefault4">BG/Check</label>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="row">
                                          <div class="col-6">
                                            <label class="form-label" for="desc">Keterangan</label>
                                            <textarea class="form-control" id="desc" name="desc" rows="2"></textarea>
                                          </div>
                                        </div>
                                        <hr>
                                        <div class="col-sm-12">
                                          <h3>Data piutang dari pelanggan</h3>
                                          <br>
                                          <div class="table-responsive custom-scrollbar signal-table">
                                            <table class="table" id="data">
                                              <thead>
                                                <tr>
                                                  <th scope="col"> </th>
                                                  <th scope="col">No. Invoicing</th>
                                                  <th scope="col">Tanggal</th>
                                                  <th scope="col">No. Purchase Order</th>
                                                  <th scope="col">Total</th>
                                                  <th scope="col">Pembayaran</th>
                                                  <th scope="col">No. Faktur</th>
                                                  <th scope="col">Tgl. Faktur</th>
                                                </tr>
                                              </thead>
                                              <tbody id="datalist">

                                              </tbody>
                                            </table>
                                          </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                          <div class="col-8">
                                            <button class="btn btn-warning" type="button"
                                              onclick="closeModal()">Back</button>
                                            <button class="btn btn-primary" type="submit" name="submit">Submit</button>
                                          </div>
                                          <div class="col-4">
                                            <div class="row">
                                              <div class="col-5">
                                                <p style="text-align: right;">Total Pembayaran : </p>
                                              </div>
                                              <div class="col-7">
                                                <p id="totalPayment">Rp 0</p>
                                              </div>
                                            </div>
                                            <div class="row">
                                              <div class="col-5">
                                                <p style="text-align: right;">Total Hutang Perusahaan : </p>
                                              </div>
                                              <div class="col-7">
                                                <p id="totalCredit">Rp 0</p>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                              aria-expanded="false">Menu</button>
                            <ul class="dropdown-menu dropdown-block" id="myTab" role="tablist">
                              <li class="nav-item"><a class="dropdown-item active txt-primary f-w-500 f-18"
                                  id="home-tab" data-bs-toggle="tab" href="#listPelunasan" role="tab"
                                  aria-controls="home" aria-selected="true">Daftar Pelunasan</a></li>
                              <!--<li class="nav-item"><a class="dropdown-item txt-primary f-w-500 f-18" id="profile-tabs" data-bs-toggle="tab" href="#SOComplete" role="tab" aria-controls="profile" aria-selected="false">SO Complete</a></li>
                                        <li class="nav-item"><a class="dropdown-item txt-primary f-w-500 f-18" id="close-tabs" data-bs-toggle="tab" href="#SOClose" role="tab" aria-controls="close" aria-selected="false">SO Closed</a></li>-->
                            </ul>
                            <hr>
                            <div class="tab-content" id="myTabContent">
                              <div class="tab-pane fade show active" id="listPelunasan" role="tabpanel">
                                <h3>Daftar Pelunasan</h3><small>By Supplier</small>
                                <div class="table-responsive custom-scrollbar user-datatable">
                                  <table class="display" id="basic-12">
                                    <thead>
                                      <tr>
                                        <th>Nomor</th>
                                        <th>Tanggal</th>
                                        <th>ID Supplier</th>
                                        <th>Nama Supplier </th>
                                        <th>Pembayaran</th>
                                        <th>Keterangan</th>
                                        <th>Action</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php
                                      $query = "SELECT dp.DebtPaymentID, dp.CreatedOn, dp.SupplierNum, s.SupplierName, dp.PaymentMethod, dp.Description
                                      FROM debtpaymentheader dp, supplier s
                                      WHERE dp.SupplierNum=s.SupplierNum";
                                      $result = mysqli_query($conn, $query);
                                      while ($row = mysqli_fetch_array($result)) {
                                        echo "<tr>
                                          <td>" . $row["DebtPaymentID"] . "</td>
                                          <td>" . $row["CreatedOn"] . "</td>
                                          <td>" . $row["SupplierNum"] . "</td>
                                          <td>" . $row["SupplierName"] . "</td>
                                          <td>" . $row["PaymentMethod"] . "</td>
                                          <td>" . $row["Description"] . "</td>
                                          <td>
                                            <ul class='action'> 
                                                <button onclick='view(this)' type='button' class='light-card border-primary border b-r-10' style='padding:5px 10px 5px 10px;' value='" . $row["DebtPaymentID"] . "'><i class='fa fa-eye txt-primary'></i></button>
                                            </ul>
                                          </td>
                                        </tr>";
                                      }
                                      ?>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                              <!--
                                      <div class="tab-pane fade" id="historiBarang" role="tabpanel">
                                        
                                      </div>
                                      -->
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
        <script src="../../assets/js/height-equal.js"></script>
        <script src="../../assets/js/notify/bootstrap-notify.min.js"></script>
        <script src="../../assets/js/datatable/datatables/jquery.dataTables.min.js"></script>
        <script src="../../assets/js/datatable/datatables/datatable.custom.js"></script>
        <script src="../../assets/js/modalpage/validation-modal.js"></script>
        <!-- Plugins JS Ends-->
        <!-- Theme js-->
        <script src="../../assets/js/script.js"></script>
        <!-- Plugin used-->
</body>

</html>