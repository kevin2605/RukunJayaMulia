<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  include "../headcontent.php";
  include "../DBConnection.php";
  $userID = $_COOKIE['UserID'];

  $query = "SELECT mutasifan FROM useraccesslevel WHERE UserID = '$userID'";
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($result);

  $hasCRUDAccess = strpos($row['mutasifan'], 'C') !== false || // Create
    strpos($row['mutasifan'], 'R') !== false || // Read
    strpos($row['mutasifan'], 'U') !== false || // Update
    strpos($row['mutasifan'], 'D') !== false;  // Delete
  
  $accessDenied = !$hasCRUDAccess;
  ?>

  <!-- AJAX SCRIPT and DYNAMIC TABLE -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <!-- script sweetaler2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    /*function syncNumber(x) {
      qty = x.value;
      document.getElementById("qtyTwo").value = qty;
      document.getElementById("qtyRes").value = qty;
    }*/
    function PC4() {
      document.getElementById("productOne").value = "PC4.PH - BAHAN PAPERCUP 4 HOT ( Pallet )";
      document.getElementById("productRes").value = "PC4.FH - BAHAN PAPERCUP 4 HOT ( FAN )";
    }
    function PC8() {
      document.getElementById("productOne").value = "PC8.PH - BAHAN PAPERCUP 8 HOT ( Pallet )";
      document.getElementById("productRes").value = "PC8.FH - BAHAN PAPERCUP 8 HOT ( FAN )";
    }
    function PC12() {
      document.getElementById("productOne").value = "PC12.PH - BAHAN PAPERCUP 12 HOT ( Pallet )";
      document.getElementById("productRes").value = "PC12.FH - BAHAN PAPERCUP 12 HOT ( FAN )";
    }
    function PC16() {
      document.getElementById("productOne").value = "PC16.PH - BAHAN PAPERCUP 16 HOT ( Pallet )";
      document.getElementById("productRes").value = "PC16.FH - BAHAN PAPERCUP 16 HOT ( FAN )";
    }
    function PB12() {
      document.getElementById("productOne").value = "PS12.PH - BAHAN PAPERSOUP 12 HOT ( Pallet )";
      document.getElementById("productRes").value = "PS12.FH - BAHAN PAPERSOUP 12 HOT ( FAN )";
    }
    function PB17() {
      document.getElementById("productOne").value = "PS17.PH - BAHAN PAPERSOUP 17 HOT ( Pallet )";
      document.getElementById("productRes").value = "PS17.FH - BAHAN PAPERSOUP 17 HOT ( FAN )";
    }
    function PB23() {
      document.getElementById("productOne").value = "PB23.PH - BAHAN PAPERBOWL 23 HOT ( Pallet )";
      document.getElementById("productRes").value = "PB23.FH - BAHAN PAPERBOWL 23 HOT ( FAN )";
    }
    function PB28() {
      document.getElementById("productOne").value = "PB28.PH - BAHAN PAPERBOWL 28 HOT ( Pallet )";
      document.getElementById("productRes").value = "PB28.FH - BAHAN PAPERBOWL 28 HOT ( FAN )";
    }
    function PB33() {
      document.getElementById("productOne").value = "PB33.PH - BAHAN PAPERBOWL 33 HOT ( Pallet )";
      document.getElementById("productRes").value = "PB33.FH - BAHAN PAPERBOWL 33 HOT ( FAN )";
    }

    function viewMut(x) {
      document.location = "view-mutation-PTF.php?id=" + x.value;
    }
    function printInv(button) {
      var MutationID = button.value;
      var url = "../Process/generate_premium_pdf.php?MutationID=" + MutationID;
      window.open(url, '_blank');
    }
  </script>
