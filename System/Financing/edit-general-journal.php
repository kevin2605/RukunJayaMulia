<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  include "../headcontent.php";
  include "../DBConnection.php";
  ?>

  <script>
    function editInv(str) {
      //document.location = "editSalesOrder.php?id=" + str.value;
    }

    function disableOtherInput(currentInput) {
      let row = currentInput.closest('tr');
      let flowinInput = row.querySelector('.debit');
      let flowoutInput = row.querySelector('.credit');

      if (currentInput === flowinInput && flowinInput.value !== '') {
        flowoutInput.setAttribute('readonly', true);
      } else if (currentInput === flowoutInput && flowoutInput.value !== '') {
        flowinInput.setAttribute('readonly', true);
      }
      if (flowinInput.value === '') {
        flowoutInput.removeAttribute('readonly');
      }
      if (flowoutInput.value === '') {
        flowinInput.removeAttribute('readonly');
      }
    }
    
    function getAccName(x){
        //get account name
        $.ajax({
            type: "POST",
            url: "../Process/getAccountName.php",
            data: "acctcd=" + x.value,
            success: function (result) {
                var res = JSON.parse(result);
                $.each(res, function (index, value) {
                x.parentElement.parentElement.cells[1].getElementsByTagName("input")[0].value = value.AccountName;
                });
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
            <div class="row">
              <div class="col-sm-6 ps-0">
                <h3>JURNAL UMUM</h3>
              </div>
              <div class="col-sm-6 pe-0">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="index.html">
                      <svg class="stroke-icon">
                        <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                      </svg></a></li>
                <li class="breadcrumb-item">Keuangan</li>
                <li class="breadcrumb-item">Jurnal Umum</li>
                <li class="breadcrumb-item">Edit</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <!-- Container-fluid starts-->
        <?php
        $gjid = $_GET["gjid"];
        $query = "SELECT * FROM genjournalheader WHERE GenJourID ='" . $gjid . "'";
        $result = mysqli_query($conn, $query);
        $mut = mysqli_fetch_assoc($result);
        ?>
        <div class="container-fluid">
          <div class="row">
              <div class="card">
                <div class="card-header">
                  Informasi Jurnal Umum
                </div>
                <div class="card-body">
                  <div class="mb-2 row">
                    <label class="col-sm-1">#ID</label>
                    <div class="col-sm-3">
                      <input class="form-control" type="text" value="<?php echo $mut["GenJourID"]; ?>" readonly>
                    </div>
                  </div>
                  <div class="mb-2 row">
                    <label class="col-sm-1">Tgl. Jurnal</label>
                    <div class="col-sm-3">
                      <input class="form-control" type="text" value="<?php echo $mut["JournalDate"]; ?>" readonly>
                    </div>
                  </div>
                  <div class="mb-2 row">
                    <label class="col-sm-1">Memo</label>
                    <div class="col-sm-3">
                      <input class="form-control" type="text" value="<?php echo $mut["MemoID"]; ?>" readonly>
                    </div>
                    <div class="col-sm-3">
                      <input class="form-control" type="text" value="<?php echo $mut["MemoDesc"]; ?>" readonly>
                    </div>
                  </div>
                  <div class="mb-2 row">
                    <label class="col-sm-1">Keterangan</label>
                    <div class="col-sm-6">
                      <input class="form-control" type="text" value="<?php echo $mut["Description"]; ?>" readonly>
                    </div>
                  </div>
                </div>
              </div>
          </div>
          <div class="row">
            <datalist id="accOptions">
                <?php
                    $queryp = "SELECT * FROM chartofaccount";
                    $resultp = mysqli_query($conn, $queryp);
                    while ($rowp = mysqli_fetch_array($resultp)) {
                        echo '<option value="' . $rowp["AccountCD"] . '">' . $rowp["AccountName"] . '</option>';
                    }
                ?>
            </datalist>
            <div class="card">
                <div class="card-body">
                    <div class="col-md-8">
                        <form action="../Process/editGenJournal.php" method="POST">
                            <input type="hidden" name="gjid" value="<?php echo $gjid; ?>">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Kode Akun</th>
                                        <th>Nama Akun</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $query = "SELECT *
                                                FROM genjournaldetail
                                                WHERE GenJourID='".$gjid."'";
                                        $result = mysqli_query($conn, $query);
                                        while ($row = mysqli_fetch_array($result)) {
                                            echo '
                                                    <tr>
                                                        <input type="hidden" name="no[]" value="'.$row["No"].'">
                                                        <td style="width: 15%;">
                                                            <input type="text" class="form-control prodlist" name="akun[]" list="accOptions" onChange="getAccName(this)" value="'.$row["AccountCD"].'" required>
                                                        </td>
                                                        <td style="width: 45%;">
                                                            <input type="text" class="form-control" name="namaakun[]" placeholder="-" value="'.$row["AccountName"].'" readonly>
                                                        </td>
                                                        <td style="width: 15%;">
                                                            <input type="number" class="form-control digits debit" name="debit[]" oninput="disableOtherInput(this)" value="'.$row["Debit"].'">
                                                        </td>
                                                        <td style="width: 15%;">
                                                            <input type="number" class="form-control digits credit" name="credit[]" oninput="disableOtherInput(this)" value="'.$row["Credit"].'">
                                                        </td>
                                                    </tr>
                                                ';
                                        }
                                    ?>
                                </tbody>
                            </table>
                            <br>
                            <a class="btn btn-warning" href="general-journal.php">Back</a>
                            <button class="btn btn-primary">Save</button>
                        </form>
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
  <script src="../../assets/js/form-wizard/form-wizard.js"></script>
  <script src="../../assets/js/form-wizard/image-upload.js"></script>
  <!-- Plugins JS Ends-->
  <!-- Theme js-->
  <script src="../../assets/js/script.js"></script>
  <!-- Plugin used-->
</body>

</html>