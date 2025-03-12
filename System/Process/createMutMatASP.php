<?php
include "../DBConnection.php";

// Set timezone
date_default_timezone_set("Asia/Jakarta");

// Generate Mutation ID
$query = "SELECT MutationID FROM mutationfanheader WHERE substr(CreatedOn, 6, 2)='" . date("m") . "' ORDER BY MutationID DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$lastnumber = $row["MutationID"] != "" ? intval(substr($row["MutationID"], 9)) + 1 : 1;

$mufid = "MUF-" . date("ym") . "-" . str_pad($lastnumber, 4, "0", STR_PAD_LEFT);
echo $mufid."\n";

if (isset($_COOKIE["UserID"]) && !empty($_COOKIE["UserID"])) {
    $creator = $_COOKIE["UserID"];
} else {
    die("Error: Cookie 'UserID' tidak ada atau kosong.");
}
$datetime = date('Y-m-d H:i:s');
$prodFrom = explode(" - ",$_POST["materialFrom"])[0];
$qtyFrom = $_POST["qtyFrom"];
$prodTo = explode(" - ",$_POST["materialTo"])[0];
$qtyTo = $_POST["qtyTo"];
$desc = "Mutasi Konversi Bahan All Sheet ke Fan " . $prodTo;

// Insert header

$queryh = "INSERT INTO `mutationfanheader` (`MutationID`, `CreatedOn`, `CreatedBy`, `Description`, `CategoryCD`) 
           VALUES ('$mufid', '$datetime', '$creator', '$desc', 'MASP')";
$resulth = mysqli_query($conn, $queryh);

if ($resulth) {
    $insertSuccess = true;

    // Insert mutation detail
    $queryInsertMutDet = "INSERT INTO `mutationdetailfan`(`MutationID`, `CreatedOn`, `MaterialCD`, `FlowIn`, `FlowOut`, `UnitCD`, `Description`)
                        VALUES ('$mufid','$datetime','$prodFrom','0','$qtyFrom','DOS','Mutasi Konversi'),
                        ('$mufid','$datetime','$prodTo','$qtyTo','0','FAN','Mutasi Konversi')";
    if (!mysqli_query($conn, $queryInsertMutDet)) {
        echo "Error insert mutasi konversi dos ke fan: " . mysqli_error($conn);
        $insertSuccess = false;
    }

    if($insertSuccess){
        //find palet avg price
        $queryFindAvgPrice = "SELECT AvgPrice FROM material WHERE MaterialCD='$prodFrom'";
        $resultAvg = mysqli_query($conn, $queryFindAvgPrice);
        $rowAvg = mysqli_fetch_assoc($resultAvg);
        echo $rowAvg["AvgPrice"];

        //count avg each fan
        $AvgFan = ($rowAvg["AvgPrice"]*$qtyFrom)/$qtyTo;

        // Update stok dos berkurang
        $queryUpdateStock1 = "UPDATE material SET StockQty = StockQty - '$qtyFrom' WHERE MaterialCD = '$prodFrom'";
        mysqli_query($conn,$queryUpdateStock1);

        // Update stok fan bertambah
        $queryUpdateStock2 = "UPDATE material SET StockQty = StockQty + '$qtyTo', AvgPrice = '$AvgFan' WHERE MaterialCD = '$prodTo'";
        mysqli_query($conn,$queryUpdateStock2);

        // insert material flow history
        $queryUpdateStock = "INSERT INTO `materialflowhistory`(`Date`, `ReferenceKey`, `MaterialCD`, `FlowIn`, `FlowOut`, `Description`) 
                            VALUES ('$datetime','$mufid','$prodFrom','0','$qtyFrom','$desc'),
                                    ('$datetime','$mufid','$prodTo','$qtyTo','0','$desc')";
        mysqli_query($conn,$queryUpdateStock);
    }

    if ($insertSuccess) {
        logAction($conn, $creator, 'Create', 'menambahkan mutasi konversi', 0, $mufid);
        header("Location:../Mutation/mutationASPrint.php?status=new-success");
    } else {
        logAction($conn, $creator, 'Create', 'gagal menambahkan mutasi konversi', 1, $mufid);
        header("Location:../Mutation/mutationASPrint.php?status=error");
    }
} else {
    logAction($conn, $creator, 'Create', 'gagal menambahkan header mutasi konversi', 1, $mufid);
    header("Location:../Mutation/mutationASPrint.php?status=error");
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