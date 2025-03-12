<?php

include "../DBConnection.php";

if (!isset($_POST["submitInv"])) {
    header("Location:../Sales/sales.php?status=no-entry");
}
if (isset($_COOKIE["UserID"]) && !empty($_COOKIE["UserID"])) {
    $creator = $_COOKIE["UserID"];
} else {
    die("Error: Cookie 'UserID' tidak ada atau kosong.");
}

date_default_timezone_set("Asia/Jakarta");

// Generate Invoice ID
$query = "SELECT InvoiceID FROM invoiceheader WHERE substr(CreatedOn,6,2)='" . date("m") . "' ORDER BY InvoiceID DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if ($row["InvoiceID"] != "") {
    $lastnumber = substr($row["InvoiceID"], 10);
    $lastnumber = intval($lastnumber);
    $lastnumber += 1;
} else {
    $lastnumber = 1;
}

$invid = "SINV-" . date("ym") . "-" . str_pad($lastnumber, 4, "0", STR_PAD_LEFT);

// Retrieve form data
$salesorder = explode(" - ", $_POST["salesorder"])[0];
$creator = $_COOKIE["UserID"];
$custid = $_POST["custid"];
$pricelistcd = $_POST["pricelistcd"];
$kodeAkun = "4-1000";
$tipepembayaran = $_POST["tipepembayaran"];
$gudang = $_POST["gudang"];
$ekspedisi = $_POST["ekspedisi"];
$desc = $_POST["desc"];
$cashdisc = isset($_POST["cashdisc"]) ? $_POST["cashdisc"] : 0;
$amountField = isset($_POST["amountField"]) ? str_replace(['Rp. ', '.'], '', $_POST["amountField"]) : 0;
$amountField = floatval($amountField);

$datetime = date('Y-m-d H:i:s');
$invdate = $_POST["invdate"];
if($tipepembayaran == "TN"){
    $duedate = $invdate;
}else if($tipepembayaran == "TP15"){
    $duedate = date("Y-m-d", strtotime("+15 day", strtotime($invdate)));
}else if($tipepembayaran == "TP30"){
    $duedate = date("Y-m-d", strtotime("+1 month", strtotime($invdate)));
}else if($tipepembayaran == "TP60"){
    $duedate = date("Y-m-d", strtotime("+2 month", strtotime($invdate)));
}
$date = date('Y-m-d');
$minus = false;
$arrProd = $_POST["products"];
$arrQty = $_POST["quantities"];
for ($i = 0; $i < count($arrProd); $i++) {
    $products = $arrProd[$i];
    $arrproduct = explode(" - ", $products);
    $product = $arrproduct[0];
    $quantity = $arrQty[$i];
    $querystock = "SELECT StockQty FROM product WHERE ProductCD='" . $product . "'";
    $resultstock = mysqli_query($conn, $querystock);
    $rowstock = mysqli_fetch_assoc($resultstock);

    if ($rowstock["StockQty"] < $quantity) {
        $minus = true;
    }
}

