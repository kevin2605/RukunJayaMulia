<?php
include "../DBConnection.php";

// Set timezone
date_default_timezone_set("Asia/Jakarta");

// Ambil data dari POST
$desc = $_POST["desc"];
$group = explode(" - ", $_POST["group"])[0];
$bahan = explode(" - ", $_POST["bahan"])[0];
$unit = $_POST["unit"];
$flowout = $_POST["flowout"];
$creator = $_COOKIE["UserID"] ?? 'unknown';
$datetime = date('Y-m-d H:i:s');

//generate ReleaseOrderID
$queryID = "SELECT ReleaseOrderID FROM materialreleaseorder WHERE substr(CreatedOn,6,2)='" . date("m") . "' ORDER BY CreatedOn DESC LIMIT 1";
$resultID = mysqli_query($conn, $queryID);
$rowID = mysqli_fetch_assoc($resultID);

if ($rowID["ReleaseOrderID"] != "") {
    $lastnumber = substr($rowID["ReleaseOrderID"], 9);
    $lastnumber = intval($lastnumber);
    $lastnumber += 1;
} else {
    $lastnumber = 1;
}
$skb = "SKB-" . date("ym") . "-" . str_pad($lastnumber, 4, "0", STR_PAD_LEFT);

//insert realease order
$query = "INSERT INTO `materialreleaseorder`(`ReleaseOrderID`, `CreatedOn`, `MaterialCD`, `GroupCD`, `Quantity`, `UnitCD`, `Description`, `CreatedBy`) 
            VALUES ('$skb','$datetime','$bahan','$group','$flowout','$unit','$desc','$creator')";
$result = mysqli_query($conn, $query);

//check which ledger will be added
if($group == "BPPK"){
    $queryA = "SELECT * FROM mat_ppk_ledger ORDER BY 1 DESC LIMIT 1";
    $resultA = mysqli_query($conn, $queryA);
    $rowA = mysqli_fetch_assoc($resultA);

    $remaining = $rowA["RemainingAmount"];
    $newAmount = $remaining + $flowout; //flowout barang keluar dari stok masuk ledger

    $query = "INSERT INTO `mat_ppk_ledger`(`CreatedOn`, `ReferenceKey`, `MaterialCD`, `Quantity`, `UnitCD`, `InVsOut`, `RemainingAmount`) 
              VALUES ('$datetime','$skb','$bahan','$flowout','$unit','0','$newAmount')";
    $result = mysqli_query($conn, $query);
}else if($group == "BPPH"){
    $queryB = "SELECT * FROM mat_pph_ledger ORDER BY 1 DESC LIMIT 1";
    $resultB = mysqli_query($conn, $queryB);
    $rowB = mysqli_fetch_assoc($resultB);

    $remaining = $rowB["RemainingAmount"];
    $newAmount = $remaining + $flowout; //flowout barang keluar dari stok masuk ledger

    $query = "INSERT INTO `mat_pph_ledger`(`CreatedOn`, `ReferenceKey`, `MaterialCD`, `Quantity`, `UnitCD`, `InVsOut`, `RemainingAmount`) 
              VALUES ('$datetime','$skb','$bahan','$flowout','$unit','0','$newAmount')";
    $result = mysqli_query($conn, $query);
}else if($group == "BPETK"){
    $queryC = "SELECT * FROM mat_petk_ledger ORDER BY 1 DESC LIMIT 1";
    $resultC = mysqli_query($conn, $queryC);
    $rowC = mysqli_fetch_assoc($resultC);

    $remaining = $rowC["RemainingAmount"];
    $newAmount = $remaining + $flowout; //flowout barang keluar dari stok masuk ledger

    $query = "INSERT INTO `mat_pph_ledger`(`CreatedOn`, `ReferenceKey`, `MaterialCD`, `Quantity`, `UnitCD`, `InVsOut`, `RemainingAmount`) 
              VALUES ('$datetime','$skb','$bahan','$flowout','$unit','0','$newAmount')";
    $result = mysqli_query($conn, $query);
}

// Log aksi
logAction($conn, $creator, 'Create', 'membuat SPK', $result ? 0 : 1, $skb);

// Redirect setelah berhasil atau gagal
$status = $result ? 'success' : 'error';
header("Location: ../Production/material-release-order.php?status=" . $status);
exit();

mysqli_close($conn);

function logAction($conn, $userID, $actionDone, $actionMSG, $actionStatus, $recordID)
{
    $timestamp = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO systemlog (Timestamp, UserID, ActionDone, ActionMSG, ActionStatus, RecordID) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssssss", $timestamp, $userID, $actionDone, $actionMSG, $actionStatus, $recordID);
        if (!$stmt->execute()) {
            error_log("Log action failed: " . $stmt->error);
        }
        $stmt->close();
    } else {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
}
?>