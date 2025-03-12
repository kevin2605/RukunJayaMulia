<!DOCTYPE html>
<html lang="en">
  <head>
    <?php 
      include "../headcontent.php"; 
      include "../DBConnection.php";
    ?>

    <!-- AJAX SCRIPT and DYNAMIC TABLE -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- SWEET ALERT -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        var i = 100;
        function addNewComponent(){
            i++;
            $('#tComp #tCompBody').append(`
                <tr id="row${i}">
                    <td>
                        <input type="text" class="form-control prodlist" name="codes[]" list="componentlist" onchange="getCompNameType(this)" required>
                            <datalist id="componentlist" style="width:3rem;">
                                <?php $queryp = "SELECT * FROM salarycomponent";
                                $resultp = mysqli_query($conn, $queryp);
                                while ($rowp = mysqli_fetch_array($resultp)) {
                                    echo '<option value="' . $rowp["ComponentCode"] . '">' . $rowp["ComponentName"] . '</option>';
                                } ?>
                            </datalist>
                    </td>
                    <td>
                        <input type="text" class="form-control" readonly>
                    </td>
                    <td>
                        <input type="text" class="form-control" readonly>
                    </td>
                    <td>
                        <input type="number" class="form-control digits" name="amount[]" placeholder="0">
                    </td>
                    <td>
                        <button id="${i}" type="button" class="btn btn-danger bremove" style="padding:5px 10px 5px 10px;">
                            <i class="icofont icofont-close-line-circled"></i>
                        </button>
                    </td>
                </tr>`);
        }

        $("document").ready(function () {
            $(document).on('click', '.bremove', function () {
                i--;
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
            });
        });

        function getCompNameType(parm){
            //get name and type
            var compcode = parm.value;
            $.ajax({
                type: "POST",
                url: "../Process/getCompNameType.php",
                data: "code=" + compcode,
                success: function (result) {
                    var res = JSON.parse(result);
                    $.each(res, function (index, value) {
                        parm.parentElement.parentElement.cells[1].getElementsByTagName("input")[0].value = value.ComponentName;
                        parm.parentElement.parentElement.cells[2].getElementsByTagName("input")[0].value = value.ComponentType;
                    });
                }
            });
        }

        function updateCompVal(parm){
          var nik = document.getElementById("NIK").value;
          var code = parm.parentElement.parentElement.getElementsByTagName("input")[0].value;
          console.log(nik);
          console.log(code);
          console.log(parm.value);
          
          //update value in database
          $.ajax({
              type: "POST",
              url: "../Process/updateCompVal.php", 
              data: "nik="+nik+"&code="+code+"&value="+parm.value,
              success: function(result){
                //alert(result);
                if(result == 1){
                  Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Edit nilai komponen berhasil!",
                    showConfirmButton: false,
                    timer: 2000
                  });
                }else{
                  Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Edit nilai komponen error!",
                    showConfirmButton: false,
                    timer:2000
                  });
                }
              }
          });
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
                if ($_GET["status"] == "success-component") {
                  echo '<div class="alert txt-success border-success outline-2x alert-dismissible fade show alert-icons" role="alert">
                  <p><b> Selamat! </b>Komponen berhasil di daftarkan ke database.</p>
                  <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
                }else if ($_GET["status"] == "success-profile") {
                  echo '<div class="alert txt-success border-success outline-2x alert-dismissible fade show alert-icons" role="alert">
                  <p><b> Selamat! </b>Profil karyawan berhasil diperbarui!</p>
                  <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
                }else if ($_GET["status"] == "error") {
                  echo '<div class="alert txt-danger border-danger outline-2x alert-dismissible fade show alert-icons" role="alert">
                  <p><b> Error! </b>Terjadi kesalahan saat disimpan ke database.</p>
                  <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
                }
              }
              ?>
              <div class="row">
                <div class="col-sm-6 ps-0">
                  <h3>EDIT DETAIL KARYAWAN</h3>
                </div>
                <div class="col-sm-6 pe-0">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">                                       
                        <svg class="stroke-icon">
                          <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                        </svg></a></li>
                    <li class="breadcrumb-item">Master</li>
                    <li class="breadcrumb-item">Edit Detail Karyawan</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid starts-->
          <div class="container-fluid">
            <div class="row">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h3 class="card-title mb-0">Profil Karyawan</h3>
                            <div class="card-options"><a class="card-options-collapse" href="#" data-bs-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a><a class="card-options-remove" href="#" data-bs-toggle="card-remove"><i class="fe fe-x"></i></a></div>
                        </div>
                        <div class="card-body">
                            <?php
                                $queryp = "SELECT * FROM employee WHERE NIK='" . $_GET["NIK"] . "'";
                                $resultp = mysqli_query($conn, $queryp);
                                $employee = mysqli_fetch_assoc($resultp);
                            ?>
                            <form action="../Process/saveEmployeeProfile.php" method="POST">
                                <div class="row mb-2">
                                    <div class="profile-title">
                                        <div class="d-flex">
                                          <div class="flex-grow-1">
                                              <h2 class="mb-1"><?php echo $employee["EmpFrontName"] . ' ' . $employee["EmpLastName"]; ?></h2>
                                              <p>NIK : <?php echo $employee["NIK"]; ?></p>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" value="<?php echo $employee["NIK"]; ?>" name="nik">
                                <div class="mb-3">
                                    <h6 class="form-label">Alamat</h6>
                                    <textarea class="form-control" name="address" rows="3"><?php echo $employee["Address"]; ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kota</label>
                                    <input class="form-control" name="city" value="<?php echo $employee["City"]; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input class="form-control" type="date" name="dob" value="<?php echo $employee["DateOfBirth"]; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <input class="form-control" id="gender" name="gender" list="genderOptions" value="<?php echo $employee["Gender"]; ?>">
                                    <datalist id="genderOptions">
                                        <option>LAKI-LAKI</option>
                                        <option>PEREMPUAN</option>
                                    </datalist>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Posisi</label>
                                    <input class="form-control" id="position" name="position" list="positionOptions" value="<?php echo $employee["Position"]; ?>">
                                    <datalist id="positionOptions">
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
                                    </datalist>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Pendidikan Terakhir</label>
                                    <input class="form-control" name="lastedu" value="<?php echo $employee["LastEducation"]; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jam Kerja</label>
                                    <input class="form-control" name="workinghours" value="<?php echo $employee["WorkingHours"]; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jam Istirahat</label>
                                    <input class="form-control" id="breaktime" name="breaktime" list="breakOptions" value="<?php echo $employee["BreakTime"]; ?>">
                                    <datalist id="breakOptions">
                                        <option>30 menit</option>
                                        <option>60 menit</option>
                                    </datalist>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kategori</label>
                                    <input class="form-control" id="category" name="category" list="categoryOptions" value="<?php echo $employee["Category"]; ?>">
                                    <datalist id="categoryOptions">
                                        <option>HARIAN</option>
                                        <option>KONTRAK</option>
                                        <option>TETAP</option>
                                    </datalist>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <input class="form-control" id="status" name="status" list="statusOptions" value="<?php echo $employee["Status"]; ?>">
                                    <datalist id="statusOptions">
                                        <option>AKTIF</option>
                                        <option>NON-AKTIF</option>
                                    </datalist>
                                </div>
                                <div class="form-footer">
                                <button class="btn btn-primary btn-block">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8">
                    <form class="card" action="../Process/saveEmployeeComponent.php" method="POST">
                        <input type="hidden" value="<?php echo $employee["NIK"]; ?>" name="NIK" id="NIK">
                        <div class="card-header pb-0">
                            <h3 class="card-title mb-0">Komponen Gaji Karyawan</h3>
                        </div>
                        <div class="card-body">
                            <table class="table" id="tComp" style="width: 100%;">
                                <thead>
                                    <tr>
                                    <th style="width: 10%;">Kode</th>
                                    <th style="width: 25%;">Keterangan</th>
                                    <th style="width: 10%;">Jenis</th>
                                    <th style="width: 15%;">Nominal</th>
                                    <th style="width: 5%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tCompBody">
                                    <?php
                                        $count = 0;
                                        $query = "SELECT ec.ComponentCode, s.ComponentName, s.ComponentType, ec.ComponentValue
                                                  FROM employeecomponent ec, salarycomponent s
                                                  WHERE NIK='" . $_GET["NIK"] . "'
                                                        AND ec.ComponentCode=s.ComponentCode";
                                        $result = mysqli_query($conn, $query);
                                        while ($row = mysqli_fetch_array($result)) {
                                            $count++;
                                            echo '<tr id="row'.$count.'">
                                                        <td>
                                                            <input type="text" class="form-control prodlist" name="codes[]" value="'.$row["ComponentCode"].'" list="componentlist" onchange="getCompNameType(this)" required>
                                                                <datalist id="componentlist" style="width:3rem;">';
                                      ?>
                                                                    <?php $queryq = "SELECT * FROM salarycomponent";
                                                                    $resultq = mysqli_query($conn, $queryq);
                                                                    while ($rowq = mysqli_fetch_array($resultq)) {
                                                                        echo '<option value="' . $rowq["ComponentCode"] . '">' . $rowq["ComponentName"] . '</option>';
                                                                    } ?>
                                    <?php
                                             echo '
                                                                </datalist>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" value="'.$row["ComponentName"].'" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" value="'.$row["ComponentType"].'" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control digits" name="amount[]" value="'.$row["ComponentValue"].'" onchange="updateCompVal(this)" placeholder="0">
                                                        </td>
                                                        <td>
                                                            <button id="'.$count.'" type="button" class="btn btn-danger bremove" style="padding:5px 10px 5px 10px;">
                                                                <i class="icofont icofont-close-line-circled"></i>
                                                            </button>
                                                        </td>
                                                    </tr>';
                                    
                                        }
                                    ?>
                                <tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-4">
                                    Status : <b><?php echo $count; ?></b> Komponen tersimpan.
                                </div>
                                <div class="col-8 text-end">
                                    <button class="btn btn-warning" type="button" onclick="addNewComponent()"><i class="fa fa-plus-circle"></i></button>
                                    <button class="btn btn-primary" type="submit">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </form>
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