if (!$minus) {
    // Query Insert
    $queryh = "INSERT INTO `invoiceheader`(
    `InvoiceID`, `SalesOrderID`, `CreatedOn`, `DueDate`, `CreatedBy`, `ExpeditionID`,
    `CustID`, `AccountCD`, `PaymentCD`, `WarehCD`, `Description`, 
    `LastEdit`, `DPAmount`, `TotalInvoice`, `TotalPaid`, 
    `Cashdisc`, `PaidDate`, `InvoiceStatus`
) VALUES (
    '$invid','$salesorder','$invdate', '$duedate', '$creator', '$ekspedisi',
    '$custid','$kodeAkun','$tipepembayaran','$gudang','$desc',
    '$datetime', $amountField, NULL, 0,
    $cashdisc, NULL, 0
)";
    $resulth = mysqli_query($conn, $queryh);

    $totalInv = 0;

    if ($resulth == 1) {
        $arrProd = $_POST["products"];
        $arrPrice = $_POST["prices"];
        $arrQty = $_POST["quantities"];
        $arrDiscount = $_POST["discounts"];

        for ($i = 0; $i < count($arrProd); $i++) {
            $products = $arrProd[$i];
            $arrproduct = explode(" - ", $products);
            $product = $arrproduct[0];
            $price = $arrPrice[$i];
            $quantity = $arrQty[$i];
            $discount = $arrDiscount[$i];
            if ($discount == NULL) {
                $discount = 0;
            }
            $subtotal = ($price - $discount) * $quantity;
            $totalInv += $subtotal;

            $queryd = "INSERT INTO `invoicedetail`(`InvoiceID`, `CreatedOn`, `ProductCD`, `Quantity`, `Price`, `Discount`, `Subtotal`) 
                       VALUES ('$invid','$datetime','$product','$quantity','$price','$discount','$subtotal')";
            $resultd = mysqli_query($conn, $queryd);

            $queryx = "UPDATE salesorderdetail SET QuantitySent=QuantitySent+" . $quantity . " WHERE SalesOrderID='" . $salesorder . "' AND ProductCD='" . $product . "'";
            $resultx = mysqli_query($conn, $queryx);

            $queryg = "UPDATE product SET StockQty=StockQty-" . $quantity . " WHERE ProductCD='" . $product . "'";
            $resultg = mysqli_query($conn, $queryg);
        }
    }

    if ($cashdisc == 1) {
        $diskonCash = $totalInv * 0.02; // 2% dari subtotal
        $beforetax = ($totalInv - $diskonCash - $amountField) / 1.11;
        $tax = $beforetax * 0.11;
    }else if($cashdisc == 2){
        $diskonCash = $totalInv * 0.04; // 4% dari subtotal
        $beforetax = ($totalInv - $diskonCash - $amountField) / 1.11;
        $tax = $beforetax * 0.11;
    }else {
        $beforetax = ($totalInv - $amountField) / 1.11;
        $tax = $beforetax * 0.11;
    }
    $totalNett = $totalInv - $amountField - $diskonCash;
    
    $queryu = "UPDATE invoiceheader SET TotalInvoice='" . $totalNett . "' WHERE InvoiceID='" . $invid . "'";
    $resultu = mysqli_query($conn, $queryu);

    //insert journal data
    $queryjd = "INSERT INTO `journaldata`(`JournalDate`, `AccountCD`, `AccountName`, `Debit`, `Credit`, `Notes`) 
                VALUES ('$date','4-1000','Penjualan','$beforetax','0','$invid'),('$date','1-1300','Piutang Usaha','$totalNett','0','$invid');";
    $resultjd = mysqli_query($conn, $queryjd);

    $completeorder = 1;
    $queryz = "SELECT Quantity, QuantitySent FROM salesorderdetail WHERE SalesOrderID='" . $salesorder . "'";
    $resultz = mysqli_query($conn, $queryz);
    while ($rowz = mysqli_fetch_array($resultz)) {
        if ($rowz["Quantity"] != $rowz["QuantitySent"]) {
            $completeorder = 0;
        }
    }

    /*
    $querydp = "SELECT COUNT(*) AS count FROM downpaymentheader WHERE SalesOrderID='$salesorder'";
    $resultdp = mysqli_query($conn, $querydp);
    $rowdp = mysqli_fetch_assoc($resultdp);
    if ($completeorder == 1 || $rowdp['count'] > 0) {
        $queryt = "UPDATE salesorderheader SET Finish=1 WHERE SalesOrderID='" . $salesorder . "'";
        $resultt = mysqli_query($conn, $queryt);
    }

    $queryu = "UPDATE invoiceheader SET TotalInvoice='" . $totalInv . "' WHERE InvoiceID='" . $invid . "'";
    $resultu = mysqli_query($conn, $queryu);

    $completeorder = 1;
    $queryz = "SELECT Quantity, QuantitySent FROM salesorderdetail WHERE SalesOrderID='" . $salesorder . "'";
    $resultz = mysqli_query($conn, $queryz);
    while ($rowz = mysqli_fetch_array($resultz)) {
        if ($rowz["Quantity"] != $rowz["QuantitySent"]) {
            $completeorder = 0;
        }
    }*/

    if ($completeorder == 1) {
        $queryt = "UPDATE salesorderheader SET Finish=1 WHERE SalesOrderID='" . $salesorder . "'";
        $resultt = mysqli_query($conn, $queryt);
    }

    logAction($conn, $creator, 'Create', 'membuat invoice ', $resulth == 1 && $resultd == 1 && $resultu == 1 ? 0 : 1, $invid);

    if ($resulth == 1 && $resultd == 1 && $resultu == 1 && $resultjd == 1) {
        header("Location:../Sales/invoice.php?status=success-inv");
    } else {
        header("Location:../Sales/invoice.php?status=error-inv");
    }
} else {
    logAction($conn, $creator, 'Create', 'Create Invoice Header - Stock Minus', 1, null);
    header("Location:../Sales/invoice.php?status=stock-minus");
}

function logAction($conn, $userID, $actionDone, $actionMSG, $actionStatus, $recordID)
{
    $timestamp = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO systemlog (Timestamp, UserID, ActionDone, ActionMSG, ActionStatus, RecordID) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $timestamp, $userID, $actionDone, $actionMSG, $actionStatus, $recordID);
    $stmt->execute();
    $stmt->close();
}
?>