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
$prodOne = explode(" - ",$_POST["productOne"])[0];
$qtyOne = $_POST["qtyOne"];
$prodTwo = explode(" - ",$_POST["productTwo"])[0];
$qtyTwo = $_POST["qtyTwo"];
$prodRes = explode(" - ",$_POST["productRes"])[0];
$qtyRes = $_POST["qtyRes"];
$desc = "Mutasi Produk Premium " . $prodRes;

// Insert header

$queryh = "INSERT INTO `mutationheader` (`MutationID`, `CreatedOn`, `CreatedBy`, `Description`, `CategoryCD`) 
           VALUES ('$mutid', '$datetime', '$creator', '$desc', 'PRE')";
$resulth = mysqli_query($conn, $queryh);

if ($resulth) {
    $insertSuccess = true;

    // Insert mutation detail keluar
    $queryInsertMutDet = "INSERT INTO `mutationdetail`(`MutationID`, `CreatedOn`, `ProductCD`, `FlowIn`, `FlowOut`, `UnitCD`, `Description`)
                        VALUES ('$mutid','$datetime','$prodOne','0','$qtyOne','BJ','Mutasi Premium'),
                                ('$mutid','$datetime','$prodTwo','0','$qtyTwo','BJ','Mutasi Premium')";
    if (!mysqli_query($conn, $queryInsertMutDet)) {
        echo "Error insert mutasi premium barang keluar: " . mysqli_error($conn);
        $insertSuccess = false;
    }

    // Insert mutation detail masuk
    $queryInsertMutDet = "INSERT INTO `mutationdetail`(`MutationID`, `CreatedOn`, `ProductCD`, `FlowIn`, `FlowOut`, `UnitCD`, `Description`)
                        VALUES ('$mutid','$datetime','$prodRes','$qtyRes','0','SLOP','Mutasi Premium')";
    if (!mysqli_query($conn, $queryInsertMutDet)) {
        echo "Error insert mutasi premium barang masuk: " . mysqli_error($conn);
        $insertSuccess = false;
    }

    if ($insertSuccess) {
        logAction($conn, $creator, 'Create', 'menambahkan mutasi premium', 0, $mutid);
        header("Location:../Mutation/premium.php?status=new-success");
    } else {
        logAction($conn, $creator, 'Create', 'gagal menambahkan mutasi premium', 1, $mutid);
        header("Location:../Mutation/premium.php?status=error");
    }
} else {
    logAction($conn, $creator, 'Create', 'gagal menambahkan header mutasi premium', 1, $mutid);
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