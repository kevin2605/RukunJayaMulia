<?php
include "../DBConnection.php";

// Set timezone
date_default_timezone_set("Asia/Jakarta");

// Generate Mutation ID
$query = "SELECT MutationID FROM mutationheader WHERE substr(CreatedOn, 6, 2)='" . date("m") . "' ORDER BY MutationID DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$lastnumber = $row["MutationID"] != "" ? intval(substr($row["MutationID"], 9)) + 1 : 1;

$mutid = "MUT-" . date("ym") . "-" . str_pad($lastnumber, 4, "0", STR_PAD_LEFT);
echo $mutid."\n";

if (isset($_COOKIE["UserID"]) && !empty($_COOKIE["UserID"])) {
    $creator = $_COOKIE["UserID"];
} else {
    die("Error: Cookie 'UserID' tidak ada atau kosong.");
}
$datetime = date('Y-m-d H:i:s');
$materialFrom = explode(" - ",$_POST["materialFrom"])[0];
$qtyFrom = $_POST["qtyFrom"];
$materialTo = explode(" - ",$_POST["materialTo"])[0];
$qtyTo = $_POST["qtyTo"];
$desc = "Mutasi Bahan PC 8 Generik";

// Insert header

$queryh = "INSERT INTO `mutationheader` (`MutationID`, `CreatedOn`, `CreatedBy`, `Description`, `CategoryCD`) 
           VALUES ('$mutid', '$datetime', '$creator', '$desc', 'BBPC8')";
$resulth = mysqli_query($conn, $queryh);

if ($resulth) {
    $insertSuccess = true;

    // Insert mutation detail keluar
    $queryInsertMutDet = "INSERT INTO `mutationdetailmat`(`MutationID`, `CreatedOn`, `MaterialCD`, `FlowIn`, `FlowOut`, `UnitCD`, `Description`)
                        VALUES ('$mutid','$datetime','$materialFrom','0','$qtyFrom','PLT','Mutasi Bahan PC8')";
    if (!mysqli_query($conn, $queryInsertMutDet)) {
        echo "Error insert mutasi premium barang keluar: " . mysqli_error($conn);
        $insertSuccess = false;
    }

    // Insert mutation detail masuk
    $queryInsertMutDet = "INSERT INTO `mutationdetailmat`(`MutationID`, `CreatedOn`, `MaterialCD`, `FlowIn`, `FlowOut`, `UnitCD`, `Description`)
                        VALUES ('$mutid','$datetime','$materialTo','$qtyTo','0','PLT','Mutasi Bahan PC8')";
    if (!mysqli_query($conn, $queryInsertMutDet)) {
        echo "Error insert mutasi premium barang masuk: " . mysqli_error($conn);
        $insertSuccess = false;
    }

    if ($insertSuccess) {
        logAction($conn, $creator, 'Create', 'menambahkan mutasi bahan PC8', 0, $mutid);
        header("Location:../Mutation/mutationMaterialPC8.php?status=new-success");
    } else {
        logAction($conn, $creator, 'Create', 'gagal menambahkan mutasi bahan PC8', 1, $mutid);
        header("Location:../Mutation/mutationMaterialPC8.php?status=error");
    }
} else {
    logAction($conn, $creator, 'Create', 'gagal menambahkan header mutasi bahan PC8', 1, $mutid);
    header("Location:../Mutation/premium.php?status=error");
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