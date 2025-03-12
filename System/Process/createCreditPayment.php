<?php

include "../DBConnection.php";

//set timezone
date_default_timezone_set("Asia/Jakarta");

//create payment id
$duplicate = true;
$cpid = 0;
while($duplicate){
    $cpid = rand(111111,999999);
    $query = "SELECT CreditPaymentID FROM creditpaymentheader WHERE CreditPaymentID = '" . $cpid . "'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row["CreditPaymentID"] == "") {
        $duplicate = false;
    }
}

$customer = $_POST["customer"];
$paydate = $_POST["tanggal"];
$method = $_POST["method"];
$desc = $_POST["desc"];
$creator = $_COOKIE["UserID"];
$createdOn = date('Y-m-d H:i:s');

$queryh = "INSERT INTO `creditpaymentheader`(`CreditPaymentID`, `CreatedOn`, `CreatedBy`, `CustID`, `PaymentMethod`, `Description`)
            VALUES ('$cpid', '$paydate', '$creator', '$customer', '$method', '$desc')";
$resulth = mysqli_query($conn, $queryh);

if ($resulth == 1) {
    
    $arrID = $_POST["InvID"];
    $arrOutstand = $_POST["Outstanding"];
    $arrPayment = $_POST["TotalPayment"];

    for ($i = 0; $i < count($arrID); $i++) {
        $id = $arrID[$i];
        $amount = str_replace(',', '', $arrPayment[$i]);
        $outstanding = str_replace(',', '', $arrOutstand[$i]);
        if($outstanding > 0 && $amount > 0){
            echo $id."|".$amount."|".$outstanding."<br>";
            //insert credit payment detail
            $queryd = "INSERT INTO `creditpaymentdetail`(`CreditPaymentID`, `CreatedOn`, `InvoiceID`, `TotalPayment`)
                VALUES ('$cpid', '$paydate', '$id', '$amount')";
            $resultd = mysqli_query($conn, $queryd);

            //update invoice status
            if($outstanding == $amount){
                $queryu = "UPDATE invoiceheader SET TotalPaid = TotalPaid + $amount, InvoiceStatus=2, PaidDate='".$paydate."' WHERE InvoiceID='".$id."'";
                $resultu = mysqli_query($conn, $queryu);
            }else if($amount < $outstanding){
                $queryu = "UPDATE invoiceheader SET TotalPaid = TotalPaid + $amount, PaidDate='".$paydate."' WHERE InvoiceID='".$id."'";
                $resultu = mysqli_query($conn, $queryu);
            }
        }
    }
}

if ($resulth && $resultd && $resultu) {
    logAction($conn, $creator, 'Create', 'membuat Pelunasan Piutang', 0, $cpid);
    header("Location:../Payment/payment-of-credit.php?status=success");
} else {
    logAction($conn, $creator, 'Create', 'Add Pelunasan Piutang Failed', 1, $cpid);
    header("Location:../Payment/payment-of-credit.php?status=error");
}

function logAction($conn, $userID, $actionDone, $actionMSG, $actionStatus, $recordID)
{
    $timestamp = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO systemlog (Timestamp, UserID, ActionDone, ActionMSG, ActionStatus, RecordID) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssssss", $timestamp, $userID, $actionDone, $actionMSG, $actionStatus, $recordID);
        $stmt->execute();
        $stmt->close();
    } else {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
}
?>