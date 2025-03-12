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
                                    <p><b> Selamat! </b>'.$_GET["SlipNum"].' berhasil di edit!</p>
                                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>';
                            }else if ($_GET["status"] == "error") {
                                echo '<div class="alert txt-danger border-danger outline-2x alert-dismissible fade show alert-icons" role="alert">
                                    <p><b> Error! </b>Slip gaji gagal di edit!</p>
                                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>';
                            }
                        }
                    ?>
                    <div class="row">
                      <div class="col-sm-6 ps-0">
                        <h3>EDIT SLIP GAJI</h3>
                      </div>
                      <div class="col-sm-6 pe-0">
                        <ol class="breadcrumb">
                          <li class="breadcrumb-item"><a href="index.html">
                              <svg class="stroke-icon">
                                <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                              </svg></a></li>
                          <li class="breadcrumb-item">Tools</li>
                          <li class="breadcrumb-item">Edit Slip Gaji</li>
                        </ol>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-10">
                    <div class="card">
                      <div class="card-header">
                        <div class="row g-3">
                            <?php
                                $query = "SELECT es.SlipNum, es.NIK, e.EmpFrontName, e.EmpLastName, e.Position, es.Periode
                                        FROM employee e, empsalaryheader es
                                        WHERE e.NIK=es.NIK
                                                AND es.SlipNum='".$_GET["SlipNum"]."'";
                                $result = mysqli_query($conn, $query);
                                $rowHeader = mysqli_fetch_assoc($result);
                            ?>
                            <div class="col-3">
                                <label class="form-label" for="employee">No. Slip</label>
                                <input class="form-control" value="<?php echo $rowHeader["SlipNum"]; ?>" readonly>
                            </div>
                            <div class="col-1">
                                <label class="form-label" for="employee">NIK</label>
                                <input class="form-control" value="<?php echo $rowHeader["NIK"]; ?>" readonly>
                            </div>
                            <div class="col-4">
                                <label class="form-label" for="employee">Nama Karyawan</label>
                                <input class="form-control" value="<?php echo $rowHeader["EmpFrontName"] . ' ' . $rowHeader["EmpLastName"]; ?>" readonly>
                            </div>
                            <div class="col-2">
                                <label class="form-label" for="employee">Jabatan</label>
                                <input class="form-control" value="<?php echo $rowHeader["Position"]; ?>" readonly>
                            </div>
                            <div class="col-2">
                                <label class="form-label" for="employee">Periode</label>
                                <input class="form-control" value="<?php echo $rowHeader["Periode"]; ?>" readonly>
                            </div>
                        </div>
                      </div>
                      <div class="card-body">
                        <h5>Detail Slip Gaji</h5>
                        <br>
                        <table id="tInv" class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width:5%">No</th>
                                    <th style="width:10%">Tipe</th>
                                    <th style="width:30%">Keterangan</th>
                                    <th style="width:20%">Nilai</th>
                                    <th style="width:15%">Kali</th>
                                </tr>
                            </thead>
                            <tbody id="tInvBody">
                                <?php
                                    $ctr = 0;
                                    $query = "SELECT es.ComponentCode, sc.ComponentName, sc.ComponentType, es.ComponentValue, es.Multiplier
                                              FROM empsalarydetail es, salarycomponent sc
                                              WHERE es.ComponentCode=sc.ComponentCode
                                                    AND es.SlipNum='".$_GET["SlipNum"]."'";
                                    $result = mysqli_query($conn, $query);
                                    while($rowDetail = mysqli_fetch_array($result)){
                                        $ctr++;
                                        echo "<tr>
                                                <td>".$ctr."</td>
                                                <td>".$rowDetail["ComponentType"]."</td>
                                                <td>".$rowDetail["ComponentName"]."</td>
                                                <td>".number_format($rowDetail["ComponentValue"],0,",",".")."</td>
                                                <td>";
                                                    if($rowDetail["Multiplier"] == 0){
                                                        echo "<form action='../Process/saveMultiplier.php' method='POST'>
                                                                <input type='hidden' name='slipnum' value='".$rowHeader["SlipNum"]."'>
                                                                <input type='hidden' name='code' value='".$rowDetail["ComponentCode"]."'>
                                                                <input class='form-control' type='text' name='multiplier' style='width:50%;float:left;margin-right:5px;'> 
                                                                <button class='btn btn-primary'>Save</button>
                                                              </form>
                                                            ";
                                                    }else{
                                                        echo $rowDetail["Multiplier"];
                                                    }
                                        echo "  </td>
                                              </tr>
                                        ";
                                    }
                                ?>
                            </tbody>
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