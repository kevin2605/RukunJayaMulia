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
$prodOne = explode(" - ",$_POST["productOne"])[0];
$qtyOut = $_POST["qtyOut"];
$prodRes = explode(" - ",$_POST["productRes"])[0];
$desc = "Mutasi Fan ke Fan Print " . $prodRes;

// Insert header

$queryh = "INSERT INTO `mutationfanheader` (`MutationID`, `CreatedOn`, `CreatedBy`, `Description`, `CategoryCD`) 
           VALUES ('$mufid', '$datetime', '$creator', '$desc', 'FTF')";
$resulth = mysqli_query($conn, $queryh);

if ($resulth) {
    $insertSuccess = true;

    // Insert mutation detail
    $queryInsertMutDet = "INSERT INTO `mutationdetailfan`(`MutationID`, `CreatedOn`, `MaterialCD`, `FlowIn`, `FlowOut`, `UnitCD`, `Description`)
                        VALUES ('$mufid','$datetime','$prodOne','0','$qtyOut','FAN','Mutasi Fan Print'),
                        ('$mufid','$datetime','$prodRes','0','0','FAN','Mutasi Fan Print')";
    if (!mysqli_query($conn, $queryInsertMutDet)) {
        echo "Error insert mutasi konversi palet ke fan: " . mysqli_error($conn);
        $insertSuccess = false;
    }

    if($insertSuccess){
        // Update stok fan polos - dikurangi
        $queryUpdateStock = "UPDATE material SET StockQty = StockQty - '$qtyOut' WHERE MaterialCD = '$prodOne';";
        mysqli_query($conn,$queryUpdateStock);

        // insert fan flow history keluar
        $queryUpdateStock = "INSERT INTO `materialflowhistory`(`Date`, `ReferenceKey`, `MaterialCD`, `FlowIn`, `FlowOut`, `Description`) 
                            VALUES ('$datetime','$mufid','$prodOne','0','$qtyOut','$desc')";
        mysqli_query($conn,$queryUpdateStock);

        //update material fan print setelah penerimaan dari hasil printing
    }

    if ($insertSuccess) {
        logAction($conn, $creator, 'Create', 'menambahkan mutasi konversi', 0, $mufid);
        header("Location:../Mutation/convertFANtoFAN.php?status=new-success");
    } else {
        logAction($conn, $creator, 'Create', 'gagal menambahkan mutasi konversi', 1, $mufid);
        header("Location:../Mutation/convertFANtoFAN.php?status=error");
    }
} else {
    logAction($conn, $creator, 'Create', 'gagal menambahkan header mutasi konversi', 1, $mufid);
    header("Location:../Mutation/convertFANtoFAN.php?status=error");
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