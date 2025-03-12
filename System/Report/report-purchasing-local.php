<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    include "../headcontent.php";
    include "../DBConnection.php";
    ?>

    <!-- AJAX SCRIPT and DYNAMIC TABLE -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <!-- script sweetaler2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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
                                <h3>REPORT PEMBELIAN (LOKAL)</h3>
                            </div>
                            <div class="col-sm-6 pe-0">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">
                                            <svg class="stroke-icon">
                                                <use href="../../assets/svg/icon-sprite.svg#stroke-home"></use>
                                            </svg></a></li>
                                    <li class="breadcrumb-item">Report</li>
                                    <li class="breadcrumb-item">Pembelian</li>
                                    <li class="breadcrumb-item">Lokal</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Container-fluid starts-->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3>FILTER</h3>
                                </div>
                                <div class="card-body">
                                    <form class="form theme-form" method="POST">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="mb-3 row">
                                                        <label class="col-sm-2">Supplier</label>
                                                        <div class="col-sm-10">
                                                            <input class="form-control" id="supplier" name="supplier" list="custOptions" placeholder="-- Pilih Supplier --">
                                                                <datalist id="custOptions">
                                                                    <?php
                                                                        $queryc = "SELECT * FROM supplier WHERE Status=1";
                                                                        $resultc = mysqli_query($conn,$queryc);
                                                                        while ($rowc = mysqli_fetch_array($resultc)) 
                                                                        {
                                                                            echo '<option value="'.$rowc["SupplierNum"].' - '.$rowc["SupplierName"].'"></option>';
                                                                        }
                                                                    ?>
                                                                </datalist>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 row">
                                                        <label class="col-sm-2">Tanggal Awal Faktur</label>
                                                        <div class="col-sm-10">
                                                            <input class="form-control" id="startdatefaktur" name="startdatefaktur" type="date">
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 row">
                                                        <label class="col-sm-2">Tanggal Akhir Faktur</label>
                                                        <div class="col-sm-10">
                                                            <input class="form-control" id="enddatefaktur" name="enddatefaktur" type="date">
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 row">
                                                        <label class="col-sm-2">Status</label>
                                                        <div class="col-sm-10">
                                                            <input class="form-control" id="status" name="status" list="statOptions" placeholder="-- Pilih Status --">
                                                                <datalist id="statOptions">
                                                                    <option>PENDING</option>
                                                                    <option>LUNAS</option>
                                                                </datalist>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--<button class="btn btn-primary" type="button" onclick="submitFilter()"><i class="fa fa-search"></i> Search</button>-->
                                        <button class="btn btn-primary" name="btnSearch"><i class="fa fa-search"></i> Search</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3>REPORT</h3>
                                </div>
                                <div class="card-body">
                                    <div class="dt-ext table-responsive custom-scrollbar">
                                        <table class="display" id="export-button">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">NO NOTA</th>
                                                    <th rowspan="2">NO FAKTUR PAJAK</th>
                                                    <th rowspan="2">TGL FP/NOTA</th>
                                                    <th rowspan="2">NAMA SUPLIER</th>
                                                    <th rowspan="2">NAMA BARANG</th>
                                                    <th colspan="3" class="text-center">Lain-lain</th>
                                                    <th rowspan="2">PPN MASUKAN</th>
                                                    <th rowspan="2">UTANG DAGANG</th>
                                                </tr>
                                                <tr>
                                                    <th>DPP</th>
                                                    <th>PPh 23</th>
                                                    <th>KETERANGAN</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $tAmount = 0 ;
                                                    $tDPP = 0;
                                                    $tPPN = 0;
                                                    if(isset($_POST["btnSearch"])){
                                                        $query = "SELECT rih.RCV_InvoiceID, rih.TaxInvoiceNumber, rih.TaxInvoiceDate, s.SupplierName, rid.ItemCD, rih.DPP, rih.PPN,
                                                                         rih.TotalAmount, rih.Status
                                                                  FROM receptioninvoiceheader rih, receptioninvoicedetail rid, receptionheader rh, purchaseorderheader po,
                                                                       supplier s
                                                                  WHERE rih.ReceptionID=rh.ReceptionID
                                                                        AND rh.PurchaseOrderID=po.PurchaseOrderID
                                                                        AND po.SupplierNum=s.SupplierNum";
                                            
                                                        if($_POST["supplier"] != ''){
                                                            $suppliers = explode(" - ",$_POST["supplier"]);
                                                            $query .= " AND s.SupplierNum ='".$suppliers[0]."'";
                                                        }
                                                        if($_POST["startdatefaktur"] != ''){
                                                            $query .= " AND rih.TaxInvoiceDate >='".$_POST["startdatefaktur"]."'";
                                                        }
                                                        if($_POST["enddatefaktur"] != ''){
                                                            $query .= " AND rih.TaxInvoiceDate <='".$_POST["enddatefaktur"]."'";
                                                        }
                                                        if($_POST["status"] == 'PENDING'){
                                                            $query .= " AND rih.Status ='0'";
                                                        }else if($_POST["status"] == 'LUNAS'){
                                                            $query .= " AND rih.Status ='1'";
                                                        }

                                                        $result = mysqli_query($conn,$query);
                                                        while($row = mysqli_fetch_array($result)){
                                                            echo' <tr>
                                                                    <td>'.$row["RCV_InvoiceID"].'</td>
                                                                    <td>'.$row["TaxInvoiceNumber"].'</td>
                                                                    <td>'.$row["TaxInvoiceDate"].'</td>
                                                                    <td>'.$row["SupplierName"].'</td>
                                                                    <td>'.$row["ItemCD"].'</td>
                                                                    <td>'.number_format($row["DPP"],0,',','.').'</td>
                                                                    <td> </td>
                                                                    <td> </td>
                                                                    <td>'.number_format($row["PPN"],0,',','.').'</td>
                                                                    <td>'.number_format($row["TotalAmount"],0,',','.').'</td>
                                                                </tr>';
                                                                $tAmount += $row["TotalAmount"];
                                                                $tDPP += $row["DPP"];
                                                                $tPPN += $row["PPN"];
                                                        }
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                    <div class="col-2">
                                        <label>Total Hutang : <?php echo 'Rp ' .  number_format($tAmount, 0, ',', '.'); ?></label>
                                    </div>
                                    <div class="col-2">
                                        <label>Total DPP : <?php echo 'Rp ' .  number_format($tDPP, 0, ',', '.'); ?></label>
                                    </div>
                                    <div class="col-2">
                                        <label>Total PPN : <?php echo 'Rp ' .  number_format($tPPN, 0, ',', '.'); ?></label>
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
    <script src="../../assets/js/notify/bootstrap-notify.min.js"></script>
    <script src="../../assets/js/datatable/datatables/jquery.dataTables.min.js"></script>
    <script src="../../assets/js/datatable/datatable-extension/dataTables.buttons.min.js"></script>
    <script src="../../assets/js/datatable/datatable-extension/jszip.min.js"></script>
    <script src="../../assets/js/datatable/datatable-extension/buttons.colVis.min.js"></script>
    <script src="../../assets/js/datatable/datatable-extension/pdfmake.min.js"></script>
    <script src="../../assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js"></script>
    <script src="../../assets/js/datatable/datatable-extension/buttons.html5.min.js"></script>
    <script src="../../assets/js/datatable/datatable-extension/custom.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="../../assets/js/script.js"></script>
    <!-- Plugin used-->
</body>

</html>