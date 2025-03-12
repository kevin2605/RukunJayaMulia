<?php

include "../DBConnection.php";

// Set timezone
date_default_timezone_set("Asia/Jakarta");

// Generate PO ID
$query = "SELECT PurchaseOrderID FROM importpurchaseorderheader WHERE substr(CreatedOn,6,2)='" . date("m") . "' ORDER BY CreatedOn DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if ($row["PurchaseOrderID"] != "") {
    $lastnumber = substr($row["PurchaseOrderID"], 9);
    $lastnumber = intval($lastnumber);
    $lastnumber += 1;
} else {
    $lastnumber = 1;
}
// New Purchase Order ID
$poid = "POI-" . date("ym") . "-" . str_pad($lastnumber, 4, "0", STR_PAD_LEFT);

// Parameters
$deliverydate = $_POST["deliverydate"];
$kat = explode(" - ", $_POST["kategori"]);
$kategori = $kat[0];
$creator = $_POST["creator"];
$supplier = $_POST["supplier"];
$termin = $_POST["termin"];
$shipadd = $_POST["shipadd"];
$desc = $_POST["desc"];
$datetime = date('Y-m-d H:i:s');

//create Purchase Order Header
$queryh = "INSERT INTO `importpurchaseorderheader`(`PurchaseOrderID`, `CreatedOn`, `CreatedBy`, `DeliveryDate`, `ShippingAddress`, `CategoryCD`, `SupplierNum`, `Termin`, `Description`,
         `TotalPurchase`, `TotalPaid`, `ApprovalStatus`, `ApprovalBy`, `ApprovalOn`, `LastEdit`, `Finish`) VALUES 
         ('$poid','$datetime','$creator','$deliverydate','$shipadd','$kategori','$supplier','$termin','$desc','0','0','Pending',NULL,NULL,'$datetime','0')";
$resulth = mysqli_query($conn, $queryh);

// Total purchasing
$totalInvoice = 0;
$resultd = true;

if ($resulth == 1) {
    $arrMaterial = $_POST["materials"];
    $arrQty = $_POST["quantities"];
    $arrUnit = $_POST["units"];
    $arrPrice = $_POST["prices"];
    $arrSubt = $_POST["subtotals"];

    for ($i = 0; $i < count($arrMaterial); $i++) {
        $matcd = $arrMaterial[$i];
        $qty = $arrQty[$i];
        $unit = $arrUnit[$i];
        $price = $arrPrice[$i];
        $subtotal = $qty * $price;

        $queryd = "INSERT INTO `importpurchaseorderdetail`(`PurchaseOrderID`, `CreatedOn`, `ItemCD`, `Quantity`, `UnitCD`, `Price`, `Subtotal`, `QuantityReceived`) 
                    VALUES ('$poid','$datetime','$matcd','$qty','$unit','$price','$subtotal','0')";
        $resultd = $resultd && mysqli_query($conn, $queryd);

        $totalInvoice += $subtotal;
    }
}

$queryu = "UPDATE importpurchaseorderheader SET TotalPurchase='" . $totalInvoice . "' WHERE PurchaseOrderID='" . $poid . "'";
$resultu = mysqli_query($conn, $queryu);

// Log aktivitas ke tabel systemlog
logAction($conn, $creator, 'Create', 'membuat purchase order bahan baku import', ($resulth == 1 && $resultd == 1 && $resultu == 1) ? 0 : 1, $poid);

if ($resulth == 1 && $resultd == 1) {
    header("Location:../Import-Purchasing/purchasing-material.php?status=success-po");
} else {
    header("Location:../Import-Purchasing/purchasing-material.php?status=error-po");
}

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