<?php

include "../DBConnection.php";

// Set timezone
date_default_timezone_set("Asia/Jakarta");

$namaekspedisi = $_POST["namaekspedisi"];
$alamat = $_POST["alamat"];
$telp = $_POST["telp"];
$npwp = $_POST["npwp"];
$desc = $_POST["desc"];

// Check id
$duplicate = true;
$expid = 0;

while ($duplicate) {
    $expid = rand(10000, 99999);
    $query = "SELECT COUNT(*) AS dup FROM expedition WHERE ExpeditionID='$expid'";
    $res = mysqli_query($conn, $query);
    $ctr = mysqli_fetch_assoc($res);
    $row = $ctr["dup"];

    if ($row >= 1) {
        $duplicate = true;
    } else {
        $duplicate = false;
    }
}

if (isset($_COOKIE["UserID"]) && !empty($_COOKIE["UserID"])) {
    $creator = $_COOKIE["UserID"];
} else {
    die("Error: Cookie 'UserID' tidak ada atau kosong.");
}

$createdOn = date('Y-m-d H:i:s');

$query = "INSERT INTO `expedition`(`ExpeditionID`, `ExpeditionName`, `Address`, `PhoneNumber`, `NPWP`, `Description`) 
            VALUES ('$expid','$namaekspedisi','$alamat','$telp','$npwp','$desc')";
$result = mysqli_query($conn, $query);

if ($result) {
    logAction($conn, $creator, 'Create', 'menambahkan nama ekspedisi', 0, $namalogo);
    header("Location:../Shipment/expedition.php.php?status=success");
} else {
    logAction($conn, $creator, 'Create', 'gagal menambahkan nama ekspedisi', 1, $namalogo);
    header("Location:../Shipment/expedition.php.php?status=error");
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