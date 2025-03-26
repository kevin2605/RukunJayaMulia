<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  include "../headcontent.php";
  session_start();
  include "../DBConnection.php";
  $userID = $_COOKIE['UserID'];

  $query = "SELECT pHasilProduksi FROM useraccesslevel WHERE UserID = '$userID'";
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($result);

  $hasCRUDAccess = strpos($row['pHasilProduksi'], 'C') !== false || // Create
    strpos($row['pHasilProduksi'], 'R') !== false || // Read
    strpos($row['pHasilProduksi'], 'U') !== false || // Update
    strpos($row['pHasilProduksi'], 'D') !== false;  // Delete
  
  $accessDenied = !$hasCRUDAccess;
  ?>

  <!-- AJAX SCRIPT and DYNAMIC TABLE -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <!-- script sweetaler2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    var weight = 0;
    function getProdOrder(x){
      var spk = x.value.split(" | ")[0];
      $.ajax({
        type: "POST",
        url: "../Process/getSPKDetail.php",
        data: "spk=" + spk,
        success: function (result) {
          var res = JSON.parse(result);
          $.each(res, function (index, value) {
            document.getElementById("desc").value = value.Description;
            document.getElementById("group").value = value.GroupCD + " - " + value.GroupName;
            document.getElementById("mesin").value = value.MachineCD + " - " + value.MachineName;
            document.getElementById("produk").value = value.ProductCD + " - " + value.ProductName;
            document.getElementById("qtyspk").value = formatRupiah(value.QtyOrder);
            document.getElementById("qtyrcv").value = formatRupiah(value.QtyProduced);
            weight = value.WeightPerPcs;
            //eror
          });
        }
      });
    }

    function countWeight(x){
      //untuk jumlah hasil produksi
      var qty = x.value.replace('.', '');
      x.value = formatRupiah(x.value); 

      //hitung hasil + terproduksi < jumlah spk
      let qtySPK = document.getElementById("qtyspk").value.replace('.', '');
      let qtyRCV = document.getElementById("qtyrcv").value;
      let qtytotal = (+qty + +qtyRCV);
      
      if(qtytotal > qtySPK){
        document.getElementById("submit").disabled = true;
      }else{
        document.getElementById("submit").disabled = false;
      }

      //untuk total berat
      var tWeight = qty * weight + "";
      document.getElementById("tweight").value = formatRupiah(tWeight) + " gr"; 
    }

    function formatRupiah(angka, prefix){
      var number_string = angka.replace(/[^,\d]/g, '').toString(),
      split   		= number_string.split(','),
      sisa     		= split[0].length % 3,
      rupiah     		= split[0].substr(0, sisa),
      ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
      
      // tambahkan titik jika yang di input sudah menjadi angka ribuan
      if(ribuan){
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
      }
    
      rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
      return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }

    function validateForm() {
      let newoutcome = document.getElementById("hasil").value;
      let newloss = document.getElementById("rusak").value;
      let newtotaloutcome = parseInt(newoutcome) + parseInt(newloss) + parseInt(exoutcome) + parseInt(loss);

      if (newtotaloutcome > estoutcome) {
        Swal.fire({
          position: "center",
          icon: "error",
          title: "Hasil  melebihi Estimasi Produksi!",
          showConfirmButton: false,
          timer: 3000
        });
        return false;
      }
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
                  <p><b> Selamat! </b>Hasil Produksi berhasil disimpan ke database.</p>
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
                  <h3>HASIL PRODUKSI</h3>
                </div>
                <div class="col-sm-6 pe-0">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">
                        <svg class="stroke-icon">
                          <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                        </svg></a></li>
                    <li class="breadcrumb-item">Hasil Produksi</li>
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
                  <p><b> Selamat! </b>Hasil Produksi berhasil disimpan ke database.</p>
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
                        <h3>HASIL PRODUKSI</h3>
                      </div>
                      <div class="col-sm-6 pe-0">
                        <ol class="breadcrumb">
                          <li class="breadcrumb-item"><a href="index.html">
                              <svg class="stroke-icon">
                                <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                              </svg></a></li>
                          <li class="breadcrumb-item">Hasil Produksi</li>
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
                        $hasAccess = false;
                        $userID = isset($_COOKIE["UserID"]) ? $_COOKIE["UserID"] : '';

                        if (!empty($userID)) {
                          $query_access = "SELECT pHasilProduksi FROM useraccesslevel WHERE UserID = '$userID'";
                          $result_access = mysqli_query($conn, $query_access);

                          if ($result_access) {
                            $row_access = mysqli_fetch_assoc($result_access);
                            $access_level = $row_access['pHasilProduksi'];
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

                        // Tampilkan tombol dengan kondisi disable jika tidak ada akses
                        echo '<button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg" ' . (!$hasAccess ? 'disabled' : '') . '><i class="fa fa-plus-circle"></i> New</button>';


                        ?>

                        <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
                          aria-labelledby="myExtraLargeModal" aria-hidden="true">
                          <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h4 class="modal-title" id="myExtraLargeModal">Input Hasil Produksi</h4>
                                <button class="btn-close py-0" type="button" data-bs-dismiss="modal"
                                  aria-label="Close"></button>
                              </div>
                              <div class="modal-body dark-modal">
                                <div class="card-body custom-input">
                                  <form class="row g-3" action="../Process/createProdOutcome.php" method="POST" onsubmit="return validateForm()">
                                    <div class="col-4">
                                        <div class="col-12">
                                            <label class="form-label" for="spk">Nomor SPK<span style="color:red;">*</span></label>
                                            <input class="form-control" id="spk" name="spk" list="spkOptions" onchange="getProdOrder(this)" placeholder="-- Pilih SPK --" required>
                                            <datalist id="spkOptions">
                                              <?php
                                                  $query = "SELECT ProductionOrderID, Description FROM productionorder WHERE Status=0";
                                                  $result = mysqli_query($conn,$query);
                                                  while($row = mysqli_fetch_array($result)){
                                                      echo '<option value="'.$row["ProductionOrderID"].' | '.$row["Description"].'"></option>';
                                                  }
                                              ?>
                                            </datalist>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label" for="tanggal">Tanggal</label>
                                            <input class="form-control" id="tanggal" name="tanggal" type="date" value="<?php echo date('Y-m-d'); ?>" readonly>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label" for="desc">Keterangan</label>
                                            <textarea class="form-control" id="desc" name="desc" rows="5" readonly></textarea>
                                        </div>
                                    </div>
                                    <div class="col-8" style="border-left:1px solid black;">
                                        <div class="tab-content" id="myTabContent">
                                            <div class="tab-pane fade show active" id="bahan" role="tabpanel" aria-labelledby="home-tab">
                                                <div class="row custom-input" style="padding-left:5px;">
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="mb-3 row">
                                                                <div class="col-sm-3">
                                                                    <label class="form-label" for="bahan">Group Bahan</label>
                                                                </div>
                                                                <div class="col-sm-9">
                                                                    <input class="form-control" id="group" name="group" type="text" placeholder="-" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3 row">
                                                                <div class="col-sm-3">
                                                                    <label class="form-label" for="mesin">Mesin</label>
                                                                </div>
                                                                <div class="col-sm-9">
                                                                    <input class="form-control" id="mesin" name="mesin" type="text" placeholder="-" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3 row">
                                                                <div class="col-sm-3">
                                                                    <label class="form-label" for="produk">Produk Jadi</label>
                                                                </div>
                                                                <div class="col-sm-9">
                                                                    <input class="form-control" id="produk" name="produk" type="text" placeholder="-" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3 row">
                                                                <label class="col-sm-3" for="qtyspk">Jumlah SPK (pcs)</label>
                                                                <div class="col-sm-9">
                                                                    <input class="form-control" id="qtyspk" type="text" placeholder="0" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3 row">
                                                                <label class="col-sm-3" for="qtyrcv">Telah Diterima (pcs)</label>
                                                                <div class="col-sm-9">
                                                                    <input class="form-control" id="qtyrcv" type="text" placeholder="0" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3 row">
                                                                <label class="col-sm-3" for="quantity">Jumlah Hasil (pcs)<span style="color:red;">*</span></label>
                                                                <div class="col-sm-9">
                                                                    <input class="form-control" id="quantity" name="quantity" type="text" onkeyup="countWeight(this)" placeholder="0" required>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3 row">
                                                                <label class="col-sm-3" for="tweight">Total Berat</label>
                                                                <div class="col-sm-9">
                                                                    <input class="form-control" id="tweight" name="tweight" type="text" placeholder="0 gr" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3 row">
                                                              <label class="col-sm-3" for="workhour">Jam Kerja</label>
                                                              <div class="col-sm-9">
                                                                <select class="form-select" id="workhour" name="workhour" required="">
                                                                  <option>5</option>
                                                                  <option selected>8</option>
                                                                  <option>12</option>
                                                                </select>
                                                              </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" id="flexSwitchCheckDefault" type="checkbox" role="switch" required>
                                            <label class="form-check-label" for="flexSwitchCheckDefault">Are you sure above information are true</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary" id="submit" type="submit">Submit</button>
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
                          <li class="nav-item"><a class="dropdown-item active txt-primary f-w-500 f-18" id="contact-tab"
                              data-bs-toggle="tab" href="#inputProduksi" role="tab" aria-controls="contact"
                              aria-selected="false">Hasil Produksi</a></li>
                        </ul>
                        <hr>
                        <div class="tab-content" id="myTabContent">
                          <div class="tab-pane fade show active" id="inputProduksi" role="tabpanel">
                            <h3>Hasil Produksi</h3>
                            <br>
                            <div class="table-responsive custom-scrollbar user-datatable">
                              <table class="display" id="basic-12">
                                <thead>
                                  <tr>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">No. SPK</th>
                                    <th scope="col">Mesin</th>
                                    <th scope="col">Produk</th>
                                    <th scope="col">Jam Kerja</th>
                                    <th scope="col">Hasil Produksi</th>
                                    <th scope="col">Target</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                  $query = "SELECT pr.CreatedOn, pr.ProductionOrderID, m.MachineName, m.Speed, m.Cavity, p.ProductName, pr.WorkingHour, pr.ProdOutcome
                                            FROM productionresulthistory pr, machine m, product p
                                            WHERE pr.MachineCD=m.MachineCD
                                                  AND pr.ProductCD=p.ProductCD
                                            ORDER BY 1 DESC";
                                  $result = mysqli_query($conn, $query);
                                  while ($row = mysqli_fetch_array($result)) {
                                    echo '
                                                    <tr>
                                                        <td>' . $row["CreatedOn"] . '</td>
                                                        <td>' . $row["ProductionOrderID"] . '</td>
                                                        <td>' . $row["MachineName"] . '</td>
                                                        <td>' . $row["ProductName"] . '</td>
                                                        <td>' . $row["WorkingHour"] . '</td>
                                                        <td>' . number_format($row["ProdOutcome"], 0, '.', ',') . '</td>';

                                    //calculate target
                                    $target = $row["Speed"] * $row["Cavity"] * 60 * $row["WorkingHour"];
                                    $currTarget = ($row["ProdOutcome"] / $target) * 100;
                                    if ($currTarget >= 85) {
                                      echo '<td><span class="badge badge-light-success">Yes</span></td>';
                                    } else {
                                      echo '<td><span class="badge badge-light-danger">No</span></td>';
                                    }
                                    echo '       
                                                    </tr>
                                                ';
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