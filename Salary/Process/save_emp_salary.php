<?php
include "../DBConnection.php";

//set timezone
date_default_timezone_set("Asia/Jakarta");


$nik = explode(" - ", $_POST["employee"])[0];
$periode = $_POST["month"];
$months = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
$createdOn = date('Y-m-d H:i:s');
$creator = $_COOKIE["UserID"];

$compcodes = $_POST["componentcode"];
//print_r ($compcode);
$compvalues = $_POST["componentvalue"];
//print_r ($compvalue);
$multipliers = $_POST["multiplier"];
//print_r ($multiplier);

//CHECK IF EVER GENERATED BEFORE
$queryC = "SELECT COUNT(*) AS exist FROM empsalaryheader WHERE NIK='$nik' AND Periode='$periode'";
$resultC = mysqli_query($conn, $queryC);
$rowC = mysqli_fetch_assoc($resultC);

if($rowC["exist"] > 0){
    logAction($conn, $creator, 'Create', 'gagal membuat slip gaji karyawan', 1, $nik);
    header("Location:../Tools/employee-salary-list.php?status=exist");
}else{
    // Generate Slip Number
    $query = "SELECT SlipNum FROM empsalaryheader WHERE substr(CreatedOn,6,2)='" . date("m") . "' ORDER BY SlipNum DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row["SlipNum"] != "") {
        $lastnumber = substr($row["SlipNum"], 10);
        $lastnumber = intval($lastnumber);
        $lastnumber += 1;
    } else {
        $lastnumber = 1;
    }

    $slipnum = "SLIP-" . date("ym") . "-" . str_pad($lastnumber, 4, "0", STR_PAD_LEFT);

    //INSERT SLIP HEADER
    $queryH = "INSERT INTO `empsalaryheader`(`SlipNum`, `NIK`, `CreatedOn`, `Periode`) VALUES ('$slipnum','$nik','$createdOn','$periode')";
    $resulth = mysqli_query($conn, $queryH);
    if($resulth == 1){
        //INSERT SLIP DETAIL
        for($i = 0; $i < count($compcodes); $i++){
            $code = $compcodes[$i];
            $value = $compvalues[$i];
            $multiplier = $multipliers[$i];

            $queryd = "INSERT INTO `empsalarydetail`(`SlipNum`, `NIK`, `Periode`, `ComponentCode`, `ComponentValue`, `Multiplier`)
                    VALUES ('$slipnum','$nik','$periode','$code','$value','$multiplier')";
            $resultd = mysqli_query($conn, $queryd);
            if($resultd != 1){
                die("Komponen Gaji bermasalah! Cek kembali!");
            }
        }
    }

    if ($resulth == 1 && $resultd == 1) {
        logAction($conn, $creator, 'Create', 'berhasil membuat slip gaji karyawan', 0, $slipnum);
        header("Location:../Tools/employee-salary-list.php?status=success&slipnum=".$slipnum);
    } else {
        logAction($conn, $creator, 'Create', 'gagal membuat slip gaji karyawan', 1, $slipnum);
        header("Location:../Tools/employee-salary-list.php?status=error");
    }
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