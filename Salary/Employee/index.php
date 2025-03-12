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
              <?php
              if (isset($_GET["status"])) {
                if ($_GET["status"] == "success") {
                  echo '<div class="alert txt-success border-success outline-2x alert-dismissible fade show alert-icons" role="alert">
                  <p><b> Selamat! </b>Karyawan baru berhasil di daftarkan ke database.</p>
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
                  <h3>KARYAWAN</h3>
                </div>
                <div class="col-sm-6 pe-0">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">
                        <svg class="stroke-icon">
                          <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                        </svg></a></li>
                    <li class="breadcrumb-item">Master</li>
                    <li class="breadcrumb-item">Karyawan</li>
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
                          <p><b> Selamat! </b>Karyawan baru berhasil di daftarkan ke database.</p>
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
                        <h3>KARYAWAN</h3>
                      </div>
                      <div class="col-sm-6 pe-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">
                                <svg class="stroke-icon">
                                <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                                </svg></a></li>
                            <li class="breadcrumb-item">Master</li>
                            <li class="breadcrumb-item">Karyawan</li>
                        </ol>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <?php
                                $canUpdate = false;
                                if (!empty($userID)) {
                                $query_access = "SELECT masteremployee FROM useraccesslevel WHERE UserID = '$userID'";
                                $result_access = mysqli_query($conn, $query_access);
                                if ($result_access) {
                                    $row_access = mysqli_fetch_assoc($result_access);
                                    $access_level = $row_access['masteremployee'];
                                    if (strpos($access_level, 'U') !== false) {
                                    $canUpdate = true;
                                    }
                                } else {
                                    die("Error: Gagal mengambil data akses pengguna.");
                                }
                                } else {
                                die("Error: Cookie 'UserID' tidak ada atau kosong.");
                                }
                                ?>
                                <button class="btn btn-outline-primary" type="button" <?php echo !$canUpdate ? 'disabled' : 'data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg"'; ?>>
                                <i class="fa fa-plus-circle"></i> New
                                </button>
                                <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModal" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myExtraLargeModal">Form Karyawan Baru</h4>
                                                <button class="btn-close py-0" type="button" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body dark-modal">
                                                <div class="card-body custom-input">
                                                    <form class="row g-3" action="../Process/createEmployee.php" method="POST">
                                                        <div class="col-6"> 
                                                            <label class="form-label" for="firstname">Nama Depan</label>
                                                            <input class="form-control" id="firstname" name="firstname" type="text" placeholder="-" required>
                                                        </div>
                                                        <div class="col-6"> 
                                                            <label class="form-label" for="lastname">Nama Akhir</label>
                                                            <input class="form-control" id="lastname" name="lastname" type="text" placeholder="-" required>
                                                        </div>
                                                        <div class="col-6"> 
                                                            <label class="form-label" for="nik">Nomor Induk Karyawan</label>
                                                            <input class="form-control" id="nik" name="nik" type="text" placeholder="-" required>
                                                        </div>
                                                        <div class="col-3"> 
                                                            <label class="form-label" for="borncity">Kota Kelahiran</label>
                                                            <input class="form-control" id="borncity" name="borncity" type="text" placeholder="-" required>
                                                        </div>
                                                        <div class="col-3"> 
                                                            <label class="form-label" for="dob">Tanggal Lahir</label>
                                                            <input class="form-control digits" id="dob" name="dob" type="date" required>
                                                        </div>
                                                        <div class="col-9"> 
                                                            <label class="form-label" for="address">Alamat <i>*sesuai domisili</i></label>
                                                            <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                                                        </div>
                                                        <div class="col-3"> 
                                                            <label class="form-label" for="cityaddress">Kota</label>
                                                            <input class="form-control" id="cityaddress" name="cityaddress" type="text" placeholder="-" required>
                                                        </div>
                                                        <div class="col-3"> 
                                                            <label class="form-label" for="gender">Jenis Kelamin</label>
                                                            <select class="form-select" id="gender" name="gender" required="">
                                                                <option selected="" disabled="" value="">Pilih...</option>
                                                                <option>LAKI-LAKI</option>
                                                                <option>PEREMPUAN</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-3"> 
                                                            <label class="form-label" for="lastedu">Pendidikan Terakhir</label>
                                                            <input class="form-control" id="lastedu" name="lastedu" type="text" placeholder="-" required>
                                                        </div>
                                                        <div class="col-6"> 
                                                            <label class="form-label" for="kategori">Kategori Karyawan</label>
                                                            <select class="form-select" id="kategori" name="kategori" required="">
                                                                <option selected="" disabled="" value="">Pilih...</option>
                                                                <option>HARIAN</option>
                                                                <option>KONTRAK</option>
                                                                <option>TETAP</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-3"> 
                                                            <label class="form-label" for="workinghours">Jam Kerja</label>
                                                            <input class="form-control" id="workinghours" name="workinghours" type="text" placeholder="-" required>
                                                        </div>
                                                        <div class="col-3"> 
                                                            <label class="form-label" for="breaktime">Jam Istirahat</label>
                                                            <select class="form-select" id="breaktime" name="breaktime" required="">
                                                                <option selected="" disabled="" value="">Pilih...</option>
                                                                <option>30 menit</option>
                                                                <option>60 menit</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-3"> 
                                                            <label class="form-label" for="position">Jabatan</label>
                                                            <select class="form-select" id="position" name="position" required="">
                                                                <option selected="" disabled="" value="">Pilih...</option>
                                                                <option>TEKNISI</option>
                                                                <option>OPERATOR</option>
                                                                <option>KEPALA QC</option>
                                                                <option>QUALITY CONTROL</option>
                                                                <option>ADMIN</option>
                                                                <option>HELPER</option>
                                                                <option>DRIVER</option>
                                                                <option>MARKETING</option>
                                                                <option>SECURITY</option>
                                                                <option>KEBERSIHAN</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-3"> 
                                                            <label class="form-label" for="status">Status</label>
                                                            <select class="form-select" id="status" name="status" required="">
                                                                <option selected="" disabled="" value="">Pilih...</option>
                                                                <option>AKTIF</option>
                                                                <option>NON-AKTIF</option>
                                                            </select>
                                                        </div>
                                                        <hr>
                                                        <div class="col-12"> 
                                                            <div class="form-check form-switch">
                                                            <input class="form-check-input" id="flexSwitchCheckDefault" type="checkbox" role="switch" required>
                                                            <label class="form-check-label" for="flexSwitchCheckDefault">Pastikan informasi karyawan diatas adalah benar!</label>
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
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Menu</button>
                                <ul class="dropdown-menu dropdown-block" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="dropdown-item active txt-primary f-w-500 f-18" id="home-tab" data-bs-toggle="tab" href="#daftarBarang" role="tab" aria-controls="home" aria-selected="true">Status : Aktif</a>
                                    </li>
                                </ul>
                                <hr>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="daftarBarang" role="tabpanel">
                                        <h3>Daftar Karyawan</h3><small>Status : Aktif</small>
                                        <div class="table-responsive custom-scrollbar user-datatable">
                                        <table class="display" id="basic-12">
                                            <thead>
                                            <tr>
                                                <th>Nomor Induk</th>
                                                <th>Nama</th>
                                                <th>Tgl Lahir</th>
                                                <th>Alamat</th>
                                                <th>Kota</th>
                                                <th>Jenis Kelamin</th>
                                                <th>Jabatan</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $query = "SELECT * FROM employee";
                                            $result = mysqli_query($conn, $query);
                                            while ($row = mysqli_fetch_array($result)) {
                                                echo '
                                                        <tr>
                                                            <td>' . $row["NIK"] . '</td>
                                                            <td>' . $row["EmpFrontName"] . ' ' . $row["EmpLastName"] . '</td>
                                                            <td>' . $row["DateOfBirth"] . '</td>
                                                            <td>' . $row["Address"] . '</td>
                                                            <td>' . $row["City"] . '</td>
                                                            <td>' . $row["Gender"] . '</td>
                                                            <td>' . $row["Position"] . '</td>
                                                            <td>' . $row["Status"] . '</td>
                                                            <td>
                                                                <ul>'; 
                                                                if ($canUpdate) {
                                                                    echo '<button style="padding:5px 10px 5px 10px;" onclick="editEmployee(this)" type="button" class="light-card border-warning border b-r-10" value="' . $row["NIK"] . '"><i class="icon-pencil-alt txt-warning"></i></button>';
                                                                }else{echo "";}
                                                echo            '</ul>
                                                            </td>
                                                        </tr>
                                                    ';
                                            }
                                            ?>
                                        </table>
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