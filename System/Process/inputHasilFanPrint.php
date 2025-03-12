<?php

include "../DBConnection.php";

// Set timezone
date_default_timezone_set("Asia/Jakarta");

$mutationid = $_POST["mutationid"];
$materialcd = $_POST["materialcd"];
$qtyFanPrint = $_POST["qtyFanPrint"];

if (isset($_COOKIE["UserID"]) && !empty($_COOKIE["UserID"])) {
    $creator = $_COOKIE["UserID"];
} else {
    die("Error: Cookie 'UserID' tidak ada atau kosong.");
}

$createdOn = date('Y-m-d H:i:s');

//update mutation detail
$query = "UPDATE mutationdetailfan SET FlowIn='$qtyFanPrint', CreatedOn='$createdOn' WHERE MutationID='$mutationid' AND MaterialCD='$materialcd'";
$result = mysqli_query($conn, $query);

//update masuk stok fan print 
$queryUpdateStok = "UPDATE material SET StockQty = StockQty+'$qtyFanPrint' WHERE MaterialCD='$materialcd'";
$result = mysqli_query($conn, $queryUpdateStok);

// insert fan flow history masuk
$queryUpdateStock = "INSERT INTO `materialflowhistory`(`Date`, `ReferenceKey`, `MaterialCD`, `FlowIn`, `FlowOut`, `Description`) 
                    VALUES ('$createdOn','$mutationid','$materialcd','$qtyFanPrint','0','Input Hasil Printing')";
mysqli_query($conn,$queryUpdateStock);

if ($result) {
    logAction($conn, $creator, 'Create', 'berhasil input hasil fan printing', 0, $namalogo);
    header("Location:../Mutation/view-mutation-FTF.php?status=success&id=".$mutationid);
} else {
    logAction($conn, $creator, 'Create', 'gagal input hasil fan printing', 1, $namalogo);
    echo "Error: " . mysqli_error($conn) . "<br>";
    header("Location:../Mutation/view-mutation-FTF.php?status=error&id=".$mutationid);
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