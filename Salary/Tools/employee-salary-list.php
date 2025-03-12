<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    include "../headcontent.php";
    session_start();
    include "../DBConnection.php";
    $userID = $_COOKIE['UserID'];

    $query = "SELECT empsalarylist FROM useraccesslevel WHERE UserID = '$userID'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    $hasCRUDAccess = strpos($row['empsalarylist'], 'C') !== false || // Create
        strpos($row['empsalarylist'], 'R') !== false || // Read
        strpos($row['empsalarylist'], 'U') !== false || // Update
        strpos($row['empsalarylist'], 'D') !== false;  // Delete
    
    $accessDenied = !$hasCRUDAccess;
    ?>

    <!-- AJAX SCRIPT and DYNAMIC TABLE -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <!-- script sweetaler2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        $("document").ready(function () {
            $("#buttonGen").click(function () {
                //get customer
                var nik = document.getElementById("employee").value.split(" - ")[0];
                var periode = document.getElementById("month").value;
                var months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                var month = months.indexOf(periode) + 1;
                console.log(nik);
                console.log(month);

                if(nik != "" && month != 0) {
                  var xmlhttp=new XMLHttpRequest();
                  xmlhttp.onreadystatechange=function() {
                    if (this.readyState==4 && this.status==200) {
                        console.log(nik);
                        console.log(month);
                        document.getElementById("detailSalary").innerHTML=this.responseText;
                    }
                  }
                  detail = true;
                  xmlhttp.open("GET","../Process/getEmpSalComp.php?nik=" + nik + "&periode=" + month,true);
                  xmlhttp.send();
                }else if (nik == "" || periode == "") {
                  Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Silahkan pilih NIK Karyawan dan Periode terlebih dahulu!",
                    showConfirmButton: false,
                    timer:2000
                  });
                }
            });
        });

        function print(str) {
            var slipnum = str.value;
            var url = "../Process/generate_salary_slip_pdf.php?SlipNum=" + slipnum;
            window.open(url, '_blank');
        }

        function editEmpSalary(str){
            var slipnum = str.value;
            var url = "../Tools/view-salary-slip.php?SlipNum=" + slipnum;
            window.open(url,'_self');
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
                  <h3>HITUNG GAJI</h3>
                </div>
                <div class="col-sm-6 pe-0">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">
                        <svg class="stroke-icon">
                          <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                        </svg></a></li>
                    <li class="breadcrumb-item">Tools</li>
                    <li class="breadcrumb-item">Hitung Gaji</li>
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
                                    <p><b> Selamat! </b>'.$_GET["slipnum"].' berhasil dibuat!</p>
                                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>';
                            }else if ($_GET["status"] == "error") {
                                echo '<div class="alert txt-danger border-danger outline-2x alert-dismissible fade show alert-icons" role="alert">
                                    <p><b> Error! </b>Slip gaji gagal dibuat!</p>
                                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>';
                            }else if ($_GET["status"] == "exist") {
                              echo '<div class="alert txt-danger border-danger outline-2x alert-dismissible fade show alert-icons" role="alert">
                                  <p><b> Gagal! </b>Slip gaji dengan karyawan pada periode tersebut telah dibuat!</p>
                                  <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>';
                          }
                        }
                    ?>
                    <div class="row">
                      <div class="col-sm-6 ps-0">
                        <h3>DAFTAR GAJI</h3>
                      </div>
                      <div class="col-sm-6 pe-0">
                        <ol class="breadcrumb">
                          <li class="breadcrumb-item"><a href="index.html">
                              <svg class="stroke-icon">
                                <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                              </svg></a></li>
                          <li class="breadcrumb-item">Tools</li>
                          <li class="breadcrumb-item">Daftar Gaji</li>
                        </ol>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-10">
                    <div class="card">
                      <div class="card-body">
                        <?php
                          $canCreate = false;
                          $canPrint = false;
                          $canUpdate = false;
                          if (!empty($userID)) {
                          $query_access = "SELECT empsalarylist FROM useraccesslevel WHERE UserID = '$userID'";
                          $result_access = mysqli_query($conn, $query_access);
                          if ($result_access) {
                              $row_access = mysqli_fetch_assoc($result_access);
                              $access_level = $row_access['empsalarylist'];
                              if (strpos($access_level, 'C') !== false) {
                                $canCreate = true;
                              }
                              if (strpos($access_level, 'R') !== false) {
                                $canPrint = true;
                              }
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
                          <button class="btn btn-outline-primary" type="button" <?php echo !$canCreate ? 'disabled' : 'data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg"'; ?>>
                          <i class="fa fa-plus-circle"></i> New
                          </button>
                          <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModal" aria-hidden="true">
                              <div class="modal-dialog modal-xl">
                                  <div class="modal-content">
                                      <div class="modal-header">
                                          <h4 class="modal-title" id="myExtraLargeModal">Generate Gaji Karyawan</h4>
                                          <button class="btn-close py-0" type="button" data-bs-dismiss="modal"
                                          aria-label="Close"></button>
                                      </div>
                                      <div class="modal-body dark-modal">
                                          <div class="card-body custom-input">
                                              <form class="row g-3" action="../Process/save_emp_salary.php" method="POST">
                                                  <div class="col-6">
                                                      <label class="form-label" for="employee">NIK - Nama Karyawan</label>
                                                      <input class="form-control" id="employee" name="employee" list="empOptions" placeholder="Pilih Karyawan" required>
                                                      <datalist id="empOptions">
                                                          <?php
                                                          $query = "SELECT NIK, EmpFrontName, EmpLastName FROM employee";
                                                          $result = mysqli_query($conn, $query);
                                                          while ($row = mysqli_fetch_array($result)) {
                                                              echo '<option value="' . $row["NIK"] . ' - '.$row["EmpFrontName"].' '.$row["EmpLastName"].'"></option>';
                                                          }
                                                          ?>
                                                      </datalist>
                                                  </div>
                                                  <div class="col-4"> 
                                                      <label class="form-label" for="month">Periode (Bulan)</label>
                                                      <input class="form-control" id="month" name="month" list="monthOptions" placeholder="Pilih Periode" required>
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
                                                  <div class="col-2">
                                                      <label class="form-label"
                                                          for="buttonGen"><i>Generate</i></label>
                                                      <button
                                                          class="form-control btn btn-primary"
                                                          type="button"
                                                          id="buttonGen">Generate</button>
                                                  </div>
                                                  <hr>
                                                  <p class="f-w-600 f-18">Detail Slip Gaji</p>
                                                  <div id="detailSalary">
                                                        <!-- TABEL DISINI -->
                                                        <p>No data found!</p>  
                                                  </div>
                                                  <hr>
                                                  <div class="col-12">
                                                      <button class="btn btn-primary" type="submit">Submit</button>
                                                  </div>
                                              </form>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <hr>
                          <h3>Daftar Gaji Karyawan</h3>
                          <div class="table-responsive custom-scrollbar user-datatable">
                          <table class="display" id="basic-12">
                              <thead>
                              <tr>
                                  <th>No. Slip</th>
                                  <th>NIK</th>
                                  <th>Nama</th>
                                  <th>Periode</th>
                                  <th>Tgl. Pembuatan</th>
                                  <th>Tgl. Cetak</th>
                                  <th>Action</th>
                              </tr>
                              </thead>
                              <tbody>
                              <?php
                              $query = "SELECT es.SlipNum, es.NIK, e.EmpFrontName, e.EmpLastName, es.Periode, es.CreatedOn, es.PrintDate
                                        FROM employee e, empsalaryheader es
                                        WHERE e.NIK=es.NIK";
                              $result = mysqli_query($conn, $query);
                              while ($row = mysqli_fetch_array($result)) {
                                  echo '
                                          <tr>
                                              <td>' . $row["SlipNum"] . '</td>
                                              <td>' . $row["NIK"] . '</td>
                                              <td>' . $row["EmpFrontName"] . ' ' . $row["EmpLastName"] . '</td>
                                              <td>' . $row["Periode"] . '</td>
                                              <td>' . $row["CreatedOn"] . '</td>
                                              <td>' . $row["PrintDate"] . '</td>
                                              <td>
                                                  <ul>'; 
                                                  if ($canUpdate) {
                                                    echo '<button style="padding:5px 10px 5px 10px;" onclick="editEmpSalary(this)" type="button" class="light-card border-warning border b-r-10" value="' . $row["SlipNum"] . '"><i class="icon-pencil-alt txt-warning"></i></button> ';
                                                  }
                                                  if ($canPrint) {
                                                      echo '<button style="padding:5px 10px 5px 10px;" onclick="print(this)" type="button" class="light-card border-info border b-r-10 action-button" value="' . $row['SlipNum'] . '"><i class="fa fa-print txt-info"></i></button>';
                                                  }
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