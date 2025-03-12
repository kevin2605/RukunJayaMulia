<?php

include "../DBConnection.php";

//set timezone
date_default_timezone_set("Asia/Jakarta");

$firstname = $_POST["firstname"];
$lastname = $_POST["lastname"];
$nik = $_POST["nik"];
$borncity = $_POST["borncity"];
$dob = $_POST["dob"];
$address = $_POST["address"];
$cityaddress = $_POST["cityaddress"];
$lastedu = $_POST["lastedu"];
$kategori = $_POST["kategori"];
$gender = $_POST["gender"];
$position = $_POST["position"];
$status = $_POST["status"];
$workinghours = $_POST["workinghours"];
$breaktime = $_POST["breaktime"];
$createdOn = date('Y-m-d H:i:s');
$creator = $_COOKIE["UserID"];

$query = "INSERT INTO `employee`(`NIK`, `EmpFrontName`, `EmpLastName`, `BornCity`, `DateOfBirth`, `Address`, `City`, `Gender`, `LastEducation`, `Position`, `Status`, `WorkingHours`, `BreakTime`, `Category`, `CreatedOn`)
            VALUES ('$nik', '$firstname', '$lastname', '$borncity', '$dob', '$address','$cityaddress', '$gender', '$lastedu', '$position', '$status', '$workinghours', '$breaktime', '$kategori', '$createdOn')";
$result = mysqli_query($conn, $query);

if ($result) {
    logAction($conn, $creator, 'Create', 'berhasil mendaftarkan karyawan baru', 0, $nik);
    header("Location:../Employee/index.php?status=success");
} else {
    logAction($conn, $creator, 'Create', 'gagal mendaftarkan karyawan baru', 1, $nik);
    header("Location:../Employee/index.php?status=error");
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