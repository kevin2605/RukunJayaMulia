<?php
include "../DBConnection.php";

//set timezone
date_default_timezone_set("Asia/Jakarta");


$onecheckin = $_POST["one-checkin"];
$onecheckout = $_POST["one-checkout"];
$twocheckin = $_POST["two-checkin"];
$twocheckout = $_POST["two-checkout"];
$threecheckin = $_POST["three-checkin"];
$threecheckout = $_POST["three-checkout"];
$createdOn = date('Y-m-d H:i:s');
$creator = $_COOKIE["UserID"];


$query = "UPDATE `setting_working_hour` SET `CheckIn`='$onecheckin',`CheckOut`='$onecheckout' WHERE `Shift`='Shift 1'";
$result = mysqli_query($conn, $query);

$query = "UPDATE `setting_working_hour` SET `CheckIn`='$twocheckin',`CheckOut`='$twocheckout' WHERE `Shift`='Shift 2'";
$result = mysqli_query($conn, $query);

$query = "UPDATE `setting_working_hour` SET `CheckIn`='$threecheckin',`CheckOut`='$threecheckout' WHERE `Shift`='Shift 3'";
$result = mysqli_query($conn, $query);

if ($result) {
    logAction($conn, $creator, 'Update', 'berhasil memperbarui jam kerja', 0, "-");
    header("Location:../Setting/index.php?status=success");
} else {
    logAction($conn, $creator, 'Update', 'gagal memperbarui jam kerja', 1, "-");
    header("Location:../Setting/index.php?status=error");
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