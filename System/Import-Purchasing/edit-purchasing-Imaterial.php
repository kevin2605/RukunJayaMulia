<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    include "../headcontent.php";
    include "../DBConnection.php";
    ?>

</head>
<script>
    function updateSubtotal(input) {
        var row = input.closest('tr');
        var quantity = parseInt(row.querySelector('.quantity').value) || 0;
        var price = parseInt(row.querySelector('.price').value) || 0;
        var subtotal = quantity * price;
        row.querySelector('.subtotal').value = subtotal;

        // Hitung total keseluruhan
        updateTotal();
    }

    function updateTotal() {
        var total = 0;
        document.querySelectorAll('.subtotal').forEach(function (subtotalInput) {
            total += parseInt(subtotalInput.value) || 0;
        });
        document.getElementById('totalAmount').value = total;
    }

    document.addEventListener('DOMContentLoaded', updateTotal);

    function mainAddress() {
        var cb = document.getElementById("chk-Add");
        var ta = document.getElementById("shipadd");

        if (cb.checked == true) {
            shipadd.value = "Pergudangan Safe N Lock";
        } else {
            shipadd.value = null;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.bremove').forEach(function (button) {
            button.addEventListener('click', function () {
                var row = this.closest('tr');
                var itemCD = row.querySelector('.prodlist').value;
                if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '../Process/delete-itemPO.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            row.remove();
                            updateTotal();
                        }
                    };
                    xhr.send('itemCD=' + encodeURIComponent(itemCD));
                }
            });
        });
    });
