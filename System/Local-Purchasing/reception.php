<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  include "../headcontent.php";
  session_start();
  include "../DBConnection.php";
  $userID = $_COOKIE['UserID'];

  $query = "SELECT tPenerimaanBarang FROM useraccesslevel WHERE UserID = '$userID'";
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($result);

  $hasCRUDAccess = strpos($row['tPenerimaanBarang'], 'C') !== false || // Create
    strpos($row['tPenerimaanBarang'], 'R') !== false || // Read
    strpos($row['tPenerimaanBarang'], 'U') !== false || // Update
    strpos($row['tPenerimaanBarang'], 'D') !== false;  // Delete
  
  $accessDenied = !$hasCRUDAccess;
  ?>

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
          text: 'Anda tidak memiliki akses untuk Approve.',
          confirmButtonColor: '#3085d6',
          confirmButtonText: 'OK'
        });
      }
    });
  </script>

  <!-- AJAX SCRIPT and DYNAMIC TABLE -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <!-- script sweetaler2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    var i = 1;

    $("document").ready(function () {
      $(document).on('click', '.bremove', function () {
        i--;
        var button_id = $(this).attr("id");
        $('#row' + button_id + '').remove();
      });

      $("#buttonGen").click(function () {
        //get purchase order
        var posupp = document.getElementById("poid").value;
        var poid = posupp.split(" | ");
        document.getElementById("category").value = poid[2];

        //get po detail
        if (posupp != "") {
          $.ajax({
            type: "POST",
            url: "../Process/getPODetail.php",
            data: "id=" + poid[0] + "&category=" + poid[2],
            success: function (result) {
              $("#tPO #tPOBody tr").remove();
              var res = JSON.parse(result);
              $.each(res, function (index, value) {
                i++;
                if (poid[2] == "BB") {
                  var trow = '<tr id="row' + i + '">' +
                    '<td style="width:30%">' +
                    '<input type="text" class="form-control prodlist" name="items[]" value="' + value.ItemCD + " - " + value.MaterialName + '" readonly>' +
                    '</td>' +
                    '<td style="width:20%">' +
                    '<input type="number" class="form-control" name="qty1[]" min="1" max="' + (value.Quantity - value.QuantityReceived)*1.3 + '" placeholder="Max. ' + (value.Quantity - value.QuantityReceived) + ' - ' + (value.Quantity - value.QuantityReceived)*1.3 + '" step=".1" required>' +
                    '</td>' +
                    '<td style="width:10%">' +
                    '<input type="text" class="form-control" name="unit1[]" value="' + value.UnitCD + '" placeholder="0" readonly>' +
                    '</td>' +
                    '<td style="width:20%">' +
                    '<input type="number" class="form-control" name="qty2[]" min="1" placeholder="0" required>' +
                    '</td>' +
                    '<td style="width:10%">' +
                    '<input type="text" class="form-control" name="unit2[]" value="' + value.UnitCD_2 + '" readonly>' +
                    '</td>' +
                    '<td style="width:10%">' +
                    '<button id="' + i + '" type="button" class="btn btn-danger bremove"><i class="icofont icofont-close-line-circled"></i></button>' +
                    '</td>' +
                    '</tr>';
                } else if (poid[2] == "BPP") {
                  var trow = '<tr id="row' + i + '">' +
                    '<td style="width:30%">' +
                    "<input type='text' class='form-control prodlist' name='items[]' value='" + value.ItemCD + " - " + value.GoodsName + "' readonly>" +
                    '</td>' +
                    '<td style="width:20%">' +
                    '<input type="number" class="form-control" name="qty1[]" min="1" max="' + (value.Quantity - value.QuantityReceived)*1.3 + '" placeholder="Max. ' + (value.Quantity - value.QuantityReceived) + ' - ' + (value.Quantity - value.QuantityReceived)*1.3 + '" step=".1" required>' +
                    '</td>' +
                    '<td style="width:10%">' +
                    '<input type="text" class="form-control" name="unit1[]" value="' + value.UnitCD + '" readonly>' +
                    '</td>' +
                    '<td style="width:20%">' +
                    '<input type="number" class="form-control" name="qty2[]" ' + (value.UnitCD_2 === value.UnitCD ? 'disabled' : '') + ' required>' +
                    '</td>' +
                    '<td style="width:10%">' +
                    '<input type="text" class="form-control" name="unit2[]" value="' + (value.UnitCD_2 !== "0" && value.UnitCD_2 !== 0 ? value.UnitCD_2 : '') + '" ' + (value.UnitCD_2 === value.UnitCD ? 'disabled' : '') + ' readonly>' +
                    '</td>' +
                    '<td style="width:10%">' +
                    '<button id="' + i + '" type="button" class="btn btn-danger bremove"><i class="icofont icofont-close-line-circled"></i></button>' +
                    '</td>' +
                    '</tr>';
                } else if (poid[2] == "SPR") {
                  var trow = '<tr id="row' + i + '">' +
                    '<td style="width:30%">' +
                    '<input type="text" class="form-control prodlist" name="items[]" value="' + value.ItemCD + " - " + value.PartName + '" readonly>' +
                    '</td>' +
                    '<td style="width:20%">' +
                    '<input type="number" class="form-control" name="qty1[]" min="1" max="' + (value.Quantity - value.QuantityReceived) + '" placeholder="Max. ' + (value.Quantity - value.QuantityReceived) + '" step=".1" required>' +
                    '</td>' +
                    '<td style="width:10%">' +
                    '<input type="text" class="form-control" name="unit1[]" value="' + value.UnitCD + '" placeholder="0" readonly>' +
                    '</td>' +
                    '<td style="width:20%">' +
                    '<input type="number" class="form-control" name="qty2[]" disabled>' +
                    '</td>' +
                    '<td style="width:10%">' +
                    '<input type="text" class="form-control" name="unit2[]" disabled>' +
                    '</td>' +
                    '<td style="width:10%">' +
                    '<button id="' + i + '" type="button" class="btn btn-danger bremove"><i class="icofont icofont-close-line-circled"></i></button>' +
                    '</td>' +
                    '</tr>';
                }
                $('#tPO #tPOBody').append(trow);
              });
            }
          });
        } else {
          Swal.fire({
            position: "center",
            icon: "error",
            title: "No Data!",
            text: "Silahkan pilih Purchase Order telebih dahulu",
            showConfirmButton: false,
            timer: 2000
          });
        }
      });
    });

    function viewReception(x) {
      document.location = "viewReception.php?id=" + x.value;
    }

    function printInv(button) {
      var ReceptionID = button.value;
      var url = "../Process/generate_reception_pdf.php?ReceptionID=" + ReceptionID;
      window.open(url, '_blank');
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
                  <p><b> Selamat! </b>Penerimaan Barang berhasil disimpan ke database.</p>
                  <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
                } else if ($_GET["status"] == "error") {
                  echo '<div class="alert txt-danger border-danger outline-2x alert-dismissible fade show alert-icons" role="alert">
                  <p><b> Error! </b>Terjadi kesalahan saat pembuatan/penyimpanan Penerimaan Barang ke database.</p>
                  <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
                }
              }
              ?>
              <div class="row">
                <div class="col-sm-6 ps-0">
                  <h3>PENERIMAAN BARANG</h3>
                </div>
                <div class="col-sm-6 pe-0">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">
                        <svg class="stroke-icon">
                          <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                        </svg></a></li>
                    <li class="breadcrumb-item">Pembelian (Lokal)</li>
                    <li class="breadcrumb-item">Penerimaan Barang</li>
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
                        <p><b> Selamat! </b>Penerimaan Barang berhasil disimpan ke database.</p>
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
                      } else if ($_GET["status"] == "error") {
                        echo '<div class="alert txt-danger border-danger outline-2x alert-dismissible fade show alert-icons" role="alert">
                        <p><b> Error! </b>Terjadi kesalahan saat pembuatan/penyimpanan Penerimaan Barang ke database.</p>
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
                      }
                    }
                    ?>
                    <div class="row">
                      <div class="col-sm-6 ps-0">
                        <h3>PENERIMAAN BARANG</h3>
                      </div>
                      <div class="col-sm-6 pe-0">
                        <ol class="breadcrumb">
                          <li class="breadcrumb-item"><a href="index.html">
                              <svg class="stroke-icon">
                                <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                              </svg></a></li>
                          <li class="breadcrumb-item">Pembelian (Lokal)</li>
                          <li class="breadcrumb-item">Penerimaan Barang</li>
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
                          $query_access = "SELECT tPenerimaanBarang FROM useraccesslevel WHERE UserID = '$userID'";
                          $result_access = mysqli_query($conn, $query_access);

                          if ($result_access) {
                            $row_access = mysqli_fetch_assoc($result_access);
                            $access_level = $row_access['tPenerimaanBarang'];
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
                        <button class="btn btn-outline-primary" type="button" <?php echo $hasAccess ? 'data-bs-toggle="modal" data-bs-target=".bd-example-modal-xl"' : 'disabled'; ?>>
                          <i class="fa fa-plus-circle"></i> New
                        </button>
                        <div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog"
                          aria-labelledby="myExtraLargeModal" aria-hidden="true">
                          <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h4 class="modal-title" id="myExtraLargeModal">Form Penerimaan Barang</h4>
                                <button class="btn-close py-0" type="button" data-bs-dismiss="modal"
                                  aria-label="Close"></button>
                              </div>
                              <div class="modal-body dark-modal">
                                <div class="card-body custom-input">
                                  <form class="row g-3" action="../Process/createReception.php" method="POST"
                                    enctype="multipart/form-data">
                                    <div class="col-3">
                                      <label class="form-label" for="exampleFormControlInput1">Tanggal</label>
                                      <input class="form-control" name="daterec" id="exampleFormControlInput1"
                                        type="datetime-local" value="<?php echo date('Y-m-d H:i:s'); ?>">
                                    </div>
                                    <div class="col-6">
                                      <label class="form-label" for="poid">Purchase Order<span
                                          style="color:red;">*</span></label>
                                      <input class="form-control" id="poid" name="poid" list="poOptions"
                                        placeholder="-- Pilih Purchase Order --" required>
                                      <datalist id="poOptions">
                                        <?php
                                        $query = "SELECT po.PurchaseOrderID, s.SupplierName, po.CategoryCD
                                            FROM purchaseorderheader po, supplier s 
                                            WHERE po.ApprovalStatus='Approved' 
                                                  AND po.Finish=0 
                                                  AND po.SupplierNum=s.SupplierNum
                                                  ORDER BY po.PurchaseOrderID";
                                        $result = mysqli_query($conn, $query);
                                        while ($row = mysqli_fetch_array($result)) {
                                          echo '<option value="' . $row["PurchaseOrderID"] . ' | ' . $row["SupplierName"] . ' | ' . $row["CategoryCD"] . '"></option>';
                                        }
                                        ?>
                                      </datalist>
                                    </div>
                                    <div class="col-3">
                                      <label class="form-label" for="buttonGen">Generate</label>
                                      <button class="form-control btn btn-primary" type="button"
                                        id="buttonGen">Generate</button>
                                    </div>
                                    <div class="col-3">
                                      <label class="form-label" for="gudang">Gudang<span
                                          style="color:red;">*</span></label>
                                      <input class="form-control" id="gudang" name="gudang" list="gudangOptions"
                                        placeholder="Gudang" required>
                                      <datalist id="gudangOptions">
                                        <?php
                                        $query = "SELECT WarehCD, WarehName FROM warehouse";
                                        $result = mysqli_query($conn, $query);
                                        while ($row = mysqli_fetch_array($result)) {
                                          echo '<option value="' . $row["WarehCD"] . '">' . $row["WarehName"] . '</option>';
                                        }
                                        ?>
                                      </datalist>
                                    </div>
                                    <div class="col-3">
                                      <label class="form-label" for="termin">Termin (Hari)<span
                                          style="color:red;">*</span></label>
                                      <input class="form-control" id="termin" name="termin" list="terminOptions"
                                        placeholder="Termin" required>
                                      <datalist id="terminOptions">
                                        <option value="5"></option>
                                        <option value="10"></option>
                                        <option value="15"></option>
                                        <option value="20"></option>
                                        <option value="25"></option>
                                        <option value="30"></option>
                                      </datalist>
                                    </div>
                                    <div class="col-12">
                                      <label class="form-label" for="desc">Keterangan</label>
                                      <input class="form-control" id="desc" name="desc" type="text">
                                    </div>
                                    <input type="hidden" id="category" name="category">
                                    <hr>
                                    <ul class="simple-wrapper nav nav-tabs" id="myTab" role="tablist">
                                      <li class="nav-item"><a class="nav-link active txt-primary" id="home-tab"
                                          data-bs-toggle="tab" href="#detail" role="tab" aria-controls="home"
                                          aria-selected="true">Detail</a></li>
                                      <li class="nav-item"><a class="nav-link txt-primary" id="dokumen-tabs"
                                          data-bs-toggle="tab" href="#dokumen" role="tab" aria-controls="dokumen"
                                          aria-selected="false">Dokumen</a></li>
                                      <li class="nav-item"><a class="nav-link txt-primary" id="profile-tabs"
                                          data-bs-toggle="tab" href="#photofiles" role="tab" aria-controls="profile"
                                          aria-selected="false">Foto</a></li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                      <div class="tab-pane fade show active" id="detail" role="tabpanel"
                                        aria-labelledby="home-tab">
                                        <table id="tPO" class="table">
                                          <thead>
                                            <tr>
                                              <th>Barang</th>
                                              <th>Jumlah</th>
                                              <th>Satuan</th>
                                              <th>Jumlah</th>
                                              <th>Satuan</th>
                                              <th>Action</th>
                                            </tr>
                                          </thead>
                                          <tbody id="tPOBody">
                                            <!-- APPEND BY AJAX -->
                                          </tbody>
                                        </table>
                                      </div>
                                      <div class="tab-pane fade show " id="dokumen" role="tabpanel"
                                        aria-labelledby="dokumen-tab">
                                        <div class="row">
                                          <div class="col-6">
                                            <label class="form-label" for="noSuratJalan">No. Surat Jalan</label>
                                            <input class="form-control" id="noSuratJalan" name="noSuratJalan"
                                              type="text">
                                            <label class="form-label" for="noInvoice">No. Invoice</label>
                                            <input class="form-control" id="noInvoice" name="noInvoice" type="text">
                                          </div>
                                          <div class="col-4">
                                            <label class="form-label" for="dokSuratJalan">Dokumen Surat
                                              Jalan</label>
                                            <input class="form-control" id="dokSuratJalan" name="dokSuratJalan"
                                              type="file">
                                            <label class="form-label" for="dokInvoice">Dokumen Invoice</label>
                                            <input class="form-control" id="dokInvoice" name="dokInvoice" type="file">
                                          </div>
                                        </div>
                                        <hr>
                                      </div>
                                      <div class="tab-pane fade show" id="photofiles" role="tabpanel"
                                        aria-labelledby="profile-tab">
                                        <div class="card">
                                          <div class="card-body">
                                            <label class="form-label" for="dokbarang">Dokumen Barang</label>
                                            <input class="form-control" id="dokbarang" name="dokbarang[]" type="file"
                                              multiple required>
                                            <div id="previewContainer" class="preview-container"></div>
                                            <script>
                                              document.getElementById('dokbarang').addEventListener('change', function (event) {
                                                const previewContainer = document.getElementById('previewContainer');
                                                previewContainer.innerHTML = ''; // Clear previous previews
                                                const files = event.target.files;
                                                Array.from(files).forEach(file => {
                                                  if (file.type.startsWith('image/')) {
                                                    const reader = new FileReader();
                                                    reader.onload = function (e) {
                                                      const img = document.createElement('img');
                                                      img.src = e.target.result;
                                                      previewContainer.appendChild(img);
                                                    }
                                                    reader.readAsDataURL(file);
                                                  }
                                                });
                                              });
                                            </script>
                                            <!-- Container for image preview -->
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <br>
                                    <div class="col-12">
                                      <div class="form-check form-switch">
                                        <input class="form-check-input" id="flexSwitchCheckDefault" type="checkbox"
                                          role="switch" required>
                                        <label class="form-check-label" for="flexSwitchCheckDefault">Apakah informasi
                                          diatas sudah benar?</label>
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
                          <li class="nav-item"><a class="dropdown-item active txt-primary f-w-500 f-18" id="home-tab"
                              data-bs-toggle="tab" href="#listnoinvoicing" role="tab" aria-controls="home"
                              aria-selected="true">Belum Invoicing</a></li>
                          <li class="nav-item"><a class="dropdown-item txt-primary f-w-500 f-18" id="profile-tabs"
                              data-bs-toggle="tab" href="#listinvoicing" role="tab" aria-controls="profile"
                              aria-selected="false">Sudah Invoicing</a></li>
                        </ul>
                        <hr>
                        <div class="tab-content" id="myTabContent">
                          <div class="tab-pane fade show active" id="listnoinvoicing" role="tabpanel">
                            <h3>Daftar Penerimaan</h3><small>Status : Belum Invoicing</small>
                            <br>
                            <div class="table-responsive custom-scrollbar user-datatable">
                              <table class="display" id="basic-12">
                                <thead>
                                  <tr>
                                    <th>Nomor Penerimaan</th>
                                    <th>Tanggal</th>
                                    <th>Purchase Order</th>
                                    <th>Supplier</th>
                                    <th>No Surat Jalan</th>
                                    <th>No Invoice</th>
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
                                  $query_access = "SELECT tPenerimaanBarang FROM useraccesslevel WHERE UserID = '$creator'";
                                  $result_access = mysqli_query($conn, $query_access);
                                  $can_update = false;
                                  if ($result_access) {
                                    $row_access = mysqli_fetch_assoc($result_access);
                                    $access_level = $row_access['tPenerimaanBarang'];
                                    if (strpos($access_level, 'R') !== false) {
                                      $can_update = true;
                                    }
                                  } else {
                                    die("Error: Gagal mengambil data akses pengguna.");
                                  }
                                  $queryR = "SELECT r.ReceptionID, r.CreatedOn, r.PurchaseOrderID, s.SupplierName, r.SuratJalan, r.Invoice
                                  FROM receptionheader r, purchaseorderheader p, supplier s 
                                  WHERE r.PurchaseOrderID=p.PurchaseOrderID
                                        AND p.SupplierNum=s.SupplierNum
                                        AND r.Status = 0";
                                  $resultR = mysqli_query($conn, $queryR);
                                  while ($rowR = mysqli_fetch_array($resultR)) {
                                    echo '<tr>
                                          <td>' . $rowR["ReceptionID"] . '</td>
                                          <td>' . $rowR["CreatedOn"] . '</td>
                                          <td>' . $rowR["PurchaseOrderID"] . '</td>
                                          <td>' . $rowR["SupplierName"] . '</td>
                                          <td>' . $rowR["SuratJalan"] . '</td>
                                          <td>' . $rowR["Invoice"] . '</td>
                                          <td> 
                                          <ul> ';
                                    if ($can_update) {
                                      echo '<button style="padding:5px 10px 5px 10px;" onclick="viewReception(this)" value="' . $rowR["ReceptionID"] . '" type="button" class="light-card border-primary border b-r-10"><i class="fa fa-eye txt-primary"></i></button>';
                                    }
                                    echo '   <button style="padding:5px 10px 5px 10px;" onclick="printInv(this)" type="button" class="light-card border-info border b-r-10" value="' . $rowR["ReceptionID"] . '"><i class="fa fa-print txt-info"></i></button>                                                          
                                          </ul>
                                          </td>
                                      </tr>';
                                  }
                                  ?>

                                </tbody>
                              </table>
                            </div>
                          </div>
                          <div class="tab-pane fade" id="listinvoicing" role="tabpanel">
                            <h3>Daftar Penerimaan</h3><small>Status : Invoicing</small>
                            <br><br>
                            <div class="table-responsive custom-scrollbar user-datatable">
                              <table class="display" id="basic-100">
                                <thead>
                                  <tr>
                                    <th>Nomor Penerimaan</th>
                                    <th>Tanggal</th>
                                    <th>Purchase Order</th>
                                    <th>Supplier</th>
                                    <th>Surat Jalan</th>
                                    <th>Invoice</th>
                                    <th>Action</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                  $queryR = "SELECT r.ReceptionID, r.CreatedOn, r.PurchaseOrderID, s.SupplierName, r.SuratJalan,  r.Invoice
                                  FROM receptionheader r, purchaseorderheader p, supplier s 
                                  WHERE r.PurchaseOrderID=p.PurchaseOrderID
                                        AND p.SupplierNum=s.SupplierNum
                                        AND r.Status = 1";
                                  $resultR = mysqli_query($conn, $queryR);
                                  while ($rowR = mysqli_fetch_array($resultR)) {
                                    echo '<tr>
                                        <td>' . $rowR["ReceptionID"] . '</td>
                                        <td>' . $rowR["CreatedOn"] . '</td>
                                        <td>' . $rowR["PurchaseOrderID"] . '</td>
                                        <td>' . $rowR["SupplierName"] . '</td>
                                        <td>' . $rowR["SuratJalan"] . '</td>
                                        <td>' . $rowR["Invoice"] . '</td>
                                        <td> 
                                        <ul> ';
                                    if ($can_update) {
                                      echo '<button style="padding:5px 10px 5px 10px;" onclick="viewReception(this)" value="' . $rowR["ReceptionID"] . '" type="button" class="light-card border-primary border b-r-10"><i class="fa fa-eye txt-primary"></i></button>';
                                    }
                                    echo '      <button style="padding:5px 10px 5px 10px;" onclick="printInv(this)" type="button" class="light-card border-info border b-r-10" value="' . $rowR["ReceptionID"] . '"><i class="fa fa-print txt-info"></i></button>
                                          </ul>
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
        <style>
          .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
          }

          .preview-container img {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
          }
        </style>
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
        <script src="../../assets/js/dropzone/dropzone.js"></script>
        <script src="../../assets/js/dropzone/dropzone-script.js"></script>
        <script src="../../assets/js/form-validation-custom.js"></script>
        <script src="../../assets/js/height-equal.js"></script>
        <script src="../../assets/js/notify/bootstrap-notify.min.js"></script>
        <script src="../../assets/js/datatable/datatables/jquery.dataTables.min.js"></script>
        <script src="../../assets/js/datatable/datatables/datatable.custom.js"></script>
        <script src="../../assets/js/tooltip-init.js"></script>
        <script src="../../assets/js/modalpage/validation-modal.js"></script>
        <script src="../../assets/js/dropzone/dropzone.js"></script>
        <script src="../../assets/js/dropzone/dropzone-script.js"></script>
        <script src="../../assets/js/filepond/filepond-plugin-image-preview.js"></script>
        <script src="../../assets/js/filepond/filepond-plugin-file-rename.js"></script>
        <script src="../../assets/js/filepond/filepond-plugin-image-transform.js"></script>
        <script src="../../assets/js/filepond/filepond.js"></script>
        <script src="../../assets/js/filepond/custom-filepond.js"></script>
        <!-- Plugins JS Ends-->
        <!-- Theme js-->
        <script src="../../assets/js/script.js"></script>
        <!-- Plugin used-->
</body>

</html>