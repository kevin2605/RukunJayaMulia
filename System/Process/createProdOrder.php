<?php
include "../DBConnection.php";

// Set timezone
date_default_timezone_set("Asia/Jakarta");

// Ambil data dari POST
$desc = $_POST["desc"];
$group = explode(" - ",  $_POST["group"])[0];
$produk = explode(" - ",  $_POST["produk"])[0];
$unit = $_POST["unit"];
$quantity = $_POST["quantity"];
$machine = explode(" - ", $_POST["machine"])[0];
$creator = $_COOKIE["UserID"] ?? 'unknown';
$datetime = date('Y-m-d H:i:s');

$queryCheck = "SELECT COUNT(*) as total FROM productionorder WHERE ProductCD = '$produk' AND Status=0";
$resultCheck = mysqli_query($conn, $queryCheck);
$rowCheck = mysqli_fetch_assoc($resultCheck);

if ($rowCheck['total'] >= 2) {
    header("Location: ../Production/production-order.php?status=limit-exceeded");
    exit();
} else {
    $query = "SELECT ProductionOrderID FROM productionorder WHERE substr(CreatedOn,6,2)='" . date("m") . "' ORDER BY CreatedOn DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row["ProductionOrderID"] != "") {
        $lastnumber = substr($row["ProductionOrderID"], 9);
        $lastnumber = intval($lastnumber);
        $lastnumber += 1;
    } else {
        $lastnumber = 1;
    }

    $spk = "SPK-" . date("ym") . "-" . str_pad($lastnumber, 4, "0", STR_PAD_LEFT);

    $query = "INSERT INTO `productionorder`(`ProductionOrderID`, `CreatedOn`, `CreatedBy`, `Description`, `GroupCD`, `MachineCD`, `ProductCD`, `UnitCD`, `QtyOrder`, `QtyProduced`, `Status`) 
              VALUES ('$spk','$datetime','$creator','$desc','$group','$machine','$produk','$unit','$quantity','0','0')";
    $result = mysqli_query($conn, $query);

    // Log aksi
    logAction($conn, $creator, 'Create', 'membuat SPK', $result ? 0 : 1, $spk);

    // Redirect setelah berhasil atau gagal
    $status = $result ? 'success' : 'error';
    header("Location: ../Production/production-order.php?status=" . $status);
    exit();
}

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