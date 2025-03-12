<?php
include "../DBConnection.php";

// Set timezone
date_default_timezone_set("Asia/Jakarta");

// Ambil data dari form
$purchaseOrderID = $_POST["PurchaseOrderID"]; // Ubah ini sesuai dengan nama field di form Anda
$deliverydate = $_POST["deliverydate"];
$kategori = $_POST["kategori"];
$creator = $_POST["creator"];
$supplier = $_POST["supplier"];
$termin = $_POST["termin"];
$shipadd = $_POST["shipadd"];
$desc = $_POST["desc"];
$datetime = date('Y-m-d H:i:s');

// Update Purchase Order Header
$queryh = "UPDATE `purchaseorderheader` SET 
            `DeliveryDate` = ?, 
            `ShippingAddress` = ?, 
            `CategoryCD` = ?, 
            `SupplierNum` = ?, 
            `Termin` = ?, 
            `Description` = ?, 
            `LastEdit` = ? 
            WHERE `PurchaseOrderID` = ?";
$stmt = $conn->prepare($queryh);
$stmt->bind_param("ssssssss", $deliverydate, $shipadd, $kategori, $supplier, $termin, $desc, $datetime, $purchaseOrderID);
$resulth = $stmt->execute();
$stmt->close();

// Hapus detail lama
$queryDeleteDetails = "DELETE FROM `purchaseorderdetail` WHERE `PurchaseOrderID` = ?";
$stmt = $conn->prepare($queryDeleteDetails);
$stmt->bind_param("s", $purchaseOrderID);
$stmt->execute();
$stmt->close();

// Total purchasing
$totalInvoice = 0;
$resultd = true;

if ($resulth) {
    $arrMaterial = $_POST["materials"] ?? [];
    $arrQty = $_POST["quantities"] ?? [];
    $arrUnit = $_POST["units"] ?? []; // Pastikan ini mengambil data units dari form
    $arrPrice = $_POST["prices"] ?? [];

    // Debugging: Cetak isi array units
    error_log("Units array: " . print_r($arrUnit, true));

    for ($i = 0; $i < count($arrMaterial); $i++) {
        $matcd = $arrMaterial[$i];
        $qty = $arrQty[$i];
        $unit = $arrUnit[$i]; // Ini seharusnya berisi nilai seperti "KG"
        $price = $arrPrice[$i];
        $subtotal = $qty * $price;

        // Debugging: Cetak nilai setiap item
        error_log("Saving item: Material=$matcd, Qty=$qty, Unit=$unit, Price=$price");

        $queryd = "INSERT INTO `purchaseorderdetail`
                   (`PurchaseOrderID`, `CreatedOn`, `ItemCD`, `Quantity`, `UnitCD`, `Price`, `Subtotal`, `QuantityReceived`) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, '0')";
        $stmt = $conn->prepare($queryd);
        $stmt->bind_param("sssdsdd", $purchaseOrderID, $datetime, $matcd, $qty, $unit, $price, $subtotal);
        $resultd = $stmt->execute();

        // Debugging: Cetak hasil query
        if (!$resultd) {
            error_log("Error inserting detail: " . $stmt->error);
        } else {
            error_log("Successfully inserted detail for Material: $matcd, Unit: $unit");
        }

        $stmt->close();

        $totalInvoice += $subtotal;
    }
}

$queryu = "UPDATE purchaseorderheader SET TotalPurchase=? WHERE PurchaseOrderID=?";
$stmt = $conn->prepare($queryu);
$stmt->bind_param("ds", $totalInvoice, $purchaseOrderID);
$resultu = $stmt->execute();
$stmt->close();

// Log aktivitas ke tabel systemlog
logAction($conn, $creator, 'Update', 'mengupdate purchase order bahan baku', ($resulth && $resultd && $resultu) ? 0 : 1, $purchaseOrderID);

if ($resulth && $resultd) {
    header("Location:../Local-Purchasing/purchasing-other.php?status=success-update");
} else {
    header("Location:../Local-Purchasing/purchasing-other.php?status=error-update");
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