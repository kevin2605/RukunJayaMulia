<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  include "../headcontent.php";
  session_start();
  include "../DBConnection.php";
  $userID = $_COOKIE['UserID'];

  $query = "SELECT masteremployee FROM useraccesslevel WHERE UserID = '$userID'";
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($result);

  $hasCRUDAccess = strpos($row['masteremployee'], 'C') !== false || // Create
    strpos($row['masteremployee'], 'R') !== false || // Read
    strpos($row['masteremployee'], 'U') !== false || // Update
    strpos($row['masteremployee'], 'D') !== false;  // Delete
  
  $accessDenied = !$hasCRUDAccess;

  if(strpos($row['KodeAkun'], 'U') !== false){
    $can_update = true;
  }else{$can_update = false;}
  ?>

  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
      function editEmployee(x) {
          Swal.fire({
              title: "Edit Karyawan",
              text: "Apakah anda yakin mengubah data dari " + x.parentElement.parentElement.parentElement.cells[1].innerText + "?",
              icon: "question",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              confirmButtonText: "Ya, setuju!",
              cancelButtonColor: "#d33",
              cancelButtonText: "Tidak"
          }).then((result) => {
              if (result.isConfirmed) {
                  document.location = "Edit-Employee.php?NIK=" + x.value;
              }
          });
      }
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
                  <h3>SETTING</h3>
                </div>
                <div class="col-sm-6 pe-0">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">
                        <svg class="stroke-icon">
                          <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                        </svg></a></li>
                    <li class="breadcrumb-item">Master</li>
                    <li class="breadcrumb-item">Setting</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid starts-->
          <div class="container-fluid <?php echo $accessDenied ? 'hidden' : ''; ?>">
          <?php endif; ?>
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
                          <p><b> Selamat! </b>Jam kerja berhasil diperbarui.</p>
                          <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                      } else if ($_GET["status"] == "error") {
                        echo '<div class="alert txt-danger border-danger outline-2x alert-dismissible fade show alert-icons" role="alert">
                          <p><b> Error! </b>Jam kerja gagal diperbarui.</p>
                          <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                      }
                    }
                    ?>
                    <div class="row">
                      <div class="col-sm-6 ps-0">
                        <h3>SETTING</h3>
                      </div>
                      <div class="col-sm-6 pe-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">
                                <svg class="stroke-icon">
                                <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                                </svg></a></li>
                            <li class="breadcrumb-item">Master</li>
                            <li class="breadcrumb-item">Setting</li>
                        </ol>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3>Working Hour</h3>
                                <p>Tentukan jam kerja pada setiap shift.</p>
                            </div>
                            <form class="form theme-form" action="../Process/setWorkingHour.php" method="POST">
                                <div class="card-body custom-input">
                                    <div class="row">
                                        <div class="col-4"></div>
                                        <div class="col-4">Check In</div>
                                        <div class="col-4">Check Out</div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <?php
                                                $ctr = 0;
                                                $query = "SELECT * FROM setting_working_hour";
                                                $result = mysqli_query($conn, $query);
                                                while ($row = mysqli_fetch_array($result)) {
                                                    $ctr++;
                                                    if($ctr == 1){
                                                        echo '  <div class="mb-3 row">
                                                                    <label class="col-sm-3">Shift 1</label>
                                                                    <div class="col-sm-4">
                                                                    <input class="form-control" name="one-checkin" type="time" value="'.$row["CheckIn"].'">
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                    <input class="form-control" name="one-checkout" type="time" value="'.$row["CheckOut"].'">
                                                                    </div>
                                                                </div>';
                                                    }else if($ctr == 2){
                                                        echo '  <div class="mb-3 row">
                                                                    <label class="col-sm-3">Shift 2</label>
                                                                    <div class="col-sm-4">
                                                                    <input class="form-control" name="two-checkin" type="time" value="'.$row["CheckIn"].'">
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                    <input class="form-control" name="two-checkout" type="time" value="'.$row["CheckOut"].'">
                                                                    </div>
                                                                </div>';
                                                    }else if($ctr == 3){
                                                        echo '  <div class="mb-3 row">
                                                                    <label class="col-sm-3">Shift 3</label>
                                                                    <div class="col-sm-4">
                                                                    <input class="form-control" name="three-checkin" type="time" value="'.$row["CheckIn"].'">
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                    <input class="form-control" name="three-checkout" type="time" value="'.$row["CheckOut"].'">
                                                                    </div>
                                                                </div>';
                                                    }
                                                }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="card-footer text-end">
                                        <div class="col-sm-9 offset-sm-3">
                                            <button class="btn btn-primary me-3" type="submit">Save</button>
                                            <!--<input class="btn btn-danger" type="reset" value="Cancel">-->
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