</script>

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
                                <h3>EDIT PURCHASE ORDER IMPORT</h3>
                            </div>
                            <div class="col-sm-6 pe-0">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">
                                            <svg class="stroke-icon">
                                                <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                                            </svg></a></li>
                                    <li class="breadcrumb-item">Edit PO Import (PPSP)</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Container-fluid starts-->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card height-equal">
                                <div class="modal-body dark-modal">
                                    <div class="card-body custom-input">

                                        <form class="row g-3" action="../Process/updatePOIMaterial.php" method="POST">
                                            <?php
                                            include "../DBConnection.php";
                                            $purchaseOrderID = $_GET['PurchaseOrderID'];

                                            $queryHeader = "SELECT * FROM importpurchaseorderheader WHERE PurchaseOrderID = '$purchaseOrderID'";
                                            $resultHeader = mysqli_query($conn, $queryHeader);
                                            $rowHeader = mysqli_fetch_assoc($resultHeader);

                                            $queryDetails = "SELECT * FROM importpurchaseorderdetail WHERE PurchaseOrderID = '$purchaseOrderID'";
                                            $resultDetails = mysqli_query($conn, $queryDetails);
                                            ?>
                                            <input type="hidden" name="PurchaseOrderID"
                                                value="<?php echo $purchaseOrderID; ?>">

                                            <div class="col-3">
                                                <label class="form-label" for="first-name">Purchase Order ID</label>
                                                <input class="form-control" id="first-name" type="text"
                                                    value="<?php echo $rowHeader['PurchaseOrderID']; ?>" readonly>
                                            </div>
                                            <div class="col-3">
                                                <label class="form-label" for="orderdate">Tanggal Order</label>
                                                <input class="form-control" id="orderdate" type="date"
                                                    value="<?php echo date('Y-m-d', strtotime($rowHeader['CreatedOn'])); ?>"
                                                    readonly>
                                            </div>

                                            <div class="col-3">
                                                <label class="form-label" for="deliverydate">Tanggal Kirim<span
                                                        style="color:red;">*</span></label>
                                                <input class="form-control" id="deliverydate" name="deliverydate"
                                                    type="date" value="<?php echo $rowHeader['DeliveryDate']; ?>"
                                                    readonly>
                                            </div>
                                            <div class="col-3">
                                                <label class="form-label" for="kategori">Kategori Pembelian<span
                                                        style="color:red;">*</span></label>
                                                <input class="form-control" id="kategori" name="kategori"
                                                    value="<?php echo $rowHeader['CategoryCD']; ?>" readonly>
                                            </div>
                                            <div class="col-3">
                                                <label class="form-label" for="creator">Pembuat PO</label>
                                                <input class="form-control" id="creator" name="creator" type="text"
                                                    value="<?php echo $_COOKIE["UserID"] . ' - ' . $_COOKIE["Name"] ?>"
                                                    readonly>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label" for="supplier">Supplier<span
                                                        style="color:red;">*</span></label>
                                                <input class="form-control" id="supplier" name="supplier"
                                                    list="supplierOptions"
                                                    value="<?php echo $rowHeader['SupplierNum']; ?>" required>
                                                <datalist id="supplierOptions">
                                                    <?php
                                                    $query = "SELECT SupplierNum, SupplierName FROM supplier WHERE Status='1'";
                                                    $result = mysqli_query($conn, $query);
                                                    while ($row = mysqli_fetch_array($result)) {
                                                        echo '<option value="' . $row["SupplierNum"] . '">' . $row["SupplierName"] . '</option>';
                                                    }
                                                    ?>
                                                </datalist>
                                            </div>
                                            <div class="col-3">
                                                <label class="form-label" for="termin">Termin (Hari)<span
                                                        style="color:red;">*</span></label>
                                                <input class="form-control" id="termin" name="termin"
                                                    list="terminOptions" value="<?php echo $rowHeader['Termin']; ?>"
                                                    required>
                                                <datalist id="terminOptions">
                                                    <option value="5"></option>
                                                    <option value="10"></option>
                                                    <option value="15"></option>
                                                    <option value="20"></option>
                                                    <option value="25"></option>
                                                    <option value="30"> </option>
                                                </datalist>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" for="shipadd">Alamat Pengiriman<span
                                                        style="color:red;">*</span></label>
                                                <textarea class="form-control" id="shipadd" name="shipadd" rows="3"
                                                    required><?php echo $rowHeader['ShippingAddress']; ?></textarea>
                                                <input class="checkbox_animated" id="chk-Add" type="checkbox"
                                                    style="margin-top:5px;" onclick="mainAddress()"> Alamat Utama
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" for="desc">Keterangan</label>
                                                <textarea class="form-control" id="desc" name="desc"
                                                    rows="2"><?php echo $rowHeader['Description']; ?></textarea>
                                            </div>
                                            <hr>
                                            <div class="d-flex pb-0">
                                                <h3>Detil Order</h3>
                                            </div>
                                            <table id="dinamis" class="table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Barang</th>
                                                        <th scope="col">Jumlah</th>
                                                        <th scope="col">Satuan</th>
                                                        <th scope="col">Harga (exclude)</th>
                                                        <th scope="col">Subtotal</th>
                                                        <th scope="col">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="dbody">
                                                    <?php
                                                    while ($rowDetails = mysqli_fetch_assoc($resultDetails)) {
                                                        ?>
                                                        <tr id="row1">
                                                            <td>
                                                                <input type="text" class="form-control prodlist"
                                                                    name="materials[]"
                                                                    value="<?php echo $rowDetails['ItemCD']; ?>"
                                                                    list="prodOptions" onChange="appendProductTable(this)"
                                                                    required>
                                                                <datalist id="prodOptions">
                                                                    <?php
                                                                    $queryp = "SELECT MaterialCD,MaterialName FROM material";
                                                                    $resultp = mysqli_query($conn, $queryp);
                                                                    while ($rowp = mysqli_fetch_array($resultp)) {
                                                                        echo '<option value="' . $rowp["MaterialCD"] . '">' . $rowp["MaterialName"] . '</option>';
                                                                    }
                                                                    ?>
                                                                </datalist>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control quantity"
                                                                    name="quantities[]"
                                                                    value="<?php echo $rowDetails['Quantity']; ?>"
                                                                    onchange="updateSubtotal(this)" required>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name="units[]"
                                                                    value="<?php echo $rowDetails['UnitCD']; ?>"
                                                                    style="border-style:none;" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control price"
                                                                    name="prices[]"
                                                                    value="<?php echo $rowDetails['Price']; ?>"
                                                                    onchange="updateSubtotal(this)" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control subtotal"
                                                                    name="subtotals[]"
                                                                    value="<?php echo $rowDetails['Subtotal']; ?>"
                                                                    style="border-style:none;" readonly>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-danger bremove">
                                                                    <i class="icofont icofont-close-line-circled"></i>
                                                                </button>
                                                            </td>
                                                        </tr>

                                                        <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                            <hr>
                                            <div class="col-12">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" id="flexSwitchCheckDefault"
                                                        type="checkbox" role="switch" required>
                                                    <label class="form-check-label" for="flexSwitchCheckDefault">Apakah
                                                        informasi diatas sudah benar?</label>
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