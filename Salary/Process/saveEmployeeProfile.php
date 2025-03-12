<?php
include "../DBConnection.php";

//set timezone
date_default_timezone_set("Asia/Jakarta");


$nik = $_POST["nik"];
$dob = $_POST["dob"];
$address = $_POST["address"];
$city = $_POST["city"];
$lastedu = $_POST["lastedu"];
$kategori = $_POST["category"];
$gender = $_POST["gender"];
$position = $_POST["position"];
$status = $_POST["status"];
$workinghours = $_POST["workinghours"];
$breaktime = $_POST["breaktime"];
$createdOn = date('Y-m-d H:i:s');
$creator = $_COOKIE["UserID"];


$query = "UPDATE `employee` SET `DateOfBirth`='$dob',`Address`='$address',`City`='$city',`Gender`='$gender',`LastEducation`='$lastedu',`Position`='$position',
          `Status`='$status',`WorkingHours`='$workinghours', `Category`='$kategori',`BreakTime`='$breaktime' WHERE `NIK`='$nik'";
$result = mysqli_query($conn, $query);

if ($result) {
    logAction($conn, $creator, 'Update', 'berhasil memperbarui profil karyawan', 0, $nik);
    header("Location:../Employee/Edit-Employee.php?status=success-profile&NIK=".$nik);
} else {
    logAction($conn, $creator, 'Update', 'gagal memperbarui profil karyawan', 1, $nik);
    header("Location:../Employee/Edit-Employee.php?status=success-profile&NIK=".$nik);
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