</head>

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
              if ($_GET["status"] == "new-success") {
                echo '<div class="alert txt-success border-success outline-2x alert-dismissible fade show alert-icons" role="alert">
                    <p><b> Selamat! </b>Mutasi baru berhasil disimpan ke database.</p>
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
              } else if ($_GET["status"] == "error") {
                echo '<div class="alert txt-danger border-danger outline-2x alert-dismissible fade show alert-icons" role="alert">
                    <p><b> Error! </b>Terjadi kesalahan saat pembuatan/penyimpanan mutasi ke database.</p>
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
              }
            }
            ?>
            <div class="row">
              <div class="col-sm-6 ps-0">
                <h3>KONVERSI PALET KE FAN</h3>
              </div>
              <div class="col-sm-6 pe-0">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="index.html">
                      <svg class="stroke-icon">
                        <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                      </svg></a></li>
                  <li class="breadcrumb-item">Mutasi</li>
                  <li class="breadcrumb-item">Konversi</li>
                  <li class="breadcrumb-item">Palet ke Fan</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <!-- Container-fluid starts-->
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <?php
                $hasAccess = false;
                $userID = isset($_COOKIE["UserID"]) ? $_COOKIE["UserID"] : '';
                if (!empty($userID)) {
                  $query_access = "SELECT tSaldoAwal FROM useraccesslevel WHERE UserID = '$userID'";
                  $result_access = mysqli_query($conn, $query_access);
                  if ($result_access) {
                    $row_access = mysqli_fetch_assoc($result_access);
                    $access_level = $row_access['tSaldoAwal'];
                    // Periksa apakah 'C' ada dalam string akses
                    if (strpos($access_level, 'C') !== false) {
                      $hasAccess = true;
                    }
                  } else {
                    die("Error: Gagal mengambil data akses pengguna.");
                  }
                } else {
                  die("Error: Cookie 'UserID' tidak ada atau kosong.");
                }
              ?>
              <div class="container-fluid">
                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                  data-bs-target=".bd-example-modal-lg1">
                  <i class="fa fa-plus-circle"></i> New
                </button>
                <div class="modal fade bd-example-modal-lg1" tabindex="-1" role="dialog"
                  aria-labelledby="myExtraLargeModal" aria-hidden="true">
                  <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                      <div class="modal-header">
                        <p class="mt-1 f-m-light">Silahkan pilih bahan yang akan di konversi.</p>
                      </div>
                      <div class="modal-body dark-modal">
                        <div class="card-body custom-input">
                          <div class="row">
                            <div class="col-md-2 col-xs-12">
                              <div class="nav flex-column nav-pills nav-primary" id="ver-pills-tab" role="tablist"
                                aria-orientation="vertical">
                                <a class="f-w-600 nav-link active" onclick="PC4()" id="ver-pills-home-tab"
                                  data-bs-toggle="pill" href="#PS12PREM" role="tab" aria-controls="ver-pills-home"
                                  aria-selected="true">BAHAN PC 4</a>
                                <a class="f-w-600 nav-link" onclick="PC8()" id="ver-pills-components-tab"
                                  data-bs-toggle="pill" href="#PS12PREM" role="tab" aria-controls="ver-pills-components"
                                  aria-selected="false">BAHAN PC 8</a>
                                <a class="f-w-600 nav-link" onclick="PC12()" id="ver-pills-pages-tab"
                                  data-bs-toggle="pill" href="#PS12PREM" role="tab" aria-controls="ver-pills-pages"
                                  aria-selected="false">BAHAN PC 12</a>
                                <a class="f-w-600 nav-link" onclick="PC16()" id="ver-pills-settings-tab"
                                  data-bs-toggle="pill" href="#PS12PREM" role="tab" aria-controls="ver-pills-settings"
                                  aria-selected="false">BAHAN PC 16</a>
                                <a class="f-w-600 nav-link" onclick="PB12()" id="ver-pills-settings-tab"
                                  data-bs-toggle="pill" href="#PS12PREM" role="tab" aria-controls="ver-pills-settings"
                                  aria-selected="false">BAHAN PB 12</a>
                                <a class="f-w-600 nav-link" onclick="PB17()" id="ver-pills-settings-tab"
                                  data-bs-toggle="pill" href="#PS12PREM" role="tab" aria-controls="ver-pills-settings"
                                  aria-selected="false">BAHAN PB 17</a>
                                <a class="f-w-600 nav-link" onclick="PB23()" id="ver-pills-settings-tab"
                                  data-bs-toggle="pill" href="#PS12PREM" role="tab" aria-controls="ver-pills-settings"
                                  aria-selected="false">BAHAN PB 23</a>
                                <a class="f-w-600 nav-link" onclick="PB28()" id="ver-pills-settings-tab"
                                  data-bs-toggle="pill" href="#PS12PREM" role="tab" aria-controls="ver-pills-settings"
                                  aria-selected="false">BAHAN PB 28</a>
                                <a class="f-w-600 nav-link" onclick="PB33()" id="ver-pills-settings-tab"
                                  data-bs-toggle="pill" href="#PS12PREM" role="tab" aria-controls="ver-pills-settings"
                                  aria-selected="false">BAHAN PB 33</a>
                              </div>
                            </div>
                            <div class="col-md-10 col-xs-12">
                              <div class="tab-content" id="ver-pills-tabContent">
                                <div class="tab-pane fade show active" id="PS12PREM" role="tabpanel"
                                  aria-labelledby="ver-pills-home-tab">
                                  <div class="card">
                                    <div class="card-body custom-input">
                                      <form action="../Process/createMutPTF.php" method="POST">
                                        <div class="row g-3">
                                          <div class="col-4">
                                            <label class="form-label" for="mutid">Mutasi ID<span
                                                style="color:red;">*</span></label>
                                            <input class="form-control" id="mutid" name="mutid" type="text"
                                              placeholder="auto-generated" readonly>
                                          </div>
                                          <div class="col-4">
                                            <label class="form-label" for="tglmutasi">Tanggal<span
                                                style="color:red;">*</span></label>
                                            <input class="form-control" id="tglmutasi" name="tglmutasi" type="text"
                                              value="<?php echo date('Y-m-d'); ?>" readonly>
                                          </div>
                                          <div class="col-4">
                                            <label class="form-label" for="creator">Dibuat Oleh<span
                                                style="color:red;">*</span></label>
                                            <input class="form-control" id="creator" name="creator" type="text"
                                              value="<?php echo $_COOKIE['UserID'] . ' - ' . $_COOKIE['Name']; ?>"
                                              readonly>
                                          </div>
                                        </div>
                                        <br>
                                        <p class="mt-1 f-m-light">Konversi Dari</p>
                                        <hr>
                                        <div class="row g-3">
                                          <div class="col-6">
                                            <label class="form-label" for="productOne">Bahan</label>
                                            <input class="form-control" id="productOne" name="productOne" type="text"
                                              value="PC4.PH - BAHAN PAPERCUP 4 HOT ( Pallet )" readonly>
                                          </div>
                                          <div class="col-2">
                                            <label class="form-label" for="qtyOne">Jumlah (PLT) <span
                                                style="color:red;">*</span></label>
                                            <input class="form-control digits" id="qtyOne" name="qtyOne" type="number"
                                              onchange="syncNumber(this)" required>
                                          </div>
                                        </div>
                                        <br><br><br>
                                        <p class="mt-1 f-m-light">Konversi Ke</p>
                                        <hr>
                                        <div class="row g-3">
                                          <div class="col-6">
                                            <label class="form-label" for="productRes">Bahan</label>
                                            <input class="form-control" id="productRes" name="productRes" type="text"
                                              value="PC4.FH - BAHAN PAPERCUP 4 HOT ( FAN )" readonly>
                                          </div>
                                          <div class="col-2">
                                            <label class="form-label" for="qtyRes">Jumlah (FAN)</label>
                                            <input class="form-control digits" id="qtyRes" name="qtyRes" type="number"
                                             required>
                                          </div>
                                        </div>
                                        <br>
                                        <div class="col-12">
                                          <div class="form-check form-switch">
                                            <input class="form-check-input" id="flexSwitchCheckDefault" type="checkbox"
                                              role="switch" required>
                                            <label class="form-check-label" for="flexSwitchCheckDefault">Are you sure
                                              above
                                              information are true</label>
                                          </div>
                                        </div>
                                        <div class="col-12">
                                          <button class="btn btn-primary" type="submit">Submit</button>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <hr>
              <div class="table-responsive custom-scrollbar user-datatable">
                <table class="display" id="basic-12">
                  <thead>
                    <tr>
                      <th>Mutasi ID</th>
                      <th>Tanggal</th>
                      <th>Keterangan</th>
                      <th>Dibuat Oleh</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (isset($_COOKIE["UserID"]) && !empty($_COOKIE["UserID"])) {
                      $creator = $_COOKIE["UserID"];
                    } else {
                      die("Error: Cookie 'UserID' tidak ada atau kosong.");
                    }

                    $query_access = "SELECT tSaldoAwal FROM useraccesslevel WHERE UserID = '$creator'";
                    $result_access = mysqli_query($conn, $query_access);
                    $can_update = false;

                    if ($result_access) {
                      $row_access = mysqli_fetch_assoc($result_access);
                      $access_level = $row_access['tSaldoAwal'];
                      if (strpos($access_level, 'R') !== false) {
                        $can_update = true;
                      }
                    } else {
                      die("Error: Gagal mengambil data akses pengguna.");
                    }
                    $queryM = "SELECT muh.MutationID, muh.CreatedOn, muh.Description, su.Name
                              FROM (mutationfanheader muh 
                              JOIN systemuser su ON muh.CreatedBy = su.UserID)
                              WHERE muh.CategoryCD = 'CON'";
                    $resultM = mysqli_query($conn, $queryM);
                    while ($rowM = mysqli_fetch_array($resultM)) {
                      echo '<tr>
                                        <td>' . $rowM["MutationID"] . '</td>
                                        <td>' . $rowM["CreatedOn"] . '</td>
                                        <td>' . $rowM["Description"] . '</td>
                                        <td>' . $rowM["Name"] . '</td>
                                        <td> 
                                          <ul class="action" style="list-style: none; padding: 0; margin: 0;">';
                      if ($can_update) {
                        echo '<li style="display: inline; margin-right: 5px;">
                                          <button style="padding: 5px 10px;" onclick="viewMut(this)" type="button" class="light-card border-primary border b-r-10" value="' . $rowM["MutationID"] . '">
                                              <i class="fa fa-eye txt-primary"></i>
                                          </button>
                                        </li>';
                      }
                      /*echo '<li style="display: inline;">
                                        <button style="padding: 5px 10px;" onclick="printInv(this)" type="button" class="light-card border-info border b-r-10" value="' . $rowM["MutationID"] . '">
                                            <i class="fa fa-print txt-info"></i>
                                        </button>
                                      </li>
                                        </td>
                                      </tr>';*/
                    }
                    ?>
                  </tbody>
                </table>
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