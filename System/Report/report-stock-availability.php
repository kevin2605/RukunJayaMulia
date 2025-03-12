<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    include "../headcontent.php";
    session_start();

    // Koneksi ke database
    include "../DBConnection.php"; // Sesuaikan dengan file koneksi database Anda
    
    // Ambil ID pengguna dari sesi atau cookie
    $userID = $_COOKIE['UserID']; // Sesuaikan dengan cara Anda menyimpan ID pengguna
    
    // Ambil akses level dari database
    $query = "SELECT tInvoice FROM useraccesslevel WHERE UserID = '$userID'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    // Cek akses CRUD dan tentukan apakah akses diizinkan
    $hasCRUDAccess = strpos($row['tInvoice'], 'C') !== false || // Create
        strpos($row['tInvoice'], 'R') !== false || // Read
        strpos($row['tInvoice'], 'U') !== false || // Update
        strpos($row['tInvoice'], 'D') !== false;  // Delete
    
    // Jika tidak memiliki akses CRUD, tampilkan pesan dan redirect
    $accessDenied = !$hasCRUDAccess;
    ?>

    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    

    <!-- AJAX SCRIPT and DYNAMIC TABLE -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <!-- script sweetaler2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
    <script>
        $('.showinfo').click(function (e) {
            e.preventDefault();
            $(this).closest('td').find(".test").toggle();
        });
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
                                if ($_GET["status"] == "success-inv") {
                                    echo '<div class="alert txt-success border-success outline-2x alert-dismissible fade show alert-icons" role="alert">
                                    <p><b> Selamat! </b>Invoice baru berhasil disimpan ke database.</p>
                                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>';
                                } else if ($_GET["status"] == "error-inv") {
                                    echo '<div class="alert txt-danger border-danger outline-2x alert-dismissible fade show alert-icons" role="alert">
                                <p><b> Error! </b>Terjadi kesalahan saat pembuatan/penyimpanan Invoice ke database.</p>
                                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>';
                                } else if ($_GET["status"] == "stock-minus") {
                                    echo '<div class="alert txt-danger border-danger outline-2x alert-dismissible fade show alert-icons" role="alert">
                                    <p><b> Fail! </b>Stock produk tidak cukup untuk membuat Invoice</p>
                                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>';
                                }
                            }
                            ?>
                            <div class="row">
                                <div class="col-sm-6 ps-0">
                                    <h3>KEKURANGAN SO</h3>
                                </div>
                                <div class="col-sm-6 pe-0">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="index.html">
                                                <svg class="stroke-icon">
                                                    <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                                                </svg></a></li>
                                        <li class="breadcrumb-item">Report</li>
                                        <li class="breadcrumb-item">Kekurangan SO</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Container-fluid starts-->
                    <div class="container-fluid <?php echo $accessDenied ? 'hidden' : ''; ?>">
                    <?php endif; ?>
                    <div class="">
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
                                                if ($_GET["status"] == "success-inv") {
                                                    echo '<div class="alert txt-success border-success outline-2x alert-dismissible fade show alert-icons" role="alert">
                                                        <p><b> Selamat! </b>Invoice baru berhasil disimpan ke database.</p>
                                                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                                                    </div>';
                                                } else if ($_GET["status"] == "error-inv") {
                                                    echo '<div class="alert txt-danger border-danger outline-2x alert-dismissible fade show alert-icons" role="alert">
                                                        <p><b> Error! </b>Terjadi kesalahan saat pembuatan/penyimpanan Invoice ke database.</p>
                                                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                                                    </div>';
                                                } else if ($_GET["status"] == "stock-minus") {
                                                    echo '<div class="alert txt-danger border-danger outline-2x alert-dismissible fade show alert-icons" role="alert">
                                                    <p><b> Stok Minus! </b>Tidak cukup untuk membuat Invoice</p>
                                                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                                                    </div>';
                                                }
                                            }
                                            ?>
                                            <div class="row">
                                                <div class="col-sm-6 ps-0">
                                                    <h3>KEKURANGAN SO</h3>
                                                </div>
                                                <div class="col-sm-6 pe-0">
                                                    <ol class="breadcrumb">
                                                        <li class="breadcrumb-item"><a href="index.html">
                                                                <svg class="stroke-icon">
                                                                    <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                                                                </svg></a></li>
                                                        <li class="breadcrumb-item">Report</li>
                                                        <li class="breadcrumb-item">Kekurangan SO</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="table-responsive custom-scrollbar signal-table">
                                                        <table class="table table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th>Urutan Report</th>
                                                                    <th>Kode Produk</th>
                                                                    <th>Nama Produk</th>
                                                                    <th>Jumlah Stok</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="produk-table-body">
                                                                <?php
                                                                //QUERY ALL PRODUCT
                                                                $query = "SELECT * FROM product ORDER BY Sequence";
                                                                $result = mysqli_query($conn, $query);
                                                                while ($row = mysqli_fetch_array($result)) {
                                                                    echo '<tr>
                                                                            <td><a data-toggle="collapse" href="#' . $row["ProductCD"] . '" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-plus-square-o"></i></a></td>
                                                                            <td>' . $row["Sequence"] . '</td>
                                                                            <td>' . $row["ProductCD"] . '</td>
                                                                            <td>' . $row["ProductName"] . '</td>
                                                                            <td>' . number_format($row["StockQty"], 0, ',', '.') . '</td>
                                                                            
                                                                         </tr>';
                                                                    echo '<tr>
                                                                            <td colspan=7 class="collapse" id="' . $row["ProductCD"] . '">
                                                                                <div class="card">
                                                                                    <table class="table table-striped">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Sales Order</th>
                                                                                                <th scope="col">Tanggal</th>
                                                                                                <th scope="col">Jumlah Sisa</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>';
                                                                                            $tPesanan = 0;
                                                                                            $queryp = "SELECT * FROM salesorderdetail WHERE ProductCD='".$row["ProductCD"]."' AND Quantity != QuantitySent";
                                                                                            $resultp = mysqli_query($conn, $queryp);
                                                                                            while ($rowp = mysqli_fetch_array($resultp)) {
                                                                                                echo '<tr>
                                                                                                    <td>' . $rowp["SalesOrderID"] . '</td>
                                                                                                    <td>' . $rowp["CreatedOn"] . '</td>
                                                                                                    <td>' . number_format($rowp["Quantity"] - $rowp["QuantitySent"], 0, ',', '.') . '</td>
                                                                                                </tr>';
                                                                                                $tPesanan += $rowp["Quantity"] - $rowp["QuantitySent"];
                                                                                            }
                                                                                            
                                                                    echo '                  <tr>
                                                                                                <td colspan="2" class="text-end">Total</td>
                                                                                                <td>'.number_format($tPesanan, 0, ',', '.').'</td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
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
                    <script src="../../assets/js/select2/custom-inputsearch.js"></script>
                    <!-- Plugins JS Ends-->

                    <!-- JS FOR NOTF -->
                    <script src="../../assets/js/notify/index.js"></script>
                    <!-- Theme js-->
                    <script src="../../assets/js/script.js"></script>
                    <!-- Plugin used-->
</body>

</html>