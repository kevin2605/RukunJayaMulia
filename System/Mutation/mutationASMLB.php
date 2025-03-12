<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  include "../headcontent.php";
  include "../DBConnection.php";
  ?>

  <!-- AJAX SCRIPT and DYNAMIC TABLE -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <!-- script sweetaler2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    function syncNumber(x) {
      qty = x.value;
      document.getElementById("qtyTo").value = qty;
    }
    function qpack() {
      document.getElementById("materialTo").value = "PC8.PQPACK - BAHAN PAPERCUP 8 HOT Q-PACK";
    }
    function scup() {
      document.getElementById("materialTo").value = "PC8.PSCUP - BAHAN PAPERCUP 8 HOT S-CUP";
    }

    function viewMut(x) {
      document.location = "view-mutation-material.php?id=" + x.value;
    }
    function printInv(button) {
      var MutationID = button.value;
      var url = "../Process/generate_mutationmpc8_pdf.php?MutationID=" + MutationID;
      window.open(url, '_blank');
    }
  </script>
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
                <h3>MUTASI BAHAN ALL SHEET MLB</h3>
              </div>
              <div class="col-sm-6 pe-0">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="index.html">
                      <svg class="stroke-icon">
                        <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                      </svg></a></li>
                  <li class="breadcrumb-item">Mutasi</li>
                  <li class="breadcrumb-item">Bahan All Sheet MLB</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <!-- Container-fluid starts-->
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
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
                        <p class="mt-1 f-m-light">Silahkan pilih tujuan bahan yang akan di mutasi.</p>
                      </div>
                      <div class="modal-body dark-modal">
                        <div class="card-body custom-input">
                          <div class="row">
                            <div class="col-md-12 col-xs-12">
                              <div class="tab-content" id="ver-pills-tabContent">
                                <div class="tab-pane fade show active" id="PS12PREM" role="tabpanel"
                                  aria-labelledby="ver-pills-home-tab">
                                  <div class="card">
                                    <div class="card-body custom-input">
                                      <form action="../Process/createMutMatASLB.php" method="POST">
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
                                        <p class="mt-1 f-m-light">Bahan Keluar</p>
                                        <hr>
                                        <div class="row g-3">
                                          <div class="col-7">
                                            <label class="form-label" for="materialFrom">Bahan</label>
                                            <input class="form-control" id="materialFrom" name="materialFrom" type="text"
                                              value="BMLBP.LH - BAHAN ALL SHEET MLB" readonly>
                                          </div>
                                          <div class="col-2">
                                            <label class="form-label" for="qtyFrom">Jumlah (DOS)<span style="color:red;">*</span></label>
                                            <input class="form-control digits" id="qtyFrom" name="qtyFrom" type="number"  required>
                                          </div>
                                        </div>
                                        <br><br><br>
                                        <p class="mt-1 f-m-light">Bahan Masuk</p>
                                        <hr>
                                        <div class="row g-3">
                                          <div class="col-7">
                                            <label class="form-label" for="materialTo">Bahan</label>
                                            <input class="form-control" id="materialTo" name="materialTo" type="text" list="MLBOptions" required>
                                            <datalist id="MLBOptions">
                                                <?php
                                                $query = "SELECT MaterialCD, MaterialName FROM material WHERE MaterialCD LIKE '%BMLB%' AND UnitCD_2 = 'FAN'";
                                                $result = mysqli_query($conn, $query);
                                                while ($row = mysqli_fetch_array($result)) {
                                                    echo '<option value="' . $row["MaterialCD"] . ' - ' . $row["MaterialName"] . '"></option>';
                                                }
                                                ?>
                                            </datalist>
                                          </div>
                                          <div class="col-2">
                                            <label class="form-label" for="qtyTo">Jumlah (FAN)<span style="color:red;">*</span></label>
                                            <input class="form-control digits" id="qtyTo" name="qtyTo" type="number" required>
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
                              WHERE muh.CategoryCD = 'MASLB'";
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
                      echo '<li style="display: inline;">
                                        <button style="padding: 5px 10px;" onclick="printInv(this)" type="button" class="light-card border-info border b-r-10" value="' . $rowM["MutationID"] . '">
                                            <i class="fa fa-print txt-info"></i>
                                        </button>
                                      </li>
                                        </td>
                                      </tr>';
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